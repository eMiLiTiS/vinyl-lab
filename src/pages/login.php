<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../database/conexion.php';
iniciarSesionSegura();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    redirect_to_login();
}

function verificarRateLimiting() {
    $tiempo_actual = time();
    $limite_tiempo = 300;
    $max_intentos = 5;
    
    if (!isset($_SESSION['login_intentos'])) {
        $_SESSION['login_intentos'] = [];
    }
    
    $_SESSION['login_intentos'] = array_filter(
        $_SESSION['login_intentos'],
        function($timestamp) use ($tiempo_actual, $limite_tiempo) {
            return ($tiempo_actual - $timestamp) < $limite_tiempo;
        }
    );
    
    if (count($_SESSION['login_intentos']) >= $max_intentos) {
        $tiempo_restante = $limite_tiempo - ($tiempo_actual - min($_SESSION['login_intentos']));
        return ['permitido' => false, 'tiempo_restante' => ceil($tiempo_restante / 60)];
    }
    
    $_SESSION['login_intentos'][] = $tiempo_actual;
    return ['permitido' => true];
}

$rate_limit = verificarRateLimiting();

if (!$rate_limit['permitido']) {
    echo "<script>
        alert('Demasiados intentos. Espera " . $rate_limit['tiempo_restante'] . " minutos.');
        window.location.href = '" . URL_LOGIN . "';
    </script>";
    exit;
}

$nombre = limpiarInput($_POST['nombre'] ?? '');
$pass = $_POST['pass'] ?? '';

if (empty($nombre) || empty($pass)) {
    echo "<script>alert('Completa todos los campos.'); window.location.href = '" . URL_LOGIN . "';</script>";
    exit;
}

$sql = "SELECT id, nombre, pass, email FROM usuarios WHERE nombre = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $nombre);
$stmt->execute();
$result = $stmt->get_result();

$login_exitoso = false;
$user = null;

if ($result && $result->num_rows === 1) {
    $user = $result->fetch_assoc();
    if (password_verify($pass, $user['pass'])) {
        $login_exitoso = true;
    }
}

usleep(random_int(100000, 300000));
$stmt->close();

if ($login_exitoso) {
    $update_sql = "UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("i", $user['id']);
    $update_stmt->execute();
    $update_stmt->close();
}

$conn->close();

if ($login_exitoso) {
    regenerarSesion();
    $_SESSION['usuario'] = $user['nombre'];
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['autenticado'] = true;
    $_SESSION['login_tiempo'] = time();
    unset($_SESSION['login_intentos']);
    redirect_to_index();
} else {
    echo "<script>alert('Usuario o contraseña incorrectos.'); window.location.href = '" . URL_LOGIN . "';</script>";
    exit;
}
?>