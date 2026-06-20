<?php // views/partials/footer.php ?>
<footer class="footer-urmiri text-white mt-5">
  <div class="container py-5">
    <div class="row g-4">
      <div class="col-md-4">
        <h5 class="fw-bold mb-3">🌿 Complejo Urmiri</h5>
        <p class="small text-white-50">
          Aguas termales naturales en el corazón del altiplano paceño.
          Descanso, naturaleza y bienestar en un solo lugar.
        </p>
        <div class="social-icons mt-3">
          <a href="#" class="text-white me-3 fs-5"><i class="bi bi-facebook"></i></a>
          <a href="#" class="text-white me-3 fs-5"><i class="bi bi-instagram"></i></a>
          <a href="#" class="text-white me-3 fs-5"><i class="bi bi-whatsapp"></i></a>
        </div>
      </div>
      <div class="col-md-4">
        <h6 class="fw-bold mb-3">Navegación</h6>
        <ul class="list-unstyled small">
          <li><a href="<?= BASE_URL ?>index.php" class="text-white-50 text-decoration-none">Inicio</a></li>
          <li><a href="<?= BASE_URL ?>views/habitaciones/habitaciones.php" class="text-white-50 text-decoration-none">Habitaciones</a></li>
          <li><a href="<?= BASE_URL ?>views/auth/login.php" class="text-white-50 text-decoration-none">Iniciar Sesión</a></li>
          <li><a href="<?= BASE_URL ?>views/auth/registro.php" class="text-white-50 text-decoration-none">Registrarse</a></li>
        </ul>
      </div>
      <div class="col-md-4">
        <h6 class="fw-bold mb-3">Contacto</h6>
        <ul class="list-unstyled small text-white-50">
          <li><i class="bi bi-geo-alt me-2"></i>Urmiri, La Paz — Bolivia</li>
          <li><i class="bi bi-telephone me-2"></i>+591 2 123-4567</li>
          <li><i class="bi bi-envelope me-2"></i>info@urmiri.bo</li>
          <li><i class="bi bi-clock me-2"></i>Abierto 7 días a la semana</li>
        </ul>
      </div>
    </div>
    <hr class="border-secondary">
    <p class="text-center text-white-50 small mb-0">
      &copy; <?= date('Y') ?> SGAT-Urmiri — Sistema de Gestión y Automatización Turística
    </p>
  </div>
</footer>
