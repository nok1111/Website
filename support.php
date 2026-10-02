<?php require_once 'engine/init.php'; include 'layout/overall/header.php';
?>

<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="text-align:center"><div class="donate-page__title" style="color:white">Support In Game</div><br></br>

<?php
$cache = new Cache('engine/cache/support');
if ($cache->hasExpired()) {
	// Fetch all staffs in-game.
	if ($config['ServerEngine'] == 'TFS_03') {
		$staffs = support_list03();
	} else $staffs = support_list();
	// Fetch group ids and names from config.php
	$groups = $config['ingame_positions'];
	// Loops through groups, separating each group element into an ID variable and name variable
	foreach ($groups as $group_id => $group_name) {
		// Loops through list of staffs
		if (!empty($staffs))
		foreach ($staffs as $staff) {
			if ($staff['group_id'] == $group_id) $srtGrp[$group_name][] = $staff;
		}
	}
	if (!empty($srtGrp)) {
		$cache->setContent($srtGrp);
		$cache->save();
	}
} else {
	$srtGrp = $cache->load();
}
$writeHeader = true;
if (!empty($srtGrp)) {
	foreach (array_reverse($srtGrp) as $grpName => $grpList) {
		?>
		<table id="supportTable" class="table table-striped">
			<?php if ($writeHeader) {
			$writeHeader = false; ?>
			<tr class="yellow">
				<th width="30%"><div class="donate-page__bonuses-title flex-cc">Group</div></th>
				<th width="40%"><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px">Name</div></th>
				<th width="30%"><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px">Status</div></th>
			</tr>
			<?php
			}
			foreach ($grpList as $char) {
				if ($char['name'] != $config['website_char']) {
					echo '<tr>';
					echo "<td width='30%'><div class='donate-page__bonuses-title flex-cc'>". $grpName ."</div></td>";
					echo '<td width="40%"><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px"><a href="characterprofile.php?name='. $char['name'] .'">'. $char['name'] .'</a></div></td>';
					echo "<td width='30%'><div class='donate-page__bonuses-title flex-cc' style='padding-left:30px'>". online_id_to_name($char['online']) ."</div></td>";
					echo '</tr>';
				}
			}
			?>
		</table>
		<?php
	}
}
echo'</table>'; include 'layout/overall/footer.php'; ?>