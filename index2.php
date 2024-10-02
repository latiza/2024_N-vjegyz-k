<?php
require("db_functions.php");
require("pagination.php");
header("Expires: 0");

// Beállítások
$totalRecords = getTotalRecords($dbconn);
$itemsPerPage = 9;
$totalPages = ceil($totalRecords / $itemsPerPage);
$currentPage = (isset($_GET['oldal'])) ? (int)$_GET['oldal'] : 1;
$offset = ($currentPage - 1) * $itemsPerPage;

// Keresési kifejezés
$searchTerm = (isset($_POST['kifejezes'])) ? $_POST['kifejezes'] : "";

// Adatok lekérdezése
$contacts = fetchContacts($dbconn, $searchTerm, $offset, $itemsPerPage);

// Lapozó generálása
$pagination = generatePagination($currentPage, $totalPages);

// HTML kimenet előkészítése
$output = "";
if (empty($contacts)) {
    $output = "<article><h2>Nincs találat a rendszerben!</h2></article>\n";
} else {
    foreach ($contacts as $contact) {
        $output .= "<article>
            <img src=\"kepek/{$contact['foto']}\" alt=\"{$contact['nev']}\">
            <h2>{$contact['nev']}</h2>
            <h3>{$contact['cegnev']}</h3>
            <p>Mobil: <a href=\"tel:{$contact['mobil']}\">{$contact['mobil']}</a></p>
            <p>E-mail: <a href=\"mailto:{$contact['email']}\">{$contact['email']}</a></p>
        </article>\n";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Névjegykártyák</title>
<link href="stilus.css" rel="stylesheet">
</head>
<body>
<h1>Névjegykártyák</h1>
<form method="post" action="">
    <input type="search" id="kifejezes" name="kifejezes" value="<?php echo htmlspecialchars($searchTerm); ?>">
    <input type="submit" value="Keresés">
</form>
<?php echo $pagination; ?>
<div class="container">
    <?php echo $output; ?>
</div>
</body>
</html>
