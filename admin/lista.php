<?php
session_start();
if (!isset($_SESSION['belepett'])) { 
	header("Location: false.html"); // azaz ide
	exit();
}
require"../kapcsolat.php";
/**
 * @var string $rendez: A rendezés címe -> Az isset() itt azt ellenőrzi, hogy a $_GET['rendez'] és $_POST['kifejezes'] változók léteznek-e, és nem null értékűek. Ha léteznek, akkor az értéküket használja, ha nem, akkor az alapértelmezett értéket ("nev" vagy "") adja vissza. Ez a klasszikus ternáris, bocs Elvis operátor helyes használata.
 */
//$rendez    = isset($_GET['rendez'])    ? $_GET['rendez']     : "nev";
//$kifejezes = isset($_POST['kifejezes']) ? $_POST['kifejezes'] : "";

/**
 * @var mixed $rendez -> A "??" operátor az ún. nullás egyesítési operátor, ami csak akkor működik jól, ha az adott változó valóban null vagy undefined. Az "isset()" függvény azonban ellenőrzi, hogy a változó létezik-e, és értékkel rendelkezik-e. Azonban az "isset()" függvényt és a "??" operátort nem kell együtt használni, mert a "??" magában is megoldja azt, amit az "isset()" tesz.
 */
$rendez    = $_GET['rendez'] ?? "nev";
$kifejezes = $_POST['kifejezes'] ?? "";

/*$sql = "SELECT *
		FROM nevjegyek
		WHERE (
			nev LIKE '%{$kifejezes}%'
			OR cegnev LIKE '%{$kifejezes}%'
			OR mobil LIKE '%{$kifejezes}%'
			OR email LIKE '%{$kifejezes}%'
		)
		ORDER BY {$rendez} ASC"; //abc sorrend szerint rendez
$eredmeny = mysqli_query($dbconn, $sql);
*/

/**
 * @var mixed $validColumns A rendezéshez érvényes oszlopok-> A fehér lista (angolul: whitelist) egy biztonsági koncepció, amelyben előre meghatározol egy olyan listát, amely csak engedélyezett elemeket tartalmaz. Csak azok az elemek, amelyek szerepelnek a listán, használhatók vagy hozzáférhetők egy adott művelethez.
 */
$validColumns = ['nev', 'cegnev', 'mobil', 'email'];
$rendez = in_array($rendez, $validColumns) ? $rendez : 'nev';

// Előkészített SQL lekérdezés
$sql = "SELECT * 
        FROM nevjegyek 
        WHERE (nev LIKE ? 
        OR cegnev LIKE ? 
        OR mobil LIKE ? 
        OR email LIKE ?)
        ORDER BY {$rendez} ASC";

/**
 * @var array $stmt -> Az előkészített utasítás (statement) az SQL lekérdezéshez tartozó objektum. Az SQL lekérdezés param
*/
//$stmt = mysqli_prepare($dbconn, $sql);
$stmt = $dbconn->prepare($sql);
/*tt a $dbconn egy adatbázis kapcsolatot jelölő objektum, és az oop megközelítésben a prepare() metódust hívjuk meg ezen az objektumon, hogy létrehozzuk az előkészített utasítást (statement). */
// Ha az előkészítés sikeres
if ($stmt) {
	/**
	 * @var mixed $searchTerm -> A kifejezés, ami az oszlopokra kerül szűrés. Az SQL lekérdezésben az %{$kifejezes
	 * % (százalékjel): Az SQL LIKE operátorában a % egy helyettesítő karakter (wildcard), amely bármennyi karaktert (nullától kezdve) helyettesíthet. Így például a "%valami%" minta minden olyan találatot visszaad, amely tartalmazza a "valami" karakterláncot, függetlenül attól, mi van előtte vagy utána.
	 */
    $searchTerm = "%{$kifejezes}%"; 

    /**
	 * @var mysqli_stmt_bind_param -> A mysqli_stmt_bind_param
	 * Paraméterek bekötése (bind_param) - 4 darab 's' típusú paraméter
	 * az előre elkészített (prepared) SQL utasítás paramétereinek bekötésére szolgál, amelyet a MySQLi-ben a mysqli_stmt_bind_param() függvénnyel végzel. Ez a függvény biztosítja, hogy a felhasználói bemeneteket biztonságosan adjuk át az SQL lekérdezésbe, megakadályozva az SQL injekció támadásokat. 
	 * 'ssss': Ez az ún. típusjelző string. Minden karakter azt jelzi, hogy az adott helyen milyen típusú adatot adsz át a SQL lekérdezéshez:
	 * s: string (karakterlánc)
	 * i: integer (egész szám)
	 * d: double (lebegőpontos szám)
	 * b: blob (bináris adat)
	 * Ebben az esetben négy s van megadva, ami azt jelenti, hogy négy string (karakterlánc) paramétert kötsz az SQL lekérdezés megfelelő helyére.
	 * $searchTerm, $searchTerm, $searchTerm, $searchTerm: Ezek a paraméterek, amelyeket az SQL lekérdezésben a LIKE operátorok helyére kötsz. Minden helyettesítő karaktert (?) a lekérdezésben egy-egy értékkel helyettesítesz, ebben az esetben a $searchTerm változóval, amely a keresési kifejezést tartalmazza (pl. "%János%").
	 */
	//mysqli_stmt_bind_param($stmt, 'ssss', $searchTerm, $searchTerm, $searchTerm, $searchTerm);

	
/**
 * $stmt->bind_param -> A mysqli_stmt_bind_param 
 * az OOP megközelítésben a bind_param() metódust közvetlenül az objektumon hívjuk meg. Ez ugyanazt a célt szolgálja, mint a procedurális változatban a mysqli_stmt_bind_param(), azaz összeköti a lekérdezésben található helyettesítő karaktereket (?) a paraméterekkel. 
 */
$stmt->bind_param('ssss', $searchTerm, $searchTerm, $searchTerm, $searchTerm);


    // Utasítás végrehajtása
    //mysqli_stmt_execute($stmt);
	/*procedurális stílusban az előkészített utasítás (prepared statement) végrehajtását jelenti. Ezzel futtatod az SQL lekérdezést, amelyet korábban a mysqli_stmt_bind_param() segítségével paraméterezett. */
	$stmt->execute();
	/*Objektumorientált stílusban a execute() metódust közvetlenül a statement objektumon hívod meg. */

    // Eredmények lekérdezése
    //$eredmeny = mysqli_stmt_get_result($stmt);
	/*az előkészített utasítás végrehajtása után az eredményeket lekérdezzük, és a $stmt objektumból visszakapjuk őket. A lekérdezés eredményét egy olyan objektum formájában kapjuk meg, amelyet később feldolgozhatunk, például a mysqli_fetch_assoc() függvénnyel.*/
	$eredmeny = $stmt->get_result();/*Objektumorientált stílusban az eredmények lekérdezése ugyanígy működik, de közvetlenül a statement objektumon hívod meg a get_result() metódust. */

    // Eredmények feldolgozása
    if ($eredmeny) {
		$kimenet = "<table>
		<tr>
			<th>Fotó</th>
			<th><a href=\"?rendez=nev\">Név</a></th>
			<th><a href=\"?rendez=cegnev\">Cégnév</a></th>
			<th><a href=\"?rendez=mobil\">Mobil</a></th>
			<th><a href=\"?rendez=email\">E-mail</a></th>
			<th>Művelet</th>
		</tr>";
		/*while ($sor = mysqli_fetch_assoc($eredmeny)) {
			$kimenet.= "<tr>
				<td><img src=\"../kepek/{$sor['foto']}\" alt=\"{$sor['nev']}\"></td>
				<td>{$sor['nev']}</td>
				<td>{$sor['cegnev']}</td>
				<td>{$sor['mobil']}</td>
				<td>{$sor['email']}</td>
				<td><a href=\"torles.php?id={$sor['id']}\">Törlés</a> | <a href=\"modositas.php?id={$sor['id']}\">Módosítás</a></td>
			</tr>";
		}*/
		while ($sor = $eredmeny->fetch_assoc()) {
			// HTML special characters escape-elése
			$foto = htmlspecialchars($sor['foto'], ENT_QUOTES, 'UTF-8');/*A felhasználói bemenetek megjelenítésekor megakadályozza, hogy rosszindulatú kódok (például <script>) fussanak le a böngészőben. Ezzel védi az alkalmazást az XSS támadásoktól.*/
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
		$kimenet.= "</table>";
    }

    // Utasítás bezárása
   // mysqli_stmt_close($stmt);
	/*procedurális stílusban arra szolgál, hogy bezárd az előkészített utasítást (statement). Ez akkor hasznos, ha már végrehajtottad az SQL lekérdezést, feldolgoztad az eredményeket, és nincs további szükséged az előkészített utasításra. A statement bezárása felszabadítja az erőforrásokat, amelyeket a statement lekötött. */
	$stmt->close();
	/*(OOP) stílusban a statement bezárását a close() metódussal végzed, amit közvetlenül az $stmt objektumon hívsz meg */
} else {
    // Hibakezelés
    echo "Hiba az előkészített utasítás létrehozása során: " . mysqli_error($dbconn);
}


?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Névjegykártyák</title>
<link href="../stilus.css" rel="stylesheet">
</head>

<body>
<h1>Névjegykártyák</h1>


<form method="post" action="">
	<input type="search" id="kifejezes" name="kifejezes">
</form>


<p><a href="felvitel.php">Új névjegy</a> | <a href="kilepes.php">Kilépés</a></p>
<?php print $kimenet; ?>
<p><a href="felvitel.php">Új névjegy</a> | <a href="kilepes.php">Kilépés</a></p>
</body>
</html>
