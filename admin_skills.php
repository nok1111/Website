<?php require_once 'engine/init.php'; include 'layout/overall/header.php';
protect_page();
admin_only($user_data);

// PREP: Create a function that echos player skills
function playerSkill($skills, $id) {
	if (!$skills) return 0;
	else {
		return $skills[$id]['value'];
	}
}

// UPDATE SKILLS POST
if (isset($_POST['pid']) && (int)$_POST['pid'] > 0) {
	$pid = (int)$_POST['pid'];
	if ($config['ServerEngine'] != 'TFS_10') $status = user_is_online($pid);
	else $status = user_is_online_10($pid);

	if (!$status) {
		// New player level
		$level = (int)$_POST['level'];

		// Fetch stat gain for vocation
		$statgain = $config['vocations_gain'][(int)$_POST['vocation']];
		$playercnf = $config['player'];

		/*
		if ((int)$_POST['vocation'] !== 0) {
			// Fetch base level and stats:
			$baselevel = $config['level'];
			$basehealth = $config['health'];
			$basemana = $config['mana'];
			$basecap = $config['cap'];
		} else { // No vocation stats
			// Fetch base level and stats:
			$baselevel = $config['nvlevel'];
			$basehealth = $config['nvHealth'];
			$basemana = $config['nvMana'];
			$basecap = $config['nvCap'];
		}
		*/

		$LevelsFromBase = $level - $playercnf['base']['level'];
		$newhp = $playercnf['base']['health'] + ($statgain['hp'] * $LevelsFromBase);
		$newmp = $playercnf['base']['mana'] + ($statgain['mp'] * $LevelsFromBase);
		$newcap = $playercnf['base']['cap'] + ($statgain['cap'] * $LevelsFromBase);

		// Calibrate hp/mana/cap
		if ($config['ServerEngine'] != 'TFS_10') {
mysql_update("UPDATE `player_skills` SET `value`='". (int)$_POST['fist'] ."' WHERE `player_id`='$pid' AND `skillid`='0' LIMIT 1;");
mysql_update("UPDATE `player_skills` SET `value`='". (int)$_POST['club'] ."' WHERE `player_id`='$pid' AND `skillid`='1' LIMIT 1;");
mysql_update("UPDATE `player_skills` SET `value`='". (int)$_POST['sword'] ."' WHERE `player_id`='$pid' AND `skillid`='2' LIMIT 1;");
mysql_update("UPDATE `player_skills` SET `value`='". (int)$_POST['axe'] ."' WHERE `player_id`='$pid' AND `skillid`='3' LIMIT 1;");
mysql_update("UPDATE `player_skills` SET `value`='". (int)$_POST['dist'] ."' WHERE `player_id`='$pid' AND `skillid`='4' LIMIT 1;");
mysql_update("UPDATE `player_skills` SET `value`='". (int)$_POST['shield'] ."' WHERE `player_id`='$pid' AND `skillid`='5' LIMIT 1;");
mysql_update("UPDATE `player_skills` SET `value`='". (int)$_POST['fish'] ."' WHERE `player_id`='$pid' AND `skillid`='6' LIMIT 1;");
mysql_update("UPDATE `players` SET `maglevel`='". (int)$_POST['magic'] ."' WHERE `id`='$pid' LIMIT 1;");
mysql_update("UPDATE `players` SET `vocation`='". (int)$_POST['vocation'] ."' WHERE `id`='$pid' LIMIT 1;");
mysql_update("UPDATE `players` SET `level`='". $level ."' WHERE `id`='$pid' LIMIT 1;");
mysql_update("UPDATE `players` SET `experience`='". level_to_experience($level) ."' WHERE `id`='$pid' LIMIT 1;");
// Update HP/mana/cap accordingly to level & vocation
mysql_update("UPDATE `players` SET `health`='". $newhp ."', `healthmax`='". $newhp ."', `mana`='". $newmp ."', `manamax`='". $newmp ."', `cap`='". $newcap ."' WHERE `id`='$pid' LIMIT 1;");
		} else {
			mysql_update("UPDATE `players` SET `health`='". $newhp ."', `healthmax`='". $newhp ."', `mana`='". $newmp ."', `manamax`='". $newmp ."', `cap`='". $newcap ."', `vocation`='". (int)$_POST['vocation'] ."', `skill_fist`='". (int)$_POST['fist'] ."', `skill_club`='". (int)$_POST['club'] ."', `skill_sword`='". (int)$_POST['sword'] ."', `skill_axe`='". (int)$_POST['axe'] ."', `skill_dist`='". (int)$_POST['dist'] ."', `skill_shielding`='". (int)$_POST['shield'] ."', `skill_fishing`='". (int)$_POST['fish'] ."', `maglevel`='". (int)$_POST['magic'] ."', `level`='". $level ."', `experience`='". level_to_experience($level) ."' WHERE `id`='$pid' LIMIT 1;");
		}
?>
<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">
<div class="donate-page__title" style="color:lime">Players Skills Updated</div><br></br></div></div>
<?php
	} else {
		?>
		<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">
		<font color="red" size="7">Player must be offline!</font></div></div>
		<?php
	}
}

// Stage 1: Fetch name
if (isset($_GET['name'])) {
	$name = getValue($_GET['name']);
} else $name = false;
//if (isset($_POST['name'])) $name = getValue($_POST['name']);

// Stage 2: Fetch user id and skills
$skills = false;
$pid = 0;
if ($name !== false) {
	if (user_character_exist($name)) {
		$pid = user_character_id($name);

		if ($config['ServerEngine'] != 'TFS_10') {
			$skills = mysql_select_multi("SELECT `value` FROM `player_skills` WHERE `player_id`='$pid' LIMIT 7;");
			$player = mysql_select_single("SELECT `maglevel`, `level`, `vocation` FROM `players` WHERE `id`='$pid' LIMIT 1;");
			$skills[] = array('value' => $player['maglevel']);
			$skills[] = array('value' => $player['level']);
			$skills[] = array('value' => $player['vocation']);
		} else {
			$player = mysql_select_single("SELECT `skill_fist`, `skill_club`, `skill_sword`, `skill_axe`, `skill_dist`, `skill_shielding`, `skill_fishing`, `maglevel`, `level`, `vocation` FROM `players` WHERE `id`='$pid' LIMIT 1;");
			$skills = array(
				0 => array('value' => $player['skill_fist']),
				1 => array('value' => $player['skill_club']),
				2 => array('value' => $player['skill_sword']),
				3 => array('value' => $player['skill_axe']),
				4 => array('value' => $player['skill_dist']),
				5 => array('value' => $player['skill_shielding']),
				6 => array('value' => $player['skill_fishing']),
				7 => array('value' => $player['maglevel']),
				8 => array('value' => $player['level']),
				9 => array('value' => $player['vocation'])
			);
		}

		//data_dump($skills, false, "Player skills");
	} else $name = false;
}

?>
<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">
<form action="" method="<?php if (!$name) echo "get"; else echo "post";?>">
	<input type="hidden" name="pid" value="<?php echo $pid; ?>">
	<table class="table">
		<tr class="yellow">
			<td colspan="2"><div class="donate-page__title" style="color:white">Player Skills Administration</div><br></br></td>
		</tr>
		<tr>
			<td>
				<input name="name" type="text" style="font-size:18px;color:white;background: rgba(14, 9, 8, 0.9);border: 1px solid #2d1f1e;" placeholder="Character name" <?php if ($name !== false) echo "value='$name' disabled";?>>
				<br><br>
				<div class="hts__block-files-title">Vocation:</div><br>
				<select style="font-size:18px;color:white;background: rgba(14, 9, 8, 0.9);border: 1px solid #2d1f1e;" name="vocation" <?php if (!$name) echo "disabled";?>>
					<?php
					$vocations = $config['vocations'];
					foreach ($vocations as $vid => $vname) {
						?>
						<option value="<?php echo $vid; ?>" <?php if ($vid == playerSkill($skills, 9)) echo "selected"?> ><?php echo $vname['name']; ?></option>
						<?php
					}
					?>
				</select>
				<br><br>
				<div class="hts__block-files-title">Fist fighting:</div><br>
				<input name="fist" type="text" <?php if (!$name) echo "disabled";?> value="<?php echo playerSkill($skills, 0); ?>" style="font-size:18px;color:white;background: rgba(14, 9, 8, 0.9);border: 1px solid #2d1f1e;">
				<br><br>
				<div class="hts__block-files-title">Club fighting:</div><br>
				<input name="club" type="text" <?php if (!$name) echo "disabled";?> value="<?php echo playerSkill($skills, 1); ?>" style="font-size:18px;color:white;background: rgba(14, 9, 8, 0.9);border: 1px solid #2d1f1e;">
				<br><br>
				<div class="hts__block-files-title">Sword fighting:</div><br>
				<input name="sword" type="text" <?php if (!$name) echo "disabled";?> value="<?php echo playerSkill($skills, 2); ?>" style="font-size:18px;color:white;background: rgba(14, 9, 8, 0.9);border: 1px solid #2d1f1e;">
				<br><br>
				<div class="hts__block-files-title">Axe fighting:</div><br>
				<input name="axe" type="text" <?php if (!$name) echo "disabled";?> value="<?php echo playerSkill($skills, 3); ?>" style="font-size:18px;color:white;background: rgba(14, 9, 8, 0.9);border: 1px solid #2d1f1e;">
				<br><br>
			</td>
			<td style="padding-top:40px">
			<div class="hts__block-files-title">Dist fighting:</div><br>
				<input name="dist" type="text" <?php if (!$name) echo "disabled";?> value="<?php echo playerSkill($skills, 4); ?>" style="font-size:18px;color:white;background: rgba(14, 9, 8, 0.9);border: 1px solid #2d1f1e;">
				<br><br>
				<div class="hts__block-files-title">Shield fighting:</div><br>
				<input name="shield" type="text" <?php if (!$name) echo "disabled";?> value="<?php echo playerSkill($skills, 5); ?>" style="font-size:18px;color:white;background: rgba(14, 9, 8, 0.9);border: 1px solid #2d1f1e;">
				<br><br>
				<div class="hts__block-files-title">Fish fighting:</div><br>
				<input name="fish" type="text" <?php if (!$name) echo "disabled";?> value="<?php echo playerSkill($skills, 6); ?>" style="font-size:18px;color:white;background: rgba(14, 9, 8, 0.9);border: 1px solid #2d1f1e;">
				<br><br>
				<div class="hts__block-files-title">Level:</div><br>
				<input name="level" type="text" <?php if (!$name) echo "disabled";?> value="<?php echo playerSkill($skills, 8); ?>" style="font-size:18px;color:white;background: rgba(14, 9, 8, 0.9);border: 1px solid #2d1f1e;">
				<br><br>
				<div class="hts__block-files-title">Magic level:</div><br>
				<input name="magic" type="text" <?php if (!$name) echo "disabled";?> value="<?php echo playerSkill($skills, 7); ?>" style="font-size:18px;color:white;background: rgba(14, 9, 8, 0.9);border: 1px solid #2d1f1e;">
				<br><br>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<?php
					if (!$name) {
						?>
						<button class="btn yellow flex-cc header__info-button" type="submit" value="Fetch character skills info">Fetch Character Skills Info</button>
						<?php
					} else {
						?>
						<button class="btn yellow flex-cc header__info-button" type="submit" value="UPDATE SKILLS">Update Skill</button>
						<?php
					}
				?>
			</td>
		</tr>
	</table><br></br>
	<a href="admin_skills.php">Reset fields / search new character</a>
</form></div></div>
<?php
// end
 include 'layout/overall/footer_login.php'; ?>
