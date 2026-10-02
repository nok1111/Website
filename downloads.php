<?php require_once 'engine/init.php'; include 'layout/overall/header.php'; ?>

<h1 class="cd-page-title">Downloads</h1>

<div class="dl-steps">

	<!-- Step 1: Download -->
	<div class="dl-step">
		<div class="dl-step-number">1</div>
		<div class="dl-step-title">Download & Install</div>
		<div class="dl-step-desc">To connect to our servers, you need to download and install our game client.</div>

		<div class="dl-step-subtitle">Game Client</div>
		<div class="dl-step-text">Download and install the game client to get started.</div>
		<a href="https://drive.google.com/file/d/1gppC_MXgd-s7u3xIucl7KlmnvOwlQ0tF/view?usp=sharing" target="_blank" class="dl-btn"><i class="fa fa-cloud-download"></i> Google Mirror — Download Client</a>
		</div>

	<!-- Step 2: Create Account -->
	<div class="dl-step">
		<div class="dl-step-number">2</div>
		<div class="dl-step-title">Create an Account</div>
		<div class="dl-step-desc">Create an account and a new character to start playing. It only takes a minute!</div>

		<a href="register.php" class="cd-btn-hero" style="display:inline-flex;margin-top:16px;">+ Create an Account</a>
	</div>

	<!-- Step 3: Info -->
	<div class="dl-step">
		<div class="dl-step-number">3</div>
		<div class="dl-step-title">About the Project</div>
		<div class="dl-step-desc">Knowledge is power! Be aware of all promotions, bonuses and settings of our game servers.</div>

		<div class="dl-info-list">
			<div class="dl-info-item"><i class="fa fa-info-circle"></i> <a href="serverinfo.php" target="_blank">Description of the Server</a></div>
			<div class="dl-info-item"><i class="fa fa-calendar"></i> <a href="#">Grand Opening: somewhere in 2026</a></div>
			<div class="dl-info-item"><i class="fa fa-gift"></i> <a href="buypoints.php">Store and Premium</a></div>
			<div class="dl-info-item"><i class="fa fa-cog"></i> <a href="index.php" target="_blank">Change-log and Updates</a></div>
		</div>
	</div>

</div>

<?php
include 'layout/overall/footer.php'; ?>
