<?php

// Függvény az ékezetes karakterek eltávolítására és más speciális karakterek kezelésére
function ekezettelen($szoveg) {
    // Az ékezetes karakterek és azok egyszerűsített változatai
    $mit  = array("á", "é", "í", "ó", "ö", "ő", "ú", "ü", "ű", "Á", "É", "Í", "Ó", "Ö", "Ő", "Ú", "Ü", "Ű", "_", " ");
    $mire = array("a", "e", "i", "o", "o", "o", "u", "u", "u", "A", "E", "I", "O", "O", "O", "U", "U", "U", "-", "-");
    
    // Az ékezetes karakterek cseréje
    $szoveg = str_replace($mit, $mire, $szoveg);

    // A basename() használata, hogy csak a fájlnév maradjon meg, elkerülve az útvonal manipulációkat
    return basename($szoveg);
}

// Függvény a fájlméret emberbarát megjelenítéséhez (pl. MB, KB)
function human_filesize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $power = $bytes > 0 ? floor(log($bytes, 1024)) : 0;
    return number_format($bytes / (1024 ** $power), 2, '.', ',') . ' ' . $units[$power];
}

// Ellenőrzi, hogy a formot elküldték-e
if (isset($_POST['rendben'])) {

    // Engedélyezett MIME típusok. Csak ezeket a típusokat fogadjuk el.
    $mime = array("image/jpeg", "image/pjpeg", "image/gif", "image/png");
    
    // A fájl MIME típusának megbízható ellenőrzése a szerveren (nem csak a $_FILES['type'])
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $_FILES['fajl']['tmp_name']);
    finfo_close($finfo);
    
    // Ellenőrzi a fájl MIME típusát és méretét (2MB max)
    if (in_array($mimeType, $mime) && $_FILES['fajl']['size'] < 2000000) {

        // Ellenőrzi, hogy nem történt-e hiba a feltöltés során
        if ($_FILES['fajl']['error'] === UPLOAD_ERR_OK) {

            // A feltöltött fájl információinak megjelenítése olvasható formában
            $kimenet = "<h3>Feltöltött fájl adatai:</h3>
            <ul>
                <li>Fájlnév: " . htmlspecialchars($_FILES['fajl']['name'], ENT_QUOTES, 'UTF-8') . "</li>
                <li>Ideiglenes név: " . htmlspecialchars($_FILES['fajl']['tmp_name'], ENT_QUOTES, 'UTF-8') . "</li>
                <li>Hibakód: {$_FILES['fajl']['error']}</li>
                <li>Fájlméret: " . human_filesize($_FILES['fajl']['size']) . "</li>
                <li>Fájltípus: " . htmlspecialchars($mimeType, ENT_QUOTES, 'UTF-8') . "</li>
            </ul>";

            // Új fájlnév generálása az időbélyegző és az ékezetek eltávolításával
            $fajl = date("U-") . ekezettelen($_FILES['fajl']['name']);

            // Ellenőrzi, hogy a fájl már létezik-e a "kepek" mappában, és ha nem, menti a fájlt
            if (!file_exists("kepek/" . $fajl)) {
                move_uploaded_file($_FILES['fajl']['tmp_name'], "kepek/" . $fajl);
            } else {
                // Ha már létezik a fájl, jelezze a felhasználónak
                $kimenet = "<p style='color: red;'>Hiba: A fájl már létezik a 'kepek' mappában!</p>";
            }
        } else {
            // Hibakezelés, ha hiba történt a fájl feltöltése közben
            $kimenet = "<p style='color: red;'>Hiba a fájl feltöltése közben!</p>";
        }
    } else {
        // Hibakezelés, ha a fájl típusa nem megfelelő vagy a fájl túl nagy
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

<!-- Az űrlap, amely POST metódust használ a fájlok feltöltéséhez -->
<form method="post" action="" enctype="multipart/form-data">
    <?php
    // A feltöltési kimenet megjelenítése, ha van
    if (isset($kimenet)) {
        echo $kimenet;
    }
    ?>
    
    <!-- Fájl kiválasztása -->
    <input type="file" id="fajl" name="fajl" required>
    
    <!-- Feltöltés gomb -->
    <input type="submit" id="rendben" name="rendben" value="Feltöltés">
</form>

</body>
</html>
