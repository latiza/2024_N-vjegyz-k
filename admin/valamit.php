<?php


require("../kapcsolat.php");
$kifejezes = (isset($_POST['kifejezes'])) ? $_POST['kifejezes'] : "";
$cegnev = 'Invitel';
$sql = "SELECT *
		FROM nevjegyek 
		WHERE cegnev 
		LIKE '%{$cegnev}%'
		"; 
$eredmeny = mysqli_query($dbconn, $sql);

$kimenet = "<table>
<tr>
	<th>Fotó</th>
	<th><a href=\"?rendez=nev\">Név</a></th>
	<th><a href=\"?rendez=cegnev\">Cégnév</a></th>
	<th><a href=\"?rendez=mobil\">Mobil</a></th>
	<th><a href=\"?rendez=email\">E-mail</a></th>
	<th>Művelet</th>
</tr>";
while ($sor = mysqli_fetch_assoc($eredmeny)) {
	$kimenet.= "<tr>
		<td><img src=\"../kepek/{$sor['foto']}\" alt=\"{$sor['nev']}\"></td>
		<td>{$sor['nev']}</td>
		<td>{$sor['cegnev']}</td>
		<td>{$sor['mobil']}</td>
		<td>{$sor['email']}</td>
		<td><a href=\"torles.php?id={$sor['id']}\">Törlés</a> | <a href=\"modositas.php?id={$sor['id']}\">Módosítás</a></td>
	</tr>";
}
$kimenet.= "</table>";
?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Névjegykártyák</title>
<link href="../stilus.css" rel="stylesheet">
</head>

<body>
<h1>Inviteles dolgozók</h1>
<form method="post" action="">
	<input type="submit" id="kifejezes" name="kifejezes">
</form>

<p><a href="felvitel.php">Új névjegy</a> | <a href="kilepes.php">Kilépés</a></p>
<?php print $kimenet; ?>
<p><a href="felvitel.php">Új névjegy</a> | <a href="kilepes.php">Kilépés</a></p>
</body>
</html>
