<?php
session_start();

if (isset($_POST['rendben'])) {
	
	// Változók tisztítása
	$email  = strip_tags(strtolower(trim($_POST['email'])));
	$jelszo = strip_tags($_POST['jelszo']);

	// Változók ellenőrzése
	if (empty($email) || 
		!filter_var($email, FILTER_VALIDATE_EMAIL) || 
		!preg_match("/^[a-zA-Z ]*$/", $jelszo)) {
			$hiba = "Hibás e-mail címet vagy jelszót adtál meg!";
	}
		/**
	 * ha mail cím nem üres, mert lehet a beviteli mezőt telenyomta spacekkel, akkor tisztás után még lehet üres, majd megnézzük filterrel, nem valós e a mail cím, mert a vagynál azt kell tudni, hogy ha vagy vagy nem igaz, de egy igaz, akkor az egész feltétel igaz lesz, a php feldolgozás úgy működik, ha a első igaz, akkor ami tőle jobbra található, azokat már ki sem értékeli, hanem kiszáll belőle igazzal. Ha ez az első feltétel itt nem igaz, akkor megy tovább a filterre, ha az nem igaz, vagyis igaz, akkor ott kiszáll, tehát az a lényeg, hogy a vagy feltétel esetén az első vagy feltételnél kiszáll igazzal. Az és feltételnél meg az első hamisnál száll ki, és az egész hamis lesz. Ez azért érdekes majd hogyha if feltételbe több tagot teszek bele, akkor a sorrenden lehet esetleg a program sebességén változtatni, gyorsítani. ha a jelszó nem olyan, mint amit kértünk, hogy a-tól z-ig karakterek szerepeljenek benne,  
	 */
	// Beléptetés
	else {
		// Sikeres
		if ($email == "jancsi@gmail.com" && $jelszo == "juliska") {
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
	<!--1) ez a require az input mezőben, nem a legszerencsésebb ez a natív védelem, mert a böngészőből ki lehet szedni egy f12 és inspektorral,ráadásul régebbi explorereknél nem is működik. A type=email is text tipussá butul vissza a régi böngészőkben, így átengedik a rossz mail címet is. Tehát a lényeg, hogy az kevés, hogy a html5-ben vannak lehetőségek,a hibák ellenőrzésére. Akár itt használhatunk mintákat, reguláris kifejezéseket,html5 patterns.com-on meg lehet nézni a lehetőségeket, tehát sok mintát lehet találni telefonszám, bankkártyaszám stb.ellenőrzésére.  -->
	<p><label for="jelszo">Jelszó:*</label><br>
	<input type="password" id="jelszo" name="jelszo" required></p>
	<p><em>A *-gal jelölt mezők kitöltése kötelező!</em></p>
	<input type="submit" id="rendben" name="rendben" value="Belépés">
</form>
</body>
</html>