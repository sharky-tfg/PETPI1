<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/core/Database.php';

echo "<h1>TEST PROCESAR SOLICITUD</h1>";

$db = Database::getConnection();

echo "<h2>Conexion correcta</h2>";

//ver solicitudes
$stmt = $db->query("SELECT COUNT(*) as total FROM solicitudes WHERE estado = 'pendiente'");
$total = $stmt->fetch();
echo "<p>Solicitudes pendientes: " . $total['total'] . "</p>";

//ver estructura
echo "<h2>Estructura de solicitudes:</h2>";
$stmt = $db->query("DESCRIBE solicitudes");
$columnas = $stmt->fetchAll();
echo "<pre>";
print_r($columnas);
echo "</pre>";

//probar insert
echo "<h2>Prueba insert perritos:</h2>";
$stmt = $db->prepare("INSERT INTO perritos (nombre, raza) VALUES ('test', 'test')");
if($stmt->execute()){
    echo "Insert correcto";
} else {
    echo "Error en insert";
}
?>
