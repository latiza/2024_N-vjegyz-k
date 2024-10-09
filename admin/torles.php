<?php

if (isset($_GET['id'])) {
	require("../kapcsolat.php");
	$id = (int)$_GET['id'];

	// Fotó lekérdezése törléshez
	$sql = "SELECT foto
			FROM nevjegyek
			WHERE id = {$id}";
	$eredmeny = mysqli_query($dbconn, $sql);
	$sor = mysqli_fetch_assoc($eredmeny);
//ha nincs kép, akkor nem kell törölni
	if ($sor['foto'] != "nincskep.png") {
		unlink("../kepek/{$sor['foto']}");
	}

	// Rekord törlése
	$sql = "DELETE FROM nevjegyek
			WHERE id = {$id}";
	mysqli_query($dbconn, $sql);
}
header("Location: lista.php");

