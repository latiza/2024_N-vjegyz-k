<?php
//require "kapcsolat.php";
/* Kiválasztott mennyiség */
$mennyit = isset($_GET['mennyit']) ? (int)$_GET['mennyit'] : 9;

/* Teljes rekordok száma */
$sql = "SELECT * FROM nevjegyek";
$eredmeny = mysqli_query($dbconn, $sql);
$osszes = mysqli_num_rows($eredmeny);

/* Lapozási beállítások */
$lapok = ceil($osszes / $mennyit); // ceil: felfelé kerekít
$aktualis = (isset($_GET['oldal'])) ? (int)$_GET['oldal'] : 1;
$honnan = ($aktualis - 1) * $mennyit;

/* Adatok lekérdezése a kiválasztott mennyiség alapján */
$sql = "SELECT * FROM nevjegyek LIMIT $honnan, $mennyit";
$eredmeny = mysqli_query($dbconn, $sql);

/* Lapozó generálása */
$lapozo = "<p>";
$lapozo .= ($aktualis != 1) ? "<a href=\"?oldal=1&mennyit=$mennyit\">Első</a> | " : "Első | ";

$lapozo .= ($aktualis > 1 && $aktualis <= $lapok) ? "<a href=\"?oldal=".($aktualis-1)."&mennyit=$mennyit\">Előző</a> | " : "Előző | ";

for ($oldal = 1; $oldal <= $lapok; $oldal++) {
    $lapozo .= ($aktualis != $oldal) ? "<a href=\"?oldal={$oldal}&mennyit=$mennyit\">{$oldal}</a> | " : $oldal." | ";
}

$lapozo .= ($aktualis > 0 && $aktualis < $lapok) ? "<a href=\"?oldal=".($aktualis+1)."&mennyit=$mennyit\">Következő</a> | " : "Következő | ";
$lapozo .= ($aktualis != $lapok) ? "<a href=\"?oldal={$lapok}&mennyit=$mennyit\">Utolsó</a>" : "Utolsó";
$lapozo .= "</p>";

//print $lapozo;
