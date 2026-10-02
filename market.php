<?php require_once 'engine/init.php'; include 'layout/overall/header.php';

$server = $config['shop']['imageServer'];
$imageType = $config['shop']['imageType'];
$items = getItemList();
$compare = &$_GET['compare'];

// If we failed to load items.xml, a string is returned (not an array)
// with the attempted loaded file path.
// So if $items is not an array, send an error message, include the footer and ignore rest of this page.
if (is_array($items) === false):
	?><div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">
	<div class="donate-page__title" style="color:white">Marketplace</div><br></br>
	<p>Failed to load item list.</p>
	<p>Attempted to load this file: <?php echo $items; ?></p>
	<p>Configure correct 'server_path' in config.php.</p>
	<p>If the path is correct, make sure your web user has access to read it.</p></div></div>
	<?php
	include 'layout/overall/footer.php';
	die();
endif;

// If you are not comparing any items, present the list.
if (!$compare) {
	$cache = new Cache('engine/cache/market');
	$cache->setExpiration(60);
	if ($cache->hasExpired()) {
		$offers = array(
			'wts' => mysql_select_multi("SELECT `mo`.`id`, `mo`.`itemtype` AS `item_id`, `mo`.`amount`, `mo`.`price`, `mo`.`created`, `mo`.`anonymous`, `p`.`name` AS `player_name` FROM `market_offers` AS `mo` INNER JOIN `players` AS `p` ON `mo`.`player_id`=`p`.`id` WHERE `mo`.`sale` = '1'  ORDER BY `mo`.`created` DESC;"),
			'wtb' => mysql_select_multi("SELECT `mo`.`id`, `mo`.`itemtype` AS `item_id`, `mo`.`amount`, `mo`.`price`, `mo`.`created`, `mo`.`anonymous`, `p`.`name` AS `player_name` FROM `market_offers` AS `mo` INNER JOIN `players` AS `p` ON `mo`.`player_id`=`p`.`id` WHERE `mo`.`sale` = '0'  ORDER BY `mo`.`created` DESC;")
		);
		$cache->setContent($offers);
		$cache->save();
	} else {
		$offers = $cache->load();
	}
	?>
	<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">
	<div class="donate-page__title" style="color:white">Marketplace</div><br></br>
	<p>You can buy and sell items by clicking on the <a target="_BLANK" href="http://znote.eu/images/depotmarket.jpg">market in depot.</a> <br>To sell an item: Place item inside your depot, click on market, search for your item and sell it.</p>
</div></div>
<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">
<div class="donate-page__title" style="color:white">Search Item</div><br></br>
<div style="margin-left:auto;margin-right:auto">
	<form action="" class="market_item_search">
		<label for="compareSearch">Item search:</label>
		<input type="text" id="compareSearch" name="compare" style="font-size:18px;color:white;background: rgba(14, 9, 8, 0.9);border: 1px solid #2d1f1e;">
		<button type="submit" value="Search" class="btn yellow  header__info-button">Search</button>
	</form></div></div></div>
	<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">
	<div class="donate-page__title" style="color:white">Want to Sell</div><br></br>
	<table class="table tbl-hover">
		<tr class="yellow">
			<td><div class="donate-page__bonuses-title flex-cc">Item name</div></td>
			<td><div class="donate-page__bonuses-title flex-cc">Item</div></td>
			<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:20px">Count</div></td>
			<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:20px">Price for 1</div></td>
			<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:35px">Added</div></td>
			<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:50px">By</div></td>
			<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:50px">Compare</div></td>
		</tr>
		<?php
		foreach (($offers['wts'] ? $offers['wts'] : array()) as $o) {
		?>
		<tr>
			<td><div class="donate-page__bonuses-title flex-cc"><?php echo (isset($items[$o['item_id']])) ? $items[$o['item_id']] : $o['item_id']; ?></div></td>
			<td><div class="donate-page__bonuses-title flex-cc"><img src="<?php echo "http://".$server."/".$o['item_id'].".".$imageType; ?>" alt="Item Image"></div></td>
			<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:20px"><?php echo $o['amount']; ?></div></td>
			<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:20px"><?php echo number_format($o['price'], 0, "", " "); ?></div></td>
			<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:35px"><?php echo getClock($o['created'], true, true); ?></div></td>
			<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:50px"><?php echo ($o['anonymous'] == 1) ? 'Anonymous' : "<a target='_BLANK' href='characterprofile.php?name=".$o['player_name']."'>".$o['player_name']."</a>"; ?></div></td>
			<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:50px;padding-bottom:15px"><a href="?compare=<?php echo $o['item_id']; ?>"><button class="btn yellow  header__info-button">Compare</button></a></div></td>
		</tr>
		<?php
		}
		?>
	</table></div></div>
	<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">
	<div class="donate-page__title" style="color:white">Want to Buy</div><br></br>
	<table class="table tbl-hover">
		<tr class="yellow">
			<td><div class="donate-page__bonuses-title flex-cc">Item name</div></td>
			<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:20px">Item</div></td>
			<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:20px">Count</div></td>
			<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:20px">Price for 1</div></td>
			<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:35px">Added</div></td>
			<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:50px">By</div></td>
			<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:50px">Compare</div></td>
		</tr>
		<?php
		foreach (($offers['wtb'] ? $offers['wtb'] : array()) as $o) {
		?>
		<tr>
			<td><div class="donate-page__bonuses-title flex-cc"><?php echo (isset($items[$o['item_id']])) ? $items[$o['item_id']] : $o['item_id']; ?></div></td>
			<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:20px"><img src="<?php echo "http://".$server."/".$o['item_id'].".".$imageType; ?>" alt="Item Image"></div></td>
			<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:20px"><?php echo $o['amount']; ?></div></td>
			<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:20px"><?php echo number_format($o['price'], 0, "", " "); ?></div></td>
			<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:35px"><?php echo getClock($o['created'], true, true); ?></div></td>
			<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:50px"><?php echo ($o['anonymous'] == 1) ? 'Anonymous' : "<a target='_BLANK' href='characterprofile.php?name=".$o['player_name']."'>".$o['player_name']."</a>"; ?></div></td>
			<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:50px"><a href="?compare=<?php echo $o['item_id']; ?>"><button class="btn yellow  header__info-button">Compare</button></a></div></td>
		</tr>
		<?php
		}
		?>
	</table>
	</div></div>
	<?php
} else {
	// Else You want to compare price
	$compare = ((int)$compare > 0) ? (int)$compare : getValue($compare);

	$condition = "`itemtype`='$compare'";

	if (is_string($compare)) {
		$query = array();
		foreach ($items as $id => $name) {
			if (strpos(strtolower($name), stripslashes(strtolower($compare))) !== false) {
				$query[] = $id;
			}
		}
		$condition = (!empty($query)) ? "`itemtype` IN (". implode(',', $query) .")" : false;
	}

	// First list active bids
	if ($condition === false) {
		$offers = array();
		$historyOffers = array();
	} else {
		$offers = mysql_select_multi("SELECT `mo`.`id`, `mo`.`sale`, `mo`.`itemtype` AS `item_id`, `mo`.`amount`, `mo`.`price`, `mo`.`created`, `mo`.`anonymous`, `p`.`name` AS `player_name` FROM `market_offers` AS `mo` INNER JOIN `players` AS `p` ON `mo`.`player_id`=`p`.`id` WHERE `mo`.$condition ORDER BY `mo`.`price` ASC;");
		$historyOffers = mysql_select_multi("SELECT `id`, `itemtype` AS `item_id`, `amount`, `price`, `inserted`, `expires_at` FROM `market_history` WHERE $condition AND `state`='255' ORDER BY `price` ASC;");
	}
	$buylist = false;

	// Markup
	$itemname = (isset($items[$compare])) ? $items[$compare] : $compare;
	if (!is_string($compare)) echo "<div class='donate-page__title' style='color:white'>Comparing item: ". $itemname ."</div>";
	else echo "<div class='donate-page__title' style='color:white'>Search: ". stripslashes($compare) ."</div>";
	?><div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">
	<div class="donate-page__title" style="color:white">Active Offers</div><br></br>
	<a href="market.php"><button class="btn yellow  header__info-button">Go back</button></a></div></div>
	<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">
	<table class="table tbl-hover">
		<tr class="yellow">
			<td><div class="donate-page__bonuses-title flex-cc">Item name</div></td>
			<td><div class="donate-page__bonuses-title flex-cc">Item</div></td>
			<td><div class="donate-page__bonuses-title flex-cc">Count</div></td>
			<td><div class="donate-page__bonuses-title flex-cc">Price for 1</div></td>
			<td><div class="donate-page__bonuses-title flex-cc">Added</div></td>
			<td><div class="donate-page__bonuses-title flex-cc">By</div></td>
		</tr>
		<?php
		foreach (($offers ? $offers : array()) as $o) {
			$wtb = false;
			if ($o['sale'] == 0) {
				$wtb = true;
				if ($buylist === false) $buylist = array();
				$buylist[] = $o;
			} else {
				?>
				<tr>
					<td><div class="donate-page__bonuses-title flex-cc"><?php echo (isset($items[$o['item_id']])) ? $items[$o['item_id']] : $o['item_id']; ?></div></td>
					<td><div class="donate-page__bonuses-title flex-cc"><img src="<?php echo "http://".$server."/".$o['item_id'].".".$imageType; ?>" alt="Item Image"></div></td>
					<td><div class="donate-page__bonuses-title flex-cc"><?php echo $o['amount']; ?></div></td>
					<td><div class="donate-page__bonuses-title flex-cc"><?php echo number_format($o['price'], 0, "", " "); ?></div></td>
					<td><div class="donate-page__bonuses-title flex-cc"><?php echo getClock($o['created'], true, true); ?></div></td>
					<td><div class="donate-page__bonuses-title flex-cc"><?php echo ($o['anonymous'] == 1) ? 'Anonymous' : "<a target='_BLANK' href='characterprofile.php?name=".$o['player_name']."'>".$o['player_name']."</a>"; ?></div></td>
				</tr>
				<?php
			}
		}
		?>
	</table></div></div>
	<?php
	if ($buylist !== false) {
		?><div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">
		<div class='donate-page__title' style='color:white'>>Want to buy:</div>
		<table class="table tbl-hover">
			<tr class="yellow">
				<td><div class="donate-page__bonuses-title flex-cc">Item name</div></td>
				<td><div class="donate-page__bonuses-title flex-cc">Item</div></td>
				<td><div class="donate-page__bonuses-title flex-cc">Count</div></td>
				<td><div class="donate-page__bonuses-title flex-cc">Price for 1</div></td>
				<td><div class="donate-page__bonuses-title flex-cc">Added</div></td>
				<td><div class="donate-page__bonuses-title flex-cc">By</div></td>
			</tr>
			<?php
			foreach ($buylist as $o) {
				?>
				<tr>
					<td><div class="donate-page__bonuses-title flex-cc"><?php echo (isset($items[$o['item_id']])) ? $items[$o['item_id']] : $o['item_id']; ?></div></td>
					<td><div class="donate-page__bonuses-title flex-cc"><img src="<?php echo "http://".$server."/".$o['item_id'].".".$imageType; ?>" alt="Item Image"></div></td>
					<td><div class="donate-page__bonuses-title flex-cc"><?php echo $o['amount']; ?></div></td>
					<td><div class="donate-page__bonuses-title flex-cc"><?php echo number_format($o['price'], 0, "", " "); ?></div></td>
					<td><div class="donate-page__bonuses-title flex-cc"><?php echo getClock($o['created'], true, true); ?></div></td>
					<td><div class="donate-page__bonuses-title flex-cc"><?php echo ($o['anonymous'] == 1) ? 'Anonymous' : "<a target='_BLANK' href='characterprofile.php?name=".$o['player_name']."'>".$o['player_name']."</a>"; ?></div></td>
				</tr>
				<?php
			}
			?>
		</table></div></div>
		<?php
	}
	?><div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">
	<div class='donate-page__title' style='color:white'>Old purchased offers</div>
	<table class="table tbl-hover">
		<tr class="yellow">
			<td><div class="donate-page__bonuses-title flex-cc">Item name</div></td>
			<td><div class="donate-page__bonuses-title flex-cc">Item</div></td>
			<td><div class="donate-page__bonuses-title flex-cc">Count</div></td>
			<td><div class="donate-page__bonuses-title flex-cc">Price for 1</div></td>
			<td><div class="donate-page__bonuses-title flex-cc">Offer sold</div></td>
		</tr>
		<?php
		foreach (($historyOffers ? $historyOffers : array()) as $o) {
		?>
		<tr>
			<td><div class="donate-page__bonuses-title flex-cc"><?php echo (isset($items[$o['item_id']])) ? $items[$o['item_id']] : $o['item_id']; ?></div></td>
			<td><div class="donate-page__bonuses-title flex-cc"><img src="<?php echo "http://".$server."/".$o['item_id'].".".$imageType; ?>" alt="Item Image"></div></td>
			<td><div class="donate-page__bonuses-title flex-cc"><?php echo $o['amount']; ?></div></td>
			<td><div class="donate-page__bonuses-title flex-cc"><?php echo number_format($o['price'], 0, "", " "); ?></div></td>
			<td><div class="donate-page__bonuses-title flex-cc"><?php echo getClock($o['inserted'], true, true); ?></div></td>
		</tr>
		<?php
		}
		?>
	</table></div></div>
	<?php
}
include 'layout/overall/footer_login.php'; ?>
