<?php

session_start();
if (!isset($_SESSION['belepett'])) { 
	header("Location: false.html"); // azaz ide
	exit();
}

require "../kapcsolat.php"; // Kapcsolódás az adatbázishoz

// UTF-8 karakterkódolás biztosítása
header('Content-Type: application/json; charset=utf-8');

// Ha van ID a GET kérésben, egyedi névjegy lekérdezése
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Névjegy lekérdezése az ID alapján
    $sql = "SELECT * FROM nevjegyek WHERE id = ?";
    $stmt = $dbconn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Ha találunk adatot
    if ($result->num_rows > 0) {
        $contact = $result->fetch_assoc();
        echo json_encode($contact, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    } else {
        // Ha nincs ilyen névjegy, hibaüzenet visszaadása
        http_response_code(404);
        echo json_encode(['error' => 'Névjegy nem található!'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
} else {
    // Az összes névjegy lekérdezése
    $sql = "SELECT * FROM nevjegyek";
    $result = $dbconn->query($sql);

    $contacts = [];
    while ($row = $result->fetch_assoc()) {
        $contacts[] = $row;
    }

    // Az összes névjegy visszaadása
    echo json_encode($contacts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
