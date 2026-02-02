<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../database/conexion.php';
iniciarSesionSegura();

if (!estaAutenticado()) {
    http_response_code(401);
    exit;
}

$buscar = limpiarInput($_GET['buscar'] ?? '');

if (strlen($buscar) > 100) {
    $buscar = substr($buscar, 0, 100);
}

$patron = "%$buscar%";

$sql = "SELECT id, nombre, artista, precio, imagen, visible 
        FROM vinilos 
        WHERE nombre LIKE ? OR artista LIKE ?
        ORDER BY id DESC 
        LIMIT 100";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    error_log("Error preparando búsqueda: " . $conn->error);
    http_response_code(500);
    exit;
}

$stmt->bind_param("ss", $patron, $patron);

if (!$stmt->execute()) {
    error_log("Error ejecutando búsqueda: " . $stmt->error);
    $stmt->close();
    $conn->close();
    http_response_code(500);
    exit;
}

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo '<tr>
            <td colspan="5" class="text-center text-muted py-4">
                <i class="bi bi-search"></i><br>
                No se encontraron vinilos
            </td>
          </tr>';
} else {
    while ($v = $result->fetch_assoc()) {
        $id = (int)$v['id'];
        $nombre = limpiarHTML($v['nombre']);
        $artista = limpiarHTML($v['artista'] ?? '');
        $imagen = limpiarHTML($v['imagen']);
        $precio = number_format($v['precio'], 2, ',', '.');
        $visible = (bool)$v['visible'];
        
        $badgeClass = $visible ? 'bg-success' : 'bg-secondary';
        $badgeText = $visible ? 'Visible' : 'Oculto';
        $toggleText = $visible ? 'Ocultar' : 'Mostrar';
        
        $imagenUrl = upload_url($imagen);
        
        echo '<tr>';
        
        echo '<td>
                <img src="' . $imagenUrl . '" 
                     alt="' . $nombre . '"
                     style="width:70px; border-radius:8px; box-shadow:0 4px 10px rgba(0,0,0,.2);"
                     onerror="this.src=\'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%22100%22%3E%3Crect fill=%22%23ddd%22 width=%22100%22 height=%22100%22/%3E%3Ctext fill=%22%23999%22 x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22%3ESin imagen%3C/text%3E%3C/svg%3E\'">
              </td>';
        
        echo '<td style="font-weight:600;">';
        echo $nombre;
        if (!empty($artista)) {
            echo '<br><small class="text-muted">' . $artista . '</small>';
        }
        echo '</td>';
        
        echo '<td>' . $precio . ' €</td>';
        
        echo '<td>
                <span class="badge ' . $badgeClass . '">' . $badgeText . '</span>
              </td>';
        
        echo '<td class="d-flex gap-2 justify-content-center">
                <a href="' . admin_url('toggle_vinilo.php?id=' . $id) . '"
                   class="btn btn-sm"
                   style="background-color:#c48a3a; color:white;">
                    ' . $toggleText . '
                </a>
                
                <a href="' . admin_url('eliminar_vinilo.php?id=' . $id) . '"
                   class="btn btn-danger btn-sm"
                   onclick="return confirm(\'¿Estás seguro de eliminar: ' . $nombre . '?\')">
                    Eliminar
                </a>
              </td>';
        
        echo '</tr>';
    }
}

$stmt->close();
$conn->close();
?>