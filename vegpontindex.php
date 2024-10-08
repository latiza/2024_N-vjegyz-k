<?php
// JSON fájl beolvasása
$json_data = file_get_contents('vegpont.php');
require "kapcsolat.php";
require "lapozo.php";
// JSON adatok dekódolása
$data = json_decode($json_data, true);

// Ellenőrizzük, hogy van-e adat a JSON fájlban
if (!empty($data)) {
    // JSON adatok feldolgozása
    $kimenet = "";
    foreach ($data as $sor) {
        $kimenet .= "
        <article>
        <img src=\"kepek/{$sor['foto']}\" alt=\"{$sor['nev']}\">";
        $kimenet .= "<h2>{$sor['nev']}</h2>";
        $kimenet .= "<h3>{$sor['cegnev']}</h3>";
        $kimenet .= "<p>Mobil: <a href=\"tel:{$sor['mobil']}\">{$sor['mobil']}</a></p>";
        $kimenet .= "<p>E-mail: <a href=\"mailto:{$sor['email']}\">{$sor['email']}</a></p>
        </article>\n";
    }
} else {
    // Ha nincs adat a JSON fájlban
    $kimenet = "<article><h2>Nincs találat a rendszerben!</h2></article>\n";
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Névjegykártyák</title>
    <link href="stilus.css" rel="stylesheet">
</head>
<body>
    <div class="container">
    <h1>Névjegykártyák</h1>
<?php print $lapozo; ?>
<?php print $kimenet; ?>
    </div>
    

</body>
</html>
