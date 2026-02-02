<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../database/conexion.php';
iniciarSesionSegura();
requiereAutenticacion();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestionar Catálogo - <?php echo APP_NAME; ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;600;700&family=Bebas+Neue&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?php echo style_url('styles.css'); ?>">
</head>

<body style="background-image: url('https://www.toptal.com/designers/subtlepatterns/uploads/wood_pattern.png'); background-attachment: fixed;">

<header class="main-header">
  <div class="container d-flex align-items-center justify-content-between">
    <div class="header-left d-flex align-items-center">
      <img src="<?php echo image_url('VinylLab.png'); ?>" class="header-logo me-2">
      <h1 class="header-title"><?php echo APP_NAME; ?></h1>
    </div>

    <div class="d-flex align-items-center gap-2">
      <a href="<?php echo URL_ADD_VINILO; ?>" class="btn-login-custom">Añadir vinilo</a>
      <a href="<?php echo URL_INDEX; ?>" class="btn-login-custom">Inicio</a>
      <a href="<?php echo URL_LOGOUT; ?>" class="btn-login-custom">Cerrar sesión</a>

      <button class="btn btn-hamburguesa" type="button" data-bs-toggle="offcanvas" data-bs-target="#menuLateral" id="btnHamburguesa">
        <span class="navbar-toggler-icon"></span>
      </button>
    </div>
  </div>
</header>

<div class="offcanvas offcanvas-start sidebar" tabindex="-1" id="menuLateral">
  <div class="offcanvas-header">
    <img src="<?php echo image_url('VinylLab.png'); ?>" class="sidebar-logo">
  </div>
</div>

<main class="container py-5" style="margin-top:130px;">
  <div class="card shadow-lg mx-auto p-4" style="max-width:1200px; background-color:rgba(255,243,230,0.97); border-radius:16px;">

    <h2 class="text-center mb-4" style="font-family:'Bebas Neue'; color:#5a2c0d;">
      Gestión del catálogo
    </h2>

    <input type="text" id="buscar" class="form-control form-control-lg mb-4" placeholder="Buscar vinilo por nombre o artista..." autocomplete="off">

    <div class="table-responsive">
      <table class="table align-middle text-center">
        <thead style="background-color:#3d2714; color:white;">
          <tr>
            <th>Imagen</th>
            <th>Nombre</th>
            <th>Precio</th>
            <th>Visible</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody id="resultado"></tbody>
      </table>
    </div>

  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
const input = document.getElementById('buscar');
const resultado = document.getElementById('resultado');

function buscarVinilos(valor = '') {
  fetch('<?php echo admin_url("buscar_vinilos.php"); ?>?buscar=' + encodeURIComponent(valor))
    .then(res => res.text())
    .then(data => { resultado.innerHTML = data; })
    .catch(err => {
      console.error('Error:', err);
      resultado.innerHTML = '<tr><td colspan="5" class="text-danger">Error al cargar los vinilos</td></tr>';
    });
}

buscarVinilos();
input.addEventListener('keyup', () => { buscarVinilos(input.value); });
</script>

</body>
</html>