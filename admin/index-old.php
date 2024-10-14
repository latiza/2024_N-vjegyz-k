<?php
session_start();

/**vegyél fel új táblát a nevjegyekbe, felhasznalok => 3 sor, indítás
id :tiny/int unsigned primary autoincrement
email: varchar 64
jelszo: varchar 255
majd beszúrunk egy felhasználót, mail cím: jancsi@gmail.com jelszónak beírjuk a a generált sha1 sorozatot. 
Nézz utána: sql injection, miért fontos védeni a rendszerünket.
Ha valki bejut ilyen módon, egy ilyet be tud juttatni, hogy OR 1 = 1; máris bent van a rendszerbe felhasználónév és jelszó nélkül, tehát űrlapról a megfelelő adatokkal ez bejuthat a rendszerbe, át lehet jutni rajta úgy hogy nem tudjuk a felhasználó nevet és jelszót. Ez az sql injection.
 */
if (isset($_POST['rendben'])) {
	
	// Változók tisztítása
	require"../kapcsolat.php";
//sql szempontjából veszélyesnek tűnő sztringeket hatástalanítja
	$email  = mysqli_real_escape_string($dbconn, strip_tags(strtolower(trim($_POST['email']))));
	$jelszo = sha1($_POST['jelszo']);

	// Változók ellenőrzése, preg_matchet ki kell szedni!!! mert nem enged be

	if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))
 {
		$hiba = "Rossz e-mail címet vagy jelszót adtál meg!";
	}

	// Beléptetés
	else {
		$sql = "SELECT id
				FROM felhasznalok
				WHERE email = '{$email}'
				AND jelszo = '{$jelszo}'
				LIMIT 3";//ne jelenjen meg az összes felhasználó, ha valaki betör a rendszerbe
		$eredmeny = mysqli_query($dbconn, $sql);
/**Még mindig van benne biztonsági rész, még mindig be lehet hatolni sql injectionnal, 
nézzük ezt a SELECT*-ot, ez ugye gyakorlatilag minden adatot lekérdez, ami azt jelenti, hogy van e-mail és jelszó is benne, bár a jelszó titkosítva van, de ha valaki lekéri az összes jelszót, akkor azért ezt vissza lehet fejteni, egy pár óra vagy pár nap alatt, attól függően, hogy milyen erős a titkosítás, 
lehet azt csinálni, hogy csak az id-t kérdezzük le, és az egy inkognítót biztosít a rendszernek, nem fogsz róla megtudni semmit, mert ha azt kapod vissza 1.) id, akkor mi van, abból még nem tudsz meg róla semmit, hogyha azt mondjuk SELECT id , és ha netalán sql betörés történik, akkor is kap az ember egy csomó számot, amivel nem tud mit kezdeni a hacker, de attól függetlenül még be tud menni a rendszerbe, csak úgymond nem listázódnak ki ezek az alatta lévő plusz adatok. 
A következőkben pedig az van, hogyha feltöri a rendszert és bármilyen email jeszóval be tud jutni a rendszerbe, akkor valószínűleg az összes rekordot lekérdezi az sql betörése után. 
Ezért itt if (mysqli_num_rows($eredmeny) == 1) ne azt írjuk hogy > mint nulla, hanem pontosan egyenlő egyel. Mert ha van benne 3 felhasználó és feltöri a rendszert, akkor a mysql numrows eredmény 3 lesz, tehát nem lesz egyenlő az egyel. Tehát ez itt ()nem lesz igaz, és már nem tud bejutni a rendszerbe ezzel.
Amit még lehet csinálni, ha ilyen sql befecskendezés történne, akkor még meg lehet az csinálni hogy a lekérdezések végére beírunk egy limitet, hogy ne jöjjön ki az összes felhasználó, még akkor se ha sikerült betörni a rendszerbe.
A Limithez ha egyet írnánk be, akkor megkönnyítenénk a hacker életét a világon, mert ha feltöri a rendszert akkor is beengedi a mysql num rows mivel igazzá válik a logikai vizsgálat. Ha limit 2 írunk, akkor nem biztos, hogy kiadja mind a 20 felhasználót, és akkor az eredmény sorok száma 2 lesz egy sql befecskendezés esetén is. És már ez a feltétel nem teljesül és nem engedi be a rendszer. 
Amit még lehet a rendszer védelmében tenni, hogy a 8-iksorban megadjuk a mysqli_real_escape_string()-et ez az sql szempontjából veszélyes sztringeket hatástalanítja, tehát az escape az védő karakterekkel történő ellátás, így hiába írnak be az email címbe bármit is, onnantól ez már nehezen lesz támadható.  
A sha1-nél nem kell levédenünk, mert ott eleve hexadecimális zöldségre lesz titkosítva. 
mivel a mysqli_real_escape_string($dbconn, használja ezt a kapcsolati azonosítót a require-t érdemes elé tenni, azért mert gyakorlatilag ebben a fájlban készül el ez a dbconn; és akkor ezzel biztonságossá vált a rendszer.
 */
		// Sikeres, ez is egy védelem, ha nem nagyobb mint 0
		if (mysqli_num_rows($eredmeny) == 1) {
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
