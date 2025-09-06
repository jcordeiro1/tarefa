<?php
session_start();
if (isset($_SESSION['usuario_id'])) {
    header('Location: painel/painel.php');
} else {
    header('Location: painel/login.php');
}
exit();
?>