<?php
// cosita para mostrar errores

class ManejadorErrores {
    
    //muestra error y para todo
    public static function error($texto) {
        die($texto);
    }
    
    //muestra error con estilo feo pero funciona
    public static function mostrar($msg) {
        echo "<div style='background:#ffcccc; padding:10px; border:1px solid red; margin:10px;'>⚠️ $msg</div>";
        exit;
    }
}
?>
