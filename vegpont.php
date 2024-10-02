<?php
// Adatbázis kapcsolódás
require("kapcsolat.php");
// Lekérdezés az adatbázisból
$query = "SELECT * FROM nevjegyek";
$result = mysqli_query($dbconn, $query);
// Adatok tömbbe rendezése
$data = [];
/*Az eredményhalmazból az adatokat az mysqli_fetch_assoc() függvény segítségével kérjük le soronként. Ez a függvény az egyes sorokat asszociatív tömbként adja vissza, ahol a mezőnevek a kulcsok. */
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}
// JSON formátumba alakítás (pretty printtel)
$json = json_encode($data, JSON_PRETTY_PRINT);
/*JSON_PRETTY_PRINT: A json_encode() függvény második paramétere, amely biztosítja, hogy a kimeneti JSON szépen formázott legyen, behúzásokkal és új sorokkal. */
// HTTP fejléc beállítása a megfelelő tartalomtípussal
header('Content-Type: application/json');

// JSON válasz visszaküldése
echo $json;

