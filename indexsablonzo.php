<?php
require("kapcsolat.php");

/* Beállítások */
$sql = "SELECT *
        FROM nevjegyek";
$eredmeny = mysqli_query($dbconn, $sql);

$osszes   = mysqli_num_rows($eredmeny);
$mennyit  = 9;
$lapok    = ceil($osszes / $mennyit);
$aktualis = (isset($_GET['oldal'])) ? (int)$_GET['oldal'] : 1;
$honnan   = ($aktualis - 1) * $mennyit;

/* Lapozó */
$lapozo = "<p>";
$lapozo .= ($aktualis != 1) ? "<a href=\"?oldal=1\">Első</a> | " : "Első | ";
$lapozo .= ($aktualis > 1 && $aktualis <= $lapok) ? "<a href=\"?oldal=" . ($aktualis - 1) . "\">Előző</a> | " : "Előző | ";
for ($oldal = 1; $oldal <= $lapok; $oldal++) {
    $lapozo .= ($aktualis != $oldal) ? "<a href=\"?oldal={$oldal}\">{$oldal}</a> | " : $oldal . " | ";
}
$lapozo .= ($aktualis > 0 && $aktualis < $lapok) ? "<a href=\"?oldal=" . ($aktualis + 1) . "\">Következő</a> | " : "Következő | ";
$lapozo .= ($aktualis != $lapok) ? "<a href=\"?oldal={$lapok}\">Utolsó</a>" : "Utolsó";
$lapozo .= "</p>";

/* Keresési eredmények lekérdezése */
$kifejezes = (isset($_POST['kifejezes'])) ? $_POST['kifejezes'] : "";
$sql = "SELECT *
        FROM nevjegyek
        WHERE (
            nev LIKE '%{$kifejezes}%'
            OR cegnev LIKE '%{$kifejezes}%'
            OR mobil LIKE '%{$kifejezes}%'
            OR email LIKE '%{$kifejezes}%'
        )
        ORDER BY nev ASC
        LIMIT {$honnan}, {$mennyit}";
$eredmeny = mysqli_query($dbconn, $sql);

if (@mysqli_num_rows($eredmeny) < 1) {
    $kimenet = "<article>
        <h2>Nincs találat a rendszerben!</h2>
    </article>\n";
} else {
    $kimenet = "";
    $sablon = "<article>
        <img src=\"kepek/{foto}\" alt=\"{nev}\">
        <h2>{nev}</h2>
        <h3>{cegnev}</h3>
        <p>Mobil: <a href=\"tel:{mobil}\">{mobil}</a></p>
        <p>E-mail: <a href=\"mailto:{email}\">{email}</a></p>
    </article>\n";
    
    while ($sor = mysqli_fetch_assoc($eredmeny)) {
        // Cseréljük le a sablonban a változókat
        $egyed = str_replace(
            ['{foto}', '{nev}', '{cegnev}', '{mobil}', '{email}'],
            [$sor['foto'], $sor['nev'], $sor['cegnev'], $sor['mobil'], $sor['email']],
            $sablon
        );
        $kimenet .= $egyed;
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
    <form method="post" action="">
        <input type="search" id="kifejezes" name="kifejezes">
    </form>
    <?php print $lapozo; ?>
    <div class="container">
        <?php print $kimenet; ?>
    </div>
</body>
</html>
