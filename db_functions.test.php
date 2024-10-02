<?php
require('db_functions.php');
// Tesztfüggvények a kimenet megjelenítésére
function testFunctions($dbconn) {
    // Lekérdezi az összes névjegyet
    $contacts = fetchContacts($dbconn, "", 0, 5);
    echo "<h1>Contacts:</h1>";
    echo "<pre>";
    print_r($contacts);
    echo "</pre>";

    // Lekérdezi az összes rekord számát
    $totalRecords = getTotalRecords($dbconn);
    echo "<h1>Total Records:</h1>";
    echo "<pre>";
    echo $totalRecords;
    echo "</pre>";
}

// Hívja meg a tesztfüggvényt
testFunctions($dbconn);