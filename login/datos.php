<?php
require 'conexion.php';

define('ADMIN_USER', 'bedel');
define('ADMIN_PASS', '12345678');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username === ADMIN_USER && $password === ADMIN_PASS) {
        //$_SESSION['is_admin'] = true;
        echo "correcto";
    } else {
        $error = 'Credenciales inválidas.';
    }
}
?>
