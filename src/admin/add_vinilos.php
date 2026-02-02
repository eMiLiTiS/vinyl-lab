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
  <title>Añadir Vinilo - <?php echo APP_NAME; ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;600;700&family=Bebas+Neue&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?php echo style_url('styles.css'); ?>">
</head>

<body>

<header class="main-header">
  <div class="container d-flex align-items-center justify-content-between">
    <div class="header-left d-flex align-items-center">
      <img src="<?php echo image_url('VinylLab.png'); ?>" alt="Logo" class="header-logo me-2">
      <h1 class="header-title"><?php echo APP_NAME; ?></h1>
    </div>

    <div class="d-flex align-items-center gap-2">
      <a href="<?php echo URL_GESTIONAR; ?>" class="btn-login-custom">Gestionar catálogo</a>
      <a href="<?php echo URL_INDEX; ?>" class="btn-login-custom">Inicio</a>
      <a href="<?php echo URL_LOGOUT; ?>" class="btn-login-custom">Cerrar sesión</a>
      <button class="btn btn-hamburguesa" type="button" data-bs-toggle="offcanvas" data-bs-target="#menuLateral" id="btnHamburguesa">
        <span class="navbar-toggler-icon"></span>
      </button>
    </div>
  </div>
</header>

<div class="offcanvas offcanvas-start sidebar" tabindex="-1" id="menuLateral">
  <div class="offcanvas-header flex-column align-items-start w-100">
    <img src="<?php echo image_url('VinylLab.png'); ?>" class="sidebar-logo">
  </div>
  <div class="offcanvas-body">
    <nav class="nav flex-column">
      <a class="nav-link" href="<?php echo URL_INDEX; ?>">Inicio</a>
      <a class="nav-link" href="<?php echo URL_CATALOGO; ?>">Catálogo</a>
    </nav>
  </div>
</div>

<main class="container py-5" style="margin-top: 130px;">
  <div class="card shadow-lg mx-auto p-4" style="max-width: 850px; border-radius: 14px; background-color: #fff3e6;">

    <h2 class="mb-4 text-center" style="font-family: 'Bebas Neue', cursive; color: #5a2c0d;">
      Añadir nuevo vinilo
    </h2>

    <form action="<?php echo URL_GUARDAR_VINILO; ?>" method="POST" enctype="multipart/form-data">

      <div class="mb-3">
        <label class="form-label">Nombre del vinilo</label>
        <input type="text" name="nombre" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Nombre del artista</label>
        <input type="text" name="artista" class="form-control">
      </div>

      <div class="mb-3">
        <label class="form-label">Descripción</label>
        <textarea name="descripcion" class="form-control" rows="3" required></textarea>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Precio (€)</label>
          <input type="number" step="0.01" name="precio" class="form-control" required>
        </div>

        <div class="col-md-6 mb-3">
          <label class="form-label">Año</label>
          <input type="number" name="anio" class="form-control" required>
        </div>
      </div>

      <div class="mb-4">
        <label class="form-label">Imagen del vinilo</label>
        <input type="file" name="imagen" class="form-control" accept="image/*" required>
        <small class="text-muted">Tamaño máximo: <?php echo MAX_FILE_SIZE / 1024 / 1024; ?> MB. Formatos: JPG, PNG, GIF, WEBP</small>
      </div>

      <div class="text-center">
        <button type="submit" class="btn btn-catalogo px-5">
          Guardar vinilo
        </button>
      </div>

    </form>
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const offcanvasEl = document.getElementById('menuLateral');
  const btnHamb = document.getElementById('btnHamburguesa');
  offcanvasEl.addEventListener('show.bs.offcanvas', () => btnHamb.classList.add('active'));
  offcanvasEl.addEventListener('hidden.bs.offcanvas', () => btnHamb.classList.remove('active'));
</script>

</body>
</html>