<?php
require_once __DIR__ . '/../../config/helpers.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Piscinas Termales — SGAT Urmiri</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,400&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
  <style>
    :root {
      --teal: #1a6b4a;
      --teal-dark: #0d3d28;
      --gold: #c9a84c;
      --cream: #f7f3ee;
      --water-blue: #2e7fa8;
    }
    body { font-family: 'Jost', sans-serif; background: var(--cream); }

    /* ── Hero ── */
    .pisci-hero {
      min-height: 520px;
      background: linear-gradient(160deg, var(--teal-dark) 0%, var(--teal) 55%, #1e7a5a 100%);
      display: flex; align-items: center;
      position: relative; overflow: hidden;
    }
    .pisci-hero::before {
      content: '';
      position: absolute; inset: 0;
      background: radial-gradient(ellipse at 30% 60%, rgba(46,127,168,0.25) 0%, transparent 55%),
                  radial-gradient(ellipse at 80% 30%, rgba(201,168,76,0.1) 0%, transparent 40%);
    }
    .pisci-hero::after {
      content: ''; position: absolute;
      bottom: 0; left: 0; right: 0; height: 80px;
      background: linear-gradient(to top, var(--cream), transparent);
    }
    .pisci-hero-content { position: relative; z-index: 2; }
    .hero-eyebrow {
      font-size: 0.68rem; letter-spacing: 0.3em; text-transform: uppercase;
      color: var(--gold); font-weight: 600; margin-bottom: 1rem;
    }
    .pisci-hero h1 {
      font-family: 'Cormorant Garamond', serif;
      font-size: clamp(3rem,7vw,5rem); font-weight: 300; color: #fff; line-height: 1.05;
    }
    .pisci-hero h1 em { font-style: italic; color: rgba(46,127,168,0.9); }
    .hero-deco { width: 50px; height: 1px; background: var(--gold); margin: 1.25rem 0; }
    .hero-sub { color: rgba(255,255,255,0.65); font-size: 1rem; font-weight: 300; max-width: 520px; }

    /* Ondas animadas */
    .wave-badge {
      display: inline-flex; gap: 3px; align-items: flex-end;
      background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);
      border-radius: 100px; padding: 0.5rem 1.2rem; backdrop-filter: blur(10px);
      margin-top: 2rem;
    }
    .wave-badge span { font-size: 0.85rem; color: rgba(255,255,255,0.8); margin-left: 0.5rem; }
    .wave-bar {
      width: 3px; background: var(--gold); border-radius: 2px;
      animation: wave 1.2s ease-in-out infinite;
    }
    .wave-bar:nth-child(1) { height: 8px; animation-delay: 0s; }
    .wave-bar:nth-child(2) { height: 14px; animation-delay: 0.2s; }
    .wave-bar:nth-child(3) { height: 10px; animation-delay: 0.4s; }
    .wave-bar:nth-child(4) { height: 16px; animation-delay: 0.1s; }
    .wave-bar:nth-child(5) { height: 8px; animation-delay: 0.3s; }
    @keyframes wave {
      0%,100% { transform: scaleY(1); }
      50% { transform: scaleY(0.4); }
    }

    /* ── Info strips ── */
    .info-strip {
      background: var(--teal); padding: 1.5rem 0;
    }
    .info-chip {
      display: flex; align-items: center; gap: 0.75rem;
      color: rgba(255,255,255,0.85); font-size: 0.88rem;
    }
    .info-chip i { font-size: 1.3rem; color: var(--gold); }
    .info-chip strong { display: block; color: #fff; font-size: 0.95rem; }

    /* ── Galería ── */
    .gallery-section { padding: 5rem 0; }
    .section-label {
      font-size: 0.68rem; letter-spacing: 0.25em; text-transform: uppercase;
      color: var(--gold); font-weight: 600; margin-bottom: 0.75rem;
    }
    .section-title {
      font-family: 'Cormorant Garamond', serif;
      font-size: clamp(2rem,4vw,3rem); font-weight: 300; color: #1a1a1a;
      margin-bottom: 0.5rem;
    }
    .section-deco { width: 40px; height: 1px; background: var(--gold); margin: 1rem auto 2.5rem; }

    /* Grid de galería — masonry con CSS grid */
    .gallery-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      grid-template-rows: repeat(3, 220px);
      gap: 12px;
    }
    .gallery-item {
      border-radius: 6px; overflow: hidden; position: relative; cursor: pointer;
    }
    .gallery-item:nth-child(1) { grid-column: 1/3; grid-row: 1/3; }
    .gallery-item:nth-child(4) { grid-column: 3; grid-row: 2/4; }
    .gallery-item:nth-child(6) { grid-column: 1/3; }

    .gallery-item img {
      width: 100%; height: 100%; object-fit: cover;
      transition: transform 0.6s ease;
    }
    .gallery-item:hover img { transform: scale(1.07); }
    .gallery-overlay {
      position: absolute; inset: 0;
      background: linear-gradient(to top, rgba(13,61,40,0.7) 0%, transparent 50%);
      opacity: 0; transition: opacity 0.3s;
      display: flex; align-items: flex-end; padding: 1.25rem;
    }
    .gallery-item:hover .gallery-overlay { opacity: 1; }
    .gallery-caption {
      font-family: 'Cormorant Garamond', serif;
      color: #fff; font-size: 1.1rem; font-weight: 400;
    }

    /* Imágenes placeholder con gradientes bonitos */
    .img-placeholder {
      width: 100%; height: 100%;
      display: flex; flex-direction: column; align-items: center; justify-content: center;
      gap: 0.75rem; color: rgba(255,255,255,0.8); text-align: center; padding: 1.5rem;
    }
    .img-placeholder i { font-size: 3rem; opacity: 0.7; }
    .img-placeholder span { font-family: 'Cormorant Garamond',serif; font-size: 1.1rem; }
    .img-p1 { background: linear-gradient(135deg, #1a6b4a, #2e9e6f); }
    .img-p2 { background: linear-gradient(135deg, #1a5a6b, #2e7fa8); }
    .img-p3 { background: linear-gradient(135deg, #3d6b1a, #6b9e2e); }
    .img-p4 { background: linear-gradient(135deg, #2e4a6b, #1a6b6b); }
    .img-p5 { background: linear-gradient(135deg, #6b4a1a, #9e7a2e); }
    .img-p6 { background: linear-gradient(135deg, #1a3d6b, #2e6b9e); }

    /* ── Piscinas detalle ── */
    .pool-cards { padding: 2rem 0 5rem; }
    .pool-card {
      border: none; border-radius: 8px; overflow: hidden;
      box-shadow: 0 4px 24px rgba(0,0,0,0.08);
      transition: transform 0.35s ease, box-shadow 0.35s ease;
    }
    .pool-card:hover { transform: translateY(-6px); box-shadow: 0 16px 48px rgba(0,0,0,0.14); }
    .pool-card-img {
      height: 220px; position: relative; overflow: hidden;
    }
    .pool-card-img > div { width: 100%; height: 100%; }
    .pool-temp {
      position: absolute; top: 14px; right: 14px;
      background: rgba(255,255,255,0.92); backdrop-filter: blur(8px);
      border-radius: 100px; padding: 0.3rem 0.9rem;
      font-size: 0.8rem; font-weight: 700; color: var(--teal);
    }
    .pool-card-body { background: #fff; padding: 1.5rem; }
    .pool-name {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.4rem; font-weight: 400; color: #1a1a1a; margin-bottom: 0.5rem;
    }
    .pool-desc { font-size: 0.85rem; color: #888; font-weight: 300; margin-bottom: 1rem; }
    .pool-features { display: flex; flex-wrap: wrap; gap: 0.5rem; }
    .pool-feat {
      background: #edf7f2; color: var(--teal);
      font-size: 0.72rem; font-weight: 600;
      padding: 0.3rem 0.75rem; border-radius: 100px; letter-spacing: 0.04em;
    }

    /* ── CTA Reserva ── */
    .cta-reserva {
      background: linear-gradient(135deg, var(--teal-dark) 0%, var(--teal) 100%);
      padding: 4rem 0; text-align: center;
    }
    .cta-reserva h2 {
      font-family: 'Cormorant Garamond', serif;
      font-size: 2.5rem; font-weight: 300; color: #fff; margin-bottom: 0.75rem;
    }
    .cta-reserva p { color: rgba(255,255,255,0.65); font-size: 1rem; margin-bottom: 2rem; }
    .btn-cta {
      display: inline-flex; align-items: center; gap: 0.6rem;
      padding: 0.85rem 2.5rem; background: var(--gold);
      color: #1a1a00; font-weight: 600; font-size: 0.85rem;
      letter-spacing: 0.08em; text-transform: uppercase;
      border-radius: 4px; text-decoration: none;
      transition: all 0.25s ease; border: none;
    }
    .btn-cta:hover { background: #e0ba55; color: #1a1a00; transform: translateY(-2px); box-shadow: 0 8px 24px rgba(201,168,76,0.4); }
    .btn-cta-outline {
      background: transparent;
      border: 1.5px solid rgba(255,255,255,0.4);
      color: rgba(255,255,255,0.85);
    }
    .btn-cta-outline:hover { border-color: rgba(255,255,255,0.8); background: rgba(255,255,255,0.1); color: #fff; }

    /* ── Lightbox simple ── */
    .lightbox-overlay {
      display: none; position: fixed; inset: 0; z-index: 9999;
      background: rgba(0,0,0,0.92); align-items: center; justify-content: center;
    }
    .lightbox-overlay.open { display: flex; }
    .lightbox-inner { max-width: 90vw; max-height: 85vh; position: relative; }
    .lightbox-inner img, .lightbox-inner > div {
      max-width: 100%; max-height: 80vh; border-radius: 6px;
    }
    .lightbox-close {
      position: absolute; top: -40px; right: 0;
      background: none; border: none; color: #fff; font-size: 1.8rem; cursor: pointer;
      line-height: 1;
    }
    .lightbox-caption {
      text-align: center; color: rgba(255,255,255,0.7);
      font-family: 'Cormorant Garamond',serif; font-size: 1.1rem; margin-top: 1rem;
    }
  </style>
</head>
<body>
<?php include __DIR__ . '/../partials/navbar.php'; ?>

<!-- ── Hero ── -->
<section class="pisci-hero">
  <div class="container pisci-hero-content">
    <div class="hero-eyebrow">Complejo Urmiri · Sapahaqui, La Paz</div>
    <h1>Piscinas<br><em>Termales</em></h1>
    <div class="hero-deco"></div>
    <p class="hero-sub">Aguas naturales ricas en minerales que brotan del subsuelo del altiplano boliviano, a temperatura perfecta todo el año.</p>
    <div class="wave-badge">
      <div class="wave-bar"></div><div class="wave-bar"></div>
      <div class="wave-bar"></div><div class="wave-bar"></div><div class="wave-bar"></div>
      <span>Aguas termales 100% naturales · Incluido en tu estadía</span>
    </div>
  </div>
</section>

<!-- ── Chips de info ── -->
<div class="info-strip">
  <div class="container">
    <div class="row g-3">
      <div class="col-6 col-md-3">
        <div class="info-chip"><i class="bi bi-thermometer-half"></i>
          <div><strong>38°C – 42°C</strong>Temperatura natural</div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="info-chip"><i class="bi bi-clock"></i>
          <div><strong>6:00 – 22:00</strong>Horario de acceso</div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="info-chip"><i class="bi bi-droplet-fill"></i>
          <div><strong>Minerales</strong>Azufre, calcio, magnesio</div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="info-chip"><i class="bi bi-check-circle"></i>
          <div><strong>Incluido</strong>Con toda reserva</div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ── Galería ── -->
<section class="gallery-section">
  <div class="container">
    <div class="text-center">
      <div class="section-label">Galería de imágenes</div>
      <h2 class="section-title">Nuestras Instalaciones</h2>
      <div class="section-deco"></div>
    </div>
    <div class="gallery-grid">
      <?php
      $galerias = [
        ['clase'=>'img-p1','icono'=>'bi-water','titulo'=>'Piscina Principal','subtitulo'=>'Piscina termal grande · 42°C'],
        ['clase'=>'img-p2','icono'=>'bi-droplet-half','titulo'=>'Piscina Templada','subtitulo'=>'Zona relajación · 38°C'],
        ['clase'=>'img-p3','icono'=>'bi-tree','titulo'=>'Área Natural','subtitulo'=>'Paisaje altiplánico'],
        ['clase'=>'img-p4','icono'=>'bi-moon-stars','titulo'=>'Piscina Nocturna','subtitulo'=>'Experiencia única'],
        ['clase'=>'img-p5','icono'=>'bi-people','titulo'=>'Zona Familiar','subtitulo'=>'Para toda la familia'],
        ['clase'=>'img-p6','icono'=>'bi-waves','titulo'=>'Jacuzzi Exterior','subtitulo'=>'Hidromasaje natural'],
      ];
      foreach ($galerias as $idx => $g):
        $imgPath = BASE_URL . 'uploads/piscinas/p' . ($idx+1) . '.jpg';
      ?>
      <div class="gallery-item" onclick="openLightbox('<?= $g['titulo'] ?>', <?= $idx ?>)">
        <img src="<?= $imgPath ?>" alt="<?= $g['titulo'] ?>"
             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
        <div class="img-placeholder <?= $g['clase'] ?>" style="display:none">
          <i class="bi <?= $g['icono'] ?>"></i>
          <span><?= $g['titulo'] ?></span>
          <small style="font-size:0.75rem;opacity:.7"><?= $g['subtitulo'] ?></small>
        </div>
        <div class="gallery-overlay">
          <div class="gallery-caption"><?= $g['titulo'] ?></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ── Cards detalle piscinas ── -->
<!-- ── CTA ── -->
<div class="cta-reserva">
  <div class="container">
    <h2>¿Listo para disfrutar las termas?</h2>
    <p>El acceso a todas las piscinas está incluido en tu reserva de hospedaje</p>
    <div class="d-flex gap-3 justify-content-center flex-wrap">
      <a href="<?= BASE_URL ?>views/reservas/nueva_reserva.php" class="btn-cta">
        <i class="bi bi-calendar-check"></i> Reservar Hospedaje
      </a>
      <a href="<?= BASE_URL ?>views/menu/menu.php" class="btn-cta btn-cta-outline">
        <i class="bi bi-fork-knife"></i> Ver Menú & Comidas
      </a>
    </div>
  </div>
</div>

<!-- Lightbox -->
<div class="lightbox-overlay" id="lightboxOverlay" onclick="closeLightbox(event)">
  <div class="lightbox-inner" id="lightboxInner">
    <button class="lightbox-close" onclick="closeLightbox()"><i class="bi bi-x-lg"></i></button>
    <div id="lightboxContent"></div>
    <div class="lightbox-caption" id="lightboxCaption"></div>
  </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const galerias = <?= json_encode($galerias) ?>;
function openLightbox(titulo, idx) {
  const g = galerias[idx];
  const content = document.getElementById('lightboxContent');
  const imgUrl = '<?= BASE_URL ?>uploads/piscinas/p' + (idx+1) + '.jpg';
  content.innerHTML = `
    <div class="img-placeholder ${g.clase}" style="width:640px;max-width:85vw;height:420px;border-radius:6px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:1rem">
      <i class="bi ${g.icono}" style="font-size:5rem;opacity:.6;color:#fff"></i>
      <span style="font-family:'Cormorant Garamond',serif;font-size:1.8rem;color:#fff">${titulo}</span>
      <small style="color:rgba(255,255,255,.6);font-size:.9rem">${g.subtitulo}</small>
    </div>`;
  // Si la imagen existe, mostrarla
  const img = new Image();
  img.onload = () => { content.innerHTML = `<img src="${imgUrl}" alt="${titulo}" style="max-width:90vw;max-height:80vh;border-radius:6px">`; };
  img.src = imgUrl;
  document.getElementById('lightboxCaption').textContent = titulo + ' — ' + g.subtitulo;
  document.getElementById('lightboxOverlay').classList.add('open');
  document.body.style.overflow = 'hidden';
}
function closeLightbox(e) {
  if (!e || e.target === document.getElementById('lightboxOverlay') || e.currentTarget.tagName === 'BUTTON') {
    document.getElementById('lightboxOverlay').classList.remove('open');
    document.body.style.overflow = '';
  }
}
document.addEventListener('keydown', e => { if(e.key==='Escape') closeLightbox({target:document.getElementById('lightboxOverlay')}); });
</script>
</body>
</html>
