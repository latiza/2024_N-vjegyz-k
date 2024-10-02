<?php
require("kapcsolat.php"); // Betölti az adatbázis kapcsolati beállításokat tartalmazó fájlt

/**
 * Lekérdezi az összes névjegyet az adatbázisból.
 *Az @param annotációk a PHPDoc-ban használatosak, és a kód dokumentálásáért felelősek. Segítenek a kód olvasóinak megérteni, hogy egy adott függvény paraméterei milyen típusúak, és milyen szerepet töltenek be a függvény működésében.
 * @param mysqli $dbconn Az adatbázis kapcsolati objektum
 * @param string $kifejezes A keresési kifejezés
 * @param int $honnan Az adatok kezdő indexe
 * @param int $mennyit Az oldalon megjelenő elemek száma
 * @return array Az adatbázisból lekért adatok tömbje
 */
function fetchContacts($dbconn, $kifejezes = "", $honnan = 0, $mennyit = 9) {
    // SQL lekérdezés az adatok lekérésére, ahol a keresési kifejezés található a névjegyek bármelyik mezőjében
    $sql = "SELECT * FROM nevjegyek
            WHERE (nev LIKE '%{$kifejezes}%'
            OR cegnev LIKE '%{$kifejezes}%'
            OR mobil LIKE '%{$kifejezes}%'
            OR email LIKE '%{$kifejezes}%')
            ORDER BY nev ASC
            LIMIT {$honnan}, {$mennyit}";
    
    // SQL lekérdezés végrehajtása
    $result = mysqli_query($dbconn, $sql);

    if (!$result) {
        // Hibakezelés: ha a lekérdezés nem sikerül, 500-as hibakódot küld és JSON formátumban visszaad egy hibát
        http_response_code(500);
        echo json_encode(["error" => "Adatbázis lekérdezési hiba: " . mysqli_error($dbconn)]);
        exit();
    }

    $data = array(); // Üres tömb létrehozása az adatok tárolására
    while ($row = mysqli_fetch_assoc($result)) {
        // A lekérdezés eredményéből sorokat olvasunk be és hozzáadjuk az adat tömbhöz
        $data[] = $row;
    }
    return $data; // Visszaadjuk az adatokat tartalmazó tömböt
}

function getTotalRecords($dbconn) {
    // SQL lekérdezés az összes rekord számának lekérésére a nevjegyek táblából
    $sql = "SELECT COUNT(*) AS count FROM nevjegyek";
    
    // SQL lekérdezés végrehajtása
    $result = mysqli_query($dbconn, $sql);
    $row = mysqli_fetch_assoc($result); // Az eredmény sor beolvasása
    return (int)$row['count']; // Az összes rekord számának visszaadása egész számként
}
