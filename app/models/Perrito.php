<?php
require_once __DIR__ . '/core/Database.php';

class Perrito {
    private $db;
    private $conn;
    
    
     public $id_perro;
    public $nombre;
    public $raza;
    public $edad;
    public $sexo;
    public $tamano;
    public $descripcion;
    public $motivo;        
    public $imagen;
    public $estado;
    public $fecha_publicacion;
    public $id_admin;
    public $id_usuario;
    
    public function __construct() {
        $this->db = new Database();
        if(method_exists($this->db, 'getConnection')) {
            $this->conn = $this->db->getConnection();
        }
    }
    

//Obtiene todos los perritos disponibles

    public static function obtenerTodos() {
        $db = new Database();
        $perritos = [];
        
        try {
            if(method_exists($db, 'getConnection')) {
                $conn = $db->getConnection();
                $stmt = $conn->query("SELECT * FROM perritos ORDER BY fecha_publicacion DESC");
                $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                // Conexión directa como fallback
                $conn = new PDO("mysql:host=localhost;dbname=datospetadopta", "root", "");
                $stmt = $conn->query("SELECT * FROM perritos ORDER BY fecha_publicacion DESC");
                $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            
            // Convertir a objetos Perrito
            foreach($resultados as $row) {
                $perrito = new self();
                foreach($row as $key => $value) {
                    $perrito->$key = $value;
                }
                $perritos[] = $perrito;
            }
        } catch(Exception $e) {
            error_log("Error en obtenerTodos: " . $e->getMessage());
        }
        
        return $perritos;
    }
    

//Obtiene un perrito por su ID
 
    public static function obtenerPorId($id) {
        $db = new Database();
        try {
            if(method_exists($db, 'getConnection')) {
                $conn = $db->getConnection();
                $stmt = $conn->prepare("SELECT * FROM perritos WHERE id_perro = ?");
                $stmt->execute([$id]);
                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if($resultado) {
                    $perrito = new self();
                    foreach($resultado as $key => $value) {
                        $perrito->$key = $value;
                    }
                    return $perrito;
                }
            }
        } catch(Exception $e) {
            error_log("Error en obtenerPorId: " . $e->getMessage());
        }
        return null;
    }
    

//Obtiene todas las fotos de un perrito

    public function getFotos($perrito_id) {
        try {
            if(!$this->conn) {
                if(method_exists($this->db, 'getConnection')) {
                    $this->conn = $this->db->getConnection();
                } else {
                    return [];
                }
            }
            $stmt = $this->conn->prepare(
                "SELECT * FROM fotos_perritos 
                 WHERE perrito_id = ? 
                 ORDER BY es_principal DESC, orden ASC"
            );
            $stmt->execute([$perrito_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(Exception $e) {
            error_log("Error en getFotos: " . $e->getMessage());
            return [];
        }
    }
   
   
//Obtiene solo la foto principal

    public function getFotoPrincipal($perrito_id) {
        try {
            $fotos = $this->getFotos($perrito_id);
            foreach($fotos as $foto) {
                if($foto['es_principal'] == 1) {
                    return $foto['nombre_foto'];
                }
            }
        } catch(Exception $e) {
            error_log("Error en getFotoPrincipal: " . $e->getMessage());
        }
        return 'perrillono.png';
    }
    

//Verifica si tiene fotos adicionales

    public function tieneMasFotos($perrito_id) {
        return $this->contarFotosExtra($perrito_id) > 0;
    }
    
//Cuenta las fotos adicionales
    public function contarFotosExtra($perrito_id) {
        try {
            if(!$this->conn) {
                if(method_exists($this->db, 'getConnection')) {
                    $this->conn = $this->db->getConnection();
                } else {
                    return 0;
                }
            }
            $stmt = $this->conn->prepare(
                "SELECT COUNT(*) as total FROM fotos_perritos 
                 WHERE perrito_id = ? AND es_principal = 0"
            );
            $stmt->execute([$perrito_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch(Exception $e) {
            error_log("Error en contarFotosExtra: " . $e->getMessage());
            return 0;
        }
    }
    
//Obtiene todas las fotos no principales
     
    public function getFotosSecundarias($perrito_id) {
        try {
            if(!$this->conn) {
                if(method_exists($this->db, 'getConnection')) {
                    $this->conn = $this->db->getConnection();
                } else {
                    return [];
                }
            }
            $stmt = $this->conn->prepare(
                "SELECT * FROM fotos_perritos 
                 WHERE perrito_id = ? AND es_principal = 0 
                 ORDER BY orden ASC"
            );
            $stmt->execute([$perrito_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(Exception $e) {
            error_log("Error en getFotosSecundarias: " . $e->getMessage());
            return [];
        }
    }
}
?>
