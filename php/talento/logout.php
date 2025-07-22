<?php
session_start();
session_destroy();
header("Location: visualizza.php");
exit();
