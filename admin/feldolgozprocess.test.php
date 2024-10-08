<?php

// Helper függvény az egyszerű tesztekhez
function assertEqual($expected, $actual, $message = "") {
    if ($expected === $actual) {
        echo "PASS: " . $message . PHP_EOL;
    } else {
        echo "FAIL: " . $message . PHP_EOL;
        echo "Expected: " . var_export($expected, true) . ", got: " . var_export($actual, true) . PHP_EOL;
    }
}

// Szimuláljuk a POST kérést
$_SERVER['REQUEST_METHOD'] = 'POST';

// Imitáljuk a $_POST adatokat (form adatok)
$_POST['nev'] = "Teszt Nev";
$_POST['cegnev'] = "Teszt Ceg";
$_POST['mobil'] = "123456789";
$_POST['email'] = "teszt@example.com";

// Imitáljuk a $_FILES adatokat (fájlfeltöltés)
$_FILES['foto'] = array(
    'name' => 'tesztkep.jpg',
    'type' => 'image/jpeg',
    'tmp_name' => '/tmp/phpYzdqkD',  // Ezt a fájlt egy igazi környezetben kellene megadni
    'error' => 0,
    'size' => 100000 // 100 KB, tehát nem túl nagy
);

// "Fejléc" kiírás letiltása
ob_start(); // Bufferezzük a kimenetet

// Itt meghívjuk a backend.php-t
include 'felvitelprocess.php';

$response = ob_get_clean(); // Az eredmény JSON válasz
$responseData = json_decode($response, true); // Válasz átalakítása tömbbé

// Tesztek
echo "=== TESZTEK KEZDÉSE ===" . PHP_EOL;

// Teszteljük, hogy sikeres-e a válasz
assertEqual(true, $responseData['success'], "Adatok feldolgozása sikeresen lefutott.");

// Hibaüzenetek ellenőrzése, ha vannak
if (!$responseData['success'] && isset($responseData['error'])) {
    echo "Hiba történt: " . $responseData['error'] . PHP_EOL;
} else {
    echo "Nincs hiba, adatok sikeresen feldolgozva." . PHP_EOL;
}

echo "=== TESZTEK VÉGE ===" . PHP_EOL;
