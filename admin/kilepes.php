<?php
session_start();
unset($_SESSION['belepett']);
session_destroy();
header("Location: index.php");
