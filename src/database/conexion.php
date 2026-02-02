<?php
/**
 * VINYL LAB - Conexión a Base de Datos
 * Versión: 2.1 - Nueva estructura
 * Ubicación: src/database/conexion.php
 */

// Cargar configuración central
require_once __DIR__ . '/../config/config.php';

// Deshabilitar reportes de errores MySQL en producción
mysqli_report(MYSQLI_REPORT_OFF);

// ============================================
// CONEXIÓN A BASE DE DATOS
// ============================================

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    
    if ($conn->connect_error) {
        throw new Exception("Error de conexión: " . $conn->connect_error);
    }
    
    if (!$conn->set_charset("utf8mb4")) {
        throw new Exception("Error al establecer charset UTF-8");
    }
    
} catch (Exception $e) {
    if (ES_PRODUCCION) {
        die("Error de conexión a la base de datos. Contacte al administrador.");
    } else {
        die("ERROR DE DESARROLLO: " . $e->getMessage());
    }
}

// ============================================
// FUNCIONES DE UTILIDAD
// ============================================

function ejecutarConsulta($conn, $sql, $types = "", $params = []) {
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        error_log("Error preparando consulta: " . $conn->error);
        return false;
    }
    
    if (!empty($types) && !empty($params)) {
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

function limpiarHTML($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function limpiarInput($data) {
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

function iniciarSesionSegura() {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', ES_HTTPS);
    ini_set('session.cookie_lifetime', SESSION_LIFETIME);
    
    session_name(SESSION_NAME);
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function estaAutenticado() {
    return isset($_SESSION['usuario']) && 
           isset($_SESSION['user_id']) && 
           isset($_SESSION['autenticado']) && 
           $_SESSION['autenticado'] === true;
}

function requiereAutenticacion() {
    if (!estaAutenticado()) {
        redirect_to_login();
    }
}

function regenerarSesion() {
    session_regenerate_id(true);
}

function usuarioActual() {
    if (!estaAutenticado()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'] ?? null,
        'nombre' => $_SESSION['usuario'] ?? null,
        'email' => $_SESSION['email'] ?? null
    ];
}

?>