<?php
// Kapcsolódás az adatbázishoz
require("kapcsolat.php");
/**
 * Lekérdezi az adatbázisból a 'nevjegyek' táblát.
 * @param mysqli $dbconn Az adatbázis kapcsolati objektum
 * @return array Az adatbázisból lekért adatok tömbje
 */
function fetchContacts($dbconn) {
    $query = "SELECT * FROM nevjegyek";
    $result = mysqli_query($dbconn, $query);

    if (!$result) {
        // Hibakezelés, ha a lekérdezés nem sikerült
        http_response_code(500);
        echo json_encode(["error" => "Adatbázis lekérdezési hiba: " . mysqli_error($dbconn)]);
        exit();
    }

    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}

// Adatok lekérdezése
$data = fetchContacts($dbconn);

// JSON formátumba alakítás
$json = json_encode($data);

// HTTP fejléc beállítása a megfelelő tartalomtípussal
header('Content-Type: application/json');

// JSON válasz visszaküldése
echo $json;
