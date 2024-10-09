<?php
// Kapcsolat az adatbázissal
require "../kapcsolat.php"; // Az adatbázis kapcsolatot tartalmazó fájl betöltése

// Fájl típusok és méret ellenőrzése
$mime = ["image/jpeg", "image/pjpeg", "image/gif", "image/png"]; // Engedélyezett fájltípusok
$maxFileSize = 2000000; // 2MB maximális fájlméret

// Űrlap feldolgozása
if (isset($_POST['rendben'])) { // Ellenőrzi, hogy az űrlapot elküldték-e (a "Rendben" gomb megnyomásával)

    // Változók tisztítása és formázása a felhasználói bemenetből
    $nev    = mysqli_real_escape_string($dbconn, strip_tags(ucwords(strtolower(trim($_POST['nev']))))); // A név tisztítása: ékezetek eltávolítása, szókezdő betűk nagybetűsítése, SQL injekció elleni védelem
    $cegnev = mysqli_real_escape_string($dbconn, strip_tags(trim($_POST['cegnev']))); // Cég név tisztítása és SQL injekció elleni védelem
    $mobil  = mysqli_real_escape_string($dbconn, strip_tags(trim($_POST['mobil']))); // Mobil szám tisztítása és SQL injekció elleni védelem
    $email  = mysqli_real_escape_string($dbconn, strip_tags(strtolower(trim($_POST['email'])))); // E-mail tisztítása, kisbetűsítése és SQL injekció elleni védelem

    // Változók vizsgálata és hibaüzenetek összeállítása
    $hibak = []; // Üres tömb a hibaüzenetek tárolására

    if (empty($nev)) { // Ha a név mező üres
        $hibak[] = "Nem adtál meg nevet!"; // Hibaüzenet a név hiányáért
    } elseif (strlen($nev) < 5) { // Ha a név túl rövid
        $hibak[] = "Rossz nevet adtál meg!"; // Hibaüzenet a túl rövid névért
    }

    if (!empty($mobil) && strlen($mobil) < 9) { // Ha megadtak mobil számot, de az túl rövid
        $hibak[] = "Rossz mobil számot adtál meg!"; // Hibaüzenet a rossz mobil számért
    }

    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) { // Ha az e-mail mező nem üres, de nem valid e-mail cím
        $hibak[] = "Rossz e-mail címet adtál meg!"; // Hibaüzenet a rossz e-mail címért
    }

    if ($_FILES['foto']['error'] == 0) { // Ha fájlt töltöttek fel és nincs hiba a feltöltéssel
        // Ellenőrzi a fájl méretét
        if ($_FILES['foto']['size'] > $maxFileSize) { // Ha a fájl mérete nagyobb, mint a megadott max (2MB)
            $hibak[] = "Túl nagy méretű képet töltöttél fel!"; // Hibaüzenet a nagy fájlméretért
        }

        // MIME típus ellenőrzése a biztonságosabb `finfo_file` segítségével
        $finfo = finfo_open(FILEINFO_MIME_TYPE); // Fájl MIME típusának ellenőrzése
        $mimeType = finfo_file($finfo, $_FILES['foto']['tmp_name']); // A feltöltött fájl MIME típusának lekérése
        finfo_close($finfo); // Finfo erőforrás bezárása

        if (!in_array($mimeType, $mime)) { // Ha a fájl típusa nem található a megengedett MIME típusok között
            $hibak[] = "Nem megfelelő képformátum!"; // Hibaüzenet a rossz fájlformátumért
        }

        // Fájlnév elkészítése, ha nincs hiba
        if (empty($hibak)) {
            // A fájl típus alapján kiterjesztés megadása
            switch($mimeType) {
                case "image/png": $kit = ".png"; break; // Ha a fájl PNG
                case "image/gif": $kit = ".gif"; break; // Ha a fájl GIF
                default: $kit = ".jpg"; // Minden más esetben JPG
            }
            $foto = date("U") . $kit; // Egyedi fájlnév generálása az aktuális időbélyeg alapján
        }
    } else {
        // Ha nincs új kép feltöltve, a régi fájlnevet használjuk
        $foto = $_POST['regi_foto'];
    }

    // Hibaüzenetek kiírása vagy adatbázis művelet
    if (!empty($hibak)) { // Ha vannak hibák
        // Hibaüzenetek megjelenítése
        $kimenet = "<ul>\n"; 
        foreach ($hibak as $hiba) {
            $kimenet .= "<li>" . htmlspecialchars($hiba, ENT_QUOTES, 'UTF-8') . "</li>\n"; // Biztonságos HTML megjelenítés
        }
        $kimenet .= "</ul>\n";
    } else {
        // Ha van új fájl, azt a "kepek" mappába mozgatjuk
        if ($_FILES['foto']['error'] == 0 && empty($hibak)) {
            move_uploaded_file($_FILES['foto']['tmp_name'], "../kepek/{$foto}"); // Fájl mozgatása a célmappába
        }

        // Adatbázis frissítése (SQL injekció elleni védelem)
        $id = (int)$_POST['id']; // ID átalakítása egész számra, hogy elkerüljük az SQL injekciót
        $sql = "UPDATE nevjegyek
                SET foto = '{$foto}', nev = '{$nev}', cegnev = '{$cegnev}', mobil = '{$mobil}', email = '{$email}'
                WHERE id = {$id}"; // SQL lekérdezés az adatok frissítésére

        if (mysqli_query($dbconn, $sql)) { // Ha az SQL lekérdezés sikeres
            $kimenet = "<p style='color: green;'>Az adatok sikeresen frissültek!</p>"; // Sikerüzenet
        } else {
            $kimenet = "<p style='color: red;'>Hiba történt az adatok frissítésekor!</p>"; // Hibaüzenet az SQL lekérdezés sikertelensége esetén
        }
    }
}

// Űrlap előzetes kitöltése (szerkesztéshez)
else {
    $id = (int)$_GET['id']; // Az ID lekérése a GET paraméterből és egész számmá alakítása
    $sql = "SELECT * FROM nevjegyek WHERE id = {$id}"; // SQL lekérdezés az adott rekord lekérdezéséhez
    $eredmeny = mysqli_query($dbconn, $sql); // SQL végrehajtása
    $sor = mysqli_fetch_assoc($eredmeny); // Az eredmények lekérése asszociatív tömbként

    // Az adatokat a változókba tesszük a form kitöltéséhez
    $nev    = htmlspecialchars($sor['nev'], ENT_QUOTES, 'UTF-8'); // Név
    $cegnev = htmlspecialchars($sor['cegnev'], ENT_QUOTES, 'UTF-8'); // Cég név
    $mobil  = htmlspecialchars($sor['mobil'], ENT_QUOTES, 'UTF-8'); // Mobil
    $email  = htmlspecialchars($sor['email'], ENT_QUOTES, 'UTF-8'); // E-mail
    $foto   = ($sor['foto'] != "nincskep.png") ? htmlspecialchars($sor['foto'], ENT_QUOTES, 'UTF-8') : "nincskep.png"; // Fotó (ha nincs kép, egy alapértelmezett érték használata)
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Névjegykártyák</title>
<link href="../stilus.css" rel="stylesheet">
</head>

<body>
<h1>Névjegykártyák</h1>
<form method="post" action="" enctype="multipart/form-data">
    <!-- Hibák vagy sikerüzenet megjelenítése -->
    <?php if (isset($kimenet)) echo $kimenet; ?>

    <!-- Rejtett mezők a régi fotó nevének és az ID-nak a tárolásához -->
    <input type="hidden" id="id" name="id" value="<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>">
    <input type="hidden" id="regi_foto" name="regi_foto" value="<?php echo htmlspecialchars($foto, ENT_QUOTES, 'UTF-8'); ?>">
    <input type="hidden" name="MAX_FILE_SIZE" value="2000000" /> <!-- Maximális fájlméret beállítása (2MB) -->

    <!-- Jelenlegi fotó megjelenítése -->
    <img src="../kepek/<?php echo htmlspecialchars($foto, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($nev, ENT_QUOTES, 'UTF-8'); ?>">

    <!-- Fotó feltöltése -->
    <p><label for="foto">Fotó:</label><br>
    <input type="file" id="foto" name="foto"></p>

    <!-- Név -->
    <p><label for="nev">Név*:</label><br>
    <input type="text" id="nev" name="nev" value="<?php echo $nev; ?>"></p>

    <!-- Cégnév -->
    <p><label for="cegnev">Cégnév:</label><br>
    <input type="text" id="cegnev" name="cegnev" value="<?php echo $cegnev; ?>"></p>

    <!-- Mobil -->
    <p><label for="mobil">Mobil:</label><br>
    <input type="tel" id="mobil" name="mobil" value="<?php echo $mobil; ?>"></p>

    <!-- E-mail -->
    <p><label for="email">E-mail:</label><br>
    <input type="email" id="email" name="email" value="<?php echo $email; ?>"></p>

    <p><em>A *-gal jelölt mezők kitöltése kötelező!</em></p>

    <!-- Űrlap küldése és visszaállítása -->
    <input type="submit" id="rendben" name="rendben" value="Rendben">
    <input type="reset" value="Mégse">

    <!-- Visszalépés link -->
    <p><a href="lista.php">Vissza a névjegyekhez</a></p>
</form>
</body>
</html>
