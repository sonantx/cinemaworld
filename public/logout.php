<?php
require_once __DIR__ . '/../includes/sesion.php';
session_destroy();
header("Location: index.html");
exit;
