<?php
require("kapcsolat.php");

/* Beállítások */
//$kifejezes = isset($_GET['kifejezes']) ? $_GET['kifejezes'] : "";
/*Tanuljuk meg Elvis rövidebb alakját: A ?? operátor ellenőrzi, hogy a bal oldali érték létezik-e és nem null. Ha létezik, akkor azt az értéket adja vissza, különben a jobb oldalon megadott alapértelmezett értéket (ebben az esetben egy üres stringet) adja vissza.*/
$kifejezes = $_GET['kifejezes'] ?? "";
//var_dump($kifejezes);
$mennyit = isset($_GET['mennyit']) ? (int)$_GET['mennyit'] : 9; // Alapértelmezésként 9 elem
$sql = "SELECT *
        FROM nevjegyek
        WHERE (
            nev LIKE '%{$kifejezes}%'
            OR cegnev LIKE '%{$kifejezes}%'
            OR mobil LIKE '%{$kifejezes}%'
            OR email LIKE '%{$kifejezes}%'
        )";
$eredmeny = mysqli_query($dbconn, $sql);

$osszes   = mysqli_num_rows($eredmeny);
$lapok    = ceil($osszes / $mennyit); //ceil felfelé kerekít
$aktualis = (isset($_GET['oldal'])) ? (int)$_GET['oldal'] : 1;
$honnan   = ($aktualis-1)*$mennyit;

/* Lapozó */
$lapozo = "<p>";
$lapozo.= ($aktualis != 1) ? "<a href=\"?oldal=1&kifejezes={$kifejezes}&mennyit={$mennyit}\">Első</a> | " : "Első | ";

$lapozo.= ($aktualis > 1 && $aktualis <= $lapok) ? "<a href=\"?oldal=".($aktualis-1)."&kifejezes={$kifejezes}&mennyit={$mennyit}\">Előző</a> | " : "Előző | ";

for ($oldal=1; $oldal<=$lapok; $oldal++) {
    $lapozo.= ($aktualis != $oldal) ? "<a href=\"?oldal={$oldal}&kifejezes={$kifejezes}&mennyit={$mennyit}\">{$oldal}</a> | " : $oldal." | ";
}
$lapozo.= ($aktualis > 0 && $aktualis < $lapok) ? "<a href=\"?oldal=".($aktualis+1)."&kifejezes={$kifejezes}&mennyit={$mennyit}\">Következő</a> | " : "Következő | ";
$lapozo.= ($aktualis != $lapok) ? "<a href=\"?oldal={$lapok}&kifejezes={$kifejezes}&mennyit={$mennyit}\">Utolsó</a>" : "Utolsó";
$lapozo.= "</p>";

/* Keresési eredmények */
$sql .= " ORDER BY nev ASC
          LIMIT {$honnan}, {$mennyit}";
$eredmeny = mysqli_query($dbconn, $sql);

if (@mysqli_num_rows($eredmeny) < 1) {
    $kimenet = "<article>
        <h2>Nincs találat a rendszerben!</h2>
    </article>\n";
}
else {
    $kimenet = "";
    while ($sor = mysqli_fetch_assoc($eredmeny)) {
        $kimenet.= "<article>
            <img src=\"kepek/{$sor['foto']}\" alt=\"{$sor['nev']}\">
            <h2>{$sor['nev']}</h2>
            <h3>{$sor['cegnev']}</h3>
            <p>Mobil: <a href=\"tel:{$sor['mobil']}\">{$sor['mobil']}</a></p>
            <p>E-mail: <a href=\"mailto:{$sor['email']}\">{$sor['email']}</a></p>
        </article>\n";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Névjegykártyák</title>
<link href="stilus.css" rel="stylesheet">
</head>

<body>
<h1>Névjegykártyák</h1>
<form method="get" action="">
<input type="search" id="kifejezes" name="kifejezes" value="<?php echo htmlspecialchars($kifejezes); ?>">
    <input type="submit" value="Keresés">

    <label for="mennyit">Elemek száma oldalanként:</label>
	<!--this.form.submit() segít az űrlap automatikus továbbításában a szerver felé, anélkül, hogy gombot kellene ide helyezni.-->
    <select id="mennyit" name="mennyit" onchange="this.form.submit()">
		<!--Amikor a felhasználó kiválaszt egy értéket a legördülő listából (pl. 9, 30, vagy 60 rekord megjelenítése), az űrlap elküldésre kerül, és az oldal újratöltődik. Az oldal újratöltésekor a PHP kód visszaállítja a felhasználó által kiválasztott értéket.-->
        <option value="9" <?php echo $mennyit == 9 ? 'selected' : ''; ?>>9</option>
        <option value="30" <?php echo $mennyit == 30 ? 'selected' : ''; ?>>30</option>
        <option value="60" <?php echo $mennyit == 60 ? 'selected' : ''; ?>>60</option>
    </select>
</form>

<?php echo $lapozo; ?>

<div class="container">
<?php echo $kimenet; ?>
</div>
</body>
</html>
