<?php require_once 'engine/init.php'; include 'layout/overall/header.php';
// Calculate integer values into days, hours, minutes, seconds
function toDuration($ms) {
	$duration['day'] = $ms / (24 * 60 * 60 * 1000);
	if (($duration['day'] - (int)$duration['day']) > 0)
		$duration['hour'] = ($duration['day'] - (int)$duration['day']) * 24;
	if (isset($duration['hour'])) {
		if (($duration['hour'] - (int)$duration['hour']) > 0)
			$duration['minute'] = ($duration['hour'] - (int)$duration['hour']) * 60;
		if (isset($duration['minute'])) {
			if (($duration['minute'] - (int)$duration['minute']) > 0)
				$duration['second'] = ($duration['minute'] - (int)$duration['minute']) * 60;
		}
	}
	$tmp = array();
	foreach ($duration as $type => $value) {
		if ($value >= 1) {
			$pluralType = ((int)$value === 1) ? $type : $type . 's';
			if ($type !== 'second') $tmp[] = (int)$value . " $pluralType";
			else $tmp[] = $value . " $pluralType";
		}
	}
	return implode(', ', $tmp);
}
function toYesNo($bool) {
	return ($bool) ? 'Yes' : 'No';
}
// Loading stage list
$cache = new Cache('engine/cache/stages');
$cache->useMemory(false);
if (user_logged_in() && is_admin($user_data)) {
	if (isset($_GET['loadStages'])) {
		echo "<div class='global-desc__content' style='width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px'><div class='desc-changelog__block flex-ss'><p><strong>Logged in as admin, loading engine/XML/stages.xml file and updating cache.</strong></p></div></div>";
		// STAGES XML TO PHP ARRAY
		$stagesXML = simplexml_load_file("engine/XML/stages.xml");
		if ($stagesXML !== false) {
			$stagesData = array();
			// Load config (stages enabled or disabled)
			foreach ($stagesXML->config->attributes() as $name => $value)
				$stagesData["$name"] = "$value";
			// Load stage levels
			// Each stage XML object
			foreach ($stagesXML->stage as $stage) {
				$rowData = array();
				// Each attribute name and values on current stage object
				foreach ($stage->attributes() as $name => $value) {
					$rowData["$name"] = "$value";
				}
				// Populate XML assoc array
				$stagesData['stages'][] = $rowData;
			}
			$cache->setContent($stagesData);
			$cache->save();
		}
	} else {
		$stagesData = $cache->load();
		?>
		<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">
		<form action="">
			<button class="btn yellow flex-cc header__info-button" type="submit" name="loadStages" value="Load stages.xml">Load Stages.xml</button>
		</form></div></div>
		<?php
	}
	// END STAGES XML TO PHP ARRAY
} else {
	$stagesData = $cache->load();
}
// End loading stage list

// Loading config.lua
$cache = new Cache('engine/cache/luaconfig');
$cache->useMemory(false);
if (user_logged_in() && is_admin($user_data)) {
	if (isset($_POST['loadConfig']) && isset($_POST['configData'])) {
		// Whitelist for values we are interested in
		$whitelist = array( // Etc 'maxPlayers'
			'worldType',
			'hotkeyAimbotEnabled',
			'protectionLevel',
			'killsToRedSkull',
			'killsToBlackSkull',
			'pzLocked',
			'removeChargesFromRunes',
			'timeToDecreaseFrags',
			'whiteSkullTime',
			'stairJumpExhaustion',
			'experienceByKillingPlayers',
			'expFromPlayersLevelRange',
			'loginProtocolPort',
			'maxPlayers',
			'motd',
			'onePlayerOnlinePerAccount',
			'deathLosePercent',
			'housePriceEachSQM',
			'houseRentPeriod',
			'marketOfferDuration',
			'premiumToCreateMarketOffer',
			'maxMarketOffersAtATimePerPlayer',
			'allowChangeOutfit',
			'freePremium',
			'kickIdlePlayerAfterMinutes',
			'rateExp',
			'rateSkill',
			'rateLoot',
			'rateMagic',
			'rateSpawn',
			'staminaSystem',
			'experienceStages'
		);
		// This will be the populated array with filtered relevant data
		$luaConfig = array();

		// Remove everything between first { and last }
		$poststring = $_POST['configData'];
		$first = strpos($poststring, '{');
		if ($first !== false) {
			$last = strripos($poststring, '}');
			if ($last !== false) {
				$sliced_string = substr($poststring, 0, $first).substr($poststring, $last+1);
				$poststring = $sliced_string;
			} else {
				die("Lua process error: Syntax error in config.lua");
			}
		}

		// Explode the string into string array by newline
		$rawLua = explode("\n", $poststring);
		// Clean up the array
		$length = count($rawLua);
		for ($i = 0; $i < $length; $i++) {
			// We only care about lines that have the = symbol
			if (strpos($rawLua[$i], '=') !== false) {
				// Look for inline Lua comments and remove them
				$comment = strpos($rawLua[$i], '--');
				if ($comment !== false)
					$rawLua[$i] = substr($rawLua[$i], 0, $comment);
				$rawLua[$i] = trim($rawLua[$i]); // Remove unnecessary whitespace
				// If for some reason the line is empty, ignore it. (Could be a "=" symbol inside an inline Lua comment that we sliced away)
				if (!empty($rawLua[$i])) {
					// Built a relevant data array
					$data = explode('=', $rawLua[$i]);
					// Remove unnecessary whitespace
					$data[0] = trim($data[0]);
					$data[1] = trim($data[1]);

					if (in_array($data[0], $whitelist)) {
						// Type cast: boolean
						if (in_array(strtolower($data[1]), array('true', 'false'))) {
							$data[1] = (strtolower($data[1]) === 'true') ? true : false;
						} else {
							if (strpos($data[1], '"') === false) {
								if (!in_array($data[1], array_keys($luaConfig))) {
									// Type cast: integer
									if (strlen($data[1]) > 0) {
										$data[1] = eval('return (' . $data[1] . ');');
									}
								} else {
									// Type cast: Load value from another key
									$data[1] = (isset($luaConfig[$data[1]])) ? $luaConfig[$data[1]] : null;
								}
							} else {
								// Type cast: string, just remove the quote we earlier used to determine if it was a string.
								$data[1] = str_replace('"', '', $data[1]);
							}
						}
						// Add the results
						$luaConfig[$data[0]] = $data[1];
					} // End whitelisted row
				} // End not empty row
			} // Line has \= symbol
		} // for loop
		$cache->setContent($luaConfig);
		$cache->save();
	} else {
		$luaConfig = $cache->load();
		?>
		<br>
		<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">
		<form action="" method="POST">
			<label for="configData">Find your OT server folder, put the text inside config.lua into this text field:</label><br>
			<textarea name="configData" style="font-size:18px;color:white;background: rgba(14, 9, 8, 0.9);border: 1px solid #2d1f1e;height:80px;width:100%;" placeholder="Open config.lua and copy the content into this text area."></textarea><br>
			<button class="btn yellow flex-cc header__info-button" type="submit" name="loadConfig" value="Load config data">Load Config.lua</button>
		</form></div></div>
		<?php
	}
} else {
	$luaConfig = $cache->load();
}
// End loading config.lua

$stages = false;

// Render HTML
?>
<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">
<div class="donate-page__title" style="color:white">Server Information<br></br></div>
<div style="margin-left:auto;margin-right:auto">
<p>Here you will find all basic information about <b><?php echo $config['site_title']; ?></b></p></div>
</div></div>
<?php 
if (
	($stagesData && isset($stagesData['enabled']) && $stagesData['enabled']) 
	|| (isset($luaConfig['experienceStages']) && $luaConfig['experienceStages'] === true)
): 
	$stages = true; ?>
	<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">
	<div class="donate-page__title" style="color:white">Server Rates<br></br></div>
	<div style="margin-left:auto;margin-right:auto">
	<table class="table tbl-hover">
		<tbody>
			<tr class="yellow">
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px">Minimum level</div></td>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px">Maximum level</div></td>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px">Multiplier</div></td>
			</tr>
			<?php foreach ($stagesData['stages'] as $stage): ?>
				<tr>
					<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px"><?php echo $stage['minlevel']; ?></div></td>
					<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px"><?php echo (isset($stage['maxlevel'])) ? $stage['maxlevel'] : "Unlimited"; ?></div></td>
					<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px"><?php echo $stage['multiplier']; ?>x</div></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table></div></div></div>
<?php endif; ?>

<?php if ($luaConfig): ?>
	<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">
	<div style="margin-left:auto;margin-right:auto">
	<table class="table tbl-hover">
		<tbody>
			<tr class="yellow">
				<?php if (!$stages): ?>
					<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px">Experience rate</div></td>
				<?php endif; ?>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px">Skills rate</div></td>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px">Magic rate</div></td>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px">Loot rate</div></td>
			</tr>
			<tr>
				<?php if (!$stages): ?>
					<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px"><?php echo $luaConfig['rateExp']; ?></div></td>
				<?php endif; ?>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px"><?php echo $luaConfig['rateSkill']; ?></div></td>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px"><?php echo $luaConfig['rateMagic']; ?></div></td>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px"><?php echo $luaConfig['rateLoot']; ?></div></td>
			</tr>
		</tbody>
	</table></div>
				</div></div>
				<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">
	<div class="donate-page__title" style="color:white">Miscellaneous information</div>
	<div style="margin-left:auto;margin-right:auto">
	<table class="table tbl-hover">
		<tbody>
			<tr class="yellow">
				<td colspan="2"><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px">Connection information</div></td>
			</tr>
			<tr>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px">Client</div></td>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px"><?php echo ($config['client'] / 100); ?></div></td>
			</tr>
			<tr>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px">IP</td>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px"><?php echo $_SERVER['SERVER_NAME']; ?></div></td>
			</tr>
			<tr>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px">Port</div></td>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px"><?php echo $luaConfig['loginProtocolPort']; ?></div></td>
			</tr>
		</tbody>
	</table></div>
				</div></div>
				<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">
				<div style="margin-left:auto;margin-right:auto">
	<table class="table tbl-hover">
		<tbody>
			<div class="donate-page__title" style="color:white">Pvp information</div>
			<tr>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px">World type</div></td>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px"><?php echo $luaConfig['worldType']; ?></div></td>
			</tr>
			<tr>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px">Hotkey aimbot</div></td>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px"><?php echo toYesNo($luaConfig['hotkeyAimbotEnabled']); ?></div></td>
			</tr>
			<tr>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px">Protection level</div></td>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px"><?php echo $luaConfig['protectionLevel']; ?></div></td>
			</tr>
			<tr>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px">Kills to red skull</div></td>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px"><?php echo $luaConfig['killsToRedSkull']; ?></div></td>
			</tr>
			<tr>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px">Kills to black skull</div></td>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px"><?php echo $luaConfig['killsToBlackSkull']; ?></div></td>
			</tr>
			<tr>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px">Remove rune charges</div></td>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px"><?php echo toYesNo($luaConfig['removeChargesFromRunes']); ?></div></td>
			</tr>
			<?php if (isset($luaConfig['timeToDecreaseFrags'])): ?>
				<tr>
					<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px">Time to decrease frags</td><!-- Legacy servers might need to remove *1000 -->
					<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px"><?php echo toDuration($luaConfig['timeToDecreaseFrags']*1000); ?></td>
				</tr>
			<?php endif; ?>
			<tr>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px">Experience by killing players</div></td>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px"><?php echo toYesNo($luaConfig['experienceByKillingPlayers']); ?></div></td>
			</tr>

			<?php if ($luaConfig['experienceByKillingPlayers']): ?>
				<tr>
					<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px">Experience gain kill threshold:</div></td>
					<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px"><?php echo $luaConfig['expFromPlayersLevelRange']; ?>% of your level</div></td>
				</tr>
			<?php endif; ?>

			<tr>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px">White skull duration</div></td>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px"><?php echo toDuration($luaConfig['whiteSkullTime']); ?></div></td>
			</tr>
			<tr>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px">Protection zone lock (non lethal attack)</td>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px"><?php echo toDuration($luaConfig['pzLocked']); ?></td>
			</tr>
			<tr>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px">Stair jump exhaust</div></td>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px"><?php echo toDuration($luaConfig['stairJumpExhaustion']); ?></div></td>
			</tr>
		</tbody>
	</table></div></div></div>
	<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">
	<div style="margin-left:auto;margin-right:auto">
	<table class="table tbl-hover">
		<tbody>
			
			<div class="donate-page__title" style="color:white">Other information</div>
			<tr>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px">Free premium</div></td>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px"><?php echo toYesNo($luaConfig['freePremium']); ?></div></td>
			</tr>
			<tr>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px">House rent period</div></td>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px"><?php echo $luaConfig['houseRentPeriod']; ?></div></td>
			</tr>
			<tr>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px">House SQM price</div></td>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px"><?php echo $luaConfig['housePriceEachSQM']; ?> gp</div></td>
			</tr>
			<tr>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px">AFK kickout</div></td>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px"><?php echo toDuration($luaConfig['kickIdlePlayerAfterMinutes'] * 60 * 1000); ?></div></td>
			</tr>
			<tr>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px">One player online per account</div></td>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px"><?php echo toYesNo($luaConfig['onePlayerOnlinePerAccount']); ?></div></td>
			</tr>
			<tr>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px">Max players online server limit</div></td>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px"><?php echo ($luaConfig['maxPlayers'] > 0) ? $luaConfig['maxPlayers'] : 'Unlimited'; ?></div></td>
			</tr>
			<tr>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px">Allow outfit change</div></td>
				<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px"><?php echo toYesNo($luaConfig['allowChangeOutfit']); ?></div></td>
			</tr>
			<?php if (isset($luaConfig['staminaSystem'])): ?>
				<tr>
					<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px">Stamina system</div></td>
					<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px"><?php echo toYesNo($luaConfig['staminaSystem']); ?></div></td>
				</tr>
			<?php endif; ?>
			<?php if (isset($luaConfig['premiumToCreateMarketOffer'])): ?>
				<tr>
					<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px">Premium to add items to market</div></td>
					<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px"><?php echo toYesNo($luaConfig['premiumToCreateMarketOffer']); ?></div></td>
				</tr>
			<?php endif; ?>
			<?php if (isset($luaConfig['marketOfferDuration'])): ?>
				<tr>
					<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px">Market offer duration</div></td>
					<td><div class="donate-page__bonuses-title flex-cc" style="padding-left:30px"><?php echo toDuration($luaConfig['marketOfferDuration'] * 1000); ?></div></td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table></div></div></div>
<?php else: ?>
	<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:3px"><div class="desc-changelog__block flex-ss" style="">	<p>The server administrator has yet to import server information to this page.</p></div></div>
<?php endif;
include 'layout/overall/footer_login.php'; ?>
