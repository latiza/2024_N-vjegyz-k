<?php
session_start();

if (isset($_POST['rendben'])) {
	
	// Változók tisztítása
	$email  = strip_tags(strtolower(trim($_POST['email'])));
	$jelszo = $_POST['jelszo'];

	// Változók ellenőrzése
	if (empty($email) || 
		!filter_var($email, FILTER_VALIDATE_EMAIL) || 
		!preg_match("/^[a-zA-Z ]*$/", $jelszo)) {
			$hiba = "Rossz e-mail címet vagy jelszót adtál meg!";
	}
	// Beléptetés
	else {
		require("../kapcsolat.php");
		$sql = "SELECT *
				FROM felhasznalok
				WHERE email = '{$email}'
				AND jelszo = '{$jelszo}'";
		$eredmeny = mysqli_query($dbconn, $sql);

		// Sikeres
		if (mysqli_num_rows($eredmeny) > 0) {
			$_SESSION['belepett'] = true;
			header("Location: lista.php");
		}
		// Sikertelen
		else {
			$hiba = "Hibás e-mail címet vagy jelszót adtál meg!";
		}
	}
}
?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Névjegykártyák</title>
<link href="stilus.css" rel="stylesheet">
</head>

<body>
<h1>Belépés</h1>
<form method="post" action="">
	<?php if (isset($hiba)) print $hiba; ?>
	<p><label for="email">E-mail:*</label><br>
	<input type="email" id="email" name="email" required></p>
	<p><label for="jelszo">Jelszó:*</label><br>
	<input type="password" id="jelszo" name="jelszo" required></p>
	<p><em>A *-gal jelölt mezők kitöltése kötelező!</em></p>
	<input type="submit" id="rendben" name="rendben" value="Belépés">
</form>
</body>
</html>