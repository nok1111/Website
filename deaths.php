<?php require_once 'engine/init.php'; include 'layout/overall/header.php';
$cache = new Cache('engine/cache/deaths');
if ($cache->hasExpired()) {

	if ($config['ServerEngine'] == 'TFS_02' || $config['ServerEngine'] == 'TFS_10') {
		$deaths = fetchLatestDeaths();
	} else if ($config['ServerEngine'] == 'TFS_03' || $config['ServerEngine'] == 'OTHIRE') {
		$deaths = fetchLatestDeaths_03(30);
	}
	$cache->setContent($deaths);
	$cache->save();
} else {
	$deaths = $cache->load();
}
if ($deaths) {
?>
<div class="donate-page__title" style="color:white">Latest Deaths</div><br></br>
<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:13px"><div class="desc-changelog__block flex-ss" style="">
<table id="deathsTable" class="table table-striped">
	<tr class="yellow">
		<th><div class="donate-page__desc" style="color:white">Victim</div></th>
		<th><div class="donate-page__desc" style="color:white">Time</div></th>
		<th><div class="donate-page__desc" style="color:white">Killer</div></th>
	</tr>
	<?php foreach ($deaths as $death) {
		echo '<tr>';
		echo "<td><div class='global-form__line'> <a href='characterprofile.php?name=". $death['victim'] ."'>". $death['victim'] ."</a> at level ". $death['level'] ."</div></td>";
		echo "<td style='padding-left:20px'><div class='global-form__line'>". getClock($death['time'], true) ."</div></td>";
		if ($death['is_player'] == 1) echo "<td>Player: <a href='characterprofile.php?name=". $death['killed_by'] ."'>". $death['killed_by'] ."</a></td>";
		else if ($death['is_player'] == 0) {
			if ($config['ServerEngine'] == 'TFS_03') echo "<td>Monster: ". ucfirst(str_replace("a ", "", $death['killed_by'])) ."</td>";
			else echo "<td style='padding-left:20px'><div class='global-form__line'>Monster: ". ucfirst($death['killed_by']) ."</div></td>";
		}
		else echo "<td><div class='global-form__line'>". $death['killed_by'] ."</div></td>";
		echo '</tr>';
	} ?>
</table></div></div>
<?php
} else echo 'No deaths exist.';
include 'layout/overall/footer.php'; ?>
