<?php
require "../kapcsolat.php";

// UTF-8 kódolás biztosítása a JSON válaszokhoz
header('Content-Type: application/json; charset=UTF-8');

// Ellenőrizzük, hogy GET kérés érkezett-e
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Névjegy törlése az adatbázisból
    $sql = "DELETE FROM nevjegyek WHERE id = ?";
    $stmt = $dbconn->prepare($sql);
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        // Sikeres törlés esetén átirányítás az RA-angular-list.html oldalra
        header('Location: RA-angular-list.html');
        exit();
    } else {
        // Hiba esetén visszaküldjük a hibaüzenetet UTF-8 kódolással
        http_response_code(500);
        echo json_encode(['error' => 'Hiba történt a törlés során!'], JSON_UNESCAPED_UNICODE);
    }
} else {
    // Hibás kérés vagy hiányzó azonosító esetén hibaüzenet visszaküldése
    http_response_code(400);
    echo json_encode(['error' => 'Hibás kérés vagy hiányzó azonosító!'], JSON_UNESCAPED_UNICODE);
}
