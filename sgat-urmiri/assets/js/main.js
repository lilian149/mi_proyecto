/**
 * SGAT-Urmiri — JavaScript principal
 */

// ── Disponibilidad en tiempo real ────────────────────────
function verificarDisponibilidad() {
  const habitacionId  = document.getElementById('habitacion_id')?.value;
  const fechaIngreso  = document.getElementById('fecha_ingreso')?.value;
  const fechaSalida   = document.getElementById('fecha_salida')?.value;
  const personas      = document.getElementById('cantidad_personas')?.value || 1;

  if (!habitacionId || !fechaIngreso || !fechaSalida) return;

  const baseUrl = document.getElementById('baseUrl')?.value || '/sgat-urmiri/';

  fetch(`${baseUrl}controllers/reserva_controller.php?action=verificar_disponibilidad`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `habitacion_id=${habitacionId}&fecha_ingreso=${fechaIngreso}&fecha_salida=${fechaSalida}`
  })
  .then(r => r.json())
  .then(data => {
    const statusEl  = document.getElementById('statusDisponibilidad');
    const resumen   = document.getElementById('resumenReserva');
    const btnReservar = document.getElementById('btnReservar');

    if (data.disponible) {
      // Calcular noches
      const ing = new Date(fechaIngreso);
      const sal = new Date(fechaSalida);
      const noches = Math.round((sal - ing) / (1000 * 60 * 60 * 24));

      if (noches <= 0) {
        statusEl.innerHTML = `<div class="alert alert-warning py-2 mb-0">
          <i class="bi bi-exclamation-triangle"></i> La fecha de salida debe ser posterior al ingreso.</div>`;
        resumen.style.display = 'none';
        if (btnReservar) btnReservar.disabled = true;
        return;
      }

      const precioPorNoche = parseFloat(data.precio_noche);
      const total = noches * precioPorNoche;

      statusEl.innerHTML = `<div class="alert alert-success py-2 mb-0">
        <i class="bi bi-check-circle"></i> ✅ <strong>Disponible</strong> para las fechas seleccionadas.</div>`;

      document.getElementById('numNoches').textContent  = noches;
      document.getElementById('precioNoche').textContent = 'Bs. ' + precioPorNoche.toFixed(2);
      document.getElementById('totalCalc').textContent  = 'Bs. ' + total.toFixed(2);
      document.getElementById('hidden_noches').value    = noches;
      document.getElementById('hidden_total').value     = total.toFixed(2);
      document.getElementById('hidden_precio').value    = precioPorNoche.toFixed(2);

      resumen.style.display = 'block';
      if (btnReservar) btnReservar.disabled = false;

    } else {
      statusEl.innerHTML = `<div class="alert alert-danger py-2 mb-0">
        <i class="bi bi-x-circle"></i> ❌ <strong>No disponible</strong> — La habitación ya está reservada en esas fechas.</div>`;
      resumen.style.display = 'none';
      if (btnReservar) btnReservar.disabled = true;
    }
  })
  .catch(() => {
    document.getElementById('statusDisponibilidad').innerHTML =
      `<div class="alert alert-warning py-2 mb-0"><i class="bi bi-wifi-off"></i> Error de conexión.</div>`;
  });
}

// ── Validación de fechas mínimas ─────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  const hoy = new Date().toISOString().split('T')[0];

  const ing = document.getElementById('fecha_ingreso');
  const sal = document.getElementById('fecha_salida');

  if (ing) {
    ing.min = hoy;
    ing.addEventListener('change', () => {
      const minSal = new Date(ing.value);
      minSal.setDate(minSal.getDate() + 1);
      if (sal) sal.min = minSal.toISOString().split('T')[0];
      verificarDisponibilidad();
    });
  }
  if (sal) sal.addEventListener('change', verificarDisponibilidad);

  const habSelect = document.getElementById('habitacion_id');
  if (habSelect) habSelect.addEventListener('change', verificarDisponibilidad);
});

// ── Confirmación de eliminación ──────────────────────────
function confirmarEliminar(url, nombre) {
  if (confirm(`¿Estás seguro de eliminar "${nombre}"? Esta acción no se puede deshacer.`)) {
    window.location.href = url;
  }
}

// ── Preview de imagen ────────────────────────────────────
const inputImg = document.getElementById('imagen');
if (inputImg) {
  inputImg.addEventListener('change', function () {
    const preview = document.getElementById('imgPreview');
    if (preview && this.files[0]) {
      preview.src = URL.createObjectURL(this.files[0]);
      preview.style.display = 'block';
    }
  });
}
