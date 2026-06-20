<?php
// views/partials/flash.php
$flash = getFlash();
if ($flash):
?>
<div class="alert alert-<?= $flash['tipo'] === 'error' ? 'danger' : $flash['tipo'] ?> alert-dismissible fade show" role="alert">
  <i class="bi bi-<?= $flash['tipo']==='success'?'check-circle':'exclamation-triangle' ?> me-2"></i>
  <?= sanitize($flash['msg']) ?>
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
