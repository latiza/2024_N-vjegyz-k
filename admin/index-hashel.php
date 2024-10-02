<?php
session_start();

if (isset($_POST['rendben'])) {
	
	// Változók tisztítása, <> levágása
	$email  = strip_tags(strtolower(trim($_POST['email'])));
	$jelszo = strip_tags($_POST['jelszo']);

	// Változók ellenőrzése
	if (empty($email) || 
		!filter_var($email, FILTER_VALIDATE_EMAIL) || 
		!preg_match("/^[a-zA-Z ]*$/", $jelszo)) {
			$hiba = "Hibás e-mail címet vagy jelszót adtál meg!";
	}
	// Beléptetés
	else {
		// Sikeres
		if ($email == "jancsi@gmail.com" && sha1($jelszo) == "49cef48df229f6e608f4b57c11ef05c4f014f0c6") {
			$_SESSION['belepett'] = true;
			header("Location: lista.php");
		}
		/**ugyanúgy bedrótozott jelszónak számít, de ha valaki megnézi a forráskódot, akkor ebből a számsorozatból nem biztos, hogy tudni fogja, hogy ez a juliska, kivétel azok, akinek minden nap ilyen zöld mátrixok esnek le a szemük előtt, vagy olyan rendszergazdák aki fejből tudnak 25 jegyű termékkódokat, és mindennek a sha1-es kódolását, mert már annyiszor láttak már ilyet a felhasználóktól. Szóval ez a második verzió aminek ugyanúgy kellene működnie , ha kipróbáljuk,  */
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