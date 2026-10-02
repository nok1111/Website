<?php require_once 'engine/init.php';
protect_page();
include 'layout/overall/header.php';

if (empty($_POST) === false) {
	$required_fields = array('name', 'selected_town');
	foreach($_POST as $key=>$value) {
		if (empty($value) && in_array($key, $required_fields) === true) {
			$errors[] = 'You need to fill in all fields.';
			break 1;
		}
	}

	if (empty($errors) === true) {
		if (!Token::isValid($_POST['token'])) {
			$errors[] = 'Token is invalid.';
		}
		$_POST['name'] = validate_name($_POST['name']);
		if ($_POST['name'] === false) {
			$errors[] = 'Your name can not contain more than 2 words.';
		} else {
			if (user_character_exist($_POST['name']) !== false) {
				$errors[] = 'Sorry, that character name already exist.';
			}
			if (!preg_match("/^[a-zA-Z ]+$/", $_POST['name'])) {
				$errors[] = 'Your name may only contain a-z, A-Z and spaces.';
			}
			if (strlen($_POST['name']) < $config['minL'] || strlen($_POST['name']) > $config['maxL']) {
				$errors[] = 'Your character name must be between ' . $config['minL'] . ' - ' . $config['maxL'] . ' characters long.';
			}
			$resname = explode(" ", $_POST['name']);
			$username = $_POST['name'];
			foreach($resname as $res) {
				if(in_array(strtolower($res), $config['invalidNameTags'])) {
					$errors[] = 'Your username contains a restricted word.';
				}
				if(strlen($res) == 1) {
					$errors[] = 'Too short words in your name.';
				}
			}
			if(in_array(strtolower($username), $config['creatureNameTags'])) {
				$errors[] = 'Your username contains a creature name.';
			}
			if (!in_array((int)$_POST['selected_vocation'], $config['available_vocations'])) {
				$errors[] = 'Permission Denied. Wrong vocation.';
			}
			if (!in_array((int)$_POST['selected_town'], $config['available_towns'])) {
				$errors[] = 'Permission Denied. Wrong town.';
			}
			if (!in_array((int)$_POST['selected_gender'], array(0, 1))) {
				$errors[] = 'Permission Denied. Wrong gender.';
			}
			if (vocation_id_to_name($_POST['selected_vocation']) === false) {
				$errors[] = 'Failed to recognize that vocation, does it exist?';
			}
			if (town_id_to_name($_POST['selected_town']) === false) {
				$errors[] = 'Failed to recognize that town, does it exist?';
			}
			if (gender_exist($_POST['selected_gender']) === false) {
				$errors[] = 'Failed to recognize that gender, does it exist?';
			}
			$char_count = user_character_list_count($session_user_id);
			if ($char_count >= $config['max_characters'] && !is_admin($user_data)) {
				$errors[] = 'Your account is not allowed to have more than '. $config['max_characters'] .' characters.';
			}
			if (validate_ip(getIP()) === false && $config['validate_IP'] === true) {
				$errors[] = 'Failed to recognize your IP address. (Not a valid IPv4 address).';
			}
		}
	}
}
?>

<h1 class="cd-page-title">Create Character</h1>

<?php
if (isset($_GET['success']) && empty($_GET['success'])) {
	echo '<div class="cc-message success">Congratulations! Your character has been created. See you in-game! <a href="myaccount.php">Back to your Account</a>.</div>';
} else {
	if (empty($_POST) === false && empty($errors) === true) {
		if ($config['log_ip']) {
			znote_visitor_insert_detailed_data(2);
		}
		$character_data = array(
			'name'		=>	format_character_name($_POST['name']),
			'account_id'=>	$session_user_id,
			'vocation'	=>	$_POST['selected_vocation'],
			'town_id'	=>	$_POST['selected_town'],
			'sex'		=>	$_POST['selected_gender'],
			'lastip'	=>	getIPLong(),
			'created'	=>	time()
		);
		user_create_character($character_data);
		header('Location: createcharacter.php?success');
		exit();

	} else if (empty($errors) === false) {
		echo '<div class="cc-message error">';
		echo output_errors($errors);
		echo '</div>';
	}
?>

<div class="cc-form">
<form action="" method="post">

	<!-- Name -->
	<div class="cc-field">
		<div class="cc-field-label">Character Name</div>
		<input type="text" name="name" placeholder="Enter your character name..." style="max-width:400px;">
	</div>

	<!-- Vocation -->
	<div class="cc-field">
		<div class="cc-field-label">Choose Your Class</div>
		<div class="cc-vocation-grid" id="vocation-grid">
			<?php foreach ($config['available_vocations'] as $id): ?>
			<label class="cc-vocation-tile" data-voc-id="<?php echo $id; ?>">
				<input type="radio" name="selected_vocation" value="<?php echo $id; ?>" <?php if ($id == $config['available_vocations'][0]) echo 'checked'; ?> />
				<img src="images/vocations/<?php echo $id; ?>.png" alt="<?php echo htmlspecialchars(vocation_id_to_name($id)); ?>">
				<span class="cc-voc-name"><?php echo htmlspecialchars(vocation_id_to_name($id)); ?></span>
			</label>
			<?php endforeach; ?>
		</div>
		<div class="cc-vocation-desc" id="vocation-description"></div>
	</div>

	<!-- Gender & Town row -->
	<div class="cc-select-row">
		<div class="cc-field">
			<div class="cc-field-label">Gender</div>
			<select name="selected_gender">
				<option value="1">Male</option>
				<option value="0">Female</option>
			</select>
		</div>
		<?php
		$available_towns = $config['available_towns'];
		if (count($available_towns) > 1):
		?>
		<div class="cc-field">
			<div class="cc-field-label">Town</div>
			<select name="selected_town">
				<?php foreach ($available_towns as $tid): ?>
				<option value="<?php echo $tid; ?>"><?php echo town_id_to_name($tid); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<?php
		else:
		?>
		<input type="hidden" name="selected_town" value="<?php echo end($available_towns); ?>">
		<?php endif; ?>
	</div>

	<?php Token::create(); ?>

	<input type="submit" value="Create Character">

</form>
</div>

<script>
var vocationInfo = <?php
	$vocInfo = [];
	foreach ($config['available_vocations'] as $id) {
		$name = $config['vocations'][$id]['name'];
		$desc = isset($config['vocations'][$id]['desc']) ? $config['vocations'][$id]['desc'] : '';
		$roles = isset($config['vocations'][$id]['roles']) ? $config['vocations'][$id]['roles'] : [];
		$vocInfo[$id] = ['name' => $name, 'desc' => $desc, 'roles' => $roles];
	}
	echo json_encode($vocInfo);
?>;

function updateVocationDescription(id) {
	var info = vocationInfo[id];
	var html = '<h4>' + info.name + '</h4>';
	if (info.desc) html += '<p>' + info.desc + '</p>';
	else html += '<p>No description available.</p>';
	if (info.roles && info.roles.length > 0) {
		html += '<div class="cc-roles"><span class="cc-roles-label">Roles:</span>';
		info.roles.forEach(function(roleImg) {
			html += '<img src="images/vocations/' + roleImg + '" alt="role">';
		});
		html += '</div>';
	}
	document.getElementById('vocation-description').innerHTML = html;
}

function selectTile(id) {
	document.querySelectorAll('.cc-vocation-tile').forEach(function(t) { t.classList.remove('selected'); });
	var tile = document.querySelector('.cc-vocation-tile[data-voc-id="'+id+'"]');
	if (tile) tile.classList.add('selected');
	var radio = document.querySelector('input[name="selected_vocation"][value="'+id+'"]');
	if (radio) radio.checked = true;
	updateVocationDescription(id);
}

document.querySelectorAll('.cc-vocation-tile').forEach(function(tile) {
	tile.addEventListener('click', function() {
		selectTile(this.getAttribute('data-voc-id'));
	});
});

// Initial selection
var checked = document.querySelector('input[name="selected_vocation"]:checked');
if (checked) selectTile(checked.value);
</script>

<?php
}
include 'layout/overall/footer_login.php'; ?>
