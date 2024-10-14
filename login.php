<?php
require "kapcsolat.php"; // Az adatbázis kapcsolódás

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Az űrlapadatok JSON formátumban érkeznek, így beolvassuk és dekódoljuk
    $input = json_decode(file_get_contents('php://input'), true);
    $email = mysqli_real_escape_string($dbconn, trim($input['email']));
    $jelszo = mysqli_real_escape_string($dbconn, trim($input['jelszo']));

    // Ellenőrizze, hogy az e-mail cím és jelszó nem üres
    if (empty($email) || empty($jelszo)) {
        http_response_code(400); // 400 Bad Request
        echo json_encode(['error' => 'Az e-mail cím és jelszó megadása kötelező!']);
        exit();
    }

    // Az admin felhasználó ellenőrzése az adatbázisban
    $sql = "SELECT * FROM felhasznalok WHERE email = ? LIMIT 1";
    $stmt = $dbconn->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();

        // Ellenőrzés: jelszó megfelel-e a titkosított verziónak
        if (password_verify($jelszo, $admin['jelszo'])) {
            // Indítsunk munkamenetet és tároljuk az admin státuszát
            session_start();
            $_SESSION['admin'] = true;
            $_SESSION['email'] = $admin['email'];

            http_response_code(200); // Sikeres
            echo json_encode(['success' => true]);
        } else {
            http_response_code(401); // 401 Unauthorized
            echo json_encode(['error' => 'Helytelen jelszó!']);
        }
    } else {
        http_response_code(404); // 404 Not Found
        echo json_encode(['error' => 'Az e-mail cím nem található!']);
    }
}
