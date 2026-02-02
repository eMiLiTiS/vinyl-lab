<?php
/**
 * VINYL LAB - Catálogo Público
 * Versión: 2.1 - Nueva estructura
 * Ubicación: src/pages/catalogo.php
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../database/conexion.php';
iniciarSesionSegura();

$sql = "SELECT * FROM vinilos WHERE visible = 1 ORDER BY id DESC";
$vinilos = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Catálogo — <?php echo APP_NAME; ?></title>

  <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;600;700&family=Bebas+Neue&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?php echo style_url('styles.css'); ?>" />
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512'><text y='0.9em' font-size='400'>💿</text></svg>">
</head>

<body>

  <header class="main-header">
    <div class="container d-flex align-items-center justify-content-between">
      <div class="header-left d-flex align-items-center">
        <img src="<?php echo image_url('VinylLab.png'); ?>" alt="Logo Vinyl Lab" class="header-logo me-2">
        <h1 class="header-title"><?php echo APP_NAME; ?></h1>
      </div>

      <div class="d-flex align-items-center gap-2">
        <?php if (estaAutenticado()): ?>
          <a href="<?php echo URL_GESTIONAR; ?>" class="btn-login-custom">Gestionar catálogo</a>
          <a href="<?php echo URL_LOGOUT; ?>" class="btn-login-custom">Cerrar sesión</a>
        <?php else: ?>
          <a href="<?php echo URL_LOGIN; ?>" class="btn-login-custom">Iniciar sesión</a>
        <?php endif; ?>

        <a href="<?php echo URL_INDEX; ?>" class="btn-login-custom">Inicio</a>

        <button class="btn btn-hamburguesa" type="button" data-bs-toggle="offcanvas" data-bs-target="#menuLateral" id="btnHamburguesa">
          <span class="navbar-toggler-icon"></span>
        </button>
      </div>
    </div>
  </header>

  <div class="offcanvas offcanvas-start sidebar" tabindex="-1" id="menuLateral">
    <div class="offcanvas-header flex-column align-items-start w-100">
      <div class="logo-container">
        <img src="<?php echo image_url('VinylLab.png'); ?>" alt="Logo Vinyl Lab" class="sidebar-logo">
      </div>
    </div>
    <div class="offcanvas-body">
      <nav class="nav flex-column">
        <a class="nav-link" href="<?php echo URL_INDEX; ?>">Inicio</a>
        <a class="nav-link" href="<?php echo URL_CATALOGO; ?>">Catálogo</a>
        <a class="nav-link" href="#">Ofertas</a>
        <a class="nav-link" href="#">Contacto</a>
        <?php if (estaAutenticado()): ?>
          <a class="nav-link" href="<?php echo URL_GESTIONAR; ?>">Gestionar catálogo</a>
        <?php endif; ?>
      </nav>
    </div>
  </div>

  <main class="main-content container py-5" style="margin-top: 120px;">
    <h2 class="mb-4 text-center">Catálogo de Vinilos</h2>
    
    <?php if ($vinilos && $vinilos->num_rows > 0): ?>
      <div class="row g-4">
        <?php while ($vinilo = $vinilos->fetch_assoc()): ?>
          <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm" style="background-color: rgba(255,255,255,0.9); border: none;">

              <?php if (!empty($vinilo['imagen'])): ?>
                <img src="<?php echo upload_url($vinilo['imagen']); ?>" 
                     class="card-img-top"
                     alt="<?php echo limpiarHTML($vinilo['nombre']); ?>"
                     onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%22100%22%3E%3Crect fill=%22%23ddd%22 width=%22100%22 height=%22100%22/%3E%3Ctext fill=%22%23999%22 x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22%3ESin imagen%3C/text%3E%3C/svg%3E'">
              <?php endif; ?>

              <div class="card-body d-flex flex-column">
                <h5 class="card-title" style="font-family: 'Bebas Neue', sans-serif;">
                  <?php echo limpiarHTML($vinilo['nombre']); ?>
                </h5>
                
                <?php if (!empty($vinilo['artista'])): ?>
                  <p class="card-text text-muted mb-2">
                    <small><?php echo limpiarHTML($vinilo['artista']); ?></small>
                  </p>
                <?php endif; ?>
                
                <p class="card-text mb-2">
                  <strong><?php echo number_format($vinilo['precio'], 2, ',', '.'); ?> €</strong>
                </p>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    <?php else: ?>
      <div class="alert alert-info text-center">
        <i class="bi bi-info-circle"></i> No hay vinilos disponibles en este momento.
      </div>
    <?php endif; ?>
  </main>

  <footer class="footer mt-5 pt-5 pb-4">
    <div class="container">
      <div class="row gy-4">
        <div class="col-md-3 text-center text-md-start">
          <img src="<?php echo image_url('VinylLab.png'); ?>" alt="Logo Vinyl Lab" class="footer-logo mb-2">
          <p class="footer-text"><?php echo APP_TAGLINE; ?></p>
        </div>

        <div class="col-md-3 text-center text-md-start">
          <h5 class="footer-titulo">Enlaces</h5>
          <ul class="list-unstyled footer-links">
            <li><a href="<?php echo URL_INDEX; ?>">Inicio</a></li>
            <li><a href="<?php echo URL_CATALOGO; ?>">Catálogo</a></li>
            <li><a href="#">Ofertas</a></li>
            <li><a href="#">Contacto</a></li>
          </ul>
        </div>

        <div class="col-md-3 text-center text-md-start">
          <h5 class="footer-titulo">Síguenos</h5>
          <div class="social-icons">
            <a href="#"><i class="bi bi-instagram"></i></a>
            <a href="#"><i class="bi bi-facebook"></i></a>
            <a href="#"><i class="bi bi-twitter-x"></i></a>
            <a href="#"><i class="bi bi-youtube"></i></a>
          </div>
        </div>

        <div class="col-md-3 text-center text-md-start">
          <h5 class="footer-titulo">Contáctanos</h5>
          <form class="footer-form">
            <input type="text" class="form-control mb-2" placeholder="Tu nombre" required>
            <input type="email" class="form-control mb-2" placeholder="Tu email" required>
            <textarea class="form-control mb-2" rows="2" placeholder="Mensaje" required></textarea>
            <button type="submit" class="btn btn-enviar w-100">Enviar</button>
          </form>
        </div>
      </div>

      <div class="text-center mt-4 border-top pt-3 small footer-copy">
        &copy; 2026 <?php echo APP_NAME; ?> — Todos los derechos reservados.
      </div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    const offcanvasEl = document.getElementById('menuLateral');
    const btnHamb = document.getElementById('btnHamburguesa');
    offcanvasEl.addEventListener('show.bs.offcanvas', () => btnHamb.classList.add('active'));
    offcanvasEl.addEventListener('hidden.bs.offcanvas', () => btnHamb.classList.remove('active'));
  </script>

</body>
</html>
<?php $conn->close(); ?>