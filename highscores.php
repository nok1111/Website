<?php require_once 'engine/init.php'; include 'layout/overall/header.php';

if ($config['log_ip']) {
	znote_visitor_insert_detailed_data(3);
}

$highscore = $config['highscore'];
$g = $highscore['ignoreGroupId'];
$rows = $highscore['rows'];
$rowsPerPage = $highscore['rowsPerPage'];
$loadFlags = ($config['country_flags']['enabled'] && $config['country_flags']['highscores']) ? true : false;

// All available skill types with display names and categories
$skillTypes = array(
	// Combat
	7  => array('name' => 'Experience',  'cat' => 'combat'),
	8  => array('name' => 'Magic',       'cat' => 'combat'),
	3  => array('name' => 'Arcana',      'cat' => 'combat'),
	2  => array('name' => 'Melee',       'cat' => 'combat'),
	5  => array('name' => 'Defense',     'cat' => 'combat'),
	4  => array('name' => 'Distance',    'cat' => 'combat'),
	// Crafting
	10 => array('name' => 'Blacksmith',  'cat' => 'crafting'),
	11 => array('name' => 'Alchemy',     'cat' => 'crafting'),
	12 => array('name' => 'Enchanting',  'cat' => 'crafting'),
	13 => array('name' => 'Mining',      'cat' => 'crafting'),
	14 => array('name' => 'Herbalism',   'cat' => 'crafting'),
	15 => array('name' => 'Woodcutting', 'cat' => 'crafting'),
	// Progression
	16 => array('name' => 'Paragon Level', 'cat' => 'progression'),
	17 => array('name' => 'Fame Level',    'cat' => 'progression'),
);

// Parse GET params
$type = (isset($_GET['type'])) ? (int)getValue($_GET['type']) : 7;
if (!isset($skillTypes[$type])) $type = 7;

$page = getValue(@$_GET['page']);
if (!$page || $page == 0) $page = 1;
else $page = (int)$page;

// Vocation filter (only for combat skills)
$configVocations = $config['vocations'];
$vocationIds = array_keys($configVocations);
$vocation = 'all';
if (isset($_GET['vocation']) && is_numeric($_GET['vocation'])) {
	$vocation = (int)$_GET['vocation'];
	if (!in_array($vocation, $vocationIds)) $vocation = "all";
}

// Build vocation SQL clause
$vocSql = '';
if ($vocation !== 'all') {
	// Check if fromVoc exists to include both base and promoted
	if (isset($configVocations[$vocation]) && $configVocations[$vocation]['fromVoc'] !== false) {
		$vocSql = "AND `p`.`vocation` IN (" . (int)$configVocations[$vocation]['fromVoc'] . ", " . (int)$vocation . ")";
	} else {
		// Find promoted vocation that has fromVoc = this vocation
		$vocIds = array((int)$vocation);
		foreach ($configVocations as $vid => $vdata) {
			if ($vdata['fromVoc'] === $vocation) $vocIds[] = (int)$vid;
		}
		$vocSql = "AND `p`.`vocation` IN (" . implode(',', $vocIds) . ")";
	}
}

// Query function for standard skills (from players table)
function fetchStandardSkill($type, $g, $rows, $vocSql, $loadFlags) {
	$colMap = array(
		2 => 'skill_sword',
		3 => 'skill_axe',
		4 => 'skill_dist',
		5 => 'skill_shielding',
		7 => 'experience',
		8 => 'maglevel',
	);
	if (!isset($colMap[$type])) return false;

	$col = $colMap[$type];
	$valueCol = ($type == 7) ? "`p`.`level` AS `value`, `p`.`experience`" : "`p`.`$col` AS `value`";
	$orderCol = ($type == 7) ? "`p`.`experience`" : "`p`.`$col`";

	$flagJoin = '';
	$flagCol = '';
	if ($loadFlags) {
		$flagJoin = "INNER JOIN `znote_accounts` AS `za` ON `p`.`account_id`=`za`.`account_id`";
		$flagCol = ", `za`.`flag`";
	}

	$sql = "SELECT `p`.`name`, `p`.`vocation`, $valueCol $flagCol FROM `players` AS `p` $flagJoin WHERE `p`.`group_id` < $g AND `p`.`vocation` > 0 $vocSql ORDER BY $orderCol DESC LIMIT 0, $rows;";
	return mysql_select_multi($sql);
}

// Query function for crafting skills (from player_profession table)
function fetchCraftingSkill($type, $g, $rows, $vocSql, $loadFlags) {
	$colMap = array(
		10 => 'skill_blacksmith',
		11 => 'skill_alchemy',
		12 => 'skill_enchanting',
		13 => 'skill_mining',
		14 => 'skill_herbalism',
		15 => 'skill_rune_seeker',
	);
	if (!isset($colMap[$type])) return false;

	$col = $colMap[$type];
	$flagJoin = '';
	$flagCol = '';
	if ($loadFlags) {
		$flagJoin = "INNER JOIN `znote_accounts` AS `za` ON `p`.`account_id`=`za`.`account_id`";
		$flagCol = ", `za`.`flag`";
	}

	$sql = "SELECT `p`.`name`, `p`.`vocation`, `pp`.`$col` AS `value` $flagCol
		FROM `player_profession` AS `pp`
		INNER JOIN `players` AS `p` ON `pp`.`player_id` = `p`.`id`
		$flagJoin
		WHERE `p`.`group_id` < $g AND `p`.`vocation` > 0 AND `pp`.`$col` > 0 $vocSql
		ORDER BY `pp`.`$col` DESC LIMIT 0, $rows;";
	return mysql_select_multi($sql);
}

// Query function for paragon level (from player_storage, key 90000)
function fetchParagonLevel($g, $rows, $vocSql, $loadFlags) {
	$flagJoin = '';
	$flagCol = '';
	if ($loadFlags) {
		$flagJoin = "INNER JOIN `znote_accounts` AS `za` ON `p`.`account_id`=`za`.`account_id`";
		$flagCol = ", `za`.`flag`";
	}

	$sql = "SELECT `p`.`name`, `p`.`vocation`, `ps`.`value` AS `value` $flagCol
		FROM `player_storage` AS `ps`
		INNER JOIN `players` AS `p` ON `ps`.`player_id` = `p`.`id`
		$flagJoin
		WHERE `ps`.`key` = 90000 AND `ps`.`value` > 0 AND `p`.`group_id` < $g AND `p`.`vocation` > 0 $vocSql
		ORDER BY `ps`.`value` DESC LIMIT 0, $rows;";
	return mysql_select_multi($sql);
}

// Query function for fame level (from player_fame, account-wide)
function fetchFameLevel($g, $rows, $loadFlags) {
	$flagJoin = '';
	$flagCol = '';
	if ($loadFlags) {
		$flagJoin = "INNER JOIN `znote_accounts` AS `za` ON `pf`.`account_id`=`za`.`account_id`";
		$flagCol = ", `za`.`flag`";
	}

	// Fame is account-wide; pick highest-level character name per account
	$sql = "SELECT `p`.`name`, `p`.`vocation`, `pf`.`level` AS `value` $flagCol
		FROM `player_fame` AS `pf`
		INNER JOIN `players` AS `p` ON `p`.`account_id` = `pf`.`account_id` AND `p`.`group_id` < $g AND `p`.`vocation` > 0
		$flagJoin
		WHERE `pf`.`level` > 0
		GROUP BY `pf`.`account_id`
		ORDER BY `pf`.`level` DESC LIMIT 0, $rows;";
	return mysql_select_multi($sql);
}

// Fetch results based on type
$results = false;
if ($type >= 2 && $type <= 8) {
	$results = fetchStandardSkill($type, $g, $rows, $vocSql, $loadFlags);
} elseif ($type >= 10 && $type <= 15) {
	$results = fetchCraftingSkill($type, $g, $rows, $vocSql, $loadFlags);
} elseif ($type == 16) {
	$results = fetchParagonLevel($g, $rows, $vocSql, $loadFlags);
} elseif ($type == 17) {
	$results = fetchFameLevel($g, $rows, $loadFlags);
}

$totalResults = ($results) ? count($results) : 0;
$totalPages = max(1, ceil(min($rows, $totalResults) / $rowsPerPage));
if ($page > $totalPages) $page = $totalPages;

$currentCat = $skillTypes[$type]['cat'];
?>

<div class="cd-page-wrap">
<h1 class="cd-page-title">Highscores</h1>

<!-- Category Tabs -->
<div class="hs-categories">
	<div class="hs-cat-group">
		<span class="hs-cat-label">Combat</span>
		<div class="hs-pills">
			<?php foreach ($skillTypes as $tid => $tdata): if ($tdata['cat'] !== 'combat') continue; ?>
				<a href="?type=<?php echo $tid; ?>&vocation=<?php echo $vocation; ?>" class="hs-pill<?php if ($type == $tid) echo ' active'; ?>"><?php echo $tdata['name']; ?></a>
			<?php endforeach; ?>
		</div>
	</div>
	<div class="hs-cat-group">
		<span class="hs-cat-label">Crafting</span>
		<div class="hs-pills">
			<?php foreach ($skillTypes as $tid => $tdata): if ($tdata['cat'] !== 'crafting') continue; ?>
				<a href="?type=<?php echo $tid; ?>&vocation=<?php echo $vocation; ?>" class="hs-pill<?php if ($type == $tid) echo ' active'; ?>"><?php echo $tdata['name']; ?></a>
			<?php endforeach; ?>
		</div>
	</div>
	<div class="hs-cat-group">
		<span class="hs-cat-label">Progression</span>
		<div class="hs-pills">
			<?php foreach ($skillTypes as $tid => $tdata): if ($tdata['cat'] !== 'progression') continue; ?>
				<a href="?type=<?php echo $tid; ?>&vocation=<?php echo $vocation; ?>" class="hs-pill<?php if ($type == $tid) echo ' active'; ?>"><?php echo $tdata['name']; ?></a>
			<?php endforeach; ?>
		</div>
	</div>
</div>

<!-- Vocation Filter (only for non-fame types) -->
<?php if ($type != 17): ?>
<div class="hs-voc-bar">
	<a href="?type=<?php echo $type; ?>&vocation=all" class="hs-voc<?php if ($vocation === 'all') echo ' active'; ?>">All</a>
	<?php foreach ($configVocations as $v_id => $v_data):
		if ($v_data['fromVoc'] !== false || $v_id == 0) continue; ?>
		<a href="?type=<?php echo $type; ?>&vocation=<?php echo $v_id; ?>" class="hs-voc<?php if ($vocation === $v_id) echo ' active'; ?>"><?php echo $v_data['name']; ?></a>
	<?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Results Table -->
<div class="hs-table-wrap">
	<div class="hs-table-header">
		<span class="hs-showing">Showing <strong><?php echo $skillTypes[$type]['name']; ?></strong> rankings<?php if ($vocation !== 'all' && $type != 17) echo ' for <strong>' . vocation_id_to_name($vocation) . '</strong>'; ?></span>
		<?php if ($totalPages > 1): ?>
		<div class="hs-pagination">
			<?php if ($page > 1): ?><a href="?type=<?php echo $type; ?>&vocation=<?php echo $vocation; ?>&page=<?php echo $page-1; ?>" class="hs-page-btn">&laquo;</a><?php endif; ?>
			<?php for ($i = 1; $i <= $totalPages; $i++): ?>
				<a href="?type=<?php echo $type; ?>&vocation=<?php echo $vocation; ?>&page=<?php echo $i; ?>" class="hs-page-btn<?php if ($i == $page) echo ' active'; ?>"><?php echo $i; ?></a>
			<?php endfor; ?>
			<?php if ($page < $totalPages): ?><a href="?type=<?php echo $type; ?>&vocation=<?php echo $vocation; ?>&page=<?php echo $page+1; ?>" class="hs-page-btn">&raquo;</a><?php endif; ?>
		</div>
		<?php endif; ?>
	</div>

	<table class="hs-table">
		<thead>
			<tr>
				<th class="hs-col-rank">#</th>
				<th class="hs-col-name">Player</th>
				<th class="hs-col-voc">Class</th>
				<th class="hs-col-val"><?php echo ($type == 7) ? 'Level' : $skillTypes[$type]['name']; ?></th>
				<?php if ($type == 7): ?><th class="hs-col-exp">Experience</th><?php endif; ?>
			</tr>
		</thead>
		<tbody>
		<?php
		if (!$results || $totalResults == 0) {
			$colspan = ($type == 7) ? 5 : 4;
			echo '<tr><td colspan="'.$colspan.'" class="hs-empty">No rankings available yet.</td></tr>';
		} else {
			$start = ($page - 1) * $rowsPerPage;
			$end = min($start + $rowsPerPage, $totalResults);
			for ($i = $start; $i < $end; $i++) {
				$r = $results[$i];
				$rank = $i + 1;
				$flag = ($loadFlags && isset($r['flag']) && strlen($r['flag']) > 1) ? '<img src="' . $config['country_flags']['server'] . '/' . $r['flag'] . '.png" class="hs-flag"> ' : '';
				$rankClass = '';
				if ($rank == 1) $rankClass = ' hs-gold';
				elseif ($rank == 2) $rankClass = ' hs-silver';
				elseif ($rank == 3) $rankClass = ' hs-bronze';
				?>
				<tr>
					<td class="hs-col-rank"><span class="hs-rank<?php echo $rankClass; ?>"><?php echo $rank; ?></span></td>
					<td class="hs-col-name"><?php echo $flag; ?><a href="characterprofile.php?name=<?php echo urlencode($r['name']); ?>"><?php echo htmlspecialchars($r['name']); ?></a></td>
					<td class="hs-col-voc"><?php echo vocation_id_to_name($r['vocation']); ?></td>
					<td class="hs-col-val"><?php echo number_format($r['value']); ?></td>
					<?php if ($type == 7): ?><td class="hs-col-exp"><?php echo number_format($r['experience']); ?></td><?php endif; ?>
				</tr>
				<?php
			}
		}
		?>
		</tbody>
	</table>
</div>
</div>

<?php include 'layout/overall/footer_login.php'; ?>
