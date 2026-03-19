<?php
include "Conexion.php";

class Reportes extends Conexion {

    
    public function agregarReporteCliente($datos) {
        $conexion = Conexion::conectar();
        $sql = "INSERT INTO t_reportes (id_usuario, id_equipo, descripcion_problema) 
                VALUES (?, ?, ?)";
        $query = $conexion->prepare($sql);
        $query->bind_param('iis', $datos['idUsuario'], $datos['idEquipo'], $datos['problema']);
        $respuesta = $query->execute();
        $query->close();
        return $respuesta;
    }

    
    public function eliminarReporteCliente($idReporte) {
        $conexion = Conexion::conectar();
        $sql = "DELETE FROM t_reportes WHERE id_reporte = ?";
        $query = $conexion->prepare($sql);
        $query->bind_param('i', $idReporte);
        $respuesta = $query->execute();
        $query->close();
        return $respuesta;
    }

    
    public function obtenerSolucion($idReporte) {
        $conexion = Conexion::conectar();
        $sql = "SELECT solucion_problema, estatus
                FROM t_reportes 
                WHERE id_reporte = ?";
        $query = $conexion->prepare($sql);
        $query->bind_param('i', $idReporte);
        $query->execute();
        $resultado = $query->get_result();
        $reporte = $resultado->fetch_assoc();

        if(!$reporte) return null; 

        return array(
            "idReporte" => $idReporte,
            "estatus" => $reporte['estatus'],
            "solucion" => $reporte['solucion_problema']
        );
    }

    
    public function actualizarSolucion($datos) {
        $conexion = Conexion::conectar();
        $sql = "UPDATE t_reportes 
                SET id_usuario_tecnico = ?, solucion_problema = ?, estatus = ? 
                WHERE id_reporte = ?";
        $query = $conexion->prepare($sql);
        
        $query->bind_param('issi', $datos['idUsuario'], $datos['solucion'], $datos['estatus'], $datos['idReporte']);
        $respuesta = $query->execute();
        if(!$respuesta){
            echo "Error: " . $query->error;
        }
        $query->close();
        return $respuesta;
    }

}
?>