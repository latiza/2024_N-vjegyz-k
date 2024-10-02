<?php

// Függvény az ékezetes karakterek eltávolítására
function ekezettelen($szoveg) {
    // Ékezetes karakterek és azok helyettesítő karakterei
    $mit  = array("á", "é", "í", "ó", "ö", "ő", "ú", "ü", "ű", "Á", "É", "Í", "Ó", "Ö", "Ő", "Ú", "Ü", "Ű", "_", " ");
    $mire = array("a", "e", "i", "o", "o", "o", "u", "u", "u", "A", "E", "I", "O", "O", "O", "U", "U", "U", "-", "-");
    // Az ékezetes karakterek helyettesítése
    return str_replace($mit, $mire, $szoveg);
}

// Ellenőrzi, hogy a formot elküldték-e
if (isset($_POST['rendben'])) {
    // Engedélyezett MIME típusok
    $mime = array("image/jpeg", "image/pjpeg", "image/gif", "image/png");
    
    // Ellenőrzi a fájl MIME típusát és méretét
    if (in_array($_FILES['fajl']['type'], $mime) && $_FILES['fajl']['size'] < 2000000) {
        // Kimeneti HTML a feltöltött fájl adataival
        $kimenet = "<h3>Feltöltött fájl adatai:</h3>
        <ul>
            <li>Fájlnév: {$_FILES['fajl']['name']}</li>
            <li>Ideiglenes név: {$_FILES['fajl']['tmp_name']}</li>
            <li>Hibakód: {$_FILES['fajl']['error']}</li>
            <li>Fájlméret: {$_FILES['fajl']['size']} bytes</li>
            <li>Fájltípus: {$_FILES['fajl']['type']}</li>
        </ul>";

        // Új fájlnév generálása az ékezetek eltávolításával
        $fajl = date("U-") . ekezettelen($_FILES['fajl']['name']);
        
        // Fájl mentése a "kepek" mappába, ha az nem létezik
        if (!file_exists("kepek/" . $fajl)) {
            move_uploaded_file($_FILES['fajl']['tmp_name'], "kepek/" . $fajl);
        }
    } else {
        // Hibakezelés: ha a fájl nem engedélyezett típusú vagy túl nagy
        $kimenet = "<p style='color: red;'>Hiba: Érvénytelen fájltípus vagy fájlméret túl nagy!</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Fájlfeltöltés</title>
</head>

<body>
<h1>Fájlfeltöltés</h1>
<form method="post" action="" enctype="multipart/form-data">
    <?php if (isset($kimenet)) echo $kimenet; // A feltöltési kimenet megjelenítése ?>
    <input type="file" id="fajl" name="fajl">
    <input type="submit" id="rendben" name="rendben" value="Feltöltés">
</form>
</body>
</html>
