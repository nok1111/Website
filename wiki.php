<?php require_once 'engine/init.php'; include 'layout/overall/header_wiki.php'; ?>
<h1 class="cd-page-title">Wikipedia</h1>

<div class="global-desc flex-sbs prevent-select">
	<div class="global-desc__nav">
		<div class="global-desc__nav-item flex-sc" data-open-tab="server">
			<div class="global-desc__nav-icon"><img src="layout/application/templates/default/images/description/navigation/nav_icon_0.png" alt=""></div>
			<div class="global-desc__nav-text">Overview<span>Server Info</span></div>
		</div>
		<div class="global-desc__nav-item flex-sc" data-open-tab="premium">
			<div class="global-desc__nav-icon"><img src="layout/application/templates/default/images/description/navigation/nav_icon_7.png" alt=""></div>
			<div class="global-desc__nav-text">Premium<span>Premium Benefits</span></div>
		</div>
		<div class="global-desc__nav-item flex-sc" data-open-tab="fame">
			<div class="global-desc__nav-icon"><img src="layout/application/templates/default/images/description/navigation/nav_icon_5.png" alt=""></div>
			<div class="global-desc__nav-text">Fame<span>Fame System</span></div>
		</div>
		<div class="global-desc__nav-item flex-sc" data-open-tab="tasks">
			<div class="global-desc__nav-icon"><img src="layout/application/templates/default/images/description/navigation/nav_icon_3.png" alt=""></div>
			<div class="global-desc__nav-text">Tasks<span>Task System</span></div>
		</div>
		<div class="global-desc__nav-item flex-sc" data-open-tab="professions">
			<div class="global-desc__nav-icon"><img src="layout/application/templates/default/images/description/navigation/nav_icon_1.png" alt=""></div>
			<div class="global-desc__nav-text">Professions<span>Gathering &amp; Crafting</span></div>
		</div>
		<div class="global-desc__nav-item flex-sc" data-open-tab="dungeons">
			<div class="global-desc__nav-icon"><img src="layout/application/templates/default/images/description/navigation/nav_icon_3.png" alt=""></div>
			<div class="global-desc__nav-text">Dungeons<span>Dungeon System</span></div>
		</div>
		<div class="global-desc__nav-item flex-sc" data-open-tab="talents">
			<div class="global-desc__nav-icon"><img src="layout/application/templates/default/images/description/navigation/nav_icon_4.png" alt=""></div>
			<div class="global-desc__nav-text">Talents<span>Passive Skill Tree</span></div>
		</div>
		<div class="global-desc__nav-item flex-sc" data-open-tab="paragon">
			<div class="global-desc__nav-icon"><img src="layout/application/templates/default/images/description/navigation/nav_icon_7.png" alt=""></div>
			<div class="global-desc__nav-text">Paragon<span>Endgame Progression</span></div>
		</div>
		<div class="global-desc__nav-item flex-sc" data-open-tab="pets">
			<div class="global-desc__nav-icon"><img src="layout/application/templates/default/images/description/navigation/nav_icon_5.png" alt=""></div>
			<div class="global-desc__nav-text">Pets<span>Pet System</span></div>
		</div>
		<div class="global-desc__nav-item flex-sc" data-open-tab="codex">
			<div class="global-desc__nav-icon"><img src="layout/application/templates/default/images/description/navigation/nav_icon_4.png" alt=""></div>
			<div class="global-desc__nav-text">Codex<span>Codex Cards</span></div>
		</div>
		<div class="global-desc__nav-item flex-sc" data-open-tab="zonebuffs">
			<div class="global-desc__nav-icon"><img src="layout/application/templates/default/images/description/navigation/nav_icon_0.png" alt=""></div>
			<div class="global-desc__nav-text">Zone Buffs<span>Dynamic Zone Events</span></div>
		</div>

	</div>
	<div class="global-desc__content">


		<!-- ========== OVERVIEW TAB ========== -->
		<div class="global-desc__content-item" data-name-tab="server">
			<div class="global-desc__content-title flex-sc">
				<div class="global-desc__content-title-icon"><img src="layout/application/templates/default/images/description/navigation/nav_icon_0.png" alt=""></div>
				<div class="global-desc__content-title-text">Overview<span>Server Info</span></div>
			</div>
			<div class="global-desc__content-box">
				<div class="desc-main">
					<div class="desc-main__header">
						<div class="desc-main__header-info flex-sbs">
							<div class="desc-main__header-date">
								<span class="desc-main__header-date-title"><i class="far fa-clock"></i> Server Status</span>
								<span class="desc-main__header-date-text">Server is Online!</span>
							</div>
						</div>
					</div>
					<div class="desc-margin-30"></div>
					<div class="desc-text">
						<div class="desc-text__title fz_20"><i class="far fa-sparkles"></i> Welcome to <span><?php echo $config['site_title'] ?></span></div>
						<div class="desc-margin-10"></div>
						<div class="desc-text__text fz_15">
							<?php echo $config['site_title'] ?> is a fully custom RPG world with deep progression systems. Explore, fight, craft, and grow your character through dozens of interconnected systems. Play at your own pace and discover everything the world has to offer.
						</div>
					</div>
					<div class="desc-margin-20"></div>
					<div class="desc-sep"></div>
					<div class="desc-margin-20"></div>
					<div class="desc-text">
						<div class="desc-text__title fz_20"><i class="far fa-sparkles"></i> Core Systems</div>
						<div class="desc-margin-10"></div>
						<div class="desc-text__text fz_15">
							<span style="color:#FFD700">Fame System</span> &mdash; Earn fame from monster kills, tasks, and professions. Level up your fame rank and spend points in the Fame Shop.<br><br>
							<span style="color:#44AAFF">Tasks</span> &mdash; Accept daily and rotating tasks. Earn gold, experience, and fame by completing kill objectives.<br><br>
							<span style="color:#00FF88">Professions</span> &mdash; Mining, Herbalism, Woodcutting (gathering) and Blacksmith, Alchemy, Cooking, Enchanting (crafting). Each grants permanent passive bonuses.<br><br>
							<span style="color:#FF4444">Dungeons</span> &mdash; Challenge instanced dungeons with up to 6 difficulty levels. Solo or party. Leaderboards track completion times.<br><br>
							<span style="color:#9933FF">Talents</span> &mdash; Invest passive points into a vocation-specific skill tree. Unlock new spells and stat bonuses.<br><br>
							<span style="color:#FF00B3">Paragon</span> &mdash; Endgame progression unlocked at level 300. Gain Paragon levels, allocate stat points across 3 categories, and unlock milestones.<br><br>
							<span style="color:#6CCCDF">Pets</span> &mdash; Collect, level, and summon combat pets that assist you in battle.<br><br>
							<span style="color:#CC0000">Codex</span> &mdash; Collect Codex cards from monsters. Equip cards for passive bonuses and triggered effects like critical hits and dodge.<br><br>
							<span style="color:#00CCFF">Zone Buffs</span> &mdash; Dynamic buffs rotate across zones every 2 hours, granting bonuses like Double EXP, Orb Shower, Blood Pact, and more.<br><br>
							<span style="color:#FFD700">Premium</span> &mdash; Unlock bonuses across all systems: +15% Paragon XP, +10% Fame, reduced dungeon cooldowns, and more.
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- ========== PREMIUM TAB ========== -->
		<div class="global-desc__content-item" data-name-tab="premium">
			<div class="global-desc__content-title flex-sc">
				<div class="global-desc__content-title-icon"><img src="layout/application/templates/default/images/description/navigation/nav_icon_7.png" alt=""></div>
				<div class="global-desc__content-title-text">Premium<span>Premium Account Benefits</span></div>
			</div>
			<div class="global-desc__content-box">
				<div class="desc-text">
					<div class="desc-text__title fz_20"><i class="far fa-info-circle"></i> What is Premium?</div>
					<div class="desc-margin-10"></div>
					<div class="desc-text__text fz_15">
						A <b>Premium Account</b> enhances every core system in the game. Premium players progress faster, earn more rewards, and get quality-of-life improvements across all activities. Below is a full list of benefits.
					</div>
				</div>
				<div class="desc-margin-20"></div>
				<div class="desc-sep"></div>
				<div class="desc-margin-20"></div>
				<div class="desc-text">
					<div class="desc-text__title fz_20"><i class="far fa-info-circle"></i> Premium Benefits</div>
				</div>
				<div class="desc-margin-10"></div>
				<div class="desc-rate__rates flex-ss">
					<div class="desc-rate__rates-item">
						<div class="desc-rate__rates-item-title" style="color:#FFD700">Experience</div>
						<div class="desc-rate__rates-item-desc">1.5x EXP when stamina is above 40 hours</div>
						<div class="desc-rate__rates-item-value">+50% EXP</div>
					</div>
					<div class="desc-rate__rates-item">
						<div class="desc-rate__rates-item-title" style="color:#FF00B3">Paragon XP</div>
						<div class="desc-rate__rates-item-desc">Bonus Paragon experience from all sources</div>
						<div class="desc-rate__rates-item-value">+15% XP</div>
					</div>
					<div class="desc-rate__rates-item">
						<div class="desc-rate__rates-item-title" style="color:#FFD700">Fame</div>
						<div class="desc-rate__rates-item-desc">Bonus fame from all sources (kills, tasks, professions)</div>
						<div class="desc-rate__rates-item-value">+10% Fame</div>
					</div>
					<div class="desc-rate__rates-item">
						<div class="desc-rate__rates-item-title" style="color:#00FF88">Gathering Professions</div>
						<div class="desc-rate__rates-item-desc">Bonus EXP for Mining, Herbalism, Woodcutting, Fishing</div>
						<div class="desc-rate__rates-item-value">+10% Gather EXP</div>
					</div>
					<div class="desc-rate__rates-item">
						<div class="desc-rate__rates-item-title" style="color:#44AAFF">Crafting Professions</div>
						<div class="desc-rate__rates-item-desc">Bonus EXP for Blacksmith, Alchemy, Cooking, Enchanting</div>
						<div class="desc-rate__rates-item-value">+5% Craft EXP</div>
					</div>
					<div class="desc-rate__rates-item">
						<div class="desc-rate__rates-item-title" style="color:#FF4444">Dungeon Cooldown</div>
						<div class="desc-rate__rates-item-desc">Reduced cooldown between dungeon runs (8h vs 12h)</div>
						<div class="desc-rate__rates-item-value">-33% Cooldown</div>
					</div>
					<div class="desc-rate__rates-item">
						<div class="desc-rate__rates-item-title" style="color:#9933FF">Talent Reset</div>
						<div class="desc-rate__rates-item-desc">50% less gold cost when resetting your talent tree</div>
						<div class="desc-rate__rates-item-value">-50% Gold</div>
					</div>
					<div class="desc-rate__rates-item">
						<div class="desc-rate__rates-item-title" style="color:#44AAFF">Task Rerolls</div>
						<div class="desc-rate__rates-item-desc">Extra free task rerolls per day</div>
						<div class="desc-rate__rates-item-value">+5 Rerolls</div>
					</div>
					<div class="desc-rate__rates-item">
						<div class="desc-rate__rates-item-title" style="color:#44AAFF">Task Locks</div>
						<div class="desc-rate__rates-item-desc">Extra task lock slots per day</div>
						<div class="desc-rate__rates-item-value">+5 Locks</div>
					</div>
					<div class="desc-rate__rates-item">
						<div class="desc-rate__rates-item-title" style="color:#6CCCDF">Market Slots</div>
						<div class="desc-rate__rates-item-desc">More simultaneous offers on the player market</div>
						<div class="desc-rate__rates-item-value">More Offers</div>
					</div>
					<div class="desc-rate__rates-item">
						<div class="desc-rate__rates-item-title" style="color:#6CCCDF">Houses</div>
						<div class="desc-rate__rates-item-desc">Only premium players can buy houses</div>
						<div class="desc-rate__rates-item-value">Required</div>
					</div>
					<div class="desc-rate__rates-item">
						<div class="desc-rate__rates-item-title" style="color:#6CCCDF">Premium Travel</div>
						<div class="desc-rate__rates-item-desc">Access to premium-only travel destinations</div>
						<div class="desc-rate__rates-item-value">Exclusive</div>
					</div>
				</div>
				<div class="desc-margin-20"></div>
				<div class="desc-sep"></div>
			</div>
		</div>

		<!-- ========== FAME TAB ========== -->
		<div class="global-desc__content-item" data-name-tab="fame">
			<div class="global-desc__content-title flex-sc">
				<div class="global-desc__content-title-icon"><img src="layout/application/templates/default/images/description/navigation/nav_icon_5.png" alt=""></div>
				<div class="global-desc__content-title-text">Fame<span>Fame System</span></div>
			</div>
			<div class="global-desc__content-box">
				<div class="desc-text">
					<div class="desc-text__title fz_20"><i class="far fa-info-circle"></i> Fame System</div>
					<div class="desc-margin-10"></div>
					<div class="desc-text__text fz_15">
						<b>Fame</b> is a universal progression currency earned from monster kills, tasks, professions (mining, herbalism, woodcutting), and various activities. Fame has 30 levels and each level requires more points than the last.
						<div class="desc-margin-10"></div>
						<b>How to Earn Fame:</b><br>
						- Kill monsters that grant fame (varies by tier, from 1 to 100 fame per kill)<br>
						- Complete tasks<br>
						- Gather resources (mining, herbalism, woodcutting)<br>
						- Zone events and activities<br>
						- Premium players earn <b style="color:#FFD700">+10% bonus fame</b> from all sources
						<div class="desc-margin-10"></div>
						<b>How to Spend Fame:</b><br>
						- <b>Spendable Points</b> accumulate alongside your fame level. These can be spent at the <b>Fame Shop</b> for outfits, mounts, and exclusive rewards.<br>
						- Fame level also unlocks bonus task rerolls in the Task system.
					</div>
				</div>
				<div class="desc-margin-20"></div>
				<div class="desc-sep"></div>
				<div class="desc-margin-20"></div>
				<div class="desc-text">
					<div class="desc-text__title fz_20"><i class="far fa-info-circle"></i> Fame Levels</div>
				</div>
				<div class="desc-margin-10"></div>
				<div class="desc-rate__rates flex-ss">
					<div class="desc-rate__rates-item">
						<div class="desc-rate__rates-item-title">Level 1</div>
						<div class="desc-rate__rates-item-value">100 pts</div>
					</div>
					<div class="desc-rate__rates-item">
						<div class="desc-rate__rates-item-title">Level 5</div>
						<div class="desc-rate__rates-item-value">2,000 pts</div>
					</div>
					<div class="desc-rate__rates-item">
						<div class="desc-rate__rates-item-title">Level 10</div>
						<div class="desc-rate__rates-item-value">11,000 pts</div>
					</div>
					<div class="desc-rate__rates-item">
						<div class="desc-rate__rates-item-title">Level 15</div>
						<div class="desc-rate__rates-item-value">41,000 pts</div>
					</div>
					<div class="desc-rate__rates-item">
						<div class="desc-rate__rates-item-title">Level 20</div>
						<div class="desc-rate__rates-item-value">140,000 pts</div>
					</div>
					<div class="desc-rate__rates-item">
						<div class="desc-rate__rates-item-title">Level 25</div>
						<div class="desc-rate__rates-item-value">365,000 pts</div>
					</div>
					<div class="desc-rate__rates-item">
						<div class="desc-rate__rates-item-title">Level 30</div>
						<div class="desc-rate__rates-item-value">715,000 pts</div>
					</div>
				</div>
				<div class="desc-margin-20"></div>
				<div class="desc-sep"></div>
			</div>
		</div>

		<!-- ========== TASKS TAB ========== -->
		<div class="global-desc__content-item" data-name-tab="tasks">
			<div class="global-desc__content-title flex-sc">
				<div class="global-desc__content-title-icon"><img src="layout/application/templates/default/images/description/navigation/nav_icon_3.png" alt=""></div>
				<div class="global-desc__content-title-text">Tasks<span>Task System v2</span></div>
			</div>
			<div class="global-desc__content-box">
				<div class="desc-text">
					<div class="desc-text__title fz_20"><i class="far fa-info-circle"></i> Task System</div>
					<div class="desc-margin-10"></div>
					<div class="desc-text__text fz_15">
						The <b>Task System</b> provides daily objectives that reward gold, experience, and fame. Tasks are automatically generated based on your level and rotate daily.
						<div class="desc-margin-10"></div>
						<b>How it Works:</b><br>
						- You receive a set of tasks each day, accessible via the in-game task window<br>
						- Each task requires killing a specific number of a monster type<br>
						- Complete tasks to earn gold, experience, and fame rewards<br>
						- Tasks can be accepted from NPCs throughout the world
					</div>
				</div>
				<div class="desc-margin-20"></div>
				<div class="desc-sep"></div>
				<div class="desc-margin-20"></div>
				<div class="desc-text">
					<div class="desc-text__title fz_20"><i class="far fa-info-circle"></i> Rerolls &amp; Locks</div>
					<div class="desc-margin-10"></div>
					<div class="desc-text__text fz_15">
						<b>Rerolls:</b> Don't like a task? Reroll it for a new one. You get <b>5 free rerolls</b> per day (base). Premium players get <b>+5 extra</b>. Additional rerolls can be earned through Fame level (1 extra per 5 fame levels). Paid rerolls cost <b>20 gold</b> each.
						<div class="desc-margin-10"></div>
						<b>Locks:</b> Want to keep a task across resets? Lock it. You get <b>3 free locks</b> per day (base). Premium players get <b>+5 extra</b>. Paid locks cost <b>10 gold</b> each.
					</div>
				</div>
				<div class="desc-margin-20"></div>
				<div class="desc-sep"></div>
				<div class="desc-margin-20"></div>
				<div class="desc-text">
					<div class="desc-text__title fz_20"><i class="far fa-info-circle"></i> Daily Bonus</div>
					<div class="desc-margin-10"></div>
					<div class="desc-text__text fz_15">
						After completing all your active tasks for the day, you can claim a <b>Daily Bonus</b> that grants extra fame points. This resets daily, encouraging consistent play.
					</div>
				</div>
				<div class="desc-margin-20"></div>
				<div class="desc-sep"></div>
			</div>
		</div>

		<!-- ========== PROFESSIONS TAB ========== -->
		<div class="global-desc__content-item" data-name-tab="professions">
			<div class="global-desc__content-title flex-sc">
				<div class="global-desc__content-title-icon"><img src="layout/application/templates/default/images/description/navigation/nav_icon_1.png" alt=""></div>
				<div class="global-desc__content-title-text">Professions<span>Gathering &amp; Crafting</span></div>
			</div>
			<div class="global-desc__content-box">
				<div class="desc-text">
					<div class="desc-text__title fz_20"><i class="far fa-info-circle"></i> Professions Overview</div>
					<div class="desc-margin-10"></div>
					<div class="desc-text__text fz_15">
						Professions are divided into <b>Gathering</b> and <b>Crafting</b> skills. As you level up a profession, you unlock permanent passive bonuses and new recipes. Premium players earn <b style="color:#FFD700">+10% gathering EXP</b> and <b style="color:#44AAFF">+5% crafting EXP</b>.
					</div>
				</div>
				<div class="desc-margin-20"></div>
				<div class="desc-sep"></div>
				<div class="desc-margin-20"></div>
				<div class="desc-text">
					<div class="desc-text__title fz_20"><i class="far fa-info-circle"></i> Gathering Professions</div>
				</div>
				<div class="desc-margin-10"></div>
				<div class="desc-rate__rates flex-ss">
					<div class="desc-rate__rates-item">
						<div class="desc-rate__rates-item-title" style="color:#CD7F32">Mining</div>
						<div class="desc-rate__rates-item-desc">Mine veins across the world (Bronze, Silver, Sapphire, Gold, Amethyst, Ruby, Emerald). Upgrade your pickaxe to mine rarer veins. Passive: +HP per level.</div>
						<div class="desc-rate__rates-item-value">7 Vein Types</div>
					</div>
					<div class="desc-rate__rates-item">
						<div class="desc-rate__rates-item-title" style="color:#00CC66">Herbalism</div>
						<div class="desc-rate__rates-item-desc">Harvest plants and mushrooms across the world. Used for Alchemy crafting. Passive: +Mana per level.</div>
						<div class="desc-rate__rates-item-value">6 Plant Types</div>
					</div>
					<div class="desc-rate__rates-item">
						<div class="desc-rate__rates-item-title" style="color:#8B4513">Woodcutting</div>
						<div class="desc-rate__rates-item-desc">Chop trees across the world. Used for Blacksmith and other crafts. Passive: +Attack Speed per level.</div>
						<div class="desc-rate__rates-item-value">Multiple Tree Types</div>
					</div>
					<div class="desc-rate__rates-item">
						<div class="desc-rate__rates-item-title" style="color:#44AAFF">Fishing</div>
						<div class="desc-rate__rates-item-desc">Fish in water spots across the world. Provides resources for Cooking.</div>
						<div class="desc-rate__rates-item-value">Gathering</div>
					</div>
					<div class="desc-rate__rates-item">
						<div class="desc-rate__rates-item-title" style="color:#9933FF">Rune Seeking</div>
						<div class="desc-rate__rates-item-desc">Find magical runes in the world. Used for Enchanting crafting.</div>
						<div class="desc-rate__rates-item-value">Gathering</div>
					</div>
				</div>
				<div class="desc-margin-20"></div>
				<div class="desc-sep"></div>
				<div class="desc-margin-20"></div>
				<div class="desc-text">
					<div class="desc-text__title fz_20"><i class="far fa-info-circle"></i> Crafting Professions</div>
				</div>
				<div class="desc-margin-10"></div>
				<div class="desc-rate__rates flex-ss">
					<div class="desc-rate__rates-item">
						<div class="desc-rate__rates-item-title" style="color:#CC4400">Blacksmith</div>
						<div class="desc-rate__rates-item-desc">Craft weapons and armor from ingots and wood. Higher skill unlocks better recipes.</div>
						<div class="desc-rate__rates-item-value">Equipment</div>
					</div>
					<div class="desc-rate__rates-item">
						<div class="desc-rate__rates-item-title" style="color:#00CC66">Alchemy</div>
						<div class="desc-rate__rates-item-desc">Brew powerful potions from herbs and creature products. Recipes can drop from monsters.</div>
						<div class="desc-rate__rates-item-value">Potions</div>
					</div>
					<div class="desc-rate__rates-item">
						<div class="desc-rate__rates-item-title" style="color:#FF8800">Cooking</div>
						<div class="desc-rate__rates-item-desc">Prepare food items that provide temporary buffs. Uses fish and gathered ingredients.</div>
						<div class="desc-rate__rates-item-value">Food Buffs</div>
					</div>
					<div class="desc-rate__rates-item">
						<div class="desc-rate__rates-item-title" style="color:#9933FF">Enchanting</div>
						<div class="desc-rate__rates-item-desc">Enchant equipment with magical bonuses using runes and essences.</div>
						<div class="desc-rate__rates-item-value">Enchantments</div>
					</div>
					<div class="desc-rate__rates-item">
						<div class="desc-rate__rates-item-title" style="color:#CC0000">Refinery</div>
						<div class="desc-rate__rates-item-desc">Refine raw materials into higher quality components for advanced crafting.</div>
						<div class="desc-rate__rates-item-value">Materials</div>
					</div>
				</div>
				<div class="desc-margin-20"></div>
				<div class="desc-sep"></div>
				<div class="desc-margin-20"></div>
				<div class="desc-text">
					<div class="desc-text__title fz_20"><i class="far fa-info-circle"></i> Essence Drops</div>
					<div class="desc-margin-10"></div>
					<div class="desc-text__text fz_15">
						While gathering, you have a chance to find <b>Essences</b> - rare crafting materials that drop from veins, plants, and trees. Essences are used in high-tier crafting recipes and can be traded on the market.
					</div>
				</div>
				<div class="desc-margin-20"></div>
				<div class="desc-sep"></div>
			</div>
		</div>

		<!-- ========== DUNGEONS TAB ========== -->
		<div class="global-desc__content-item" data-name-tab="dungeons">
			<div class="global-desc__content-title flex-sc">
				<div class="global-desc__content-title-icon"><img src="layout/application/templates/default/images/description/navigation/nav_icon_3.png" alt=""></div>
				<div class="global-desc__content-title-text">Dungeons<span>Dungeon System</span></div>
			</div>
			<div class="global-desc__content-box">
				<div class="desc-text">
					<div class="desc-text__title fz_20"><i class="far fa-info-circle"></i> Dungeon System</div>
					<div class="desc-margin-10"></div>
					<div class="desc-text__text fz_15">
						<b>Dungeons</b> are instanced challenges that can be run solo or with a party. Each dungeon has multiple <b>difficulty levels</b> (1 through 6), with higher difficulties providing better rewards but tougher monsters and bosses.
						<div class="desc-margin-10"></div>
						<b>Key Features:</b><br>
						- <b>Difficulty Scaling:</b> Monster HP, damage, and loot scale with difficulty level<br>
						- <b>Cooldown:</b> 12-hour cooldown per dungeon per difficulty (<b style="color:#FFD700">8 hours for Premium</b>)<br>
						- <b>Leaderboards:</b> Completion times are tracked - compete for the fastest clear<br>
						- <b>Party or Solo:</b> Some dungeons require a party, others can be soloed<br>
						- <b>Unique Rewards:</b> Each dungeon has unique loot tables including exclusive equipment sets
					</div>
				</div>
				<div class="desc-margin-20"></div>
				<div class="desc-sep"></div>
				<div class="desc-margin-20"></div>
				<div class="desc-text">
					<div class="desc-text__title fz_20"><i class="far fa-info-circle"></i> How Difficulty Works</div>
					<div class="desc-margin-10"></div>
					<div class="desc-text__text fz_15">
						Each difficulty level increases the challenge and rewards:<br><br>
						<b>Difficulty 1:</b> Base monsters and boss. Good for learning mechanics.<br>
						<b>Difficulty 2-3:</b> Increased monster stats. Better loot chances.<br>
						<b>Difficulty 4-5:</b> Significantly harder. Exclusive rare drops.<br>
						<b>Difficulty 6:</b> Maximum challenge. Best possible loot and leaderboard times.
						<div class="desc-margin-10"></div>
						Each difficulty has its own separate cooldown, so you can run the same dungeon at different difficulties in the same day.
					</div>
				</div>
				<div class="desc-margin-20"></div>
				<div class="desc-sep"></div>
				<div class="desc-margin-20"></div>
				<div class="desc-text">
					<div class="desc-text__title fz_20"><i class="far fa-info-circle"></i> Cooldown Timer</div>
					<div class="desc-margin-10"></div>
					<div class="desc-rate__rates flex-ss">
						<div class="desc-rate__rates-item">
							<div class="desc-rate__rates-item-title">Free Players</div>
							<div class="desc-rate__rates-item-desc">Standard cooldown per difficulty</div>
							<div class="desc-rate__rates-item-value">12 Hours</div>
						</div>
						<div class="desc-rate__rates-item">
							<div class="desc-rate__rates-item-title" style="color:#FFD700">Premium Players</div>
							<div class="desc-rate__rates-item-desc">Reduced cooldown per difficulty</div>
							<div class="desc-rate__rates-item-value" style="color:#FFD700">8 Hours</div>
						</div>
					</div>
				</div>
				<div class="desc-margin-20"></div>
				<div class="desc-sep"></div>
			</div>
		</div>

		<!-- ========== TALENTS TAB ========== -->
		<div class="global-desc__content-item" data-name-tab="talents">
			<div class="global-desc__content-title flex-sc">
				<div class="global-desc__content-title-icon"><img src="layout/application/templates/default/images/description/navigation/nav_icon_4.png" alt=""></div>
				<div class="global-desc__content-title-text">Talents<span>Passive Skill Tree</span></div>
			</div>
			<div class="global-desc__content-box">
				<div class="desc-text">
					<div class="desc-text__title fz_20"><i class="far fa-info-circle"></i> Passive Skill Tree</div>
					<div class="desc-margin-10"></div>
					<div class="desc-text__text fz_15">
						The <b>Talent Tree</b> (Passive Skills) is a vocation-specific skill tree where you invest points to permanently improve your character. Each vocation has its own unique tree with different branches and nodes.
						<div class="desc-margin-10"></div>
						<b>How it Works:</b><br>
						- You earn <b>1 Passive Point every 8 levels</b><br>
						- Each tree has multiple <b>branches</b> with sequential <b>nodes</b><br>
						- Nodes can grant: stat bonuses (via conditions), storage-based bonuses, or new spells<br>
						- Nodes have multiple levels - invest more points for stronger effects<br>
						- You must level up previous nodes in a branch before advancing to the next
					</div>
				</div>
				<div class="desc-margin-20"></div>
				<div class="desc-sep"></div>
				<div class="desc-margin-20"></div>
				<div class="desc-text">
					<div class="desc-text__title fz_20"><i class="far fa-info-circle"></i> Node Types</div>
					<div class="desc-margin-10"></div>
					<div class="desc-rate__rates flex-ss">
						<div class="desc-rate__rates-item">
							<div class="desc-rate__rates-item-title" style="color:#44AAFF">Stat Nodes</div>
							<div class="desc-rate__rates-item-desc">Increase skills, HP, mana, speed, damage, and resistances</div>
							<div class="desc-rate__rates-item-value">Permanent</div>
						</div>
						<div class="desc-rate__rates-item">
							<div class="desc-rate__rates-item-title" style="color:#00FF88">Spell Nodes</div>
							<div class="desc-rate__rates-item-desc">Learn new exclusive spells not available elsewhere</div>
							<div class="desc-rate__rates-item-value">New Spells</div>
						</div>
						<div class="desc-rate__rates-item">
							<div class="desc-rate__rates-item-title" style="color:#9933FF">Storage Nodes</div>
							<div class="desc-rate__rates-item-desc">Special bonuses tracked via storage (e.g., crafting bonuses)</div>
							<div class="desc-rate__rates-item-value">Special</div>
						</div>
					</div>
				</div>
				<div class="desc-margin-20"></div>
				<div class="desc-sep"></div>
				<div class="desc-margin-20"></div>
				<div class="desc-text">
					<div class="desc-text__title fz_20"><i class="far fa-info-circle"></i> Resetting the Tree</div>
					<div class="desc-margin-10"></div>
					<div class="desc-text__text fz_15">
						You can reset your entire talent tree to reallocate all points. The cost is <b>50 gold per spent point</b>. Premium players pay only <b style="color:#FFD700">25 gold per point (50% discount)</b>. All learned spells from the tree are forgotten on reset, and all stat bonuses are removed until you re-allocate.
					</div>
				</div>
				<div class="desc-margin-20"></div>
				<div class="desc-sep"></div>
			</div>
		</div>

		<!-- ========== PARAGON TAB ========== -->
		<div class="global-desc__content-item" data-name-tab="paragon">
			<div class="global-desc__content-title flex-sc">
				<div class="global-desc__content-title-icon"><img src="layout/application/templates/default/images/description/navigation/nav_icon_7.png" alt=""></div>
				<div class="global-desc__content-title-text">Paragon<span>Endgame Progression</span></div>
			</div>
			<div class="global-desc__content-box">
				<div class="desc-text">
					<div class="desc-text__title fz_20"><i class="far fa-info-circle"></i> Paragon System</div>
					<div class="desc-margin-10"></div>
					<div class="desc-text__text fz_15">
						The <b>Paragon System</b> is your endgame progression path, unlocked when you reach <b>Level 300</b>. After activation, you gain Paragon XP from all activities and level up your Paragon rank up to <b>level 300</b>.
						<div class="desc-margin-10"></div>
						<b>How it Works:</b><br>
						- Earn Paragon XP from the same activities that give regular experience<br>
						- XP requirement scales: <b>BaseXP * (1 + 0.05 * ParagonLevel)</b><br>
						- Each Paragon level grants 1 point to allocate<br>
						- Premium players earn <b style="color:#FFD700">+15% bonus Paragon XP</b><br>
						- <b>Death Penalty:</b> Lose 10% of your current Paragon XP on death
					</div>
				</div>
				<div class="desc-margin-20"></div>
				<div class="desc-sep"></div>
				<div class="desc-margin-20"></div>
				<div class="desc-text">
					<div class="desc-text__title fz_20"><i class="far fa-info-circle"></i> Stat Categories</div>
					<div class="desc-margin-10"></div>
					<div class="desc-text__text fz_15">
						Points rotate between 3 categories in order: <b style="color:#FF4444">Primary</b> &rarr; <b style="color:#44AAFF">Secondary</b> &rarr; <b style="color:#00FF88">Utility</b>. Each category has 4 stats you can invest in.
					</div>
				</div>
				<div class="desc-margin-10"></div>
				<div class="desc-rate__rates flex-ss">
					<div class="desc-rate__rates-item">
						<div class="desc-rate__rates-item-title" style="color:#FF4444">Primary</div>
						<div class="desc-rate__rates-item-desc">Melee Skill, Distance Skill, Magic Level, Shielding</div>
						<div class="desc-rate__rates-item-value">+2% per point</div>
					</div>
					<div class="desc-rate__rates-item">
						<div class="desc-rate__rates-item-title" style="color:#44AAFF">Secondary</div>
						<div class="desc-rate__rates-item-desc">Max HP, Max Mana, Speed, Crafting EXP</div>
						<div class="desc-rate__rates-item-value">+2% per point</div>
					</div>
					<div class="desc-rate__rates-item">
						<div class="desc-rate__rates-item-title" style="color:#00FF88">Utility</div>
						<div class="desc-rate__rates-item-desc">Fame Gain, Codex Knowledge, Healing Power, Luck</div>
						<div class="desc-rate__rates-item-value">+2% per point</div>
					</div>
				</div>
				<div class="desc-margin-20"></div>
				<div class="desc-sep"></div>
				<div class="desc-margin-20"></div>
				<div class="desc-text">
					<div class="desc-text__title fz_20"><i class="far fa-info-circle"></i> Milestones</div>
					<div class="desc-margin-10"></div>
					<div class="desc-text__text fz_15">
						At certain Paragon levels, you unlock <b>Milestones</b> - powerful permanent bonuses that further enhance your character. These are granted automatically as you level up.
					</div>
				</div>
				<div class="desc-margin-20"></div>
				<div class="desc-sep"></div>
			</div>
		</div>

		<!-- ========== PETS TAB ========== -->
		<div class="global-desc__content-item" data-name-tab="pets">
			<div class="global-desc__content-title flex-sc">
				<div class="global-desc__content-title-icon"><img src="layout/application/templates/default/images/description/navigation/nav_icon_5.png" alt=""></div>
				<div class="global-desc__content-title-text">Pets<span>Pet System</span></div>
			</div>
			<div class="global-desc__content-box">
				<div class="desc-text">
					<div class="desc-text__title fz_20"><i class="far fa-info-circle"></i> Pet System</div>
					<div class="desc-margin-10"></div>
					<div class="desc-text__text fz_15">
						<b>Pets</b> are collectible companions that fight alongside you in battle. Each pet has unique abilities and can be leveled up to become stronger.
						<div class="desc-margin-10"></div>
						<b>Key Features:</b><br>
						- <b>Collect:</b> Obtain pets from monster drops, quests, achievements, and the Fame Shop<br>
						- <b>Summon:</b> Have one active pet at a time that assists you in combat<br>
						- <b>Level Up:</b> Pets gain experience and level up, increasing their stats and unlocking abilities<br>
						- <b>Rarity:</b> Pets come in different rarities - common, rare, and legendary<br>
						- <b>Pet Spells:</b> Each pet has unique combat abilities that trigger automatically
					</div>
				</div>
				<div class="desc-margin-20"></div>
				<div class="desc-sep"></div>
				<div class="desc-margin-20"></div>
				<div class="desc-text">
					<div class="desc-text__title fz_20"><i class="far fa-info-circle"></i> Pet Combat</div>
					<div class="desc-margin-10"></div>
					<div class="desc-text__text fz_15">
						Pets attack enemies automatically when you are in combat. Their damage scales with their level and rarity. Some pets specialize in damage, while others provide healing or support effects. Experiment with different pets to find the best companion for your playstyle.
					</div>
				</div>
				<div class="desc-margin-20"></div>
				<div class="desc-sep"></div>
			</div>
		</div>

		<!-- ========== CODEX TAB ========== -->
		<div class="global-desc__content-item" data-name-tab="codex">
			<div class="global-desc__content-title flex-sc">
				<div class="global-desc__content-title-icon"><img src="layout/application/templates/default/images/description/navigation/nav_icon_4.png" alt=""></div>
				<div class="global-desc__content-title-text">Codex<span>Codex Cards</span></div>
			</div>
			<div class="global-desc__content-box">
				<div class="desc-text">
					<div class="desc-text__title fz_20"><i class="far fa-info-circle"></i> Codex System</div>
					<div class="desc-margin-10"></div>
					<div class="desc-text__text fz_15">
						The <b>Codex</b> is a card collection system. Monsters drop <b>Codex Cards</b> that can be equipped for powerful bonuses. Cards have different trigger types and levels, allowing deep customization of your build.
						<div class="desc-margin-10"></div>
						<b>How it Works:</b><br>
						- Kill monsters to collect their Codex cards<br>
						- Cards gain <b>essence</b> (experience) and level up<br>
						- Equip cards in your Codex slots for their effects<br>
						- Some cards provide <b>passive bonuses</b> (always active)<br>
						- Other cards trigger on specific events like <b>critical hits</b>, <b>taking damage</b>, or <b>killing enemies</b>
					</div>
				</div>
				<div class="desc-margin-20"></div>
				<div class="desc-sep"></div>
				<div class="desc-margin-20"></div>
				<div class="desc-text">
					<div class="desc-text__title fz_20"><i class="far fa-info-circle"></i> Card Trigger Types</div>
				</div>
				<div class="desc-margin-10"></div>
				<div class="desc-rate__rates flex-ss">
					<div class="desc-rate__rates-item">
						<div class="desc-rate__rates-item-title" style="color:#44AAFF">Passive</div>
						<div class="desc-rate__rates-item-desc">Always active: bonus damage, HP, crit chance, dodge chance, etc.</div>
						<div class="desc-rate__rates-item-value">Always On</div>
					</div>
					<div class="desc-rate__rates-item">
						<div class="desc-rate__rates-item-title" style="color:#FF4444">On Hit</div>
						<div class="desc-rate__rates-item-desc">Triggers when you deal damage: chain lightning, firebolt, etc.</div>
						<div class="desc-rate__rates-item-value">Attack</div>
					</div>
					<div class="desc-rate__rates-item">
						<div class="desc-rate__rates-item-title" style="color:#00FF88">On Kill</div>
						<div class="desc-rate__rates-item-desc">Triggers when you kill a monster: heal, gain buff, etc.</div>
						<div class="desc-rate__rates-item-value">Kill</div>
					</div>
					<div class="desc-rate__rates-item">
						<div class="desc-rate__rates-item-title" style="color:#CC0000">On Critical</div>
						<div class="desc-rate__rates-item-desc">Triggers on critical hits: bonus damage, special effects</div>
						<div class="desc-rate__rates-item-value">Crit</div>
					</div>
				</div>
				<div class="desc-margin-20"></div>
				<div class="desc-sep"></div>
			</div>
		</div>

		<!-- ========== ZONE BUFFS TAB ========== -->
		<div class="global-desc__content-item" data-name-tab="zonebuffs">
			<div class="global-desc__content-title flex-sc">
				<div class="global-desc__content-title-icon"><img src="layout/application/templates/default/images/description/navigation/nav_icon_0.png" alt=""></div>
				<div class="global-desc__content-title-text">Zone Buffs<span>Dynamic Zone Events</span></div>
			</div>
			<div class="global-desc__content-box">
				<div class="desc-text">
					<div class="desc-text__title fz_20"><i class="far fa-info-circle"></i> Zone Buff System</div>
					<div class="desc-margin-10"></div>
					<div class="desc-text__text fz_15">
						<b>Zone Buffs</b> are dynamic modifiers that rotate across hunting zones every <b>2 hours</b>. When a zone has an active buff, all players in that zone benefit from (or are challenged by) its effects. After the buff expires, there is a 30-minute cooldown before the next rotation.
					</div>
				</div>
				<div class="desc-margin-20"></div>
				<div class="desc-sep"></div>
				<div class="desc-margin-20"></div>
				<div class="desc-text">
					<div class="desc-text__title fz_20"><i class="far fa-info-circle"></i> Available Buffs</div>
				</div>
				<div class="desc-margin-10"></div>
				<div class="desc-rate__rates flex-ss">
					<div class="desc-rate__rates-item">
						<div class="desc-rate__rates-item-title" style="color:#FFD700">Double Experience</div>
						<div class="desc-rate__rates-item-desc">Gain double experience from all monster kills</div>
						<div class="desc-rate__rates-item-value">60 min</div>
					</div>
					<div class="desc-rate__rates-item">
						<div class="desc-rate__rates-item-title" style="color:#FF4444">Monster Rush</div>
						<div class="desc-rate__rates-item-desc">Monster spawn rate is doubled</div>
						<div class="desc-rate__rates-item-value">30 min</div>
					</div>
					<div class="desc-rate__rates-item">
						<div class="desc-rate__rates-item-title" style="color:#44AAFF">Orb Shower</div>
						<div class="desc-rate__rates-item-desc">Orb drop chance drastically increased</div>
						<div class="desc-rate__rates-item-value">40 min</div>
					</div>
					<div class="desc-rate__rates-item">
						<div class="desc-rate__rates-item-title" style="color:#CC0000">Blood Pact</div>
						<div class="desc-rate__rates-item-desc">Each kill permanently boosts your stats until you die or leave the zone</div>
						<div class="desc-rate__rates-item-value">60 min</div>
					</div>
					<div class="desc-rate__rates-item">
						<div class="desc-rate__rates-item-title" style="color:#FF0000">Bounty Hunt</div>
						<div class="desc-rate__rates-item-desc">Monster kills may grant fame points; player kills grant more</div>
						<div class="desc-rate__rates-item-value">60 min</div>
					</div>
					<div class="desc-rate__rates-item">
						<div class="desc-rate__rates-item-title" style="color:#00FF88">Rapid Regeneration</div>
						<div class="desc-rate__rates-item-desc">HP and Mana regeneration increased by 200%</div>
						<div class="desc-rate__rates-item-value">60 min</div>
					</div>
					<div class="desc-rate__rates-item">
						<div class="desc-rate__rates-item-title" style="color:#00CCFF">Speed Demon</div>
						<div class="desc-rate__rates-item-desc">Movement speed +50% and spell cooldowns reduced by 20%</div>
						<div class="desc-rate__rates-item-value">30 min</div>
					</div>
					<div class="desc-rate__rates-item">
						<div class="desc-rate__rates-item-title" style="color:#8888FF">Survival Instinct</div>
						<div class="desc-rate__rates-item-desc">Damage taken reduced by 25%, max HP increased by 20%</div>
						<div class="desc-rate__rates-item-value">60 min</div>
					</div>
					<div class="desc-rate__rates-item">
						<div class="desc-rate__rates-item-title" style="color:#CC0033">Blood Moon</div>
						<div class="desc-rate__rates-item-desc">Gain 15% lifesteal. Taking damage boosts your next attack by 10%</div>
						<div class="desc-rate__rates-item-value">50 min</div>
					</div>
					<div class="desc-rate__rates-item">
						<div class="desc-rate__rates-item-title" style="color:#9933FF">Codex Knowledge</div>
						<div class="desc-rate__rates-item-desc">Monsters have a chance to grant +5 Codex essence when killed</div>
						<div class="desc-rate__rates-item-value">50 min</div>
					</div>
				</div>
				<div class="desc-margin-20"></div>
				<div class="desc-sep"></div>
				<div class="desc-margin-20"></div>
				<div class="desc-text">
					<div class="desc-text__title fz_20"><i class="far fa-info-circle"></i> Buff Categories</div>
					<div class="desc-margin-10"></div>
					<div class="desc-text__text fz_15">
						Buffs are categorized as <b style="color:#00FF88">Friendly</b> (safe, reward-focused) or <b style="color:#FF4444">Aggressive</b> (high-risk, combat-focused). Aggressive buffs tend to encourage PvP and dangerous gameplay, while Friendly buffs are pure quality-of-life improvements.
					</div>
				</div>
				<div class="desc-margin-20"></div>
				<div class="desc-sep"></div>
			</div>
		</div>

	
	</div>
</div>
<style>



.whtt-name {
    color: #f9af75;
	font-size: 20px;
    text-align: center;
	font: small-caps 500 20px Exocet, Verdana, "Open Sans", Arial, "Helvetica Neue", Helvetica, sans-serif;
	margin-top: 20px;
}

.tooltip {
    text-decoration:none;
    position:relative;
	cursor: pointer;
}
.tooltip span {
    width: 250px;
    height: 495px;
    background-image: url('https://i.imgur.com/wi9eF3O.png'); /* Set your default background image here */
    background-size: cover;
    background-repeat: no-repeat;
    background-position: -2px -5px; /* Adjusted background position */
    text-align: center;
    margin-left: 8px;
    margin-right: 8px;
    padding: 13px 19px 13px 16px; /* Add left padding for the image */
    border-radius: 5px;
    position: absolute;
    bottom: 125%;
    left: 50%;
    color: #d9d9d9;
    transform: translateX(-50%);
    font: 16px Arial, "Helvetica Neue", Helvetica, sans-serif;
    color: #f6edd6;
    display: none; /* Hide the tooltip by default */
}

.tooltip .image img {
    display: inline-block;
    width: 32px;
    height: 32px;
    margin-right: 180px; /* Add space between the image and text */
    margin-left: 90px;
    margin-top: 5px;
}

.tooltip:hover span {
    display:block;
    position:fixed;
    overflow:hidden;
	z-index:999
}
</style>


<script>
var tooltipSpans = document.querySelectorAll('.tooltip-span'); // Changed to class

window.onmousemove = function (e) {
    var x = e.clientX,
        y = e.clientY;
    
    tooltipSpans.forEach(function(tooltipSpan) {
        tooltipSpan.style.top = (y + 20) + 'px';
        tooltipSpan.style.left = (x + 20) + 'px';
    });
};


document.addEventListener('contextmenu', event => event.preventDefault());</script>
	<style>
									.prevent-select {
  -webkit-user-select: none; /* Safari */
  -ms-user-select: none; /* IE 10 and IE 11 */
  user-select: none; /* Standard syntax */
}

img {
    pointer-events: none;
}
								</style>

<?php
include 'layout/overall/footer_wiki.php'; ?>
