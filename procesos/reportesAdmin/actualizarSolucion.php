<?php
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['usuario']['id'])){
    echo json_encode(["error" => "Sesión no válida"]);
    exit();
}

if(!isset($_POST['idReporte'], $_POST['solucion'], $_POST['estatus'])){
    echo json_encode(["error" => "Datos incompletos"]);
    exit();
}

$datos = array(
    'idReporte' => $_POST['idReporte'],
    'solucion' => $_POST['solucion'],
    'estatus' => $_POST['estatus'],
    'idUsuario' => $_SESSION['usuario']['id']
);

include "../../clases/Reportes.php";
$Reportes = new Reportes();

$resultado = $Reportes->actualizarSolucion($datos);

echo json_encode(["success" => $resultado]);
?>