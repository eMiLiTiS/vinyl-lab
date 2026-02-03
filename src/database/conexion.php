<?php

/**
 * VINYL LAB - Conexión a Base de Datos
 * Ubicación: src/database/conexion.php
 */

require_once __DIR__ . '/../config/config.php';

// 1) Verificación del runtime
if (!extension_loaded('mysqli') || !function_exists('mysqli_report')) {
    // Mensaje claro para diagnosticar despliegues (Docker/Railway)
    die("Runtime sin mysqli: este deploy no tiene la extensión mysqli habilitada.");
}

// 2) Configurar reporting según entorno
// - En DESARROLLO: excepciones (más fácil de depurar)
// - En PRODUCCIÓN: evitar que se "rompa" mostrando detalles
if (defined('ES_PRODUCCION') && ES_PRODUCCION) {
    mysqli_report(MYSQLI_REPORT_OFF);
} else {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
}

// ============================================
// CONEXIÓN A BASE DE DATOS
// ============================================

try {
    // Nota: DB_PORT debe ser int o null. Si viene string, lo casteamos.
    $port = defined('DB_PORT') ? (int) DB_PORT : 3306;

    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, $port);

    // Si estás en modo OFF, connect_error sigue siendo útil:
    if ($conn->connect_error) {
        throw new Exception("Error de conexión: " . $conn->connect_error);
    }

    if (!$conn->set_charset("utf8mb4")) {
        throw new Exception("Error al establecer charset UTF-8");
    }
} catch (Throwable $e) {
    // Log siempre (no rompe UX, sí te deja rastro en Railway logs)
    error_log("[DB] " . $e->getMessage());

    if (defined('ES_PRODUCCION') && ES_PRODUCCION) {
        die("Error de conexión a la base de datos. Contacte al administrador.");
    }

    die("ERROR DE DESARROLLO: " . $e->getMessage());
}

// ============================================
// FUNCIONES DE UTILIDAD
// ============================================

function ejecutarConsulta($conn, $sql, $types = "", $params = [])
{
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        error_log("Error preparando consulta: " . $conn->error);
        return false;
    }

    if ($types !== "" && !empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    if (!$stmt->execute()) {
        error_log("Error ejecutando consulta: " . $stmt->error);
        $stmt->close();
        return false;
    }

    $result = $stmt->get_result();
    $stmt->close();

    return $result;
}

function limpiarHTML($string)
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function limpiarInput($data)
{
    if (is_array($data)) {
        return array_map('limpiarInput', $data);
    }

    $data = trim($data);
    $data = stripslashes($data);
    return $data;
}

// ============================================
// GESTIÓN DE SESIONES
// ============================================

function iniciarSesionSegura()
{
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);

    if (defined('ES_HTTPS')) {
        ini_set('session.cookie_secure', ES_HTTPS);
    }

    if (defined('SESSION_LIFETIME')) {
        ini_set('session.cookie_lifetime', SESSION_LIFETIME);
    }

    if (defined('SESSION_NAME')) {
        session_name(SESSION_NAME);
    }

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function estaAutenticado()
{
    return isset($_SESSION['usuario'], $_SESSION['user_id'], $_SESSION['autenticado'])
        && $_SESSION['autenticado'] === true;
}

function requiereAutenticacion()
{
    if (!estaAutenticado()) {
        // OJO: redirect_to_login() debe existir en tu config/helpers.
        redirect_to_login();
    }
}

function regenerarSesion()
{
    session_regenerate_id(true);
}

function usuarioActual()
{
    if (!estaAutenticado()) {
        return null;
    }

    return [
        'id' => $_SESSION['user_id'] ?? null,
        'nombre' => $_SESSION['usuario'] ?? null,
        'email' => $_SESSION['email'] ?? null
    ];
}
