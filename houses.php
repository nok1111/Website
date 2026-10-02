<?php
require_once 'engine/init.php';
include 'layout/overall/header.php';

if ($config['log_ip'])
	znote_visitor_insert_detailed_data(3);

// Fetch values
$querystring_id = &$_GET['id'];
$townid = ($querystring_id) ? (int)$_GET['id'] : $config['houseConfig']['HouseListDefaultTown'];
$towns = $config['towns'];

$order = &$_GET['order'];
$type = &$_GET['type'];

// Create Search house box
?>
<div class='global-desc__content' style='width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px'><div class='desc-changelog__block flex-ss'>
<div class="donate-page__title" style="color:white">Houses</div>
<div style="margin-left:auto;margin-right:auto"><form action="" method="get" class="houselist">
	<table>
		<tr>
			<td><div class="donate-page__bonuses-title flex-cc">Town</div></td>
			<td><div class="donate-page__bonuses-title flex-cc">Order</div></td>
			<td><div class="donate-page__bonuses-title flex-cc">Sort</div></td>
		</tr>
		<tr>
			<td>
				<select name="id" style="font-size:18px;color:white;background: rgba(14, 9, 8, 0.9);border: 1px solid #2d1f1e;">
				<?php
				foreach ($towns as $id => $name)
					echo '<option value="'. $id .'"' . ($townid != $id ?: ' selected') . '>'. $name .'</option>';
				?>
				</select>
			</td>
			<td>
				<select name="order" style="font-size:18px;color:white;background: rgba(14, 9, 8, 0.9);border: 1px solid #2d1f1e;">
				<?php
				$order_allowed = array('id', 'name', 'size', 'beds', 'rent', 'owner');
				foreach($order_allowed as $o)
					echo '<option value="' . $o . '"' . ($o != $order ?: ' selected') . '>' . ucfirst($o) . '</option>';
				?>
				</select>
			</td>
			<td>
				<select name="type" style="font-size:18px;color:white;background: rgba(14, 9, 8, 0.9);border: 1px solid #2d1f1e;">
				<?php
				$type_allowed = array('desc', 'asc');
				foreach($type_allowed as $t)
					echo '<option value="' . $t . '"' . ($t != $type ?: ' selected') . '>' . ($t == 'desc' ? 'Descending' : 'Ascending') .'</option>';
				?>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<center><button type="submit" value="Fetch houses" class="btn yellow flex-cc header__info-button"> Fetch Houses</button></center>
			</td>
		</tr>
	</table>
</form></div></div></div>
<?php
if(!in_array($order, $order_allowed))
	$order = 'id';

if(!in_array($type, $type_allowed))
	$type = 'desc';

// Create or fetch data from cache
$cache = new Cache('engine/cache/houses/houses-' . $order . '-' . $type);
$houses = array();

if ($cache->hasExpired()) {

	$houses = mysql_select_multi("
		SELECT 
			`id`, `owner`, `paid`, `warnings`, `name`, `rent`, `town_id`, 
			`size`, `beds`, `bid`, `bid_end`, `last_bid`, `highest_bidder` 
		FROM `houses` 
		ORDER BY {$order} {$type};
	");

	if ($houses !== false) {
		// Fetch player names
		$playerlist = array();

		foreach ($houses as $h)
			if ($h['owner'] > 0)
				$playerlist[] = $h['owner'];

		if (!empty($playerlist)) {
			$ids = join(',', $playerlist);
			$tmpPlayers = mysql_select_multi("SELECT `id`, `name` FROM players WHERE `id` IN ($ids);");

			// Sort $tmpPlayers by player id
			$tmpById = array();
			foreach ($tmpPlayers as $p)
				$tmpById[$p['id']] = $p['name'];

			for ($i = 0; $i < count($houses); $i++)
				if ($houses[$i]['owner'] > 0)
					$houses[$i]['ownername'] = $tmpById[$houses[$i]['owner']];
		}

		$cache->setContent($houses);
		$cache->save();
	}
} else
	$houses = $cache->load();

if ($houses !== false || !empty($houses)) {
	// Intialize stuff
	//data_dump($houses, false, "House data");
	?>
	<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">
	<div style="margin-left:auto;margin-right:auto;">
	<table id="housetable">
		<tr class="yellow">
			<th><div class="donate-page__bonuses-title flex-cc" >Name</div></th>
			<th><div class="donate-page__bonuses-title flex-cc"  style="padding-left:30px">Size</div></th>
			<th><div class="donate-page__bonuses-title flex-cc"  style="padding-left:30px">Beds</div></th>
			<th><div class="donate-page__bonuses-title flex-cc"  style="padding-left:30px">Rent</div></th>
			<th><div class="donate-page__bonuses-title flex-cc"  style="padding-left:30px">Owner</div></th>
			<th><div class="donate-page__bonuses-title flex-cc"  style="padding-left:30px">Town</div></th>
		</tr>
		<?php
		foreach ($houses as $house) {
			if ($house['town_id'] == $townid) {
				?>
				<tr>
					<td><div class="donate-page__bonuses-title flex-cc"><?php echo "<a href='house.php?id=". $house['id'] ."'>". $house['name'] ."</a>"; ?></div></td>
					<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px"><?php echo $house['size']; ?></div></td>
					<td><div class="donate-page__bonuses-title flex-cc"  style="padding-left:30px"><?php echo $house['beds']; ?></div></td>
					<td><div class="donate-page__bonuses-title flex-cc"  style="padding-left:30px"><?php echo $house['rent']; ?></div></td>
					<?php
					// Status:
					if ($house['owner'] != 0)
						echo "<td><div class='donate-page__bonuses-title flex-cc'><a href='characterprofile.php?name=". $house['ownername'] ."' target='_BLANK'>". $house['ownername'] ."</a></div></td>";
					else
						echo ($house['highest_bidder'] == 0 ? '<td><div class="donate-page__bonuses-title flex-cc">None</div></td>' : '<td><div class="donate-page__bonuses-title flex-cc"><b>Selling</b></div></td>');
					?>
					<td><div class="donate-page__bonuses-title flex-cc"><?php
					$town_name = &$towns[$house['town_id']];
					echo ($town_name ? $town_name : 'Specify town id ' . $house['town_id'] . ' name in config.php first.');
					?></div></td>
				</tr>
				<?php
			}
		}
		?>
	</table></div></div></div>

	<?php
} else {
	echo "<div class='global-desc__content' style='width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px'><div class='desc-changelog__block flex-ss'><h1>Failed to fetch data from sql->houses table.</h1><p>Is the table empty?</p></div></div>";
}

include 'layout/overall/footer_login.php'; ?>
