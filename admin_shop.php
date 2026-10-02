<?php
require_once 'engine/init.php';
include 'layout/overall/header.php';
protect_page();
admin_only($user_data);

$orders = mysql_select_multi('SELECT * FROM `znote_shop_orders` ORDER BY `id` DESC;');
$order_types = array(1 => 'Item', 2 => 'Premium Days', 3 => 'Gender Change', 4 => 'Name Change', 5 => 'Outfits', 6 =>'Mounts');
$items = getItemList();
?>
<div class="donate-page__title" style="color:white">Shop Logs</div><br></br>
<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">
<p> This Part it's only for administrators, so i didn't finished this part as i dont added any item on the shop, as for the brand new release i will leave this part like this, with the time i will fix and add more things to the website, thanks to remember me to finish admin_shop page.<br>
When i say i didn't finished, the page works, but maybe it doesnt look good like other parts of website.</p>
<br></br>
</div></div><br></br>
<div class="donate-page__title" style="color:white">Pending Orders</div><br></br>
<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">
<p>These are pending orders, like items bought, but not received or used yet.</p>
<table>
	<thead>
		<th>Id</th>
		<th>Account</th>
		<th>Type</th>
		<th>Item</th>
		<th>Count</th>
		<th>Date</th>
	</thead>
	<tbody>
		<?php foreach(($orders ? $orders : array()) as $order) { ?>
		<tr>
			<td><?php echo $order['id']; ?></td>
			<td><?php echo user_account_id_from_name($order['account_id']); ?></td>
			<td><?php echo $order_types[$order['type']] ?></td>
			<td><?php echo '(' . $order['itemid'] . ') ', (isset($items[$order['itemid']])) ? $items[$order['itemid']] : ''; ?></td>
			<td><?php echo $order['count'] ?></td>
			<td><?php echo date('Y/m/d H:i', $order['time']) ?></td>
		</tr>
		<?php } ?>
	</tbody>
</table></div></div><br></br>

<?php
$orders = mysql_select_multi('SELECT * FROM `znote_shop_logs` ORDER BY `id` DESC;');
$order_types = array(1 => 'Item', 2 => 'Premium Days', 3 => 'Gender Change', 4 => 'Name Change', 5 => 'Outfit', 6 =>'Mount', 7 =>'Custom');
?>
<div class="donate-page__title" style="color:white">Order History</div><br></br>
<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">
<p>This list contains all transactions bought in the shop.</p>
<table>
	<thead>
		<th>Id</th>
		<th>Account</th>
		<th>Type</th>
		<th>Item</th>
		<th>Count</th>
		<th>points</th>
		<th>Date</th>
	</thead>
	<tbody>
		<?php foreach(($orders ? $orders : array()) as $order) { ?>
		<tr>
			<td><?php echo $order['id']; ?></td>
			<td><?php echo $order['account_id']; ?></td>
			<td><?php echo $order_types[$order['type']] ?></td>
			<td><?php echo '(' . $order['itemid'] . ') ', (isset($items[$order['itemid']])) ? $items[$order['itemid']] : ''; ?></td>
			<td><?php echo $order['count'] ?></td>
			<td><?php echo $order['points'] ?></td>
			<td><?php echo getClock($order['time'], true, false); ?></td>
		</tr>
		<?php } ?>
	</tbody>
</table></div></div>
<?php
include 'layout/overall/footer.php';
?>
