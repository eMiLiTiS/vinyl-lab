<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../database/conexion.php';
iniciarSesionSegura();
requiereAutenticacion();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die("Método no permitido");
}

$nombre = limpiarInput($_POST['nombre'] ?? '');
$artista = limpiarInput($_POST['artista'] ?? '');
$descripcion = limpiarInput($_POST['descripcion'] ?? '');
$precio = floatval($_POST['precio'] ?? 0);
$anio = intval($_POST['anio'] ?? 0);

$errores = [];

if (empty($nombre) || strlen($nombre) < 2) {
    $errores[] = "El nombre del vinilo debe tener al menos 2 caracteres.";
}

if (strlen($nombre) > 200) {
    $errores[] = "El nombre del vinilo no puede exceder 200 caracteres.";
}

if (strlen($artista) > 150) {
    $errores[] = "El nombre del artista no puede exceder 150 caracteres.";
}

if (empty($descripcion) || strlen($descripcion) < 10) {
    $errores[] = "La descripción debe tener al menos 10 caracteres.";
}

if ($precio <= 0 || $precio > 999999.99) {
    $errores[] = "El precio debe estar entre 0.01 y 999,999.99 €";
}

if ($anio < 1900 || $anio > date('Y') + 1) {
    $errores[] = "El año debe estar entre 1900 y " . (date('Y') + 1);
}

if (!empty($errores)) {
    echo "<script>
        alert('Errores de validación:\\n" . implode("\\n", $errores) . "');
        window.history.back();
    </script>";
    exit;
}

if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
    die("<script>
        alert('Error: No se ha subido ninguna imagen o ha ocurrido un error.');
        window.history.back();
    </script>");
}

$archivo = $_FILES['imagen'];

if ($archivo['size'] > MAX_FILE_SIZE) {
    die("<script>
        alert('Error: La imagen no puede superar " . (MAX_FILE_SIZE / 1024 / 1024) . " MB.');
        window.history.back();
    </script>");
}

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $archivo['tmp_name']);
finfo_close($finfo);

if (!in_array($mimeType, ALLOWED_MIME_TYPES)) {
    die("<script>
        alert('Error: Solo se permiten imágenes (JPG, PNG, GIF, WEBP).');
        window.history.back();
    </script>");
}

$extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

if (!in_array($extension, ALLOWED_IMAGE_TYPES)) {
    die("<script>
        alert('Error: Extensión de archivo no permitida.');
        window.history.back();
    </script>");
}

if (!is_dir(DIR_UPLOADS)) {
    if (!mkdir(DIR_UPLOADS, 0755, true)) {
        die("<script>
            alert('Error: No se pudo crear el directorio de uploads.');
            window.history.back();
        </script>");
    }
}

$nombreSeguro = uniqid('vinilo_', true) . '.' . $extension;
$rutaCompleta = DIR_UPLOADS . '/' . $nombreSeguro;
$rutaDB = 'uploads/' . $nombreSeguro;

if (!move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
    die("<script>
        alert('Error: No se pudo guardar la imagen en el servidor.');
        window.history.back();
    </script>");
}

$sql = "INSERT INTO vinilos (nombre, artista, descripcion, precio, anio, imagen, visible) 
        VALUES (?, ?, ?, ?, ?, ?, 1)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    unlink($rutaCompleta);
    error_log("Error preparando statement: " . $conn->error);
    die("<script>
        alert('Error del sistema. Por favor, inténtalo de nuevo.');
        window.history.back();
    </script>");
}

$stmt->bind_param("ssssis", $nombre, $artista, $descripcion, $precio, $anio, $rutaDB);

if (!$stmt->execute()) {
    unlink($rutaCompleta);
    error_log("Error ejecutando insert de vinilo: " . $stmt->error);
    $stmt->close();
    $conn->close();
    die("<script>
        alert('Error al guardar en la base de datos.');
        window.history.back();
    </script>");
}

$stmt->close();
$conn->close();

echo "<script>
    alert('¡Vinilo agregado exitosamente!');
    window.location.href = '" . URL_GESTIONAR . "';
</script>";
exit;
?>