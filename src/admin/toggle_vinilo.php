<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../database/conexion.php';
iniciarSesionSegura();
requiereAutenticacion();

$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    echo "<script>
        alert('Error: ID de vinilo no válido.');
        window.location.href = '" . URL_GESTIONAR . "';
    </script>";
    exit;
}

$sql = "UPDATE vinilos SET visible = NOT visible WHERE id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    error_log("Error preparando update de visibilidad: " . $conn->error);
    echo "<script>
        alert('Error del sistema.');
        window.location.href = '" . URL_GESTIONAR . "';
    </script>";
    exit;
}

$stmt->bind_param("i", $id);

if (!$stmt->execute()) {
    error_log("Error ejecutando update de visibilidad: " . $stmt->error);
    $stmt->close();
    $conn->close();
    echo "<script>
        alert('Error al cambiar la visibilidad.');
        window.location.href = '" . URL_GESTIONAR . "';
    </script>";
    exit;
}

if ($stmt->affected_rows === 0) {
    error_log("No se encontró el vinilo con ID: " . $id);
}

$stmt->close();
$conn->close();

redirect(URL_GESTIONAR);
?>