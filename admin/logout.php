<?php
session_start();
session_unset(); // Munkamenet adatainak törlése
session_destroy(); // Munkamenet lezárása

header("Location: ../login.html"); // Visszairányít a bejelentkezési oldalra
exit();
