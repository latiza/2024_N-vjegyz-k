<?php
// Indítjuk a PHP session-t, hogy elérhessük a session változókat
session_start();

// Ellenőrizzük, hogy a felhasználó be van-e jelentkezve
if (!isset($_SESSION['belepett'])) { 
    // Ha a felhasználó nincs bejelentkezve, irányítsuk át a false.html oldalra
    header("Location: false.html");
    exit();
}

// Betöltjük az adatbázis kapcsolatot biztosító fájlt
require("../kapcsolat.php");

// Validáljuk a rendezési mezőt; ha nem érvényes, alapértelmezett 'nev' értéket adunk
/*Ez a sor egy tömböt hoz létre, amely azokat az oszlopneveket tartalmazza, amelyek alapján a felhasználó rendezheti az adatokat az SQL lekérdezésben.
Ez biztosítja, hogy csak az előre meghatározott oszlopok szerint lehessen rendezni az adatokat. Megakadályozza, hogy a felhasználó más, nem várt oszlopneveket próbáljon használni, ami biztonsági kockázatot jelenthet.*/
$allowed_order_by = ['nev', 'cegnev', 'mobil', 'email'];
/*isset($_GET['rendez']): Ellenőrzi, hogy létezik-e a rendez paraméter az URL query stringben ($_GET tömbben). Ha nem létezik, akkor a feltétel hamis lesz.
@param in_array($_GET['rendez'], $allowed_order_by): Ellenőrzi, hogy a rendez paraméter értéke benne van-e az $allowed_order_by tömbben. Ez biztosítja, hogy a megadott oszlopnév érvényes legyen.
? $_GET['rendez'] : 'nev': Ha mindkét feltétel igaz (azaz a rendez paraméter létezik és az értéke az engedélyezett oszlopok között van), akkor a $rendez változó értéke a $_GET['rendez'] lesz. Ha nem, akkor a $rendez változó alapértelmezett értéke 'nev' lesz.
 */
$rendez = isset($_GET['rendez']) && in_array($_GET['rendez'], $allowed_order_by) ? $_GET['rendez'] : 'nev';

// Ellenőrizzük a keresési kifejezést, és eltávolítjuk a felesleges szóközöket
$kifejezes = isset($_POST['kifejezes']) ? trim($_POST['kifejezes']) : "";

/* Előkészítjük az SQL lekérdezést, hogy megelőzzük a SQL injection-t
A stmt rövidítés a statement szóból származik, ami SQL lekérdezést jelent.
Az elnevezés általánosan használt a PHP dokumentációban és a kódmintákban, így egyértelművé teszi, hogy a változó egy SQL lekérdezést tartalmazó objektumot reprezentál. Az elnevezés segít az olvashatóságban és a kód karbantartásában, mivel egyértelműen utal a változó tartalmára és funkciójára.
A prepare() metódus az adatbázis kapcsolat ($dbconn) objektumán keresztül előkészít egy SQL lekérdezést.
Miért fontos?: Az előkészített lekérdezés (prepared statement) megakadályozza az SQL injection támadásokat azáltal, hogy a lekérdezés és a paraméterek külön kezelődnek. Ez biztonságosabb módja az SQL lekérdezések végrehajtásának.
*/
$stmt = $dbconn->prepare("SELECT * FROM nevjegyek WHERE nev LIKE ? OR cegnev LIKE ? OR mobil LIKE ? OR email LIKE ? ORDER BY {$rendez} ASC");

// A keresési kifejezést az SQL lekérdezéshez szükséges formátumban definiáljuk
$searchTerm = "%{$kifejezes}%";

// A paramétereket bind-oljuk az előkészített lekérdezéshez
/*$stmt->bind_param("ssss", ...)
Mit csinál?: A bind_param() metódus használatával hozzárendeljük a paramétereket az előkészített SQL lekérdezéshez ($stmt), amelyet korábban előkészítettünk a prepare() metódussal.
Biztosítja, hogy a lekérdezés paraméterei a megfelelő típusúak és formátumúak legyenek, és helyesen illeszkedjenek az SQL lekérdezés helyettesítő karaktereihez.
"ssss": Ez a karakterlánc az bind_param() metódus első argumentuma, és azt jelzi, hogy az összes paraméter típusát string-ként kell kezelni.
Az SQL lekérdezés paramétereit a megfelelő típusú értékekkel kell kitölteni. Itt s jelentése string, tehát mind a négy paraméter, amelyet a bind_param()-ba adunk, karakterlánc (string) típusú.
$searchTerm, $searchTerm, $searchTerm, $searchTerm: Ezek az értékek a SQL lekérdezés helyettesítő karaktereit helyettesítik. Mivel az SQL lekérdezés négy helyettesítő karaktert (?) tartalmaz, itt négy értéket kell megadni.
Miért így?: A $searchTerm változó azonos értékét használjuk mind a négy helyettesítő karakterhez, mivel a lekérdezésben a keresési kifejezés mind a négy oszlop (nev, cegnev, mobil, email) szűrésére szolgál. Ez biztosítja, hogy a keresési kifejezés ugyanúgy legyen alkalmazva mindegyik oszlopban.
 */
$stmt->bind_param("ssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm);

// Futtatjuk a lekérdezést
/*Az execute() metódus végrehajtja az előkészített SQL lekérdezést ($stmt). Ezzel az adatok lekérdezése, beszúrása, frissítése vagy törlése megtörténik az adatbázisban, az előzőleg megadott paraméterek alapján.
Az execute() metódus a lekérdezést ténylegesen futtatja, és az előkészített lekérdezés paramétereit alkalmazza. E nélkül az előkészített lekérdezés csak egy terv marad, nem hajtódik végre semmilyen művelet az adatbázisban. */
$stmt->execute();

// Lekérjük az eredményeket
/*A get_result() metódus az SQL lekérdezés eredményét egy mysqli_result objektumban adja vissza. Ez az objektum tartalmazza az adatbázisból lekérdezett sorokat, amelyeket az SQL lekérdezés végrehajtása után kapunk.
$stmt->get_result(): Miután a lekérdezést végrehajtottuk, a get_result() metódus lekéri az eredményeket a mysqli_result objektum formájában. Ez az objektum tartalmazza az SQL lekérdezés eredményeit, amelyeket a fetch_assoc() vagy hasonló metódusokkal dolgozhatunk fel.
$eredmeny: Ez a változó most egy mysqli_result objektumot tartalmaz. Ezen keresztül tudunk iterálni az eredményeken, például egy while ciklussal, és minden egyes sort egyesével feldolgozni. */
$eredmeny = $stmt->get_result();

// Kimenet előkészítése: HTML táblázat fejléc
$kimenet = "<table>
<tr>
    <th>Fotó</th>
    <th><a href=\"?rendez=nev\">Név</a></th>
    <th><a href=\"?rendez=cegnev\">Cégnév</a></th>
    <th><a href=\"?rendez=mobil\">Mobil</a></th>
    <th><a href=\"?rendez=email\">E-mail</a></th>
    <th>Művelet</th>
</tr>";

// A lekérdezés eredményeinek feldolgozása
while ($sor = $eredmeny->fetch_assoc()) {
    // HTML special characters escape-elése
    $foto = htmlspecialchars($sor['foto'], ENT_QUOTES, 'UTF-8');
    $nev = htmlspecialchars($sor['nev'], ENT_QUOTES, 'UTF-8');
    $cegnev = htmlspecialchars($sor['cegnev'], ENT_QUOTES, 'UTF-8');
    $mobil = htmlspecialchars($sor['mobil'], ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($sor['email'], ENT_QUOTES, 'UTF-8');

    // A táblázat sorainak hozzáadása
    $kimenet .= "<tr>
        <td><img src=\"../kepek/{$foto}\" alt=\"{$nev}\"></td>
        <td>{$nev}</td>
        <td>{$cegnev}</td>
        <td>{$mobil}</td>
        <td>{$email}</td>
        <td><a href=\"torles.php?id={$sor['id']}\">Törlés</a> | <a href=\"modositas.php?id={$sor['id']}\">Módosítás</a></td>
    </tr>";
}

// A táblázat lezárása
$kimenet .= "</table>";

// Lekérdezés lezárása
/*$stmt->close(): Ez a metódus bezárja az előkészített SQL lekérdezés objektumot ($stmt). Az előkészített lekérdezés objektum bezárása után nem lehet többé használni azt a lekérdezést, és az adatbázis erőforrásait felszabadítja.
Minden előkészített lekérdezés objektum adatbázis erőforrásokat használ. A close() metódus hívása felszabadítja ezeket az erőforrásokat, ezzel csökkentve a memóriahasználatot és biztosítva, hogy az adatbázis kapcsolat ne legyen túlterhelt.
Az előkészített lekérdezés objektumok bezárása javítja a teljesítményt, mivel elkerüli az adatbázis-kapcsolat túlzott terhelését és a memória szivárgását.
A close() metódus használata elősegíti a kód tisztább és karbantarthatóbb állapotát. Az erőforrások helyes kezelése és felszabadítása fontos a hosszú távú kód karbantartásában. */
$stmt->close();
// Adatbázis kapcsolat lezárása
/*A $dbconn változó az adatbázis kapcsolatra vonatkozik, amelyet a mysqli_connect() metódussal hoztunk létre. A kapcsolat lezárása (close()) biztosítja, hogy az adatbázis kapcsolat teljesen megszűnjön, és az adatbázis szerverhez tartozó erőforrásokat is felszabadítja.
Az adatbázis kapcsolat fenntartása rendszererőforrást jelent, és ha több kapcsolatot nyitunk meg anélkül, hogy lezárnánk őket, akkor a szerver teljesítménye csökkenhet. Az adatbázis kapcsolatok kezelése és lezárása fontos a rendszer stabilitása és teljesítménye szempontjából. */
$dbconn->close();
/*
$stmt->close(): Lezárja az előkészített lekérdezés objektumot és felszabadítja az ehhez kapcsolódó erőforrásokat.
$dbconn->close(): Lezárja az adatbázis kapcsolatot és felszabadítja az adatbázis kapcsolatra vonatkozó erőforrásokat.
 */
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

<!-- Keresési űrlap -->
<form method="post" action="">
    <input type="search" id="kifejezes" name="kifejezes" value="<?php echo htmlspecialchars($kifejezes, ENT_QUOTES, 'UTF-8'); ?>">
</form>

<!-- Linkek az új névjegy hozzáadásához és a kijelentkezéshez -->
<p><a href="felvitel.php">Új névjegy</a> | <a href="kilepes.php">Kilépés</a></p>

<!-- A táblázat kimenete -->
<?php echo $kimenet; ?>

<!-- Linkek ismét az új névjegyhez és a kijelentkezéshez -->
<p><a href="felvitel.php">Új névjegy</a> | <a href="kilepes.php">Kilépés</a></p>
</body>
</html>
