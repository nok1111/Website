<?php
	require_once 'layout/layout_config.php';
	$launch_seconds = (strtotime($countDown) - time());
	$delay_hide = $launch_seconds + $countDown_hide;

    // Detect if we are on the index/home page
    $__header_script = basename($_SERVER['SCRIPT_NAME'], '.php');
    $__is_index = ($__header_script === 'index');

    // Fetch data from database (only needed on index for hero stats)
    if ($__is_index) {
        $data = array(
            'bestPlayer' => mysql_select_single("SELECT `name`, `level` FROM `players` ORDER BY `experience` DESC LIMIT 1"),
            'accountCount' => mysql_select_single("SELECT COUNT(`id`) as `count` FROM `accounts`")
        );
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Ascension Online</title>

	<meta name="keywords" content="Tibia, mmorpg, custom rpg, tibia custom, experience, dungeons, fight, pvp" />
	<meta name="description" content="Ascension Online - Best Custom RPG Server Experience" />
	<meta property="og:title" content="Ascension Online">
	<meta property="og:type" content="website">
	<meta property="og:description" content="Ascension Online - Best Custom RPG Server Experience">
	<meta property="og:site_name" content="Ascension Online">

	<link href="layout/application/templates/default/images/hellgrave_icono.png" rel="shortcut icon" type="image/x-icon">

	<!-- Cinematic Dark Theme -->
	<link rel="stylesheet" href="layout/application/templates/default/css/cinematic-dark.css">
	<link rel="stylesheet" href="layout/application/templates/default/fonts/fontawesome/css/font-awesome.css">
</head>
<body>

<div class="wrapper">

<!-- ============ NAVIGATION ============ -->
<nav class="cd-nav">
	<div class="cd-nav-inner">
		<a href="index.php" class="cd-nav-logo">ASCENSION</a>

		<div class="cd-nav-links">
			<a href="index.php" class="cd-nav-link">Home</a>
			<a href="downloads.php" class="cd-nav-link">Download</a>

			<div class="cd-dropdown">
				<button class="cd-dropdown-trigger">Library <span class="arrow">&#9660;</span></button>
				<div class="cd-dropdown-menu">
					<a href="wiki.php">Wikipedia</a>
					<a href="deaths.php">Deaths</a>
					<a href="highscores.php">Highscores</a>
					<a href="killers.php">Killers</a>
					<a href="serverinfo.php">Server Info</a>
				</div>
			</div>

			<div class="cd-dropdown">
				<button class="cd-dropdown-trigger">Account <span class="arrow">&#9660;</span></button>
				<div class="cd-dropdown-menu">
					<a href="register.php">Register</a>
					<a href="myaccount.php">My Account</a>
					<a href="helpdesk.php">Contact Us</a>
					<a href="support.php">Support</a>
					<a href="forum.php">Forum</a>
				</div>
			</div>

			<div class="cd-dropdown">
				<button class="cd-dropdown-trigger">Store <span class="arrow">&#9660;</span></button>
				<div class="cd-dropdown-menu">
					<a href="buypoints.php">Donate</a>
					<a href="shop.php">Shop</a>
				</div>
			</div>
		</div>

		<div class="cd-nav-actions">
			<?php if (user_logged_in() === true): ?>
				<a href="myaccount.php" class="cd-btn-primary">Account</a>
			<?php else: ?>
				<a href="login_1.php" class="cd-btn-ghost">Login</a>
				<a href="register.php" class="cd-btn-primary">Register</a>
			<?php endif; ?>
		</div>
	</div>
</nav>

<?php if ($__is_index): ?>
<!-- ============ HERO SECTION (index only) ============ -->
<section class="cd-hero">
	<div class="cd-hero-content">
		<div class="cd-hero-badge">
			<span class="dot"></span>
			Server Online &mdash; <?php echo user_count_online(); ?> Playing
		</div>
		<h1>Enter the World<br>of <em>Ascension</em></h1>
		<p class="cd-hero-sub">A custom RPG experience with 10 unique classes, deep dungeons, and your best ot-server experience ever. Your adventure starts here.</p>
		<div class="cd-hero-buttons">
			<a href="register.php" class="cd-btn-hero">Start Playing</a>
			<a href="downloads.php" class="cd-btn-hero-outline">Download Client</a>
		</div>
		<div class="cd-stats">
			<div class="cd-stat">
				<div class="cd-stat-value"><?php echo user_count_online(); ?></div>
				<div class="cd-stat-label">Online Now</div>
			</div>
			<div class="cd-stat">
				<div class="cd-stat-value">10</div>
				<div class="cd-stat-label">Classes</div>
			</div>
			<div class="cd-stat">
				<div class="cd-stat-value"><?php echo isset($data['bestPlayer']['level']) ? $data['bestPlayer']['level'] : '—'; ?></div>
				<div class="cd-stat-label">Top Level</div>
			</div>
			<div class="cd-stat">
				<div class="cd-stat-value"><?php echo isset($data['accountCount']['count']) ? number_format($data['accountCount']['count']) : '—'; ?></div>
				<div class="cd-stat-label">Accounts</div>
			</div>
		</div>
	</div>
</section>

<!-- ============ FEATURES BAR (index only) ============ -->
<div class="cd-features">
	<div class="cd-feature">
		<div class="cd-feature-icon">&#9876;</div>
		<h3>10 Unique Classes</h3>
		<p>From Magician to Monk, each with distinct playstyles and progression.</p>
	</div>
	<div class="cd-feature">
		<div class="cd-feature-icon">&#127984;</div>
		<h3>Deep Dungeons</h3>
		<p>Tiered dungeons with unique mechanics, bosses, and exclusive loot.</p>
	</div>
	<div class="cd-feature">
		<div class="cd-feature-icon">&#10024;</div>
		<h3>Custom Systems</h3>
		<p>Passive skills, paragon boards, fame system, and more.</p>
	</div>
	<div class="cd-feature">
		<div class="cd-feature-icon">&#127942;</div>
		<h3>Weekly Events</h3>
		<p>Zone events, PvP tournaments, and seasonal content every week.</p>
	</div>
</div>
<?php endif; ?>

<!-- ============ MAIN CONTENT AREA ============ -->
<div class="home-content">
	<div class="content-area" style="display:flex;gap:32px;padding-top:<?php echo $__is_index ? '40px' : '32px'; ?>;padding-bottom:80px;">
		<div class="main-content" style="flex:1;min-width:0;">