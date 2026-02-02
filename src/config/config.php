<?php
/**
 * VINYL LAB - Configuración Central (Nueva Estructura)
 * Versión: 2.1
 * 
 * Configuración adaptada para la estructura reorganizada:
 * - public/ (assets, uploads)
 * - src/ (config, database, pages, admin)
 */

// ============================================
// DETECCIÓN DE ENTORNO
// ============================================

define('ES_PRODUCCION', getenv("MYSQLHOST") !== false);

define('ES_HTTPS', 
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
    $_SERVER['SERVER_PORT'] == 443 ||
    (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
);

// ============================================
// CONFIGURACIÓN DE RUTAS
// ============================================

define('PROTOCOLO', ES_HTTPS ? 'https://' : 'http://');
define('HOST', $_SERVER['HTTP_HOST']);

if (ES_PRODUCCION) {
    define('ROOT_PATH', '/');
} else {
    // ⚠️ CAMBIAR ESTO si tu carpeta tiene otro nombre
    define('ROOT_PATH', '/vinyl-lab/');
}

define('BASE_URL', PROTOCOLO . HOST . ROOT_PATH);

// ============================================
// RUTAS DE DIRECTORIOS (Nueva estructura)
// ============================================

// Detectar si estamos en src/config/ o en src/database/
$currentDir = __DIR__;

// Subir hasta la raíz del proyecto
if (strpos($currentDir, 'src/config') !== false || strpos($currentDir, 'src\\config') !== false) {
    define('DIR_ROOT', dirname(dirname(__DIR__)));
} elseif (strpos($currentDir, 'src/database') !== false || strpos($currentDir, 'src\\database') !== false) {
    define('DIR_ROOT', dirname(dirname(__DIR__)));
} else {
    define('DIR_ROOT', dirname(__DIR__));
}

define('DIR_SRC', DIR_ROOT . '/src');
define('DIR_PUBLIC', DIR_ROOT . '/public');
define('DIR_ASSETS', DIR_PUBLIC . '/assets');
define('DIR_UPLOADS', DIR_PUBLIC . '/uploads');
define('DIR_IMAGES', DIR_ASSETS . '/images');
define('DIR_STYLES', DIR_ASSETS . '/styles');

// ============================================
// RUTAS URL (Nueva estructura)
// ============================================

define('URL_ROOT', BASE_URL);
define('URL_PUBLIC', BASE_URL . 'public/');
define('URL_ASSETS', URL_PUBLIC . 'assets/');
define('URL_UPLOADS', URL_PUBLIC . 'uploads/');
define('URL_IMAGES', URL_ASSETS . 'images/');
define('URL_STYLES', URL_ASSETS . 'styles/');

// URLs de páginas
define('URL_PAGES', BASE_URL . 'src/pages/');
define('URL_ADMIN', BASE_URL . 'src/admin/');

// Páginas específicas
define('URL_INDEX', URL_PAGES . 'index.php');
define('URL_LOGIN', URL_PAGES . 'login.html');
define('URL_LOGIN_PROCESS', URL_PAGES . 'login.php');
define('URL_LOGOUT', URL_PAGES . 'logout.php');
define('URL_CATALOGO', URL_PAGES . 'catalogo.php');

// Admin
define('URL_GESTIONAR', URL_ADMIN . 'gestionar_catalogo.php');
define('URL_ADD_VINILO', URL_ADMIN . 'add_vinilos.php');
define('URL_GUARDAR_VINILO', URL_ADMIN . 'guardar_vinilo.php');
define('URL_ELIMINAR_VINILO', URL_ADMIN . 'eliminar_vinilo.php');
define('URL_TOGGLE_VINILO', URL_ADMIN . 'toggle_vinilo.php');
define('URL_BUSCAR_VINILO', URL_ADMIN . 'buscar_vinilos.php');

// ============================================
// CONFIGURACIÓN DE BASE DE DATOS
// ============================================

if (ES_PRODUCCION) {
    define('DB_HOST', getenv("MYSQLHOST"));
    define('DB_USER', getenv("MYSQLUSER"));
    define('DB_PASS', getenv("MYSQLPASSWORD"));
    define('DB_NAME', getenv("MYSQLDATABASE"));
    define('DB_PORT', (int) getenv("MYSQLPORT") ?: 3306);
} else {
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'vinyl_lab');
    define('DB_PORT', 3306);
}

// ============================================
// CONFIGURACIÓN DE LA APLICACIÓN
// ============================================

define('APP_NAME', 'Vinyl Lab');
define('APP_TAGLINE', 'El sonido del pasado, con la calidez del presente');

define('SESSION_NAME', 'vinyl_lab_session');
define('SESSION_LIFETIME', 7200);

define('MAX_FILE_SIZE', 5 * 1024 * 1024);
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
define('ALLOWED_MIME_TYPES', [
    'image/jpeg',
    'image/jpg', 
    'image/png',
    'image/gif',
    'image/webp'
]);

// ============================================
// MODO DEBUG
// ============================================

if (!ES_PRODUCCION) {
    define('DEBUG_MODE', true);
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    define('DEBUG_MODE', false);
    error_reporting(0);
    ini_set('display_errors', 0);
}

// ============================================
// FUNCIONES DE UTILIDAD
// ============================================

function url($path = '') {
    $path = ltrim($path, '/');
    return BASE_URL . $path;
}

function asset_url($path = '') {
    $path = ltrim($path, '/');
    return URL_ASSETS . $path;
}

function image_url($filename) {
    $filename = ltrim($filename, '/');
    return URL_IMAGES . $filename;
}

function style_url($filename) {
    $filename = ltrim($filename, '/');
    return URL_STYLES . $filename;
}

function upload_url($filename) {
    $filename = ltrim($filename, '/');
    
    if (strpos($filename, 'uploads/') === 0) {
        return URL_PUBLIC . $filename;
    }
    
    return URL_UPLOADS . $filename;
}

function page_url($page) {
    $page = ltrim($page, '/');
    return URL_PAGES . $page;
}

function admin_url($page) {
    $page = ltrim($page, '/');
    return URL_ADMIN . $page;
}

function redirect($url) {
    header("Location: " . $url);
    exit;
}

function redirect_to_login() {
    redirect(URL_LOGIN);
}

function redirect_to_index() {
    redirect(URL_INDEX);
}

// ============================================
// DEBUG CONFIG (solo en desarrollo)
// ============================================

if (DEBUG_MODE && isset($_GET['debug_config'])) {
    echo "<pre style='background:#2d2d2d; color:#f8f8f2; padding:20px; border-radius:10px;'>";
    echo "<h2 style='color:#50fa7b;'>🔧 VINYL LAB - Configuración del Sistema</h2>\n\n";
    
    echo "<h3 style='color:#8be9fd;'>🌍 ENTORNO:</h3>";
    echo "Modo: " . (ES_PRODUCCION ? "🚀 PRODUCCIÓN" : "💻 DESARROLLO") . "\n";
    echo "HTTPS: " . (ES_HTTPS ? "✅ Sí" : "❌ No") . "\n";
    echo "Debug: " . (DEBUG_MODE ? "✅ Activado" : "❌ Desactivado") . "\n\n";
    
    echo "<h3 style='color:#8be9fd;'>🔗 RUTAS URL:</h3>";
    echo "BASE_URL:       " . BASE_URL . "\n";
    echo "URL_PAGES:      " . URL_PAGES . "\n";
    echo "URL_ADMIN:      " . URL_ADMIN . "\n";
    echo "URL_ASSETS:     " . URL_ASSETS . "\n";
    echo "URL_UPLOADS:    " . URL_UPLOADS . "\n";
    echo "URL_IMAGES:     " . URL_IMAGES . "\n";
    echo "URL_STYLES:     " . URL_STYLES . "\n\n";
    
    echo "<h3 style='color:#8be9fd;'>📁 RUTAS DIRECTORIO:</h3>";
    echo "DIR_ROOT:       " . DIR_ROOT . "\n";
    echo "DIR_SRC:        " . DIR_SRC . "\n";
    echo "DIR_PUBLIC:     " . DIR_PUBLIC . "\n";
    echo "DIR_ASSETS:     " . DIR_ASSETS . "\n";
    echo "DIR_UPLOADS:    " . DIR_UPLOADS . "\n\n";
    
    echo "<h3 style='color:#8be9fd;'>🗄️ BASE DE DATOS:</h3>";
    echo "Host:           " . DB_HOST . "\n";
    echo "Usuario:        " . DB_USER . "\n";
    echo "Base de datos:  " . DB_NAME . "\n";
    echo "Puerto:         " . DB_PORT . "\n\n";
    
    echo "<h3 style='color:#ff5555;'>⚠️ RECORDATORIO:</h3>";
    echo "Elimina ?debug_config en producción\n";
    echo "</pre>";
    exit;
}

?>