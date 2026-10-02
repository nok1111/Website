<?php require_once 'engine/init.php'; include 'layout/overall/header.php';

if ($config['log_ip']) {
	znote_visitor_insert_detailed_data(4);
}

if (isset($_GET['name']) === true && empty($_GET['name']) === false) {
	$name = getValue($_GET['name']);
	$user_id = user_character_exist($name);

	if ($user_id !== false) {
		$loadOutfits = $config['show_outfits']['characterprofile'];

		if (!$loadOutfits) {
			$profile_data = user_character_data($user_id, 'account_id', 'name', 'level', 'group_id', 'vocation', 'health', 'healthmax', 'experience', 'mana', 'manamax', 'sex', 'lastlogin');
		} else { // Load outfits
			if ($config['client'] < 780) {
				$profile_data = user_character_data($user_id, 'account_id', 'name', 'level', 'group_id', 'vocation', 'health', 'healthmax', 'experience', 'mana', 'manamax', 'sex', 'lastlogin', 'lookbody', 'lookfeet', 'lookhead', 'looklegs', 'looktype');
			} else {
				$profile_data = user_character_data($user_id, 'account_id', 'name', 'level', 'group_id', 'vocation', 'health', 'healthmax', 'experience', 'mana', 'manamax', 'sex', 'lastlogin', 'lookbody', 'lookfeet', 'lookhead', 'looklegs', 'looktype', 'lookaddons');
			}
		}
		$profile_data['online'] = user_is_online_10($user_id);

		if ($config['Ach']) {
			$user_id = (int) $user_id;
			$achievementPoints = mysql_select_single("SELECT SUM(`value`) AS `sum` FROM `player_storage` WHERE `key` LIKE '30___' AND `player_id`={$user_id} LIMIT 1");
		}

		$profile_znote_data = user_znote_character_data($user_id, 'created', 'hide_char', 'comment');
		$guild_exist = false;
		if (get_character_guild_rank($user_id) > 0) {
			$guild_exist = true;
			$guild = get_player_guild_data($user_id);
			$guild_name = get_guild_name($guild['guild_id']);
		}
		?>

		<!-- PROFILE MARKUP HERE-->
		<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:13px"><div class="desc-changelog__block flex-ss" style="">
		<div style="margin-left:auto;margin-right:auto;">
		<table id="characterProfileTable">
			<thead>
				<tr class="yellow">
					<th>
						<?php if ($loadOutfits): ?>
							<div class="outfit"><div class="donate-page__bonuses-title flex-cc" style="line-height: 0px;">
								<img src="<?php echo $config['show_outfits']['imageServer']; ?>?id=<?php echo $profile_data['looktype']; ?>&addons=<?php echo $profile_data['lookaddons']; ?>&head=<?php echo $profile_data['lookhead']; ?>&body=<?php echo $profile_data['lookbody']; ?>&legs=<?php echo $profile_data['looklegs']; ?>&feet=<?php echo $profile_data['lookfeet']; ?>" alt="img">
						</div></div>
						<?php endif;
						$flags = $config['country_flags'];
						if ($flags['enabled'] && $flags['characterprofile']) {
							$account_data = user_znote_account_data($profile_data['account_id'], 'flag');
							if (strlen($account_data['flag']) > 0):
								?><!-- Player country data -->
								<div class="flag"><div class="donate-page__bonuses-title flex-cc" style="line-height: 0px;">
									<img src="<?php echo $flags['server'] . '/' . $account_data['flag']; ?>.png">
							</div></div>
								<?php
							endif;
						}
						?>
					</th>
					
					<div class="donate-page__title" style="color:white"><font color="#ffc801">Character Name</font>:&thinsp;<?php echo $profile_data['name']; ?></div>
					
				</tr>
			</thead>
			<tbody>
				<!-- Player Position -->
				<?php if ($profile_data['group_id'] > 1): 
					$position = mysql_select_single("
					SELECT 
						`a`.`type` 
					FROM `players` AS `p` 
					INNER JOIN `accounts` AS `a` 
						ON `p`.`account_id` = `a`.`id` 
					WHERE 
						`a`.`type` > 1 
						AND `p`.`id` = '{$user_id}'
					");
					$position = (isset($config['ingame_positions'][$position['type']])) ? $config['ingame_positions'][$position['type']] : "Unknown";
					?>
					<tr>
						<td><div class="donate-page__bonuses-title flex-cc" style="line-height: 0px;">Position</div></td>
						<td><div class="donate-page__bonuses-title flex-cc" style="line-height: 0px;"><?php echo $position; ?></div></td>
					</tr>
				<?php endif;
				// pending deletion?
				$deletion_time = mysql_select_single("SELECT `time` FROM `znote_deleted_characters` WHERE `character_name`='{$name}' AND `done` = '0' LIMIT 1;");
				if ($deletion_time !== false): ?>
					<tr>
						<td colspan="2" style="color: red;"><div class="donate-page__bonuses-title flex-cc" style="line-height: 0px;">Flagged for deletion by owner after <?php echo $deletion_time['time']; ?>.</div></td>
					</tr>
				<?php endif; ?>
				<!-- Player male / female -->
				<tr>
					<td><div class="donate-page__bonuses-title flex-cc" style="line-height: 0px;">Sex</div></td>
					<td><div class="donate-page__bonuses-title flex-cc" style="line-height: 0px;"><?php echo ($profile_data['sex'] == 1) ? 'Male' : 'Female'; ?></div></td>
				</tr>
				<!-- Player level -->
				<tr>
					<td><div class="donate-page__bonuses-title flex-cc" style="line-height: 0px;">Level</div></td>
					<td><div class="donate-page__bonuses-title flex-cc" style="line-height: 0px;"><?php echo $profile_data['level']; ?></div></td>
				</tr>
				<!-- Player vocation -->
				<tr>
					<td><div class="donate-page__bonuses-title flex-cc" style="line-height: 0px;">Vocation</div></td>
					<td><div class="donate-page__bonuses-title flex-cc" style="line-height: 0px;"><?php echo vocation_id_to_name($profile_data['vocation']); ?></div></td>
				</tr>
				<!-- Player guild -->
				<?php if ($guild_exist): ?>
					<tr>
						<td><div class="donate-page__bonuses-title flex-cc" style="line-height: 0px;">Guild</div></td>
						<td><div class="donate-page__bonuses-title flex-cc" style="line-height: 0px;"><b><?php echo $guild['rank_name']; ?> </b> of <a href="guilds.php?name=<?php echo $guild_name; ?>"><?php echo $guild_name; ?></a></div></td>
					</tr>
				<?php endif; ?>
				<!-- Player last login -->
				<tr>
					<td><div class="donate-page__bonuses-title flex-cc">Last Login</div></td>
					<td><div class="donate-page__bonuses-title flex-cc" style="line-height: 0px;"><?php echo ($profile_data['lastlogin'] != 0) ? getClock($profile_data['lastlogin'], true, true) : 'Never.'; ?></div></td>
				</tr>
				<!-- Achievement start -->
				<?php if ($config['Ach'] && (int)$achievementPoints['sum'] > 0): ?>
					<tr>
						<td><div class="donate-page__bonuses-title flex-cc" style="line-height: 0px;">Achievement Points</div></td>
						<td><div class="donate-page__bonuses-title flex-cc" style="line-height: 0px;"><?php echo (int)$achievementPoints['sum']; ?></div></td>
					</tr>
				<?php endif; ?>
				<!-- Display house start -->
				<?php

				$houses = mysql_select_multi("
					SELECT `id`, `owner`, `name`, `town_id` AS `town_id`
					FROM `houses`
					WHERE `owner` = {$user_id};
				");

				if ($houses !== false) {
					foreach ($houses as $h): ?>
						<tr>
							<td><div class="donate-page__bonuses-title flex-cc" style="line-height: 0px;">House</div></td>
							<td><div class="donate-page__bonuses-title flex-cc" style="line-height: 0px;"><?php echo $h['name'] . ', ' . $config['towns'][$h['town_id']]; ?></div></td>
						</tr>
					<?php endforeach;
				}
				?>
				<!-- Display player status -->
				<tr class="status_<?php echo ($profile_data['online']) ? 'online' : 'offline'; ?>">
					<td><div class="donate-page__bonuses-title flex-cc" style="line-height: 0px;">Status</div></td>
					<td><div class="donate-page__bonuses-title flex-cc" style="line-height: 0px;"><?php echo ($profile_data['online']) ? 'online' : 'offline'; ?></div></td>
				</tr>
				<!-- Player created -->
				<tr>
					<td><div class="donate-page__bonuses-title flex-cc" style="line-height: 0px;">Created</div></td>
					<td><div class="donate-page__bonuses-title flex-cc" style="line-height: 0px;"><?php echo getClock($profile_znote_data['created'], true); ?></div></td>
				</tr>
				<tr></tr><tr></tr><tr></tr><tr></tr><tr></tr><tr></tr><tr></tr><tr></tr><tr></tr>
				<!-- EQ shower -->
				<?php if ($config['EQ_shower']['enabled']): ?>
					<tr>
						<?php
						// Item image server
						$imageServer = $config['shop']['imageServer'];
						$imageType = $config['shop']['imageType'];
						$PEQ = mysql_select_multi("
							SELECT
								`player_id`,
								`pid`,
								`itemtype`,
								`count`
							FROM `player_items`
							WHERE `player_id`={$user_id}
							AND `pid`<'11'
						");

						$soulStamina = " `soul`, `stamina`,";
						if ($config['client'] < 780) {
							$soulStamina = " 0 AS `soul`, 0 AS `stamina`,";
						}

						$player_query = "
							SELECT
								`health`, `healthmax`,
								`mana`, `manamax`,
								`cap`,
								`experience`, `level`,
								{$soulStamina}
								`maglevel`,
								`skill_fist`,
								`skill_club`,
								`skill_sword`,
								`skill_axe`,
								`skill_dist`,
								`skill_shielding`,
								`skill_fishing`
							FROM `players`
							WHERE `id`={$user_id}
							LIMIT 1;
						";
						$playerstats = mysql_select_single($player_query);

						$playerstats['experience'] = number_format($playerstats['experience'],0,'',',');
						$playerstats['stamina'] = number_format($playerstats['stamina']/60,2,':','');

						$bar_length = 100;
						$bar_health = (int)($bar_length * ($playerstats['health'] / $playerstats['healthmax']));
						if ($playerstats['manamax'] > 0) {
							$bar_mana = (int)($bar_length * ($playerstats['mana'] / $playerstats['manamax']));
						}
						else {
							$bar_mana = 100;
						}

						$outfit_server = $config['show_outfits']['imageServer'];
						$outfit_storage = $config['EQ_shower']['storage_value'];

						$male_outfits = array(
							[128,129,130,131,132],
							[133,134,143,144,145],
							[146,151,152,153,154],
							[251,268,273,278,289],
							[325,328,335,367],
							//430,432,463,465,472,512,516,541,574,577,610,619,633,634,637,665,667,684,695,697,699,725,733,746,750,760,846,853,873,884,899
						);

						$female_outfits = array(
							[136,137,138,139,140],
							[141,142,147,148,149],
							[150,155,156,157,158],
							[252,269,270,279,288],
							[324,329,336,366],
							//431,433,464,466,471,513,514,542,575,578,618,620,632,635,636,664,666,683,694,696,698,724,732,745,749,759,845,852,874,885,900
						);

						$featured_outfits = ($profile_data['sex'] == 1) ? $male_outfits : $female_outfits;
						$outfit_list = array();
						$outfit_rows = COUNT($featured_outfits);
						$outfit_columns = COUNT($featured_outfits[0]);

						foreach ($featured_outfits as $row) {
							if (COUNT($row) > $outfit_columns) {
								$outfit_columns = COUNT($row);
							}
							foreach ($row as $column) {
								$outfit_list[] = $column;
							}
						}

						$highest_outfit_id = MAX($outfit_list);
						$outfit_storage_max = $outfit_storage + $highest_outfit_id + 1;

						$player_outfits = array();
						$storage_sql = mysql_select_multi("
							SELECT `key`, `value`
							FROM `player_storage`
							WHERE `player_id`={$user_id}
							AND `key` > {$outfit_storage}
							AND `key` < {$outfit_storage_max}
						");
						if ($storage_sql !== false && !empty($storage_sql)) {
							foreach ($storage_sql as $row) {
								$player_outfits[$row['key']] = $row['value'];
							}
						}

						$aquired_outfits = array();
						foreach ($outfit_list as $outfit_id) {
							$outfit_key = $outfit_storage + $outfit_id;
							if (isset($player_outfits[$outfit_key]) && $player_outfits[$outfit_key] == 3) {
								$aquired_outfits[$outfit_id] = true;
							}
						}
						?>
						<td colspan="2" id="">
							<div id="piv_flex">

								<?php if ($config['EQ_shower']['equipment']): ?>
									<div id="piv_i">
										<img class="bg" src="/engine/img/outfit.png">
										
										<?php if ($PEQ !== false && !empty($PEQ)): foreach($PEQ as $item): ?>
											<div class="itm itm-<?php echo $item['pid']; ?>">
												<img src="<?php echo "http://{$imageServer}/".$item['itemtype'].".{$imageType}"; ?>">
											</div>
										<?php endforeach; endif; ?>
										<span id="piv_cap">Cap:<br><?php echo $playerstats['cap']; ?></span>
										<?php if ($loadOutfits): ?>
											<div class="inventory_outfit">
												<img src="<?php echo $config['show_outfits']['imageServer']; ?>?id=<?php echo $profile_data['looktype']; ?>&addons=<?php echo $profile_data['lookaddons']; ?>&head=<?php echo $profile_data['lookhead']; ?>&body=<?php echo $profile_data['lookbody']; ?>&legs=<?php echo $profile_data['looklegs']; ?>&feet=<?php echo $profile_data['lookfeet']; ?>" alt="img">
											</div>
										<?php endif; ?>
									</div>
								<?php endif; ?>


								<?php if ($config['EQ_shower']['outfits']): ?>
									<div id="piv_o">
										
										<div id="piv_o_container">
											<?php foreach ($featured_outfits as $row): foreach($row as $outfit_id): $g = (isset($aquired_outfits[$outfit_id])) ? "" : "grayimg"; ?>
												<img class="o <?php echo $g; ?>" src="<?php echo $outfit_server . "?id=" . $outfit_id; ?>&addons=3&head=0&body=0&legs=0&feet=0">
											<?php endforeach; endforeach; ?>
										</div>
									</div>
								<?php endif; ?>

								<br></br>
								
							</div>
								
							<!-- Inventory style positioning -->
							<style type="text/css">
								#piv {
									background-image: url("/engine/img/o/m_m.png");
								}
								#piv_flex {
									display: flex;
									flex-wrap: wrap;
									/*align-items: center;*/
									justify-content: space-between;
									width: 100%;
									font-family: Verdana,Geneva,sans-serif;
									font-size: 7.0pt;
									line-height: 1;
									color: rgb(201,201,201);
								}
								#piv_i, #piv_s, #piv_o {
									position: relative;
								}

								#piv_i {
									width: 126px;
									height: 207px;
								}
								#piv_s {
									width: 184px;
									height: 232px;
								}
								#piv_o {
									width: <?php echo 16 + $outfit_columns * 40; ?>px;
									height: <?php echo 29 + $outfit_rows * 33; ?>px;
								}
								#piv_flex img {
									position: absolute;
									bottom:  0;
									right:  0;
								}
								#piv_i .inventory_outfit {
									position: absolute;
									top: 130px;
									left: -24px;
									width: 64px;
									height: 64px;
								}
								#piv_lifebar {
									position: absolute;
									border-radius: 6px;
									top: 6px;
									left: 14px;
									height: 11px;
									/*width: 95px;*/
									width: <?php echo $bar_health; ?>px;
									background-image: url("/engine/img/lifebarra.png");
								}
								#piv_manabar {
									position: absolute;
									border-radius: 6px;
									top: 19px;
									left: 14px;
									height: 11px;
									/*width: 95px;*/
									width: <?php echo $bar_mana; ?>px;
									background-image: url("/engine/img/manabar.png");
								}
								#piv_lifetext,
								#piv_manatext {
									position: absolute;
									display: block;
									left: 15px;
									width: <?php echo $bar_length; ?>px;
									text-align: center;
								}
								#piv_lifetext {
									top: 7px;
								}
								#piv_manatext {
									top: 20px;
								}
								#piv_lifetext span,
								#piv_manatext span {
									background-color: rgba(0,0,0,0.7);
									border-radius: 3px;
								}
								#piv_flex .itm { 

									width: 32px; 
									height: 32px; 
									position: absolute; 
								}
								#piv_flex .itm-1 { left: 48px; top: 39px; }
								#piv_flex .itm-2 { left: 11px; top: 53px; }
								#piv_flex .itm-3 { left: 85px; top: 53px; }
								#piv_flex .itm-4 { left: 48px; top: 76px; }
								#piv_flex .itm-5 { left: 85px; top: 90px; }
								#piv_flex .itm-6 { left: 11px; top: 90px; }
								#piv_flex .itm-7 { left: 48px; top: 113px; }
								#piv_flex .itm-8 { left: 48px; top: 150px; }
								#piv_flex .itm-9 { left: 11px; top: 127px; }
								#piv_flex .itm-10 { left: 85px; top: 127px; }
								#piv_cap {
									position: absolute;
									top: 162px;
									left: 85px;
									min-width: 32px;
									text-align: center;
								}

								#piv_s .txt {
									position: absolute;
								}
								#s_exp 				{ right: 22px; top: 16px; }
								#s_lvl 				{ right: 22px; top: 30px; }
								#s_hp 				{ right: 22px; top: 44px; }
								#s_mp 				{ right: 22px; top: 58px; }
								#s_soul 			{ right: 22px; top: 71px; }
								#s_cap 				{ right: 22px; top: 86px; }
								#s_stamina 			{ right: 22px; top: 100px; }
								#s_maglevel 		{ right: 22px; top: 114px; }
								#s_skill_fist 		{ right: 22px; top: 132px; }
								#s_skill_club 		{ right: 22px; top: 146px; }
								#s_skill_sword 		{ right: 22px; top: 160px; }
								#s_skill_axe 		{ right: 22px; top: 174px; }
								#s_skill_dist 		{ right: 22px; top: 188px; }
								#s_skill_shielding 	{ right: 22px; top: 202px; }
								#s_skill_fishing 	{ right: 22px; top: 215px; }

								/* Dynamically render background container size for outfits */
								#piv_o .bg {
									width: inherit;
									height: inherit;
									position: absolute;
									left: 0;
									top: 0;
								}
								#piv_o .bg_t {
									height: 21px;
									width: 100%;
								}
								#piv_o .bg_m {
									width: 100%;
									height: <?php echo $outfit_rows * 33; ?>px;
								}
								#piv_o .t_l {
									position: absolute;
									left: 0;
									top: 0;
									background-image: url("/engine/img/o/t_l.png");
									width: 8px;
									height: 21px;
								}
								#piv_o .t_m {
									position: absolute;
									right: 0;
									top: 0;
									background-image: url("/engine/img/o/t_m.png");
									width: 100%;
									height: 21px;
								}
								#piv_o .t_r {
									position: absolute;
									right: 0;
									top: 0;
									background-image: url("/engine/img/o/t_r.png");
									width: 50px;
									height: 21px;
								}
								#piv_o .m_l {
									background-image: url("/engine/img/o/m_l.png");
									width: 8px;
									height: inherit;
									float: left;
								}
								#piv_o .m_m {
									background-image: url("/engine/img/o/m_m.png");
									width: calc(100% - 16px);
									height: inherit;
									float: left;
								}
								#piv_o .m_r {
									background-image: url("/engine/img/o/m_r.png");
									width: 8px;
									height: inherit;
									float: left;
								}
								#piv_o .b_l {
									position: absolute;
									left: 0;
									bottom: 0;
									background-image: url("/engine/img/o/b_l.png");
									width: 8px;
									height: 8px;
								}
								#piv_o .b_m {
									position: absolute;
									right: 0;
									bottom: 0;
									background-image: url("/engine/img/o/b_m.png");
									width: 100%;
									height: 8px;
								}
								#piv_o .b_r {
									position: absolute;
									right: 0;
									bottom: 0;
									background-image: url("/engine/img/o/b_r.png");
									width: 8px;
									height: 8px;
								}

								/* Render outfit player sprites */
								#piv_o_container {
									height: inherit;
									width: inherit;
								}
								#piv_o_container .o {
									position: absolute;
									right: 0;
									bottom: 0;
								}

								/* Outfit column positions */
								<?php for ($column = 1; $column <= $outfit_columns; $column++): ?>
									#piv_o_container .o:nth-child(<?php echo $outfit_columns.'n+'.$column;?>) { right: <?php echo 10 + 40 * ($outfit_columns-$column); ?>px; }
								<?php endfor; ?>

								/* Outfit row positions */
								<?php for ($row = 1; $row <= $outfit_rows; $row++): ?>
									#piv_o_container .o:nth-child(n+<?php echo $outfit_columns * ($row-1)+1; ?>):nth-child(-n+<?php echo $outfit_columns*$row; ?>) { bottom: <?php echo 10 + 33 * ($outfit_rows-$row); ?>px; }
								<?php endfor; ?>

								#piv_o_container .o.grayimg {
									filter: none;
									-webkit-filter: grayscale(100%);
									-moz-filter: grayscale(100%);
									-ms-filter: grayscale(100%);
									-o-filter: grayscale(100%);
									opacity: .5;
									filter: alpha(opacity=50);
									margin-left: -25pt;
									margin-top: -25px;
								}
							</style>
						</td>
					</tr>
				<?php endif; ?>
				<!-- End EQ shower -->
			</tbody>
		</table>
		<?php if ($config['EQ_shower']['skills']): ?>
									
										<table>
									<tr><td><div class="donate-page__bonuses-title flex-cc" style="line-height: 0px;">Experience:</div></td><td> <div class="donate-page__bonuses-title flex-cc" style="line-height: 0px;"><?php echo $playerstats['experience']; ?></div></td></tr>
									<tr>
									<td><div class="donate-page__bonuses-title flex-cc" style="line-height: 0px;">Level:</div></td><td><div class="donate-page__bonuses-title flex-cc" style="line-height: 0px;"> <?php echo $playerstats['level']; ?></div></td></tr>
									<tr>
										<td><div class="donate-page__bonuses-title flex-cc" style="line-height: 0px;">Health:</div></td><td><div class="donate-page__bonuses-title flex-cc" style="line-height: 0px;"> <?php echo number_format($playerstats['health'],0,'',','); ?></td></div></tr><tr>
										<td><div class="donate-page__bonuses-title flex-cc" style="line-height: 0px;">Mana:</div></td><td><div class="donate-page__bonuses-title flex-cc" style="line-height: 0px;"> <?php echo number_format($playerstats['mana'],0,'',','); ?></td></div></tr>
										<tr><td><div class="donate-page__bonuses-title flex-cc" style="line-height: 0px;">Soul: </div></td><td><div class="donate-page__bonuses-title flex-cc" style="line-height: 0px;"><?php echo $playerstats['soul']; ?></div></td></tr>
										<tr><td><div class="donate-page__bonuses-title flex-cc" style="line-height: 0px;">Capacity: </div></td><td><div class="donate-page__bonuses-title flex-cc" style="line-height: 0px;"><?php echo number_format($playerstats['cap'],0,'',','); ?></div></td></tr>
										<tr>
										<td><div class="donate-page__bonuses-title flex-cc" style="line-height: 0px;">Stamine:</div></td><td><div class="donate-page__bonuses-title flex-cc" style="line-height: 0px;"> <?php echo $playerstats['stamina']; ?></div></td></tr>
										<tr>
										<td><div class="donate-page__bonuses-title flex-cc" style="line-height: 0px;">Magic Level: </div></td><td><div class="donate-page__bonuses-title flex-cc" style="line-height: 0px;"><?php echo $playerstats['maglevel']; ?></div></td></tr>
										<tr>
										<td><div class="donate-page__bonuses-title flex-cc" style="line-height: 0px;">Skill Fist: </div></td><td><div class="donate-page__bonuses-title flex-cc" style="line-height: 0px;"><?php echo $playerstats['skill_fist']; ?></div></td></tr>
										<tr>
										<td><div class="donate-page__bonuses-title flex-cc" style="line-height: 0px;">Skill Club: </div></td><td><div class="donate-page__bonuses-title flex-cc" style="line-height: 0px;"><?php echo $playerstats['skill_club']; ?></div></td></tr>
										<tr>
										<td><div class="donate-page__bonuses-title flex-cc" style="line-height: 0px;">Skill Sword:</div></td><td><div class="donate-page__bonuses-title flex-cc" style="line-height: 0px;"> <?php echo $playerstats['skill_sword']; ?></div></td></tr>
										<tr>
										<td><div class="donate-page__bonuses-title flex-cc" style="line-height: 0px;">Skill Axe: </div></td><td><div class="donate-page__bonuses-title flex-cc" style="line-height: 0px;"><?php echo $playerstats['skill_axe']; ?></div></td></tr>
										<tr>
										<td><div class="donate-page__bonuses-title flex-cc" style="line-height: 0px;">Skill Distance:</div></td><td><div class="donate-page__bonuses-title flex-cc" style="line-height: 0px;"> <?php echo $playerstats['skill_dist']; ?></div></td></tr>
										<tr>
										<td><div class="donate-page__bonuses-title flex-cc" style="line-height: 0px;">Skill Shielding: </div></td><td><div class="donate-page__bonuses-title flex-cc" style="line-height: 0px;"><?php echo $playerstats['skill_shielding']; ?></div></td></tr>
										<tr>
										<td><div class="donate-page__bonuses-title flex-cc" style="line-height: 0px;">Skill Fishing:</div></td><td><div class="donate-page__bonuses-title flex-cc" style="line-height: 0px;"> <?php echo $playerstats['skill_fishing']; ?></div></td></tr>
										</table>
									</div>
		
								<?php endif; ?></div></div>
								
		<!-- Player Comment -->
		<?php if (!empty($profile_znote_data['comment'])): ?>
			<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:13px"><div class="desc-changelog__block flex-ss" style="">
			<table class="comment">
				<thead>
					<tr class="yellow">
						<td><font class="profile_font" name="profile_font_comment">Comment:</font></td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><?php echo preg_replace('/\v+|\\\r\\\n/','<br/>',$profile_znote_data['comment']); ?></td>
					</tr>
				</tbody>
			</table></div></div>
		<?php endif; ?>

		<!-- Achievements start -->
		<?php if ($config['Ach']):
			$achievements = mysql_select_multi("
				SELECT `player_id`, `value`, `key`
				FROM `player_storage`
				WHERE `player_id`='$user_id'
				AND `key` LIKE '30___';
			");
			$c_achs = $config['achievements'];
			$toggle = array(
				'show' => '<a href="#show">Show</a>',
				'hide' => '<a href="#hide">Hide</a>'
			);
			if ($achievements !== false): ?>
			<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:13px"><div class="desc-changelog__block flex-ss" style="">
				<h3>Achievements: <label id="ac_label_hide" for="ac_toggle_hide"><?php echo $toggle['show']; ?></label></h3>
				<!-- <div id="accordion">
					<h3>Show/hide player achievements</h3>
					<div>

					</div>
				</div><br> -->
				<input type="checkbox" id="ac_toggle_hide" name="ac_toggle_hide">
				<table class="achievements">
					<thead>
						<tr>
							<th>Name</th>
							<th>Description</th>
							<th>Points</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($achievements as $a): ?>
							<tr>
								<td><?php echo $c_achs[$a['key']][0]; ?></td>
								<td><?php echo $c_achs[$a['key']][1]; ?></td>
								<td><?php echo $a['value']; ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table></div></div>
				<style type="text/css">
					table.achievements,
					#ac_toggle_hide {
						display: none;
					}
					#ac_toggle_hide:checked + table.achievements {
						display: table;
					}
				</style>
				<script type="text/javascript">
					document.getElementById("ac_label_hide").addEventListener("click", function(event){
						event.preventDefault();
						if (document.getElementById("ac_label_hide").innerHTML == "<?php echo str_replace('"', '\"', $toggle['show']); ?>") {
							document.getElementById("ac_label_hide").innerHTML = "<?php echo str_replace('"', '\"', $toggle['hide']); ?>";
							document.getElementById("ac_toggle_hide").checked = true;
						} else {
							document.getElementById("ac_label_hide").innerHTML = "<?php echo str_replace('"', '\"', $toggle['show']); ?>";
							document.getElementById("ac_toggle_hide").checked = false;
						}
					});
				</script>
			<?php endif; ?>
		<?php endif; ?>

		<!-- DEATH LIST -->
		<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:13px"><div class="desc-changelog__block flex-ss" style="">
		<div style="margin-left:auto;margin-right:auto">
		<table class="deathlist">
			<thead>
			<div class="donate-page__title" style="color:white">Death List</div><br></br></div>
			</thead>
			<tbody>
				<?php
				$deaths = mysql_select_multi("
					SELECT
						`player_id`,
						`time`,
						`level`,
						`killed_by`,
						`is_player`,
						`mostdamage_by`,
						`mostdamage_is_player`,
						`unjustified`,
						`mostdamage_unjustified`
					FROM `player_deaths`
					WHERE `player_id`=$user_id
					ORDER BY `time` DESC
					LIMIT 10;
				");

				if ($deaths) {
					foreach ($deaths as $d) {
						$lasthit = ($d['is_player'])
						? "<a href='characterprofile.php?name=".$d['killed_by']."'>".$d['killed_by']."</a>"
						: $d['killed_by'];

						?>
						<tr>
							<td><?php echo getClock($d['time'], true, true); ?></td>
							<td>
								<?php
								echo "Killed at level ".$d['level']." by {$lasthit}";
								if ($d['unjustified']) {
									echo " <font color='red' style='font-style: italic;'>(unjustified)</font>";
								}
								$mostdmg = ($d['mostdamage_by'] !== $d['killed_by']) ? true : false;
								if ($mostdmg) {
									$mostdmg = ($d['mostdamage_is_player'])
									? "<a href='characterprofile.php?name=".$d['mostdamage_by']."'>".$d['mostdamage_by']."</a>"
									: $d['mostdamage_by'];

									echo "<br>and by $mostdmg.";

									if ($d['mostdamage_unjustified']) {
										echo " <font color='red' style='font-style: italic;'>(unjustified)</font>";
									}
								} else {
									echo " <b>(soloed)</b>";
								}
								?>
							</td>
						</tr>
						<?php
					}
				} else {
					?>
					<tr>
						<td colspan="2">This player has never died.</td>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table></div></div></div>

		<!-- QUEST PROGRESSION -->
		<?php
		$totalquests = 0;
		$completedquests = 0;
		$firstrun = 1;

		if ($config['EnableQuests'] == true) {
			$sqlquests = mysql_select_multi("
				SELECT `player_id`, `key`, `value`
				FROM player_storage
				WHERE `player_id` = {$user_id}
			");
			if (isset($config['quests']) && !empty($config['quests'])) {
				foreach ($config['quests'] as $cquest) {
					$totalquests = $totalquests + 1;
					if ($sqlquests !== false) {
                        foreach ($sqlquests as $dbquest) {
    						if ($cquest[0] == $dbquest['key'] && $cquest[1] == $dbquest['value']) {
    							$completedquests = $completedquests + 1;
    						}
    					}
                    }
					if ($cquest[3] == 1) {
						if ($completedquests != 0) {
							if ($firstrun == 1): ?>
							<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:13px"><div class="desc-changelog__block flex-ss" style="">
								<b> Quest progression </b>
								<table id="characterprofileQuest" class="table table-striped table-hover">
									<thead>
										<tr class="yellow">
											<th>Quest:</th>
											<th>progression:</th>
										</tr>
									</thead>
									<tbody>
								<?php
								$firstrun = 0;
							endif;
							$completed = $completedquests / $totalquests * 100;
							?>
							<tr>
								<td><?php echo $cquest[2]; ?></td>
								<td id="progress">
									<span id="percent"><?php echo round($completed); ?>%</span>
									<div id="bar" style="width: '.$completed.'%"></div>
								</td>
							</tr>
							<?php
						}
						$completedquests = 0;
						$totalquests = 0;
					}
				}
			}
		}

		if ($firstrun == 0): ?>
			</tbody></table></div></div>
		<?php endif; ?>
		<!-- END QUEST PROGRESSION -->

		<!-- CHARACTER LIST -->
		<?php
		// Load other visible characters
		$otherChars = mysql_select_multi("
			SELECT
				`p`.`id`,
				`p`.`name`,
				`p`.`level`,
				`p`.`vocation`,
				`p`.`lastlogin`,
				CASE WHEN `l`.`player_id` IS NULL THEN 0 else 1 END as `online`
			FROM `players` as `o`
			JOIN `players` as `p`
				ON `o`.`account_id` = `p`.`account_id`
			LEFT JOIN `znote_players` as `z`
				ON `p`.`id` = `z`.`player_id`
			LEFT JOIN `znote_players` as `z2`
				ON `o`.`id` = `z2`.`player_id`
			LEFT JOIN `players_online` as `l` ON `p`.`id` = `l`.`player_id`
			WHERE `o`.`id` = {$user_id}
			AND `p`.`id` != `o`.`id`
			AND `z`.`hide_char` = 0
			AND `z2`.`hide_char` = 0
			ORDER BY `p`.`experience` DESC;
		");

		// Render table if there are any characters to show
		if ($otherChars !== false) {
			?>
			<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:13px"><div class="desc-changelog__block flex-ss" style="">
			<div class="donate-page__title" style="color:white">Others Characters</div><br></br></div>
				<b>Other visible characters on this account:</b><br>
				<div style="margin-left:auto;margin-right:auto;">
				<table id="characterprofileTable" class="table table-striped table-hover">
					<tr class="yellow">
						<th><div class="donate-page__bonuses-title flex-cc">Name:</div></th>
						<th><div class="donate-page__bonuses-title flex-cc">Level:</div></th>
						<th><div class="donate-page__bonuses-title flex-cc">Vocation:</div></th>
						<th><div class="donate-page__bonuses-title flex-cc">Last login:</div></th>
						<th><div class="donate-page__bonuses-title flex-cc">Status:</div></th>
					</tr>
					<?php
					// Add character rows
					foreach ($otherChars as $char):
						?>
						<tr>
							<td><div class="donate-page__bonuses-title flex-cc"><a href="characterprofile.php?name=<?php echo $char['name']; ?>"><?php echo $char['name']; ?></a></div></td>
							<td><div class="donate-page__bonuses-title flex-cc"><?php echo (int)$char['level']; ?></div></td>
							<td><div class="donate-page__bonuses-title flex-cc"><?php echo vocation_id_to_name($char['vocation']); ?></div></td>
							<td><div class="donate-page__bonuses-title flex-cc"><?php echo ($char['lastlogin'] != 0) ? getClock($char['lastlogin'], true, true) : 'Never.'; ?></div></td>
							<td><div class="donate-page__bonuses-title flex-cc"><?php echo ($char['online']) ? 'online' : 'offline'; ?></div></td>
						</tr>
						<?php
					endforeach;
					?>
				</table></div></div>
				</div>
			<?php
		}
		?>
		<!-- END CHARACTER LIST -->
		<div class="global-desc__content" style="width:100%;margin-top:3px;margin-bottom:3px;padding-top:3px;padding-bottom:3px;padding-left:3px;padding-right:13px"><div class="desc-changelog__block flex-ss" style="">
		<div style="margin-left:auto;margin-right:auto;">
		<p class="address">Address: <a href="<?php echo ($config['htwrite']) ? "//" . $_SERVER['HTTP_HOST']."/" . $profile_data['name'] : "//" . $_SERVER['HTTP_HOST'] . "/characterprofile.php?name=" . $profile_data['name']; ?>"><?php echo ($config['htwrite']) ? $_SERVER['HTTP_HOST']."/". $profile_data['name'] : $_SERVER['HTTP_HOST']."/characterprofile.php?name=". $profile_data['name']; ?></a></p></div></div></div>

		<?php
	} else {
		echo htmlentities(strip_tags($name, ENT_QUOTES)) . ' does not exist.';
	}
} else {
	header('Location: index.php');
}
include 'layout/overall/footer.php'; ?>
