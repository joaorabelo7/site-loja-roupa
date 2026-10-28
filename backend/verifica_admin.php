<?php
// Inclua este arquivo no topo de qualquer script que só o administrador pode usar.
if (session_status() === PHP_SESSION_NONE) session_start();

if (empty($_SESSION['admin'])) {
    header("Location: ../frontend/login.php");
    exit;
}