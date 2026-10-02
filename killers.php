<?php require_once 'engine/init.php'; include 'layout/overall/header.php';
if ($config['ServerEngine'] == 'TFS_02' || $config['ServerEngine'] == 'TFS_10' || $config['ServerEngine'] == 'OTHIRE') {
$cache = new Cache('engine/cache/killers');
if ($cache->hasExpired()) {
	$killers = fetchMurders();

	$cache->setContent($killers);
	$cache->save();
} else {
	$killers = $cache->load();
}
$cache = new Cache('engine/cache/victims');
if ($cache->hasExpired()) {
	$victims = fetchLoosers();

	$cache->setContent($victims);
	$cache->save();
} else {
	$victims = $cache->load();
}
$cache = new Cache('engine/cache/lastkillers');
if ($cache->hasExpired()) {
	$latests = mysql_select_multi("SELECT `p`.`name` AS `victim`, `d`.`killed_by` as `killed_by`, `d`.`time` as `time` FROM `player_deaths` as `d` INNER JOIN `players` as `p` ON d.player_id = p.id WHERE d.`is_player`='1' ORDER BY `time` DESC LIMIT 20;");
	if ($latests !== false) {
		$cache->setContent($latests);
		$cache->save();
	}
} else {
	$latests = $cache->load();
}
if ($killers) {
?><div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="text-align:center">
<div class="donate-page__title" style="color:white">Biggest Murders</div><br></br>
<table id="killersTable" class="table table-striped">
	<tr class="yellow">
		<th><div class="donate-page__bonuses-title flex-cc">Name</div></th>
		<th><div class="donate-page__bonuses-title flex-cc">Kills</div></th>
	</tr>
	<?php foreach ($killers as $killer) {
		echo '<tr>';
		echo "<td width='70%'><div class='donate-page__bonuses-title flex-cc'><a href='characterprofile.php?name=". $killer['killed_by'] ."'>". $killer['killed_by'] ."</a></div></td>";
		echo "<td width='30%'><div class='donate-page__bonuses-title flex-cc'>". $killer['kills'] ."</div></td>";
		echo '</tr>';
	} ?>
</table></div></div>
<?php
} else echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="text-align:center"><div class="donate-page__title" style="color:white">Biggest Murders</div><br></br>No player kills exist.</div></div>';

if ($victims) {
?><div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="text-align:center">
<div class="donate-page__title" style="color:white">Biggest Victims</div><br></br>
<div style="margin-left:auto;margin-right:auto">
<table id="victimsTable" class="table table-striped">
	<tr class="yellow">
		<th><div class='donate-page__bonuses-title flex-cc'>Name</div></th>
		<th><div class='donate-page__bonuses-title flex-cc'>Deaths</div></th>
	</tr>
	<?php foreach ($victims as $victim) {
		echo '<tr>';
		echo "<td width='70%'><div class='donate-page__bonuses-title flex-cc'><a href='characterprofile.php?name=". $victim['name'] ."'>". $victim['name'] ."</a></div></td>";
		echo "<td width='30%'><div class='donate-page__bonuses-title flex-cc'>". $victim['Deaths'] ."</div></td>";
		echo '</tr>';
	} ?>
</table></div></div></div>
<?php
} else echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="text-align:center"><div class="donate-page__title" style="color:white">Biggest Victims</div><br></br>No player kills exist.</div></div>';

if ($latests) {
?><div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="text-align:center">
<div class="donate-page__title" style="color:white">Latest Kills</div><br></br>
<div style="margin-left:auto;margin-right:auto">
<table id="killersTable" class="table table-striped">
	<tr class="yellow">
		<th><div class='donate-page__bonuses-title flex-cc'>Killer</div></th>
		<th><div class='donate-page__bonuses-title flex-cc'>Time</div></th>
		<th><div class='donate-page__bonuses-title flex-cc'>Victim</div></th>
	</tr>
	<?php foreach ($latests as $last) {
		echo '<tr>';
		echo "<td width='35%'><div class='donate-page__bonuses-title flex-cc'><a href='characterprofile.php?name=". $last['killed_by'] ."'>". $last['killed_by'] ."</a></div></td>";
		echo "<td width='30%'><div class='donate-page__bonuses-title flex-cc'>". getClock($last['time'], true) ."</div></td>";
		echo "<td width='35%'><div class='donate-page__bonuses-title flex-cc'><a href='characterprofile.php?name=". $last['victim'] ."'>". $last['victim'] ."</a></div></td>";
		echo '</tr>';
	} ?>
</table></div></div></div>
<?php
} else echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="text-align:center"><div class="donate-page__title" style="color:white">Latest Kills</div><br></br>No player kills exist.</div></div>';

} else if ($config['ServerEngine'] == 'TFS_03') {
	$cache = new Cache('engine/cache/killers');
	if ($cache->hasExpired()) {
		$deaths = fetchLatestDeaths_03(30, true);
		$cache->setContent($deaths);
		$cache->save();
	} else {
		$deaths = $cache->load();
	}

	if ($deaths && !empty($deaths)) {
	?><div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="text-align:center">
		<div class="donate-page__title" style="color:white">Latest Killers</div><br></br>
		<table id="deathsTable" class="table table-striped">
			<tr class="yellow">
				<th><div class='donate-page__bonuses-title flex-cc'>Killer</div></th>
				<th><div class='donate-page__bonuses-title flex-cc'>Time</div></th>
				<th><div class='donate-page__bonuses-title flex-cc'>Victim</div></th>
			</tr>
			<?php foreach ($deaths as $death) {
				echo '<tr>';
				echo "<td><div class='donate-page__bonuses-title flex-cc'><a href='characterprofile.php?name=". $death['killed_by'] ."'>". $death['killed_by'] ."</a></div></td>";
				echo "<td><div class='donate-page__bonuses-title flex-cc'>". getClock($death['time'], true) ."</div></td>";
				echo "<td><div class='donate-page__bonuses-title flex-cc'>At level ". $death['level'] .": <a href='characterprofile.php?name=". $death['victim'] ."'>". $death['victim'] ."</a></div></td>";
				echo '</tr>';
			} ?>
		</table></div></div>
		<?php
	} else echo '<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="text-align:center">No player deaths exist.</div></div>';
}
include 'layout/overall/footer_login.php'; ?>
