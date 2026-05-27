<?php
class Validator {
    public static function requerido($valor) {
        return !empty(trim($valor));
    }
}
