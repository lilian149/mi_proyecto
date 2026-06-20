<?php
// controllers/pedido_controller.php
// Guarda un pedido de comidas en la BD al confirmar pago
require_once __DIR__ . '/../config/helpers.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Método no permitido']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['items']) || !isset($data['total']) || empty($data['metodo'])) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Datos incompletos']);
    exit;
}

$db = getDB();

// Validar ítems mínimamente
$items  = $data['items'];
$total  = (float) $data['total'];
$metodo = in_array($data['metodo'], ['efectivo', 'qr']) ? $data['metodo'] : 'efectivo';
$userId = $_SESSION['usuario_id'] ?? null;

$st = $db->prepare("
    INSERT INTO pedidos (usuario_id, items, total, metodo_pago, estado)
    VALUES (:uid, :items, :total, :metodo, 'pagado')
");
$st->execute([
    ':uid'    => $userId,
    ':items'  => json_encode($items),
    ':total'  => $total,
    ':metodo' => $metodo,
]);

echo json_encode(['ok' => true, 'id' => $db->lastInsertId()]);
