<?php
	require_once 'layout/layout_config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Wikipedia - Ascension Online</title>

	<meta name="keywords" content="Tibia, mmorpg, custom rpg, tibia custom, experience, dungeons, fight, pvp" />
	<meta name="description" content="Ascension Online - Wikipedia" />

	<link href="layout/application/templates/default/images/hellgrave_icono.png" rel="shortcut icon" type="image/x-icon">

	<!-- Old style.css needed for wiki layout components -->
	<link rel="stylesheet" href="layout/application/templates/default/css/style.css">
	<!-- Cinematic Dark overrides -->
	<link rel="stylesheet" href="layout/application/templates/default/css/cinematic-dark.css">
	<link rel="stylesheet" href="layout/application/templates/default/fonts/fontawesome/css/font-awesome.css">
	<link rel="stylesheet" href="layout/application/templates/default/fonts/rpgawesome/css/rpg-awesome.css">
</head>
<body class="cd-wiki-page">

<div class="wrapper">

<!-- ============ NAVIGATION (Cinematic Dark) ============ -->
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

<div class="cd-wiki-wrapper">
	<div class="cd-wiki-container">
