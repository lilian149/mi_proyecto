<?php
// views/admin/reportes.php
require_once __DIR__ . '/../../config/helpers.php';
requireAdmin();
$currentPage = 'reportes.php';
$db = getDB();

// ── Rango de fechas (default: últimos 30 días) ───────────────
$fechaFin   = $_GET['fecha_fin']   ?? date('Y-m-d');
$fechaIni   = $_GET['fecha_ini']   ?? date('Y-m-d', strtotime('-29 days'));

// ── Ingresos por habitaciones (reservas confirmadas) ─────────
$sqlHab = "
  SELECT DATE(r.created_at) AS dia,
         SUM(r.total) AS ingresos
  FROM reservas r
  WHERE r.estado IN ('confirmada','completada')
    AND DATE(r.created_at) BETWEEN :fi AND :ff
  GROUP BY dia
  ORDER BY dia";
$stHab = $db->prepare($sqlHab);
$stHab->execute([':fi' => $fechaIni, ':ff' => $fechaFin]);
$datosHab = $stHab->fetchAll();

// ── Ingresos por pedidos (menú) ──────────────────────────────
$sqlPed = "
  SELECT DATE(p.fecha) AS dia,
         SUM(p.total) AS ingresos
  FROM pedidos p
  WHERE p.estado = 'pagado'
    AND DATE(p.fecha) BETWEEN :fi AND :ff
  GROUP BY dia
  ORDER BY dia";
$stPed = $db->prepare($sqlPed);
$stPed->execute([':fi' => $fechaIni, ':ff' => $fechaFin]);
$datosPed = $stPed->fetchAll();

// ── Totales del período ──────────────────────────────────────
$totalHab = array_sum(array_column($datosHab, 'ingresos'));
$totalPed = array_sum(array_column($datosPed, 'ingresos'));
$totalGen  = $totalHab + $totalPed;

// ── Construir mapa de días para el gráfico ───────────────────
$diasRange = [];
$cur = new DateTime($fechaIni);
$end = new DateTime($fechaFin);
while ($cur <= $end) {
    $diasRange[] = $cur->format('Y-m-d');
    $cur->modify('+1 day');
}
$mapHab = array_column($datosHab, 'ingresos', 'dia');
$mapPed = array_column($datosPed, 'ingresos', 'dia');
$labelsJS   = json_encode(array_map(fn($d) => date('d/m', strtotime($d)), $diasRange));
$valoresHab = json_encode(array_map(fn($d) => (float)($mapHab[$d] ?? 0), $diasRange));
$valoresPed = json_encode(array_map(fn($d) => (float)($mapPed[$d] ?? 0), $diasRange));

// ── Últimos 10 pedidos ───────────────────────────────────────
$ultimosPedidos = $db->prepare("
  SELECT p.*, u.nombre_completo
  FROM pedidos p
  LEFT JOIN usuarios u ON u.id = p.usuario_id
  WHERE DATE(p.fecha) BETWEEN :fi AND :ff
  ORDER BY p.fecha DESC LIMIT 10");
$ultimosPedidos->execute([':fi' => $fechaIni, ':ff' => $fechaFin]);
$pedidos = $ultimosPedidos->fetchAll();

// ── Últimas 10 reservas ──────────────────────────────────────
$ultimasReservas = $db->prepare("
  SELECT r.*, u.nombre_completo, h.nombre AS habitacion
  FROM reservas r
  JOIN usuarios u ON u.id = r.usuario_id
  JOIN habitaciones h ON h.id = r.habitacion_id
  WHERE r.estado IN ('confirmada','completada')
    AND DATE(r.created_at) BETWEEN :fi AND :ff
  ORDER BY r.created_at DESC LIMIT 10");
$ultimasReservas->execute([':fi' => $fechaIni, ':ff' => $fechaFin]);
$reservas = $ultimasReservas->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Reportes — Admin SGAT Urmiri</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <style>
    :root {
      --green-dark: #1a3d2b;
      --green:      #2e6b47;
      --gold:       #c9a84c;
      --cream:      #f7f3ee;
      --terracotta: #b5624a;
    }
    body { background: var(--cream); }

    /* Layout admin */
    .admin-wrapper { display: flex; min-height: 100vh; }
    .main-content  { flex: 1; padding: 2rem; overflow-x: hidden; }

    /* Tarjetas KPI */
    .kpi-card {
      background: #fff;
      border-radius: 12px;
      padding: 1.5rem;
      box-shadow: 0 2px 12px rgba(0,0,0,.06);
      display: flex;
      align-items: center;
      gap: 1.25rem;
    }
    .kpi-icon {
      width: 56px; height: 56px;
      border-radius: 12px;
      display: flex; align-items: center; justify-content: center;
      font-size: 1.6rem; flex-shrink: 0;
    }
    .kpi-icon.hab  { background: #e8f5ed; color: var(--green); }
    .kpi-icon.ped  { background: #fdf3e7; color: #d97706; }
    .kpi-icon.tot  { background: #f0ebf8; color: #7c3aed; }
    .kpi-label { font-size: .75rem; color: #999; text-transform: uppercase; letter-spacing: .06em; }
    .kpi-amount {
      font-size: 1.75rem; font-weight: 700; color: var(--green-dark);
      font-family: 'Cormorant Garamond', serif;
    }

    /* Filtro de fechas */
    .filter-card {
      background: #fff;
      border-radius: 12px;
      padding: 1.25rem 1.5rem;
      box-shadow: 0 2px 12px rgba(0,0,0,.06);
      margin-bottom: 1.75rem;
    }

    /* Gráfico */
    .chart-card {
      background: #fff;
      border-radius: 12px;
      padding: 1.5rem;
      box-shadow: 0 2px 12px rgba(0,0,0,.06);
      margin-bottom: 1.75rem;
    }
    .chart-title {
      font-size: .8rem;
      text-transform: uppercase;
      letter-spacing: .1em;
      color: #999;
      margin-bottom: 1rem;
    }

    /* Tablas */
    .table-card {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 2px 12px rgba(0,0,0,.06);
      overflow: hidden;
      margin-bottom: 1.75rem;
    }
    .table-card-header {
      padding: 1rem 1.5rem;
      background: var(--green-dark);
      color: #fff;
      font-size: .85rem;
      font-weight: 600;
      letter-spacing: .05em;
    }
    .table th {
      font-size: .72rem;
      text-transform: uppercase;
      letter-spacing: .08em;
      color: #aaa;
      border-bottom: 1px solid #f0ebe5;
      padding: .75rem 1rem;
    }
    .table td {
      font-size: .85rem;
      padding: .7rem 1rem;
      border-bottom: 1px solid #f9f6f2;
      vertical-align: middle;
    }
    .badge-pagado    { background: #d1fae5; color: #065f46; border-radius: 100px; padding: .25rem .75rem; font-size: .72rem; font-weight: 600; }
    .badge-efectivo  { background: #fef3c7; color: #92400e; border-radius: 100px; padding: .25rem .75rem; font-size: .72rem; font-weight: 600; }
    .badge-qr        { background: #dbeafe; color: #1e40af; border-radius: 100px; padding: .25rem .75rem; font-size: .72rem; font-weight: 600; }

    /* Botón exportar */
    .btn-export {
      background: var(--green-dark);
      color: #fff;
      border: none;
      border-radius: 8px;
      padding: .5rem 1.25rem;
      font-size: .82rem;
      font-weight: 600;
      cursor: pointer;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: .4rem;
    }
    .btn-export:hover { background: var(--green); color: #fff; }
  </style>
</head>
<body>
<div class="admin-wrapper">
  <?php include __DIR__ . '/sidebar.php'; ?>

  <div class="main-content">

    <!-- Encabezado -->
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
      <div>
        <h2 style="font-family:'Cormorant Garamond',serif;font-weight:400;color:var(--green-dark);margin:0;">
          <i class="bi bi-bar-chart-line me-2"></i> Reportes de Ingresos
        </h2>
        <p style="color:#999;font-size:.85rem;margin:.25rem 0 0;">Solo visible para administradores · SGAT Urmiri</p>
      </div>
      <button onclick="window.print()" class="btn-export">
        <i class="bi bi-printer"></i> Imprimir Reporte
      </button>
    </div>

    <!-- Filtro de fechas -->
    <div class="filter-card">
      <form method="GET" class="row g-3 align-items-end">
        <div class="col-auto">
          <label class="form-label" style="font-size:.78rem;color:#777;text-transform:uppercase;letter-spacing:.06em;">Desde</label>
          <input type="date" name="fecha_ini" class="form-control form-control-sm" value="<?= $fechaIni ?>">
        </div>
        <div class="col-auto">
          <label class="form-label" style="font-size:.78rem;color:#777;text-transform:uppercase;letter-spacing:.06em;">Hasta</label>
          <input type="date" name="fecha_fin" class="form-control form-control-sm" value="<?= $fechaFin ?>">
        </div>
        <div class="col-auto">
          <button type="submit" class="btn-export">
            <i class="bi bi-funnel"></i> Filtrar
          </button>
        </div>
        <div class="col-auto">
          <a href="reportes.php" class="btn btn-sm btn-outline-secondary">Restablecer</a>
        </div>
      </form>
    </div>

    <!-- KPIs -->
    <div class="row g-3 mb-4">
      <div class="col-md-4">
        <div class="kpi-card">
          <div class="kpi-icon hab"><i class="bi bi-house-heart"></i></div>
          <div>
            <div class="kpi-label">Ingresos · Habitaciones</div>
            <div class="kpi-amount">Bs. <?= number_format($totalHab, 2) ?></div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="kpi-card">
          <div class="kpi-icon ped"><i class="bi bi-egg-fried"></i></div>
          <div>
            <div class="kpi-label">Ingresos · Comidas</div>
            <div class="kpi-amount">Bs. <?= number_format($totalPed, 2) ?></div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="kpi-card">
          <div class="kpi-icon tot"><i class="bi bi-cash-stack"></i></div>
          <div>
            <div class="kpi-label">Total General</div>
            <div class="kpi-amount">Bs. <?= number_format($totalGen, 2) ?></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Gráfico de barras por día -->
    <div class="chart-card">
      <div class="chart-title"><i class="bi bi-graph-up me-1"></i> Ingresos diarios — <?= date('d/m/Y', strtotime($fechaIni)) ?> al <?= date('d/m/Y', strtotime($fechaFin)) ?></div>
      <canvas id="graficoIngresos" height="100"></canvas>
    </div>

    <!-- Tabla reservas -->
    <div class="table-card">
      <div class="table-card-header">
        <i class="bi bi-house-door me-2"></i> Últimas Reservas Confirmadas
      </div>
      <div class="table-responsive">
        <table class="table mb-0">
          <thead>
            <tr>
              <th>Fecha</th>
              <th>Huésped</th>
              <th>Habitación</th>
              <th>Ingreso</th>
              <th>Salida</th>
              <th style="text-align:right">Total</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($reservas)): ?>
            <tr><td colspan="6" style="text-align:center;color:#bbb;padding:2rem;">Sin reservas en este período</td></tr>
            <?php else: ?>
            <?php foreach ($reservas as $r): ?>
            <tr>
              <td><?= date('d/m/Y', strtotime($r['created_at'])) ?></td>
              <td><?= sanitize($r['nombre_completo']) ?></td>
              <td><?= sanitize($r['habitacion']) ?></td>
              <td><?= date('d/m', strtotime($r['fecha_ingreso'])) ?></td>
              <td><?= date('d/m', strtotime($r['fecha_salida'])) ?></td>
              <td style="text-align:right;font-weight:700;color:var(--green-dark);">
                Bs. <?= number_format($r['total'], 2) ?>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Tabla pedidos de comidas -->
    <div class="table-card">
      <div class="table-card-header">
        <i class="bi bi-receipt me-2"></i> Últimos Pedidos de Comidas
      </div>
      <div class="table-responsive">
        <table class="table mb-0">
          <thead>
            <tr>
              <th>Fecha / Hora</th>
              <th>Cliente</th>
              <th>Ítems</th>
              <th>Método</th>
              <th style="text-align:right">Total</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($pedidos)): ?>
            <tr><td colspan="5" style="text-align:center;color:#bbb;padding:2rem;">Sin pedidos en este período</td></tr>
            <?php else: ?>
            <?php foreach ($pedidos as $p):
              $items = json_decode($p['items'], true);
              $itemsStr = implode(', ', array_map(fn($i) => $i['qty'].'× '.$i['nombre'], $items));
            ?>
            <tr>
              <td><?= date('d/m/Y H:i', strtotime($p['fecha'])) ?></td>
              <td><?= $p['nombre_completo'] ? sanitize($p['nombre_completo']) : '<span style="color:#bbb">Invitado</span>' ?></td>
              <td style="font-size:.8rem;color:#777;max-width:220px;"><?= htmlspecialchars($itemsStr) ?></td>
              <td>
                <?php if ($p['metodo_pago'] === 'qr'): ?>
                  <span class="badge-qr"><i class="bi bi-qr-code me-1"></i>QR</span>
                <?php else: ?>
                  <span class="badge-efectivo"><i class="bi bi-cash me-1"></i>Efectivo</span>
                <?php endif; ?>
              </td>
              <td style="text-align:right;font-weight:700;color:var(--green-dark);">
                Bs. <?= number_format($p['total'], 2) ?>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div><!-- /main-content -->
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Gráfico de barras apiladas
const ctx = document.getElementById('graficoIngresos').getContext('2d');
new Chart(ctx, {
  type: 'bar',
  data: {
    labels: <?= $labelsJS ?>,
    datasets: [
      {
        label: 'Habitaciones (Bs.)',
        data: <?= $valoresHab ?>,
        backgroundColor: 'rgba(26, 61, 43, 0.8)',
        borderRadius: 4,
      },
      {
        label: 'Comidas (Bs.)',
        data: <?= $valoresPed ?>,
        backgroundColor: 'rgba(201, 168, 76, 0.8)',
        borderRadius: 4,
      }
    ]
  },
  options: {
    responsive: true,
    plugins: {
      legend: { position: 'top' },
      tooltip: {
        callbacks: {
          label: ctx => ' Bs. ' + ctx.parsed.y.toFixed(2)
        }
      }
    },
    scales: {
      x: { stacked: true, grid: { display: false } },
      y: {
        stacked: true,
        ticks: {
          callback: val => 'Bs. ' + val
        },
        beginAtZero: true
      }
    }
  }
});
</script>
</body>
</html>
