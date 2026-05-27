require_once $_SERVER['DOCUMENT_ROOT'] . '/app/core/Database.php';

class Solicitud {

    public static function crear($data, $usuario) {
        $db = Database::getConnection();

        $stmt = $db->prepare("
            INSERT INTO solicitudes
            (nombre_perro, raza, edad, sexo, tamano, descripcion, motivo,
             id_usuario, nombre_usuario)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $data['nombre_perro'],
            $data['raza'],
            $data['edad'],
            $data['sexo'],
            $data['tamano'],
            $data['descripcion'],
            $data['motivo'],
            $usuario['id_usuario'],
            $usuario['nombre']
        ]);
    }

    public static function obtenerPendientes() {
        $db = Database::getConnection();
        return $db->query("SELECT * FROM solicitudes WHERE estado='pendiente'")
                  ->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function marcarAceptada($id) {
        $db = Database::getConnection();
        $db->prepare("UPDATE solicitudes SET estado='aceptada' WHERE id_solicitud=?")
           ->execute([$id]);
    }
}
