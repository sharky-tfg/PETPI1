<?php
require_once "/app/models/Perrito.php";
echo json_encode(Perrito::obtenerTodos());
