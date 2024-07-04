<?php
// Verificar si se ha enviado una solicitud para mostrar el registro
if (isset($_GET['action']) && $_GET['action'] === 'register') {
    include '../views/register.php';
} else {
    include '../views/login.php';
}
?>