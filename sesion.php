<?php
// ============================================================
// SESION.PHP — Cerrar sesion
//
// Destruye la sesion PHP y redirige al inicio.
// ============================================================

require_once("externos.php");
$sql = "UPDATE admin_sesion SET fin=NOW() WHERE id='" . ($_SESSION['sesion_id'] ?? 0) . "'";
$db->query($sql);

session_destroy();
header("location: " . WEB_ROOT);
