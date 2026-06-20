<?php
// views/habitaciones/habitaciones.php
require_once __DIR__ . '/../../config/helpers.php';
$db = getDB();

$filtroEstado = $_GET['estado'] ?? '';
$sql = "SELECT * FROM habitaciones";
$params = [];
if ($filtroEstado) {
  $sql .= " WHERE estado = ?";
  $params[] = $filtroEstado;
}
$sql .= " ORDER BY nombre";
$st = $db->prepare($sql);
$st->execute($params);
$habitaciones = $st->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Habitaciones — SGAT Urmiri</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
  <style>
    /* ── Habitaciones Page — Luxury Thermal Spa Aesthetic ── */
    :root {
      --cream: #f7f3ee;
      --warm-stone: #e8e0d5;
      --terracotta: #b5624a;
      --deep-forest: #1a3d2b;
      --sage: #4a7c59;
      --gold: #c9a84c;
      --charcoal: #2c2c2c;
    }

    body {
      font-family: 'Jost', sans-serif;
      background: var(--cream);
    }

    /* ── Hero de habitaciones ── */
    .rooms-hero {
      position: relative;
      height: 420px;
      background: linear-gradient(160deg, var(--deep-forest) 0%, #2e5c3f 40%, #1a4a33 100%);
      display: flex;
      align-items: flex-end;
      overflow: hidden;
    }
    .rooms-hero::before {
      content: '';
      position: absolute;
      inset: 0;
      background: 
        radial-gradient(ellipse at 70% 50%, rgba(201,168,76,0.12) 0%, transparent 60%),
        url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
    .rooms-hero::after {
      content: '';
      position: absolute;
      bottom: 0; left: 0; right: 0;
      height: 120px;
      background: linear-gradient(to top, var(--cream), transparent);
    }
    .rooms-hero-content {
      position: relative;
      z-index: 2;
      padding: 0 0 3.5rem 0;
      width: 100%;
    }
    .rooms-hero-eyebrow {
      font-family: 'Jost', sans-serif;
      font-weight: 500;
      font-size: 0.7rem;
      letter-spacing: 0.3em;
      text-transform: uppercase;
      color: var(--gold);
      margin-bottom: 0.75rem;
    }
    .rooms-hero h1 {
      font-family: 'Cormorant Garamond', serif;
      font-size: clamp(2.8rem, 6vw, 4.5rem);
      font-weight: 300;
      color: #fff;
      line-height: 1.1;
      margin-bottom: 0.5rem;
    }
    .rooms-hero h1 em {
      font-style: italic;
      color: var(--gold);
    }
    .rooms-hero-sub {
      color: rgba(255,255,255,0.6);
      font-size: 0.95rem;
      font-weight: 300;
      letter-spacing: 0.05em;
    }

    /* ── Decorative line ── */
    .deco-line {
      width: 60px;
      height: 1px;
      background: var(--gold);
      margin: 1.25rem 0;
    }

    /* ── Filtros ── */
    .filter-bar {
      background: #fff;
      border-bottom: 1px solid var(--warm-stone);
      padding: 1rem 0;
      position: sticky;
      top: 66px;
      z-index: 100;
    }
    .filter-pill {
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
      padding: 0.45rem 1.25rem;
      border-radius: 100px;
      border: 1.5px solid var(--warm-stone);
      background: transparent;
      color: var(--charcoal);
      font-size: 0.82rem;
      font-weight: 500;
      letter-spacing: 0.04em;
      text-decoration: none;
      transition: all 0.25s ease;
      cursor: pointer;
    }
    .filter-pill:hover {
      border-color: var(--sage);
      color: var(--sage);
    }
    .filter-pill.active {
      background: var(--deep-forest);
      border-color: var(--deep-forest);
      color: #fff;
    }
    .filter-pill .dot {
      width: 6px; height: 6px;
      border-radius: 50%;
    }

    /* ── Contador ── */
    .rooms-count {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1rem;
      color: #999;
    }
    .rooms-count strong {
      font-size: 1.4rem;
      color: var(--charcoal);
      font-weight: 400;
    }

    /* ── Cards de Habitación ── */
    .room-card-luxury {
      background: #fff;
      border-radius: 4px;
      overflow: hidden;
      box-shadow: 0 2px 20px rgba(0,0,0,0.06);
      transition: transform 0.4s cubic-bezier(0.25,0.46,0.45,0.94),
                  box-shadow 0.4s ease;
      position: relative;
    }
    .room-card-luxury:hover {
      transform: translateY(-8px);
      box-shadow: 0 20px 50px rgba(0,0,0,0.13);
    }

    /* Imagen */
    .room-img-wrap {
      position: relative;
      overflow: hidden;
      height: 260px;
    }
    .room-img-wrap img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.7s ease;
    }
    .room-card-luxury:hover .room-img-wrap img {
      transform: scale(1.06);
    }

    /* Overlay imagen */
    .room-img-overlay {
      position: absolute;
      inset: 0;
      background: linear-gradient(to top, rgba(26,61,43,0.6) 0%, transparent 60%);
      opacity: 0;
      transition: opacity 0.4s ease;
    }
    .room-card-luxury:hover .room-img-overlay {
      opacity: 1;
    }

    /* Badge estado */
    .room-status-badge {
      position: absolute;
      top: 16px;
      left: 16px;
      padding: 0.3rem 0.85rem;
      border-radius: 100px;
      font-size: 0.68rem;
      font-weight: 600;
      letter-spacing: 0.1em;
      text-transform: uppercase;
    }
    .room-status-badge.disponible {
      background: rgba(255,255,255,0.92);
      color: #1a7a3d;
      backdrop-filter: blur(8px);
    }
    .room-status-badge.disponible::before {
      content: '';
      display: inline-block;
      width: 5px; height: 5px;
      border-radius: 50%;
      background: #1a7a3d;
      margin-right: 5px;
      vertical-align: middle;
      animation: pulse-green 2s infinite;
    }
    @keyframes pulse-green {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.4; }
    }
    .room-status-badge.ocupada {
      background: rgba(181,98,74,0.9);
      color: #fff;
    }
    .room-status-badge.mantenimiento {
      background: rgba(201,168,76,0.9);
      color: #3a2c00;
    }

    /* Número de habitación decorativo */
    .room-number {
      position: absolute;
      bottom: 14px;
      right: 16px;
      font-family: 'Cormorant Garamond', serif;
      font-size: 2.5rem;
      font-weight: 300;
      color: rgba(255,255,255,0.3);
      line-height: 1;
      opacity: 0;
      transform: translateY(10px);
      transition: all 0.4s ease;
    }
    .room-card-luxury:hover .room-number {
      opacity: 1;
      transform: translateY(0);
    }

    /* Cuerpo */
    .room-card-body {
      padding: 1.5rem 1.75rem 1.75rem;
      border-top: 3px solid transparent;
      transition: border-color 0.3s;
    }
    .room-card-luxury:hover .room-card-body {
      border-top-color: var(--gold);
    }

    .room-type-tag {
      font-size: 0.68rem;
      letter-spacing: 0.18em;
      text-transform: uppercase;
      color: var(--sage);
      font-weight: 600;
      margin-bottom: 0.5rem;
    }
    .room-name {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.5rem;
      font-weight: 400;
      color: var(--charcoal);
      margin-bottom: 0.6rem;
      line-height: 1.2;
    }
    .room-desc {
      font-size: 0.85rem;
      color: #888;
      line-height: 1.65;
      margin-bottom: 1.25rem;
      font-weight: 300;
    }

    /* Amenidades */
    .room-amenities {
      display: flex;
      gap: 1rem;
      margin-bottom: 1.5rem;
      padding-top: 1rem;
      border-top: 1px solid var(--warm-stone);
    }
    .amenity-item {
      display: flex;
      align-items: center;
      gap: 0.35rem;
      font-size: 0.78rem;
      color: #777;
    }
    .amenity-item i {
      color: var(--sage);
      font-size: 0.9rem;
    }

    /* Footer de tarjeta */
    .room-card-footer {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
    }
    .room-price {
      display: flex;
      flex-direction: column;
    }
    .room-price-label {
      font-size: 0.68rem;
      letter-spacing: 0.1em;
      text-transform: uppercase;
      color: #aaa;
      font-weight: 500;
    }
    .room-price-amount {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.8rem;
      font-weight: 600;
      color: var(--deep-forest);
      line-height: 1.1;
    }
    .room-price-unit {
      font-size: 0.75rem;
      color: #999;
      font-family: 'Jost', sans-serif;
      font-weight: 300;
    }

    /* Botón reservar */
    .btn-reservar {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.65rem 1.5rem;
      background: var(--deep-forest);
      color: #fff;
      border: none;
      border-radius: 2px;
      font-size: 0.8rem;
      font-weight: 500;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      text-decoration: none;
      transition: all 0.25s ease;
      cursor: pointer;
      white-space: nowrap;
    }
    .btn-reservar:hover {
      background: var(--gold);
      color: var(--charcoal);
      transform: translateX(3px);
    }
    .btn-reservar-login {
      background: transparent;
      border: 1.5px solid var(--warm-stone);
      color: var(--charcoal);
    }
    .btn-reservar-login:hover {
      border-color: var(--sage);
      color: var(--sage);
      background: transparent;
    }
    .btn-reservar-disabled {
      background: #f0ebe5;
      color: #bbb;
      cursor: not-allowed;
    }
    .btn-reservar-disabled:hover {
      background: #f0ebe5;
      color: #bbb;
      transform: none;
    }

    /* ── Empty state ── */
    .empty-state {
      text-align: center;
      padding: 6rem 2rem;
    }
    .empty-state-icon {
      font-size: 4rem;
      color: var(--warm-stone);
      margin-bottom: 1.5rem;
    }
    .empty-state h3 {
      font-family: 'Cormorant Garamond', serif;
      font-weight: 400;
      color: #bbb;
    }

    /* ── Sección info termal ── */
    .thermal-strip {
      background: var(--deep-forest);
      color: #fff;
      padding: 3rem 0;
      margin-top: 5rem;
    }
    .thermal-strip-item {
      text-align: center;
      padding: 1rem;
    }
    .thermal-strip-item i {
      font-size: 1.8rem;
      color: var(--gold);
      margin-bottom: 0.75rem;
      display: block;
    }
    .thermal-strip-item h6 {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.1rem;
      font-weight: 400;
      margin-bottom: 0.3rem;
    }
    .thermal-strip-item p {
      font-size: 0.8rem;
      color: rgba(255,255,255,0.55);
      margin: 0;
    }

    /* ── Animations ── */
    .room-card-luxury {
      animation: fadeInUp 0.5s ease both;
    }
    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(24px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .col-lg-4:nth-child(1) .room-card-luxury { animation-delay: 0.05s; }
    .col-lg-4:nth-child(2) .room-card-luxury { animation-delay: 0.12s; }
    .col-lg-4:nth-child(3) .room-card-luxury { animation-delay: 0.19s; }
    .col-lg-4:nth-child(4) .room-card-luxury { animation-delay: 0.26s; }
    .col-lg-4:nth-child(5) .room-card-luxury { animation-delay: 0.33s; }
    .col-lg-4:nth-child(6) .room-card-luxury { animation-delay: 0.40s; }
  </style>
</head>
<body>

<?php include __DIR__ . '/../partials/navbar.php'; ?>

<!-- Hero -->
<div class="rooms-hero">
  <div class="rooms-hero-content">
    <div class="container">
      <div class="rooms-hero-eyebrow">Aguas Termales Urmiri · Sapahaqui, La Paz</div>
      <h1>Nuestras <em>Habitaciones</em></h1>
      <div class="deco-line"></div>
      <p class="rooms-hero-sub">Descansa, revitalízate y vive una experiencia única en el altiplano boliviano</p>
    </div>
  </div>
</div>

<!-- Filtros -->
<div class="filter-bar">
  <div class="container">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
      <div class="d-flex gap-2 flex-wrap">
        <a href="habitaciones.php" class="filter-pill <?= !$filtroEstado ? 'active' : '' ?>">
          <i class="bi bi-grid-3x3-gap-fill" style="font-size:.7rem"></i> Todas
        </a>
        <a href="?estado=disponible" class="filter-pill <?= $filtroEstado==='disponible' ? 'active' : '' ?>">
          <span class="dot" style="background:#1a7a3d"></span> Disponibles
        </a>
        <a href="?estado=ocupada" class="filter-pill <?= $filtroEstado==='ocupada' ? 'active' : '' ?>">
          <span class="dot" style="background:var(--terracotta)"></span> Ocupadas
        </a>
        <a href="?estado=mantenimiento" class="filter-pill <?= $filtroEstado==='mantenimiento' ? 'active' : '' ?>">
          <span class="dot" style="background:var(--gold)"></span> Mantenimiento
        </a>
      </div>
      <div class="rooms-count">
        <strong><?= count($habitaciones) ?></strong> habitación<?= count($habitaciones)!==1?'es':'' ?> encontrada<?= count($habitaciones)!==1?'s':'' ?>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../partials/flash.php'; ?>

<!-- Grid de habitaciones -->
<div class="container py-5">

  <?php if (empty($habitaciones)): ?>
    <div class="empty-state">
      <div class="empty-state-icon"><i class="bi bi-house-slash"></i></div>
      <h3>No se encontraron habitaciones</h3>
      <p class="text-muted">Intenta con otro filtro o vuelve más tarde.</p>
      <a href="habitaciones.php" class="btn-reservar mt-3 d-inline-flex">Ver todas</a>
    </div>
  <?php endif; ?>

  <?php
  // Mapa de imágenes para las habitaciones demo (habi1-4.jpg)
  $imagenesDemo = [
    // Nombres nuevos (ya en BD tras ejecutar habitaciones_imagenes.sql)
    'habi1.jpg'           => 'habi1.jpg',
    'habi2.jpg'           => 'habi2.jpg',
    'habi3.jpg'           => 'habi3.jpg',
    'habi4.jpg'           => 'habi4.jpg',
    // Nombres antiguos (por si la BD aún tiene estos)
    'doble_estandar.jpg'  => 'habi1.jpg',
    'cabana_familiar.jpg' => 'habi3.jpg',
    'suite_premium.jpg'   => 'habi3.jpg',
    'suite_romantica.jpg' => 'habi2.jpg',
    'simple.jpg'          => 'habi4.jpg',
    'cabana_ejecutiva.jpg'=> 'habi4.jpg',
    'default.jpg'         => 'habi1.jpg',
  ];
  $iconosCapacidad = [1=>'bi-person', 2=>'bi-people', 3=>'bi-people', 4=>'bi-people-fill'];
  $i = 0;
  ?>

  <div class="row g-4">
    <?php foreach ($habitaciones as $h):
      $i++;
      // Resolver imagen: usar uploads/habitaciones/ directamente
      // Si el campo imagen ya es habi1.jpg, habi2.jpg, etc., UPLOADS_URL lo resuelve directo
      // Si es un nombre antiguo (ej: cabana_ejecutiva.jpg), el mapa $imagenesDemo hace el fallback
      $imgNombre = $h['imagen'];
      $imgSrc = UPLOADS_URL . htmlspecialchars($imgNombre);
      // Fallback: si la imagen no carga, usar el mapa demo
      $imgFallback = BASE_URL . 'uploads/habitaciones/' . ($imagenesDemo[$imgNombre] ?? 'habi1.jpg');
      $iconCap = $iconosCapacidad[min($h['capacidad'], 4)] ?? 'bi-people-fill';
    ?>
    <div class="col-md-6 col-lg-4">
      <div class="room-card-luxury">
        <div class="room-img-wrap">
          <img src="<?= $imgSrc ?>"
               alt="<?= sanitize($h['nombre']) ?>"
               onerror="this.src='<?= $imgFallback ?>';this.onerror=null;">
          <div class="room-img-overlay"></div>
          <span class="room-status-badge <?= $h['estado'] ?>">
            <?= $h['estado'] === 'disponible' ? 'Disponible' : ($h['estado'] === 'ocupada' ? 'Ocupada' : 'Mantenimiento') ?>
          </span>
          <span class="room-number"><?= str_pad($i, 2, '0', STR_PAD_LEFT) ?></span>
        </div>
        <div class="room-card-body">
          <div class="room-type-tag">Alojamiento</div>
          <h3 class="room-name"><?= sanitize($h['nombre']) ?></h3>
          <p class="room-desc"><?= sanitize(substr($h['descripcion'], 0, 100)) ?>...</p>
          <div class="room-amenities">
            <div class="amenity-item">
              <i class="bi <?= $iconCap ?>"></i>
              <span><?= $h['capacidad'] ?> persona<?= $h['capacidad']>1?'s':'' ?></span>
            </div>
            <div class="amenity-item">
              <i class="bi bi-water"></i>
              <span>Termas incluidas</span>
            </div>
            <div class="amenity-item">
              <i class="bi bi-wifi"></i>
              <span>WiFi</span>
            </div>
          </div>
          <div class="room-card-footer">
            <div class="room-price">
              <span class="room-price-label">desde</span>
              <span class="room-price-amount">Bs. <?= number_format((float)$h['precio_noche'], 0) ?></span>
              <span class="room-price-unit">por noche</span>
            </div>
            <?php if ($h['estado'] === 'disponible'): ?>
              <?php if (isLoggedIn()): ?>
                <a href="../reservas/nueva_reserva.php?hab=<?= $h['id'] ?>" class="btn-reservar">
                  <i class="bi bi-calendar-check"></i> Reservar
                </a>
              <?php else: ?>
                <a href="../auth/login.php" class="btn-reservar btn-reservar-login">
                  <i class="bi bi-lock"></i> Iniciar sesión
                </a>
              <?php endif; ?>
            <?php else: ?>
              <span class="btn-reservar btn-reservar-disabled">
                <i class="bi bi-x-circle"></i>
                <?= $h['estado'] === 'ocupada' ? 'Ocupada' : 'En mantenimiento' ?>
              </span>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- Franja informativa -->
<div class="thermal-strip">
  <div class="container">
    <div class="row g-0">
      <div class="col-6 col-md-3">
        <div class="thermal-strip-item">
          <i class="bi bi-droplet-half"></i>
          <h6>Aguas Termales</h6>
          <p>Acceso incluido en toda estadía</p>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="thermal-strip-item">
          <i class="bi bi-moon-stars"></i>
          <h6>Check-in Flexible</h6>
          <p>Desde las 14:00 hrs</p>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="thermal-strip-item">
          <i class="bi bi-shield-check"></i>
          <h6>Reserva Segura</h6>
          <p>Confirmación inmediata</p>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="thermal-strip-item">
          <i class="bi bi-telephone"></i>
          <h6>Soporte 24/7</h6>
          <p>Estamos para ayudarte</p>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
