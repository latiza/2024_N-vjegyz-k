<?php
require "../kapcsolat.php";

// Ellenőrizzük, hogy POST kérés érkezett-e
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Adatok lekérése
    $id = (int) $_POST['id'];
    $nev = mysqli_real_escape_string($dbconn, strip_tags(trim($_POST['nev'])));
    $cegnev = mysqli_real_escape_string($dbconn, strip_tags(trim($_POST['cegnev'])));
    $mobil = mysqli_real_escape_string($dbconn, strip_tags(trim($_POST['mobil'])));
    $email = mysqli_real_escape_string($dbconn, strip_tags(trim($_POST['email'])));

    // Kép feltöltése
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        // Engedélyezett fájltípusok
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];

        // MIME típus ellenőrzése
        $mimeType = mime_content_type($_FILES['foto']['tmp_name']);
        if (in_array($mimeType, $allowedMimeTypes)) {
            // Új fájlnév generálása az aktuális timestamp alapján
            $extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $fotoFileName = time() . '.' . $extension;

            // Fájl feltöltése a kepek mappába
            move_uploaded_file($_FILES['foto']['tmp_name'], "../kepek/{$fotoFileName}");

            // Régi fotó törlése, ha van
            $oldFotoQuery = "SELECT foto FROM nevjegyek WHERE id = ?";
            $stmt = $dbconn->prepare($oldFotoQuery);
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $oldFoto = $result->fetch_assoc()['foto'];

            if ($oldFoto && file_exists("../kepek/{$oldFoto}") && $oldFoto !== 'nincskep.png') {
                unlink("../kepek/{$oldFoto}");
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Nem megfelelő képformátum!']);
            exit();
        }
    } else {
        // Ha nincs új fotó, akkor megtartjuk a régit
        $fotoFileName = $_POST['regi_foto'] ?? 'nincskep.png';
    }

    // Névjegy frissítése
    $sql = "UPDATE nevjegyek SET nev = ?, cegnev = ?, mobil = ?, email = ?, foto = ? WHERE id = ?";
    $stmt = $dbconn->prepare($sql);
    $stmt->bind_param('sssssi', $nev, $cegnev, $mobil, $email, $fotoFileName, $id);

    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(['message' => 'Névjegy sikeresen frissítve!']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Hiba történt a frissítés során!']);
    }
}

