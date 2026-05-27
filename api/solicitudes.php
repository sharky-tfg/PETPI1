<?php
require_once "/app/models/Solicitud.php";
Solicitud::crear($_POST['id_usuario'], $_POST['id_perro']);
