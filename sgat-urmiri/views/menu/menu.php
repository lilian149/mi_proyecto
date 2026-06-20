<?php
require_once __DIR__ . '/../../config/helpers.php';

// Menú de comidas (en producción vendría de la BD)
$desayunos = [
  ['id'=>'d1','nombre'=>'Desayuno Continental','desc'=>'Pan fresco, mermelada, mantequilla, jugo natural y té o café.','precio'=>25,'calorias'=>'~420 kcal','icono'=>'bi-cup-hot','clase'=>'menu-amber','tag'=>'Clásico'],
  ['id'=>'d2','nombre'=>'Desayuno Buffet Completo','desc'=>'Variedad de panes, frutas frescas, huevos a elección, embutidos, cereales, jugos y bebidas calientes.','precio'=>35,'calorias'=>'~750 kcal','icono'=>'bi-egg-fried','clase'=>'menu-orange','tag'=>'Popular'],
  ['id'=>'d3','nombre'=>'Desayuno Boliviano','desc'=>'Api morado con pastel, buñuelos con miel, ch\'uño phuti y marraqueta.','precio'=>30,'calorias'=>'~580 kcal','icono'=>'bi-basket','clase'=>'menu-terracotta','tag'=>'Tradicional'],
  ['id'=>'d4','nombre'=>'Desayuno Saludable','desc'=>'Granola con yogur, ensalada de frutas, tostadas integrales, jugo verde y té de hierbas.','precio'=>28,'calorias'=>'~380 kcal','icono'=>'bi-apple','clase'=>'menu-sage','tag'=>'Fit'],
];
$almuerzos = [
  ['id'=>'a1','nombre'=>'Trucha a la Plancha','desc'=>'Trucha fresca del altiplano a la plancha con papa cocida, ensalada verde y arroz. Plato estrella de Urmiri.','precio'=>55,'calorias'=>'~620 kcal','icono'=>'bi-fish','clase'=>'menu-teal','tag'=>'Estrella ★'],
  ['id'=>'a2','nombre'=>'Sopa de Maní','desc'=>'Sopa tradicional paceña con maní tostado, verduras, papa y arroz. Reconfortante y nutritiva.','precio'=>35,'calorias'=>'~480 kcal','icono'=>'bi-bowl-hot','clase'=>'menu-warm','tag'=>'Tradicional'],
  ['id'=>'a3','nombre'=>'Pollo al Horno','desc'=>'Pollo entero al horno con hierbas aromáticas, papas doradas, ensalada mixta y crema de ají amarillo.','precio'=>48,'calorias'=>'~700 kcal','icono'=>'bi-fire','clase'=>'menu-amber','tag'=>'Familiar'],
  ['id'=>'a4','nombre'=>'Plato Vegetariano','desc'=>'Quinoa salteada con verduras del altiplano, tofu ahumado, crema de locoto y pan integral.','precio'=>40,'calorias'=>'~420 kcal','icono'=>'bi-flower1','clase'=>'menu-sage','tag'=>'Veggie'],
  ['id'=>'a5','nombre'=>'Chicharrón de Cerdo','desc'=>'Chicharrón crujiente estilo paceño con mote, llajua casera, ensalada de cebolla y maíz.','precio'=>52,'calorias'=>'~820 kcal','icono'=>'bi-award','clase'=>'menu-terracotta','tag'=>'Clásico'],
  ['id'=>'a6','nombre'=>'Lomo Saltado Altiplánico','desc'=>'Lomo de res salteado con tomate, cebolla, locoto, papas fritas y arroz. Versión local del clásico.','precio'=>58,'calorias'=>'~740 kcal','icono'=>'bi-lightning','clase'=>'menu-orange','tag'=>'Especial'],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Menú de Comidas — SGAT Urmiri</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,400&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
  <style>
    :root {
      --teal:#1a6b4a; --teal-dark:#0d3d28; --gold:#c9a84c; --cream:#f7f3ee;
    }
    body { font-family:'Jost',sans-serif; background:var(--cream); }

    /* ── Hero ── */
    .menu-hero {
      background: linear-gradient(160deg, #2c1a0d 0%, #5c3820 40%, #8b5a2b 100%);
      padding: 5rem 0 4rem; position: relative; overflow: hidden;
    }
    .menu-hero::after {
      content:''; position:absolute; bottom:0; left:0; right:0; height:80px;
      background: linear-gradient(to top, var(--cream), transparent);
    }
    .menu-hero-content { position:relative; z-index:2; }
    .menu-hero h1 {
      font-family:'Cormorant Garamond',serif; font-size:clamp(3rem,7vw,5rem);
      font-weight:300; color:#fff; line-height:1.05;
    }
    .menu-hero h1 em { font-style:italic; color:var(--gold); }
    .hero-deco { width:50px; height:1px; background:var(--gold); margin:1.25rem 0; }
    .hero-sub { color:rgba(255,255,255,.65); font-size:1rem; font-weight:300; max-width:480px; }

    /* ── Tabs ── */
    .menu-tabs-wrap {
      background:#fff; border-bottom:1px solid #e8e0d5;
      position:sticky; top:66px; z-index:100; padding:0;
    }
    .menu-tab {
      display:inline-flex; align-items:center; gap:0.5rem;
      padding:1.2rem 2rem; border:none; background:none;
      font-size:0.85rem; font-weight:600; letter-spacing:0.06em;
      text-transform:uppercase; color:#999; cursor:pointer;
      border-bottom:2px solid transparent; transition:all .25s; text-decoration:none;
    }
    .menu-tab:hover { color:var(--teal); }
    .menu-tab.active { color:var(--teal); border-bottom-color:var(--gold); }
    .menu-tab i { font-size:1.1rem; }

    /* ── Carrito flotante ── */
    .cart-bar {
      position:fixed; bottom:0; left:0; right:0; z-index:500;
      background:var(--teal-dark); color:#fff;
      padding:1rem 0; transform:translateY(100%);
      transition:transform .35s cubic-bezier(.25,.46,.45,.94);
      border-top:2px solid var(--gold);
    }
    .cart-bar.visible { transform:translateY(0); }
    .cart-bar-inner {
      display:flex; align-items:center; justify-content:space-between; gap:1rem; flex-wrap:wrap;
    }
    .cart-items-list { display:flex; gap:0.75rem; flex-wrap:wrap; }
    .cart-chip {
      background:rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.2);
      border-radius:100px; padding:.25rem .75rem;
      font-size:.78rem; display:flex; align-items:center; gap:.4rem;
    }
    .cart-chip .remove-item { cursor:pointer; opacity:.7; }
    .cart-chip .remove-item:hover { opacity:1; }
    .cart-total-wrap { text-align:right; flex-shrink:0; }
    .cart-total-label { font-size:.68rem; letter-spacing:.1em; text-transform:uppercase; opacity:.6; }
    .cart-total-amount {
      font-family:'Cormorant Garamond',serif; font-size:2rem; font-weight:600;
      color:var(--gold); line-height:1;
    }
    .btn-proceed {
      display:inline-flex; align-items:center; gap:.5rem;
      padding:.7rem 1.8rem; background:var(--gold); color:#1a1a00;
      font-weight:700; font-size:.82rem; letter-spacing:.06em; text-transform:uppercase;
      border-radius:4px; border:none; cursor:pointer; text-decoration:none;
      transition:all .25s; flex-shrink:0;
    }
    .btn-proceed:hover { background:#e0ba55; transform:translateY(-1px); }

    /* ── Sección ── */
    .menu-section { padding:3.5rem 0 2rem; }
    .section-head { margin-bottom:2.5rem; }
    .section-label { font-size:.68rem; letter-spacing:.25em; text-transform:uppercase; color:var(--gold); font-weight:600; }
    .section-title { font-family:'Cormorant Garamond',serif; font-size:2.5rem; font-weight:300; color:#1a1a1a; }
    .section-deco { width:40px; height:1px; background:var(--gold); margin:.75rem 0 0; }

    /* ── Tarjetas de menú ── */
    .menu-card {
      background:#fff; border-radius:8px; overflow:hidden;
      box-shadow:0 2px 16px rgba(0,0,0,.07);
      transition:transform .35s ease, box-shadow .35s ease, outline .15s;
      cursor:pointer; position:relative;
      outline:2px solid transparent; outline-offset:2px;
    }
    .menu-card:hover { transform:translateY(-5px); box-shadow:0 12px 36px rgba(0,0,0,.13); }
    .menu-card.selected {
      outline:2px solid var(--gold); box-shadow:0 8px 28px rgba(201,168,76,.25);
    }
    .menu-card.selected::after {
      content:''; position:absolute; inset:0;
      background:rgba(201,168,76,.06); pointer-events:none;
    }

    /* Imagen/visual de la card */
    .menu-card-visual {
      height:180px; position:relative; overflow:hidden;
    }
    .menu-visual-bg {
      width:100%; height:100%; display:flex; flex-direction:column;
      align-items:center; justify-content:center; gap:.75rem;
      transition:transform .5s ease;
    }
    .menu-card:hover .menu-visual-bg { transform:scale(1.05); }
    .menu-visual-bg i { font-size:3.5rem; color:rgba(255,255,255,.75); }
    .menu-visual-bg span {
      font-family:'Cormorant Garamond',serif; font-size:1.2rem; color:rgba(255,255,255,.9);
    }

    /* Colores por tipo */
    .menu-amber     { background:linear-gradient(135deg,#b5621a,#d4832e); }
    .menu-orange    { background:linear-gradient(135deg,#9e3d10,#c4602a); }
    .menu-terracotta{ background:linear-gradient(135deg,#8b3a2a,#b05c3e); }
    .menu-sage      { background:linear-gradient(135deg,#2e6b3e,#4a9e5c); }
    .menu-teal      { background:linear-gradient(135deg,#1a6b4a,#2e9e6f); }
    .menu-warm      { background:linear-gradient(135deg,#6b4a1a,#9e7a2e); }

    /* Tag */
    .menu-tag {
      position:absolute; top:12px; left:12px;
      background:rgba(255,255,255,.92); backdrop-filter:blur(8px);
      border-radius:100px; padding:.25rem .75rem;
      font-size:.68rem; font-weight:700; color:#333; letter-spacing:.04em;
    }

    /* Check seleccionado */
    .menu-check {
      position:absolute; top:12px; right:12px;
      width:28px; height:28px; border-radius:50%;
      background:var(--gold); display:flex; align-items:center; justify-content:center;
      opacity:0; transform:scale(.5); transition:all .25s cubic-bezier(.34,1.56,.64,1);
    }
    .menu-card.selected .menu-check { opacity:1; transform:scale(1); }
    .menu-check i { font-size:.85rem; color:#1a1a00; font-weight:bold; }

    /* Imagen real si existe */
    .menu-card-visual img {
      position:absolute; inset:0; width:100%; height:100%; object-fit:cover;
      opacity:0; transition:opacity .3s;
    }
    .menu-card-visual img.loaded { opacity:1; }
    .menu-card-visual img.loaded + .menu-visual-bg { opacity:0; }

    /* Cuerpo */
    .menu-card-body { padding:1.25rem 1.5rem 1.5rem; }
    .menu-item-name {
      font-family:'Cormorant Garamond',serif; font-size:1.25rem; font-weight:400;
      color:#1a1a1a; margin-bottom:.4rem; line-height:1.2;
    }
    .menu-item-desc { font-size:.82rem; color:#888; font-weight:300; line-height:1.6; margin-bottom:1rem; }
    .menu-card-foot { display:flex; align-items:center; justify-content:space-between; }
    .menu-price {
      font-family:'Cormorant Garamond',serif; font-size:1.6rem; font-weight:600; color:var(--teal);
    }
    .menu-price small { font-family:'Jost',sans-serif; font-size:.7rem; font-weight:300; color:#aaa; }
    .menu-kcal { font-size:.72rem; color:#bbb; font-weight:400; }

    /* Cantidad */
    .qty-ctrl {
      display:flex; align-items:center; gap:.5rem;
    }
    .qty-btn {
      width:28px; height:28px; border-radius:50%;
      border:1.5px solid #ddd; background:none; cursor:pointer;
      display:flex; align-items:center; justify-content:center;
      font-size:.9rem; color:#555; transition:all .2s;
    }
    .qty-btn:hover { border-color:var(--teal); color:var(--teal); }
    .qty-display { font-weight:600; font-size:.9rem; min-width:20px; text-align:center; }

    /* ── Resumen lateral ── */
    .order-summary {
      position:sticky; top:120px;
      background:#fff; border-radius:8px;
      box-shadow:0 4px 24px rgba(0,0,0,.09);
      overflow:hidden;
    }
    .order-summary-header {
      background:var(--teal-dark); padding:1.25rem 1.5rem;
    }
    .order-summary-header h5 {
      font-family:'Cormorant Garamond',serif; font-weight:400;
      color:#fff; font-size:1.2rem; margin:0;
    }
    .order-body { padding:1.5rem; }
    .order-line { display:flex; justify-content:space-between; align-items:flex-start; gap:.5rem; }
    .order-line-name { font-size:.85rem; color:#555; flex:1; }
    .order-line-price { font-size:.9rem; font-weight:600; color:var(--teal); white-space:nowrap; }
    .order-divider { border:none; border-top:1px dashed #e8e0d5; margin:1rem 0; }
    .order-total-line { display:flex; justify-content:space-between; align-items:center; }
    .order-total-label { font-size:.72rem; letter-spacing:.1em; text-transform:uppercase; color:#aaa; }
    .order-total-amount {
      font-family:'Cormorant Garamond',serif; font-size:2.2rem; font-weight:600; color:var(--teal-dark);
    }
    .btn-reserve-now {
      display:block; width:100%; padding:.85rem;
      background:var(--teal); color:#fff; border:none; border-radius:4px;
      font-size:.82rem; font-weight:600; letter-spacing:.06em; text-transform:uppercase;
      cursor:pointer; transition:all .25s; text-align:center; text-decoration:none;
    }
    .btn-reserve-now:hover { background:var(--teal-dark); color:#fff; }
    .empty-order {
      text-align:center; padding:2.5rem 1rem;
      color:#ccc;
    }
    .empty-order i { font-size:2.5rem; display:block; margin-bottom:.75rem; }

    /* ── Nota incluido ── */
    .included-note {
      background:#edf7f2; border-left:3px solid var(--teal);
      border-radius:0 4px 4px 0; padding:.75rem 1rem;
      font-size:.82rem; color:var(--teal); margin-top:1rem;
    }

    /* ── Animación entrada cards ── */
    .menu-card { animation:fadeUp .45s ease both; }
    .col-md-6:nth-child(1) .menu-card, .col-md-4:nth-child(1) .menu-card { animation-delay:.05s; }
    .col-md-6:nth-child(2) .menu-card, .col-md-4:nth-child(2) .menu-card { animation-delay:.12s; }
    .col-md-6:nth-child(3) .menu-card, .col-md-4:nth-child(3) .menu-card { animation-delay:.19s; }
    .col-md-6:nth-child(4) .menu-card, .col-md-4:nth-child(4) .menu-card { animation-delay:.26s; }
    .col-md-4:nth-child(5) .menu-card { animation-delay:.33s; }
    .col-md-4:nth-child(6) .menu-card { animation-delay:.40s; }
    @keyframes fadeUp {
      from { opacity:0; transform:translateY(20px); }
      to   { opacity:1; transform:translateY(0); }
    }
  </style>
</head>
<body>
<?php include __DIR__ . '/../partials/navbar.php'; ?>

<!-- Hero -->
<div class="menu-hero">
  <div class="container menu-hero-content">
    <div style="font-size:.68rem;letter-spacing:.3em;text-transform:uppercase;color:var(--gold);font-weight:600;margin-bottom:1rem">
      Complejo Urmiri · Gastronomía Boliviana
    </div>
    <h1>Menú &amp;<br><em>Comidas</em></h1>
    <div class="hero-deco"></div>
    <p class="hero-sub">Elige tus platos favoritos de desayuno y almuerzo. Los precios se agregarán automáticamente a tu reserva.</p>
  </div>
</div>

<!-- Tabs -->
<div class="menu-tabs-wrap">
  <div class="container">
    <button class="menu-tab active" onclick="showSection('desayuno',this)" id="tab-desayuno">
      <i class="bi bi-cup-hot"></i> Desayunos
    </button>
    <button class="menu-tab" onclick="showSection('almuerzo',this)" id="tab-almuerzo">
      <i class="bi bi-fork-knife"></i> Almuerzos
    </button>
  </div>
</div>

<div class="container py-4">
  <div class="row g-4">
    <!-- Cards de menú -->
    <div class="col-lg-8">

      <!-- Desayunos -->
      <div id="section-desayuno" class="menu-section">
        <div class="section-head">
          <div class="section-label">Primera comida del día</div>
          <h2 class="section-title">Desayunos</h2>
          <div class="section-deco"></div>
        </div>
        <div class="row g-3">
          <?php foreach ($desayunos as $item): ?>
          <div class="col-md-6">
            <div class="menu-card" id="card-<?= $item['id'] ?>" onclick="toggleItem('<?= $item['id'] ?>','<?= addslashes($item['nombre']) ?>',<?= $item['precio'] ?>,'desayuno')">
              <div class="menu-card-visual">
                <img src="<?= BASE_URL ?>uploads/menu/<?= $item['id'] ?>.jpg" alt="<?= $item['nombre'] ?>"
                     onload="this.classList.add('loaded')"
                     onerror="this.classList.remove('loaded');this.style.display='none';if(this.nextElementSibling)this.nextElementSibling.style.opacity='1'">
                <div class="menu-visual-bg <?= $item['clase'] ?>">
                  <i class="bi <?= $item['icono'] ?>"></i>
                  <span><?= $item['nombre'] ?></span>
                </div>
                <span class="menu-tag"><?= $item['tag'] ?></span>
                <div class="menu-check"><i class="bi bi-check-lg"></i></div>
              </div>
              <div class="menu-card-body">
                <h4 class="menu-item-name"><?= $item['nombre'] ?></h4>
                <p class="menu-item-desc"><?= $item['desc'] ?></p>
                <div class="menu-card-foot">
                  <div>
                    <div class="menu-price">Bs. <?= $item['precio'] ?><br><small>por persona</small></div>
                    <div class="menu-kcal"><i class="bi bi-fire"></i> <?= $item['calorias'] ?></div>
                  </div>
                  <div class="qty-ctrl" onclick="event.stopPropagation()">
                    <button class="qty-btn" onclick="changeQty('<?= $item['id'] ?>',-1,'<?= addslashes($item['nombre']) ?>',<?= $item['precio'] ?>,'desayuno')"><i class="bi bi-dash"></i></button>
                    <span class="qty-display" id="qty-<?= $item['id'] ?>">0</span>
                    <button class="qty-btn" onclick="changeQty('<?= $item['id'] ?>',1,'<?= addslashes($item['nombre']) ?>',<?= $item['precio'] ?>,'desayuno')"><i class="bi bi-plus"></i></button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Almuerzos -->
      <div id="section-almuerzo" class="menu-section" style="display:none">
        <div class="section-head">
          <div class="section-label">Cocina boliviana del altiplano</div>
          <h2 class="section-title">Almuerzos</h2>
          <div class="section-deco"></div>
        </div>
        <div class="row g-3">
          <?php foreach ($almuerzos as $item): ?>
          <div class="col-md-6">
            <div class="menu-card" id="card-<?= $item['id'] ?>" onclick="toggleItem('<?= $item['id'] ?>','<?= addslashes($item['nombre']) ?>',<?= $item['precio'] ?>,'almuerzo')">
              <div class="menu-card-visual">
                <img src="<?= BASE_URL ?>uploads/menu/<?= $item['id'] ?>.jpg" alt="<?= $item['nombre'] ?>"
                     onload="this.classList.add('loaded')"
                     onerror="this.classList.remove('loaded');this.style.display='none';if(this.nextElementSibling)this.nextElementSibling.style.opacity='1'">
                <div class="menu-visual-bg <?= $item['clase'] ?>">
                  <i class="bi <?= $item['icono'] ?>"></i>
                  <span><?= $item['nombre'] ?></span>
                </div>
                <span class="menu-tag"><?= $item['tag'] ?></span>
                <div class="menu-check"><i class="bi bi-check-lg"></i></div>
              </div>
              <div class="menu-card-body">
                <h4 class="menu-item-name"><?= $item['nombre'] ?></h4>
                <p class="menu-item-desc"><?= $item['desc'] ?></p>
                <div class="menu-card-foot">
                  <div>
                    <div class="menu-price">Bs. <?= $item['precio'] ?><br><small>por persona</small></div>
                    <div class="menu-kcal"><i class="bi bi-fire"></i> <?= $item['calorias'] ?></div>
                  </div>
                  <div class="qty-ctrl" onclick="event.stopPropagation()">
                    <button class="qty-btn" onclick="changeQty('<?= $item['id'] ?>',-1,'<?= addslashes($item['nombre']) ?>',<?= $item['precio'] ?>,'almuerzo')"><i class="bi bi-dash"></i></button>
                    <span class="qty-display" id="qty-<?= $item['id'] ?>">0</span>
                    <button class="qty-btn" onclick="changeQty('<?= $item['id'] ?>',1,'<?= addslashes($item['nombre']) ?>',<?= $item['precio'] ?>,'almuerzo')"><i class="bi bi-plus"></i></button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- Panel de pedido -->
    <div class="col-lg-4">
      <div class="order-summary">
        <div class="order-summary-header">
          <h5><i class="bi bi-receipt me-2"></i>Tu Pedido</h5>
        </div>
        <div class="order-body">
          <div id="orderEmpty" class="empty-order">
            <i class="bi bi-basket"></i>
            <p style="font-size:.85rem">Selecciona platos del menú para agregarlos aquí</p>
          </div>
          <div id="orderLines" style="display:none">
            <div id="orderItemsList"></div>
            <hr class="order-divider">
            <div class="order-total-line">
              <div>
                <div class="order-total-label">Total comidas</div>
                <div class="order-total-amount">Bs. <span id="orderTotal">0</span></div>
              </div>
              <i class="bi bi-fork-knife" style="font-size:2rem;color:#e8e0d5"></i>
            </div>
            <button onclick="abrirModalPago()" class="btn-reserve-now mt-3" style="border:none;width:100%;cursor:pointer;">
              <i class="bi bi-credit-card me-2"></i>Pagar Pedido
            </button>
            <button onclick="cancelarPedido()" class="btn-reserve-now mt-2"
              style="background:transparent;border:1.5px solid #e8e0d5;color:#666;font-size:.78rem;width:100%;cursor:pointer;">
              <i class="bi bi-x-circle me-1"></i> Cancelar Pedido
            </button>
          </div>
          <a href="<?= BASE_URL ?>views/piscinas/piscinas.php" class="btn-reserve-now mt-3"
             style="background:transparent;border:1.5px solid #e8e0d5;color:#666;font-size:.75rem">
            <i class="bi bi-water me-1"></i> Ver Piscinas Termales
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Barra carrito móvil -->
<div class="cart-bar" id="cartBar">
  <div class="container cart-bar-inner">
    <div>
      <div style="font-size:.68rem;letter-spacing:.1em;text-transform:uppercase;opacity:.6;margin-bottom:.25rem">Tu pedido</div>
      <div class="cart-items-list" id="cartItemsList"></div>
    </div>
    <div style="display:flex;align-items:center;gap:1.5rem">
      <div class="cart-total-wrap">
        <div class="cart-total-label">Total</div>
        <div class="cart-total-amount">Bs. <span id="cartTotal">0</span></div>
      </div>
      <a href="<?= BASE_URL ?>views/reservas/nueva_reserva.php" class="btn-proceed">
        <i class="bi bi-calendar-check"></i> Reservar
      </a>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
let order = {}; // { id: { nombre, precio, qty, tipo } }

function showSection(sec, btn) {
  document.getElementById('section-desayuno').style.display = sec==='desayuno' ? 'block' : 'none';
  document.getElementById('section-almuerzo').style.display = sec==='almuerzo' ? 'block' : 'none';
  document.querySelectorAll('.menu-tab').forEach(t => t.classList.remove('active'));
  btn.classList.add('active');
}

function toggleItem(id, nombre, precio, tipo) {
  if (order[id]) {
    changeQty(id, -order[id].qty, nombre, precio, tipo);
  } else {
    changeQty(id, 1, nombre, precio, tipo);
  }
}

function changeQty(id, delta, nombre, precio, tipo) {
  if (!order[id]) order[id] = { nombre, precio, qty: 0, tipo };
  order[id].qty = Math.max(0, order[id].qty + delta);
  document.getElementById('qty-' + id).textContent = order[id].qty;

  const card = document.getElementById('card-' + id);
  if (order[id].qty > 0) {
    card.classList.add('selected');
  } else {
    delete order[id];
    card.classList.remove('selected');
  }
  renderOrder();
}

function renderOrder() {
  const keys = Object.keys(order);
  const total = keys.reduce((s, k) => s + order[k].precio * order[k].qty, 0);

  // Panel lateral
  const empty = document.getElementById('orderEmpty');
  const lines = document.getElementById('orderLines');
  const list  = document.getElementById('orderItemsList');
  if (keys.length === 0) {
    empty.style.display = 'block'; lines.style.display = 'none';
  } else {
    empty.style.display = 'none'; lines.style.display = 'block';
    list.innerHTML = keys.map(k => `
      <div class="order-line mb-2">
        <span class="order-line-name">
          <span style="background:#f0ece6;border-radius:100px;padding:.15rem .5rem;font-size:.75rem;font-weight:700;margin-right:.4rem">${order[k].qty}×</span>
          ${order[k].nombre}
        </span>
        <span class="order-line-price">Bs.${order[k].precio * order[k].qty}</span>
      </div>`).join('');
    document.getElementById('orderTotal').textContent = total;
  }

  // Barra móvil
  const bar = document.getElementById('cartBar');
  if (keys.length > 0) {
    bar.classList.add('visible');
    document.getElementById('cartTotal').textContent = total;
    document.getElementById('cartItemsList').innerHTML = keys.map(k =>
      `<div class="cart-chip">${order[k].qty}× ${order[k].nombre.split(' ').slice(0,2).join(' ')}
         <i class="bi bi-x remove-item" onclick="changeQty('${k}',-${order[k].qty},'${order[k].nombre}',${order[k].precio},'${order[k].tipo}')"></i>
       </div>`).join('');
  } else {
    bar.classList.remove('visible');
  }

  // Guardar en sessionStorage para la reserva
  try { sessionStorage.setItem('menuOrder', JSON.stringify({ items: order, total })); } catch(e){}
}
</script>

<!-- ═══════════════════════════════════════════════════════ -->
<!-- MODAL DE PAGO DE PEDIDO                               -->
<!-- ═══════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalPago" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:12px;overflow:hidden;border:none;">

      <!-- Encabezado -->
      <div class="modal-header" style="background:var(--teal-dark);color:#fff;border:none;padding:1.25rem 1.5rem;">
        <h5 class="modal-title" style="font-family:'Cormorant Garamond',serif;font-size:1.4rem;font-weight:400;">
          <i class="bi bi-receipt me-2"></i> Pagar Pedido
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <!-- Resumen del pedido -->
      <div class="modal-body" style="padding:1.5rem;background:#f7f3ee;">

        <div id="modalResumenItems" style="margin-bottom:1rem;"></div>

        <div style="display:flex;justify-content:space-between;align-items:center;
                    background:#fff;border-radius:8px;padding:1rem 1.25rem;
                    border-left:4px solid var(--teal);margin-bottom:1.5rem;">
          <span style="font-size:.85rem;color:#666;letter-spacing:.05em;text-transform:uppercase;">Total a pagar</span>
          <span style="font-family:'Cormorant Garamond',serif;font-size:2rem;font-weight:600;color:var(--teal-dark);">
            Bs. <span id="modalTotalMonto">0</span>
          </span>
        </div>

        <!-- Tabs métodos de pago -->
        <div style="display:flex;gap:.75rem;margin-bottom:1.25rem;">
          <button onclick="mostrarMetodo('efectivo')" id="tabEfectivo"
            style="flex:1;padding:.6rem;border-radius:8px;border:2px solid var(--teal);
                   background:var(--teal);color:#fff;font-weight:600;font-size:.85rem;cursor:pointer;">
            <i class="bi bi-cash-coin me-1"></i> Efectivo
          </button>
          <button onclick="mostrarMetodo('qr')" id="tabQR"
            style="flex:1;padding:.6rem;border-radius:8px;border:2px solid #ddd;
                   background:#fff;color:#555;font-weight:600;font-size:.85rem;cursor:pointer;">
            <i class="bi bi-qr-code me-1"></i> QR / Yape
          </button>
        </div>

        <!-- Panel Efectivo -->
        <div id="panelEfectivo">
          <div style="background:#fff;border-radius:10px;padding:1.25rem;text-align:center;border:1px solid #e8e0d5;">
            <i class="bi bi-cash-stack" style="font-size:3rem;color:var(--teal);display:block;margin-bottom:.75rem;"></i>
            <p style="color:#555;font-size:.9rem;margin-bottom:1.25rem;">
              Acércate a recepción y presenta tu pedido. El personal lo procesará de inmediato.
            </p>
            <button onclick="confirmarPagoEfectivo()"
              style="width:100%;padding:.85rem;background:var(--teal-dark);color:#fff;border:none;
                     border-radius:8px;font-weight:700;font-size:.9rem;letter-spacing:.05em;cursor:pointer;">
              <i class="bi bi-check-circle me-2"></i> CONFIRMAR PAGO EN EFECTIVO
            </button>
          </div>
        </div>

        <!-- Panel QR -->
        <div id="panelQR" style="display:none;">
          <div style="background:#fff;border-radius:10px;padding:1.25rem;text-align:center;border:1px solid #e8e0d5;">
            <p style="color:#555;font-size:.85rem;margin-bottom:1rem;">
              Escanea el QR con tu app bancaria o Yape y envía el monto exacto:
            </p>
            <div style="display:inline-block;padding:.75rem;background:#fff;border:2px solid var(--teal);border-radius:10px;margin-bottom:.75rem;">
              <img src="<?= BASE_URL ?>assets/images/qr_yape.jpg" alt="QR Pago Yape"
                   style="width:180px;height:180px;object-fit:cover;border-radius:6px;">
            </div>
            <div style="background:#f0faf5;border-radius:8px;padding:.75rem;margin-bottom:1rem;">
              <span style="font-size:.8rem;color:#666;">Monto a transferir:</span><br>
              <span style="font-family:'Cormorant Garamond',serif;font-size:1.8rem;font-weight:700;color:var(--teal-dark);">
                Bs. <span id="qrMonto">0</span>
              </span>
            </div>
            <button onclick="confirmarPagoQR()"
              style="width:100%;padding:.85rem;background:var(--teal-dark);color:#fff;border:none;
                     border-radius:8px;font-weight:700;font-size:.9rem;letter-spacing:.05em;cursor:pointer;">
              <i class="bi bi-check2-all me-2"></i> YA REALICÉ EL PAGO
            </button>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<!-- MODAL ÉXITO -->
<div class="modal fade" id="modalExito" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content" style="border-radius:16px;border:none;text-align:center;overflow:hidden;">
      <div class="modal-body" style="padding:2.5rem 2rem;background:#f7f3ee;">
        <div id="exitoIcono" style="font-size:4rem;margin-bottom:1rem;">✅</div>
        <h4 id="exitoTitulo" style="font-family:'Cormorant Garamond',serif;font-weight:400;color:var(--teal-dark);margin-bottom:.5rem;">
          ¡Pedido Confirmado!
        </h4>
        <p id="exitoMensaje" style="color:#777;font-size:.9rem;margin-bottom:1.5rem;"></p>
        <button onclick="cerrarExito()"
          style="width:100%;padding:.75rem;background:var(--teal-dark);color:#fff;border:none;
                 border-radius:8px;font-weight:700;cursor:pointer;">
          Aceptar
        </button>
      </div>
    </div>
  </div>
</div>

<script>
// ── Funciones del modal de pago ─────────────────────────────
function abrirModalPago() {
  const total = document.getElementById('orderTotal').textContent;
  document.getElementById('modalTotalMonto').textContent = total;
  document.getElementById('qrMonto').textContent = total;

  // Construir resumen
  let html = '<div style="margin-bottom:.75rem;">';
  for (const id in order) {
    const it = order[id];
    html += `<div style="display:flex;justify-content:space-between;padding:.4rem 0;border-bottom:1px solid #eee;font-size:.88rem;">
      <span>${it.qty}× ${it.nombre}</span>
      <span style="color:var(--teal-dark);font-weight:600;">Bs.${(it.precio*it.qty)}</span>
    </div>`;
  }
  html += '</div>';
  document.getElementById('modalResumenItems').innerHTML = html;

  mostrarMetodo('efectivo');
  new bootstrap.Modal(document.getElementById('modalPago')).show();
}

function mostrarMetodo(metodo) {
  const esEfectivo = metodo === 'efectivo';
  document.getElementById('panelEfectivo').style.display = esEfectivo ? 'block' : 'none';
  document.getElementById('panelQR').style.display = esEfectivo ? 'none' : 'block';

  const tE = document.getElementById('tabEfectivo');
  const tQ = document.getElementById('tabQR');
  if (esEfectivo) {
    tE.style.background = 'var(--teal)'; tE.style.color = '#fff'; tE.style.borderColor = 'var(--teal)';
    tQ.style.background = '#fff'; tQ.style.color = '#555'; tQ.style.borderColor = '#ddd';
  } else {
    tQ.style.background = 'var(--teal)'; tQ.style.color = '#fff'; tQ.style.borderColor = 'var(--teal)';
    tE.style.background = '#fff'; tE.style.color = '#555'; tE.style.borderColor = '#ddd';
  }
}

async function guardarPedido(metodo) {
  const items = Object.values(order).map(it => ({
    id: it.id, nombre: it.nombre, precio: it.precio, qty: it.qty, tipo: it.tipo
  }));
  const total = parseFloat(document.getElementById('orderTotal').textContent);
  try {
    await fetch('<?= BASE_URL ?>controllers/pedido_controller.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({ items, total, metodo })
    });
  } catch(e) { /* si falla, igual mostramos éxito al cliente */ }
}

function confirmarPagoEfectivo() {
  bootstrap.Modal.getInstance(document.getElementById('modalPago')).hide();
  guardarPedido('efectivo');
  mostrarExito('efectivo');
}

function confirmarPagoQR() {
  bootstrap.Modal.getInstance(document.getElementById('modalPago')).hide();
  guardarPedido('qr');
  mostrarExito('qr');
}

function mostrarExito(metodo) {
  const total = document.getElementById('orderTotal').textContent;
  const msg = metodo === 'efectivo'
    ? `Tu pedido por Bs.${total} fue registrado. Preséntate en recepción para pagar.`
    : `Tu pago de Bs.${total} por QR fue registrado. ¡Gracias!`;

  document.getElementById('exitoMensaje').textContent = msg;
  new bootstrap.Modal(document.getElementById('modalExito')).show();
}

function cerrarExito() {
  bootstrap.Modal.getInstance(document.getElementById('modalExito')).hide();
  cancelarPedido();
}

function cancelarPedido() {
  order = {};
  renderOrder();
  document.getElementById('cartBar').classList.remove('visible');
}
</script>
</body>
</html>
