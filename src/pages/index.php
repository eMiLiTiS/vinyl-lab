<?php
/**
 * VINYL LAB - Página Principal
 * Versión: 2.1 - Nueva estructura
 * Ubicación: src/pages/index.php
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../database/conexion.php';
iniciarSesionSegura();
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo APP_NAME; ?></title>

  <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;600;700&family=Bebas+Neue&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?php echo style_url('styles.css'); ?>" />
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512'><text y='0.9em' font-size='400'>💿</text></svg>">

  <style>
    .splash-full {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
      background-image: url('https://www.toptal.com/designers/subtlepatterns/uploads/wood_pattern.png');
      background-size: cover;
      background-attachment: fixed;
      padding: 0 20px;
      color: #fff;
    }

    .splash-full .titulo-splash {
      font-family: 'Bebas Neue', sans-serif;
      font-size: 5rem;
      color: var(--boton);
      text-transform: uppercase;
      text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
      margin: 0;
    }

    .splash-full .subtitulo-splash {
      font-family: 'Raleway', sans-serif;
      font-size: 1.6rem;
      margin-top: 1rem;
      text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.2);
    }

    .splash-full .btn-catalogo {
      background-color: var(--boton);
      color: #fff;
      padding: 14px 30px;
      border: none;
      border-radius: 8px;
      font-size: 1.1rem;
      font-weight: 600;
      letter-spacing: 1px;
      text-transform: uppercase;
      transition: all 0.3s ease;
      text-decoration: none;
      margin-top: 2rem;
    }

    .splash-full .btn-catalogo:hover {
      background-color: #3d2714;
      transform: scale(1.03);
    }
  </style>
</head>

<body>

  <header class="main-header">
    <div class="container-fluid d-flex align-items-center justify-content-between py-2 px-4">
      <div class="d-flex align-items-center">
        <img src="<?php echo image_url('VinylLab.png'); ?>" alt="Logo Vinyl Lab" class="header-logo me-2">
        <h1 class="header-title mb-0"><?php echo APP_NAME; ?></h1>
      </div>

      <div class="d-flex align-items-center gap-2">
        <?php if (estaAutenticado()): ?>
          <a href="<?php echo URL_GESTIONAR; ?>" class="btn-login-custom">Gestionar catálogo</a>
          <a href="<?php echo URL_LOGOUT; ?>" class="btn-login-custom">Cerrar sesión</a>
        <?php else: ?>
          <a href="<?php echo URL_LOGIN; ?>" class="btn-login-custom">Iniciar sesión</a>
        <?php endif; ?>

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

  <section class="splash-full">
    <h1 class="titulo-splash"><?php echo APP_NAME; ?></h1>
    <p class="subtitulo-splash"><?php echo APP_TAGLINE; ?></p>
    <a href="<?php echo URL_CATALOGO; ?>" class="btn-catalogo">Ver Catálogo</a>
  </section>

  <main class="main-content container py-5">
    <section class="hero d-flex flex-column flex-lg-row align-items-center justify-content-between mb-5 gap-5">
      <div class="hero-left text-center text-lg-start position-relative">
        <div class="imagen position-relative d-inline-block mb-3">
          <img src="<?php echo image_url('beatles1.jpg'); ?>" alt="Vinilo destacado" class="imagen-principal">
          <button class="btn-play" id="playButton" aria-label="Reproducir">
            <i class="bi bi-play-fill"></i>
          </button>
        </div>
        <h2 class="titulo-disco mt-3"></h2>
        <div class="precio my-3">
          <span class="precio-texto">224,99 €</span>
        </div>
      </div>

      <div class="hero-right text-center text-lg-start">
        <h1 class="titulo-principal mb-3">Abbey Road</h1>
        <p class="eslogan mb-4"></p>
        <p class="hero-desc mb-4">
          Sumérgete en la experiencia del sonido auténtico. En <?php echo APP_NAME; ?> te traemos vinilos cuidadosamente seleccionados
          para coleccionistas y amantes de la buena música.
        </p>
        <button class="btn-catalogo">Comprar</button>
      </div>
    </section>

    <section class="galeria-discos text-center my-5">
      <h2 class="mb-4">Explora nuestra colección</h2>
      <div class="carousel3d">
        <div class="carousel3d-content">
          <div class="carousel3d-item"><img src="<?php echo image_url('beatles1.jpg'); ?>" alt="Abbey Road"></div>
          <div class="carousel3d-item"><img src="<?php echo image_url('beatles2.png'); ?>" alt="Let It Be"></div>
          <div class="carousel3d-item"><img src="<?php echo image_url('pinkfloyd.png'); ?>" alt="Pink Floyd"></div>
          <div class="carousel3d-item"><img src="<?php echo image_url('queen.png'); ?>" alt="Queen"></div>
        </div>
      </div>
    </section>

    <section class="info-historia mb-5">
      <h2>Historia del grupo</h2>
      <div class="texto-historia" id="textoHistoria">
        <p><strong>The Beatles</strong>, una banda de rock inglesa formada en <strong>Liverpool</strong> durante los
          años 1960, se convirtió en el grupo más influyente de la música popular occidental...</p>
        <p>La historia del grupo se remonta a <strong>1956</strong>, cuando <strong>John Lennon</strong> fundó <em>The Quarry Men</em>...</p>
        <p>La formación inicial también contó con <strong>Stuart Sutcliffe</strong> en el bajo...</p>
        <p>Tras ser descubiertos por <strong>Brian Epstein</strong>...</p>
        <p>Durante su período de experimentación en estudio...</p>
        <p>Tras su disolución en <strong>1970</strong>...</p>
        <p>Aunque la banda se disolvió hace más de cinco décadas...</p>
      </div>
      <button class="btn-leer-mas mt-3" id="btnLeerMas">Leer más</button>
    </section>

    <section class="info-tienda">
      <div class="row align-items-center">
        <div class="col-lg-6 mb-4 mb-lg-0">
          <img src="<?php echo image_url('vinylstore1.png'); ?>" alt="Vinyl store" class="img-fluid rounded shadow">
        </div>
        <div class="col-lg-6">
          <h2>Sobre Nuestra Tienda</h2>
          <p>
            Somos una tienda especializada en vinilos dedicada a quienes disfrutan de la música en su forma más
            auténtica. Creemos que cada disco cuenta una historia, y por eso seleccionamos cuidadosamente nuestro
            catálogo con clásicos inolvidables, ediciones especiales y nuevos lanzamientos que celebran el sonido analógico.
          </p>
        </div>
      </div>
    </section>
  </main>

  <footer class="footer mt-5 pt-5 pb-4">
    <div class="container">
      <div class="row gy-4">
        <div class="col-md-3 text-center text-md-start">
          <img src="<?php echo image_url('VinylLab.png'); ?>" alt="Logo" class="footer-logo mb-2">
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

  <audio id="abbeyRoadAudio" src="https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3"></audio>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    const offcanvasEl = document.getElementById('menuLateral');
    const btnHamb = document.getElementById('btnHamburguesa');
    offcanvasEl.addEventListener('show.bs.offcanvas', () => btnHamb.classList.add('active'));
    offcanvasEl.addEventListener('hidden.bs.offcanvas', () => btnHamb.classList.remove('active'));

    const playButton = document.getElementById('playButton');
    const audio = document.getElementById('abbeyRoadAudio');
    const img = document.querySelector('.imagen-principal');
    const imagenCont = document.querySelector('.imagen');
    let isPlaying = false;

    audio.volume = 1.0;

    function centerPlayButton() {
      if (getComputedStyle(imagenCont).position === 'static') {
        imagenCont.style.position = 'relative';
      }
      const imgRect = img.getBoundingClientRect();
      const contRect = imagenCont.getBoundingClientRect();
      const centerX = imgRect.left - contRect.left + imgRect.width / 2;
      const centerY = imgRect.top - contRect.top + imgRect.height / 2;
      playButton.style.position = 'absolute';
      playButton.style.left = centerX + 'px';
      playButton.style.top = centerY + 'px';
      playButton.style.transform = 'translate(-50%, -50%)';
    }

    if (img.complete) centerPlayButton();
    else img.addEventListener('load', centerPlayButton);
    window.addEventListener('resize', centerPlayButton);

    playButton.addEventListener('click', () => {
      if (!isPlaying) {
        audio.play().then(() => {
          playButton.innerHTML = '<i class="bi bi-pause-fill"></i>';
          isPlaying = true;
        }).catch(err => console.log(err));
      } else {
        audio.pause();
        playButton.innerHTML = '<i class="bi bi-play-fill"></i>';
        isPlaying = false;
      }
    });

    audio.addEventListener('ended', () => {
      playButton.innerHTML = '<i class="bi bi-play-fill"></i>';
      isPlaying = false;
    });

    const textoHistoria = document.getElementById('textoHistoria');
    const btnLeerMas = document.getElementById('btnLeerMas');
    let abierto = false;

    function colapsar() {
      textoHistoria.style.maxHeight = '200px';
      btnLeerMas.textContent = 'Leer más';
      abierto = false;
    }

    function expandir() {
      textoHistoria.style.maxHeight = textoHistoria.scrollHeight + 'px';
      btnLeerMas.textContent = 'Leer menos';
      abierto = true;
    }

    colapsar();

    btnLeerMas.addEventListener('click', () => {
      if (abierto) colapsar();
      else expandir();
    });

    window.addEventListener('resize', () => {
      if (abierto) textoHistoria.style.maxHeight = textoHistoria.scrollHeight + 'px';
    });
  </script>
</body>

</html>