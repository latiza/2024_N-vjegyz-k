<?php
session_start();
if (!isset($_SESSION['belepett'])) { 
	header("Location: false.html"); // azaz ide
	exit();
}
// Űrlap feldolgozása
if (isset($_POST['rendben'])) {

	// Változók tisztítása
	$nev    = strip_tags(ucwords(strtolower(trim($_POST['nev']))));
	$cegnev = strip_tags(trim($_POST['cegnev']));
	$mobil  = strip_tags(trim($_POST['mobil']));
	$email  = strip_tags(strtolower(trim($_POST['email'])));

	// Változók vizsgálata
	$mime = array("image/jpeg", "image/pjpeg", "image/gif", "image/png");
	
	if (empty($nev))
		$hibak[] = "Nem adtál meg nevet!";
	elseif (strlen($nev) < 5)
		$hibak[] = "Rossz nevet adtál meg!";
	if (!empty($mobil) && strlen($mobil) < 9)
		$hibak[] = "Rossz mobil számot adtál meg!";
	if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL))
		$hibak[] = "Rossz e-mail címet adtál meg!";

	if ($_FILES['foto']['error'] == 0 && $_FILES['foto']['size'] > 2000000)
		$hibak[] = "Túl nagy méretű képet töltöttél fel!";
	
	if ($_FILES['foto']['error'] == 0 && !in_array($_FILES['foto']['type'], $mime))
		$hibak[] = "Nem megfelelő képformátum!";

	// Fájlnév elkészítése
	switch($_FILES['foto']['type']) {
		case "image/png": $kit = ".png"; break;
		case "image/gif": $kit = ".gif"; break;
		default: $kit = ".jpg";
	}
	$foto = date("U").$kit;

	// Hibaüzenetet összeállítása
	if (isset($hibak)) {
		$kimenet = "<ul>\n";
		foreach($hibak as $hiba) {
			$kimenet.= "<li>{$hiba}</li>\n";
		}
		$kimenet.= "</ul>\n";
	}
	else {
		// Felvitel az adatbázisba
		require("../kapcsolat.php");
		$sql = "INSERT INTO nevjegyek
				(foto, nev, cegnev, mobil, email)
				VALUES
				('{$foto}', '{$nev}', '{$cegnev}', '{$mobil}', '{$email}')";
		mysqli_query($dbconn, $sql);

		// Kép mozgatása a végleges helyére
		move_uploaded_file($_FILES['foto']['tmp_name'], "../kepek/{$foto}");
		header("Location: lista.php");
	}
}

// Űrlap megjelenítése
?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Névjegykártyák</title>
<link href="../stilus.css" rel="stylesheet">
</head>

<body>
<h1>Névjegykártyák</h1>
<form method="post" action="" enctype="multipart/form-data">
	<?php if (isset($kimenet)) print $kimenet; ?>
	<input type="hidden" name="MAX_FILE_SIZE" value="2000000" />
	<p><label for="foto">Fotó:</label><br>
	<input type="file" id="foto" name="foto"></p>
	<p><label for="nev">Név*:</label><br>
	<input type="text" id="nev" name="nev" value="<?php if (isset($nev)) print $nev; ?>"></p>
	<p><label for="cegnev">Cégnév:</label><br>
	<input type="text" id="cegnev" name="cegnev" value="<?php if (isset($cegnev)) print $cegnev; ?>"></p>
	<p><label for="mobil">Mobil:</label><br>
	<input type="tel" id="mobil" name="mobil" value="<?php if (isset($mobil)) print $mobil; ?>"></p>
	<p><label for="email">E-mail:</label><br>
	<input type="email" id="email" name="email" value="<?php if (isset($email)) print $email; ?>"></p>
	<p><em>A *-gal jelölt mezők kitöltése kötelező!</em></p>
	<input type="submit" id="rendben" name="rendben" value="Rendben">
	<input type="reset" value="Mégse">
	<p><a href="lista.php">Vissza a névjegyekhez</a></p>
</form>
<?php include("footer.html"); ?>
</body>
</html>
