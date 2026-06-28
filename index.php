<?php
// index.php — Página Principal SGAT-Urmiri
require_once __DIR__ . '/config/helpers.php';
$db = getDB();

// Cargar habitaciones disponibles (máx 6 para el home)
$habitaciones = $db->query("SELECT * FROM habitaciones ORDER BY created_at DESC LIMIT 6")->fetchAll();

// Cargar servicios activos
$servicios = $db->query("SELECT * FROM servicios WHERE activo=1")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Complejo Urmiri — Aguas Termales & Hospedaje</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body>

<?php include __DIR__ . '/views/partials/navbar.php'; ?>

<!-- ── Hero ──────────────────────────────────────────────── -->
<section class="hero-section">
  <div class="container text-center">
    <span class="badge-custom mb-3 d-inline-block">🌡️ Aguas Termales Naturales · Altiplano Paceño</span>
    <h1 class="mb-4 text-white">Complejo Turístico<br><strong>Urmiri</strong></h1>
    <?php if (isLoggedIn()): ?>
      <a href="views/habitaciones/habitaciones.php" class="btn btn-outline-light btn-lg px-4">
        <i class="bi bi-calendar-plus"></i> Reservar Ahora
      </a>
    <?php else: ?>
      <a href="views/auth/login.php" class="btn btn-outline-light btn-lg px-4">
        <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
      </a>
    <?php endif; ?>
  </div>
</section>

<!-- ── Servicios ──────────────────────────────────────────── -->
<section id="servicios" class="py-5" style="background:#f0ece6;">
  <div class="container">
    <div class="text-center mb-5">
      <div style="font-size:.68rem;letter-spacing:.25em;text-transform:uppercase;color:#c9a84c;font-weight:600;margin-bottom:.5rem">
        Experiencias Urmiri
      </div>
      <h2 style="font-family:'Cormorant Garamond',serif;font-size:2.8rem;font-weight:300;color:#1a1a1a">
        Servicios Turísticos
      </h2>
      <div style="width:40px;height:1px;background:#c9a84c;margin:.75rem auto 0;"></div>
    </div>
    <div class="row g-4 justify-content-center">

      <!-- Piscinas Termales -->
      <div class="col-md-5">
        <a href="views/piscinas/piscinas.php" style="text-decoration:none">
          <div style="background:linear-gradient(135deg,#1a3d2b 0%,#1a6b4a 100%);border-radius:8px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.12);transition:transform .35s ease,box-shadow .35s ease;cursor:pointer;display:block"
               onmouseover="this.style.transform='translateY(-8px)';this.style.boxShadow='0 20px 50px rgba(0,0,0,.2)'"
               onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 24px rgba(0,0,0,.12)'">
            <!-- Visual -->
            <div style="height:220px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:1rem;position:relative;overflow:hidden">
              <div style="position:absolute;inset:0;background:radial-gradient(ellipse at 40% 60%,rgba(46,127,168,.35) 0%,transparent 60%)"></div>
              <!-- Ondas animadas -->
              <div style="display:flex;gap:4px;align-items:flex-end;margin-bottom:.5rem;position:relative;z-index:1">
                <?php for($w=0;$w<7;$w++): ?>
                <div style="width:4px;background:rgba(255,255,255,.5);border-radius:2px;animation:waveAnim 1.3s ease-in-out infinite;animation-delay:<?= $w*0.15 ?>s;height:<?= [10,18,12,22,14,20,10][$w] ?>px"></div>
                <?php endfor; ?>
              </div>
              <i class="bi bi-water" style="font-size:3.5rem;color:rgba(255,255,255,.8);position:relative;z-index:1"></i>
              <span style="font-size:.68rem;letter-spacing:.2em;text-transform:uppercase;color:#c9a84c;font-weight:600;position:relative;z-index:1">Incluido en tu estadía</span>
            </div>
            <!-- Info -->
            <div style="padding:1.75rem 2rem 2rem;border-top:1px solid rgba(255,255,255,.1)">
              <h3 style="font-family:'Cormorant Garamond',serif;font-size:1.8rem;font-weight:300;color:#fff;margin-bottom:.5rem">
                Piscinas Termales
              </h3>
              <p style="font-size:.85rem;color:rgba(255,255,255,.6);font-weight:300;margin-bottom:1.25rem;line-height:1.65">
                Aguas naturales a 38–42°C. Piscina principal, templada y jacuzzi exterior. Acceso libre durante tu estadía.
              </p>
              <div style="display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:1.5rem">
                <?php foreach(['38°C – 42°C','3 piscinas','Abierto 6–22h','Acceso libre'] as $f): ?>
                <span style="background:rgba(255,255,255,.12);color:rgba(255,255,255,.8);font-size:.7rem;font-weight:600;padding:.25rem .7rem;border-radius:100px;letter-spacing:.04em"><?= $f ?></span>
                <?php endforeach; ?>
              </div>
              <div style="display:flex;align-items:center;justify-content:space-between">
                <span style="font-family:'Cormorant Garamond',serif;font-size:1.4rem;color:#c9a84c;font-weight:400">
                  Incluido <small style="font-family:'Jost',sans-serif;font-size:.7rem;color:rgba(255,255,255,.4)">con hospedaje</small>
                </span>
                <span style="display:inline-flex;align-items:center;gap:.4rem;padding:.55rem 1.25rem;background:#c9a84c;color:#1a1a00;font-size:.75rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;border-radius:4px">
                  Ver más <i class="bi bi-arrow-right"></i>
                </span>
              </div>
            </div>
          </div>
        </a>
      </div>

      <!-- Menú de Comidas -->
      <div class="col-md-5">
        <a href="views/menu/menu.php" style="text-decoration:none">
          <div style="background:linear-gradient(135deg,#3d1f0a 0%,#7a3d10 100%);border-radius:8px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.12);transition:transform .35s ease,box-shadow .35s ease;cursor:pointer;display:block"
               onmouseover="this.style.transform='translateY(-8px)';this.style.boxShadow='0 20px 50px rgba(0,0,0,.2)'"
               onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 24px rgba(0,0,0,.12)'">
            <!-- Visual -->
            <div style="height:220px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:1rem;position:relative;overflow:hidden">
              <div style="position:absolute;inset:0;background:radial-gradient(ellipse at 60% 40%,rgba(201,168,76,.2) 0%,transparent 60%)"></div>
              <!-- Iconos flotantes -->
              <div style="display:flex;gap:1.25rem;margin-bottom:.5rem;position:relative;z-index:1">
                <?php foreach(['bi-cup-hot','bi-egg-fried','bi-fish','bi-fork-knife'] as $ico): ?>
                <i class="bi <?= $ico ?>" style="font-size:1.6rem;color:rgba(255,255,255,.5)"></i>
                <?php endforeach; ?>
              </div>
              <i class="bi bi-bowl-hot" style="font-size:3.5rem;color:rgba(255,255,255,.8);position:relative;z-index:1"></i>
              <span style="font-size:.68rem;letter-spacing:.2em;text-transform:uppercase;color:#c9a84c;font-weight:600;position:relative;z-index:1">Desayunos & Almuerzos</span>
            </div>
            <!-- Info -->
            <div style="padding:1.75rem 2rem 2rem;border-top:1px solid rgba(255,255,255,.1)">
              <h3 style="font-family:'Cormorant Garamond',serif;font-size:1.8rem;font-weight:300;color:#fff;margin-bottom:.5rem">
                Menú de Comidas
              </h3>
              <p style="font-size:.85rem;color:rgba(255,255,255,.6);font-weight:300;margin-bottom:1.25rem;line-height:1.65">
                Elige tu desayuno y almuerzo favoritos. Cocina boliviana del altiplano con productos locales frescos.
              </p>
              <div style="display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:1.5rem">
                <?php foreach(['4 desayunos','6 platos de almuerzo','Cocina boliviana','Desde Bs. 25'] as $f): ?>
                <span style="background:rgba(255,255,255,.12);color:rgba(255,255,255,.8);font-size:.7rem;font-weight:600;padding:.25rem .7rem;border-radius:100px;letter-spacing:.04em"><?= $f ?></span>
                <?php endforeach; ?>
              </div>
              <div style="display:flex;align-items:center;justify-content:space-between">
                <span style="font-family:'Cormorant Garamond',serif;font-size:1.4rem;color:#c9a84c;font-weight:400">
                  Desde Bs. 25 <small style="font-family:'Jost',sans-serif;font-size:.7rem;color:rgba(255,255,255,.4)">por persona</small>
                </span>
                <span style="display:inline-flex;align-items:center;gap:.4rem;padding:.55rem 1.25rem;background:#c9a84c;color:#1a1a00;font-size:.75rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;border-radius:4px">
                  Ver menú <i class="bi bi-arrow-right"></i>
                </span>
              </div>
            </div>
          </div>
        </a>
      </div>

    </div>
  </div>
</section>
<style>
@keyframes waveAnim {
  0%,100% { transform:scaleY(1); }
  50% { transform:scaleY(.35); }
}
</style>

<!-- ── CTA ───────────────────────────────────────────────── -->
<section class="py-5" style="background:linear-gradient(135deg,#1a6b4a,#2e9e6f);">
  <div class="container text-center text-white">
    <h2 class="fw-bold mb-3">¿Listo para tu escapada al altiplano?</h2>
    <p class="mb-4 opacity-75">Reserva ahora y disfruta de las mejores aguas termales de Bolivia</p>
    <?php if (isLoggedIn()): ?>
      <a href="views/reservas/nueva_reserva.php" class="btn btn-warning btn-lg px-5">
        <i class="bi bi-calendar-heart"></i> Hacer Reserva
      </a>
    <?php else: ?>
      <a href="views/auth/registro.php" class="btn btn-warning btn-lg px-5">
        <i class="bi bi-person-plus"></i> Registrarse Gratis
      </a>
    <?php endif; ?>
  </div>
</section>

<!-- ── Contacto ───────────────────────────────────────────── -->
<section id="contacto" class="py-5">
  <div class="container">
    <div class="text-center mb-4">
      <h2 class="fw-bold">¿Cómo llegar?</h2>
    </div>
    <div class="row align-items-center g-4">
      <div class="col-md-6">
        <div class="card border-0 shadow-sm p-4">
          <h5 class="fw-bold text-success"><i class="bi bi-geo-alt"></i> Ubicación</h5>
          <p class="text-muted">Urmiri, Municipio de Luribay<br>Provincia Loayza, La Paz — Bolivia</p>
          <hr>
          <p><i class="bi bi-telephone text-success"></i> +591 2 123-4567</p>
          <p><i class="bi bi-envelope text-success"></i> info@urmiri.bo</p>
          <p><i class="bi bi-whatsapp text-success"></i> +591 71234567</p>
        </div>
      </div>
      <div class="col-md-6 text-center">
        <div class="bg-light rounded-3 p-4">
          <i class="bi bi-map display-1 text-success"></i>
          <p class="mt-2 text-muted">A 75 km de la ciudad de La Paz<br>Acceso por la carretera a Oruro</p>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/views/partials/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>assets/js/main.js"></script>
</body>
</html>
