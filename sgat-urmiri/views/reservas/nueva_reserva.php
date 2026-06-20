<?php
// views/reservas/nueva_reserva.php
require_once __DIR__ . '/../../config/helpers.php';
requireLogin();

$db = getDB();
$habSeleccionada = (int)($_GET['hab'] ?? 0);

// Cargar habitaciones disponibles
$habitaciones = $db->query(
  "SELECT * FROM habitaciones WHERE estado != 'mantenimiento' ORDER BY nombre"
)->fetchAll();

// Si viene seleccionada, obtenerla
$habActual = null;
if ($habSeleccionada) {
  $st = $db->prepare("SELECT * FROM habitaciones WHERE id=?");
  $st->execute([$habSeleccionada]);
  $habActual = $st->fetch();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Nueva Reserva — SGAT Urmiri</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body>

<?php include __DIR__ . '/../partials/navbar.php'; ?>

<div style="background:linear-gradient(135deg,#1a6b4a,#2e9e6f);" class="py-4 text-white text-center">
  <h2 class="fw-bold mb-0"><i class="bi bi-calendar-plus"></i> Nueva Reserva</h2>
  <p class="small opacity-75 mb-0">Complejo Urmiri — Aguas Termales</p>
</div>

<div class="container py-4">
  <input type="hidden" id="baseUrl" value="<?= BASE_URL ?>">
  <?php include __DIR__ . '/../partials/flash.php'; ?>

  <div class="row g-4">
    <!-- Formulario -->
    <div class="col-lg-7">
      <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
          <h5 class="fw-bold mb-4"><i class="bi bi-house-heart"></i> Datos de la Reserva</h5>

          <form action="<?= BASE_URL ?>controllers/reserva_controller.php?action=crear"
                method="POST" id="formReserva">

            <!-- Selección de habitación -->
            <div class="mb-3">
              <label class="form-label fw-semibold">Habitación *</label>
              <select name="habitacion_id" id="habitacion_id" class="form-select" required>
                <option value="">-- Selecciona una habitación --</option>
                <?php foreach ($habitaciones as $h): ?>
                <option value="<?= $h['id'] ?>"
                        data-precio="<?= $h['precio_noche'] ?>"
                        <?= ($h['id'] == $habSeleccionada ? 'selected' : '') ?>>
                  <?= sanitize($h['nombre']) ?> —
                  <?= bs((float)$h['precio_noche']) ?>/noche
                  (<?= strtoupper($h['estado']) ?>)
                </option>
                <?php endforeach; ?>
              </select>
            </div>

            <?php if ($habActual): ?>
            <!-- Info habitación seleccionada -->
            <div class="alert alert-info py-2 small mb-3">
              <strong><?= sanitize($habActual['nombre']) ?></strong> —
              Capacidad: <?= $habActual['capacidad'] ?> personas |
              Precio: <?= bs((float)$habActual['precio_noche']) ?>/noche
            </div>
            <?php endif; ?>

            <!-- Fechas -->
            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold">Fecha de Ingreso *</label>
                <input type="date" name="fecha_ingreso" id="fecha_ingreso"
                       class="form-control" required>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Fecha de Salida *</label>
                <input type="date" name="fecha_salida" id="fecha_salida"
                       class="form-control" required>
              </div>
            </div>

            <!-- Personas -->
            <div class="mb-3">
              <label class="form-label fw-semibold">Cantidad de Personas *</label>
              <select name="cantidad_personas" id="cantidad_personas" class="form-select" required>
                <?php for ($i=1;$i<=10;$i++): ?>
                  <option value="<?= $i ?>"><?= $i ?> persona<?= $i>1?'s':'' ?></option>
                <?php endfor; ?>
              </select>
            </div>

            <!-- Observaciones -->
            <div class="mb-3">
              <label class="form-label fw-semibold">Observaciones</label>
              <textarea name="observaciones" class="form-control" rows="2"
                        placeholder="Alguna petición especial o comentario..."></textarea>
            </div>

            <!-- Estado de disponibilidad -->
            <div id="statusDisponibilidad" class="mb-3"></div>

            <!-- Campos ocultos calculados -->
            <input type="hidden" name="num_noches" id="hidden_noches" value="0">
            <input type="hidden" name="total" id="hidden_total" value="0">
            <input type="hidden" name="precio_noche" id="hidden_precio" value="0">

            <button type="submit" id="btnReservar" class="btn btn-primary-urmiri w-100 py-2 fw-bold" disabled>
              <i class="bi bi-check-circle"></i> Confirmar Reserva
            </button>
          </form>
        </div>
      </div>
    </div>

    <!-- Panel de resumen -->
    <div class="col-lg-5">
      <!-- Resumen de costo -->
      <div id="resumenReserva" class="card border-0 shadow-sm rounded-4 mb-3" style="display:none">
        <div class="card-body p-4">
          <h6 class="fw-bold mb-3"><i class="bi bi-receipt"></i> Resumen del Costo</h6>
          <table class="table table-borderless table-sm mb-0">
            <tr>
              <td class="text-muted">Precio por noche:</td>
              <td class="fw-semibold text-end" id="precioNoche">—</td>
            </tr>
            <tr>
              <td class="text-muted">Número de noches:</td>
              <td class="fw-semibold text-end" id="numNoches">—</td>
            </tr>
            <tr class="border-top">
              <td class="fw-bold fs-5">TOTAL:</td>
              <td class="text-end">
                <span class="total-amount" id="totalCalc">—</span>
              </td>
            </tr>
          </table>
          <p class="text-muted small mt-2 mb-0">
            <i class="bi bi-info-circle"></i> Precios en bolivianos (Bs.)
          </p>

          <!-- ── Método de Pago ── -->
          <hr class="my-3">
          <h6 class="fw-bold mb-3"><i class="bi bi-credit-card-2-front text-success"></i> Método de Pago</h6>
          <div class="d-flex gap-2 mb-3">
            <button type="button" class="btn btn-outline-success flex-fill py-2 fw-semibold metodo-pago-btn active"
                    id="btnEfectivo" onclick="seleccionarPago('efectivo')">
              <i class="bi bi-cash-coin fs-5 d-block mb-1"></i>
              Efectivo
            </button>
            <button type="button" class="btn btn-outline-purple flex-fill py-2 fw-semibold metodo-pago-btn"
                    id="btnQR" onclick="seleccionarPago('qr')">
              <i class="bi bi-qr-code fs-5 d-block mb-1"></i>
              Pagar con QR
            </button>
          </div>
          <input type="hidden" name="metodo_pago" id="metodo_pago" value="efectivo">

          <!-- Panel Efectivo -->
          <div id="panelEfectivo" class="alert py-2 mb-0" style="background:#f0fdf4;border:1px solid #bbf7d0;">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-cash-coin text-success fs-5"></i>
              <span class="small text-success fw-semibold">Pago en efectivo al momento del check-in (14:00 hrs).</span>
            </div>
          </div>

          <!-- Panel QR Yape -->
          <div id="panelQR" style="display:none;">
            <div class="text-center p-3 rounded-3" style="background:#f5f0ff;border:1px solid #d8b4fe;">
              <p class="small fw-semibold mb-2" style="color:#7c3aed;">
                <i class="bi bi-qr-code-scan me-1"></i>Escanea con tu app Yape para pagar
              </p>
              <img src="<?= BASE_URL ?>assets/images/qr_yape.jpg"
                   alt="QR Yape — Lilian Rocio Paxi Gorostiaga"
                   class="img-fluid rounded-3 shadow-sm"
                   style="max-width:200px;border:3px solid #a855f7;">
              <p class="small mt-2 mb-0 fw-semibold" style="color:#7c3aed;">Lilian Rocio Paxi Gorostiaga</p>
              <p class="small text-muted mb-0">Válido hasta el 01 jul. 2026</p>
              <div class="alert mt-2 py-1 mb-0 small" style="background:#ede9fe;color:#6d28d9;border:none;">
                <i class="bi bi-info-circle me-1"></i>Envía el comprobante al confirmar tu reserva.
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Información del complejo -->
      <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
          <h6 class="fw-bold mb-3"><i class="bi bi-info-circle text-success"></i> Info Importante</h6>
          <ul class="list-unstyled small text-muted">
            <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Check-in: 14:00 hrs</li>
            <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Check-out: 12:00 hrs</li>
            <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Pago al momento del check-in</li>
            <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Acceso a piscinas termales incluido</li>
            <li><i class="bi bi-check text-success me-2"></i>Se acepta cancelación con 24h de anticipación</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>assets/js/main.js"></script>

<style>
  .btn-outline-purple {
    color: #7c3aed;
    border-color: #a855f7;
    background: #fff;
  }
  .btn-outline-purple:hover, .btn-outline-purple.active {
    background: #7c3aed;
    border-color: #7c3aed;
    color: #fff;
  }
  .metodo-pago-btn {
    transition: all .2s ease;
  }
</style>

<script>
function seleccionarPago(metodo) {
  document.getElementById('metodo_pago').value = metodo;
  const btnEf  = document.getElementById('btnEfectivo');
  const btnQR  = document.getElementById('btnQR');
  const panEf  = document.getElementById('panelEfectivo');
  const panQR  = document.getElementById('panelQR');

  if (metodo === 'efectivo') {
    btnEf.classList.add('active');
    btnQR.classList.remove('active');
    panEf.style.display = 'block';
    panQR.style.display = 'none';
    // restaurar color verde activo
    btnEf.style.background = '#198754';
    btnEf.style.color = '#fff';
    btnEf.style.borderColor = '#198754';
    btnQR.style.background = '#fff';
    btnQR.style.color = '#7c3aed';
    btnQR.style.borderColor = '#a855f7';
  } else {
    btnQR.classList.add('active');
    btnEf.classList.remove('active');
    panQR.style.display = 'block';
    panEf.style.display = 'none';
    btnQR.style.background = '#7c3aed';
    btnQR.style.color = '#fff';
    btnQR.style.borderColor = '#7c3aed';
    btnEf.style.background = '#fff';
    btnEf.style.color = '#198754';
    btnEf.style.borderColor = '#198754';
  }
}

// Activar estilo inicial del botón Efectivo al cargar
document.addEventListener('DOMContentLoaded', () => {
  const btnEf = document.getElementById('btnEfectivo');
  if (btnEf) {
    btnEf.style.background = '#198754';
    btnEf.style.color = '#fff';
    btnEf.style.borderColor = '#198754';
  }
});
</script>
<?php if ($habSeleccionada): ?>
<script>
  // Auto-verificar si viene con habitación pre-seleccionada
  document.addEventListener('DOMContentLoaded', () => {
    // pequeño delay para que el JS de main.js ya esté listo
    setTimeout(() => {
      const sel = document.getElementById('habitacion_id');
      if (sel && sel.value) {
        // dispara change para activar lógica
      }
    }, 200);
  });
</script>
<?php endif; ?>
</body>
</html>
