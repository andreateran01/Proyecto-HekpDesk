<?php
header('Content-Type: application/json');

if(!isset($_POST['idReporte'])){
    echo json_encode(["error" => "No se recibió idReporte"]);
    exit();
}

$idReporte = $_POST['idReporte'];

include "../../clases/Reportes.php";
$Reportes = new Reportes();
$solucion = $Reportes->obtenerSolucion($idReporte);

if($solucion){
    echo json_encode($solucion);
} else {
    echo json_encode(["error" => "Reporte no encontrado"]);
}
?>