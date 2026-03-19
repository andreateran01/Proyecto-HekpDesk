<?php

include "Conexion.php";

class Usuarios extends Conexion {

    public function loginUsuario($usuario, $password) {
        $conexion = Conexion::conectar();

        $sql = "SELECT * FROM t_usuarios 
                WHERE usuario = ? AND password = ?";
        $query = $conexion->prepare($sql);
        $query->bind_param("ss", $usuario, $password);
        $query->execute();
        $respuesta = $query->get_result();

        if ($respuesta->num_rows > 0) {
            $datosUsuario = $respuesta->fetch_array();
            if ($datosUsuario['activo'] == 1) {
                $_SESSION['usuario']['nombre'] = $datosUsuario['usuario'];
                $_SESSION['usuario']['id'] = $datosUsuario['id_usuario'];
                $_SESSION['usuario']['rol'] = $datosUsuario['id_rol'];
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public function agregaNuevoUsuario($datos) {
        $conexion = Conexion::conectar();
        $idPersona = self::agregarPersona($datos);

        if ($idPersona > 0) {
            $sql = "INSERT INTO t_usuarios (id_rol, id_persona, usuario, password, ubicacion) 
                    VALUES (?, ?, ?, ?, ?)";
            $query = $conexion->prepare($sql);
            $query->bind_param("iisss", 
                $datos['idRol'],
                $idPersona,
                $datos['usuario'],
                $datos['password'],
                $datos['ubicacion']
            );
            return $query->execute();
        } else {
            return 0;
        }
    }

    public static function agregarPersona($datos) {
        $conexion = Conexion::conectar();

        $sql = "INSERT INTO t_persona 
                (paterno, materno, nombre, fecha_nacimiento, sexo, telefono, correo) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $query = $conexion->prepare($sql);
        $query->bind_param("sssssss", 
            $datos['paterno'], 
            $datos['materno'],
            $datos['nombre'],
            $datos['fechaNacimiento'],
            $datos['sexo'],
            $datos['telefono'],
            $datos['correo']
        );

        $query->execute();
        $idPersona = $conexion->insert_id;
        $query->close();

        return $idPersona;
    }

    public function obtenerDatosUsuario($idUsuario) {
        $conexion = Conexion::conectar();

        $sql = "SELECT 
                    usuarios.id_usuario AS idUsuario,
                    usuarios.usuario AS nombreUsuario,
                    roles.nombre AS rol,
                    usuarios.id_rol AS idRol,
                    usuarios.ubicacion AS ubicacion,
                    usuarios.activo AS estatus,
                    usuarios.id_persona AS idPersona,
                    persona.nombre AS nombrePersona,
                    persona.paterno,
                    persona.materno,
                    persona.fecha_nacimiento AS fechaNacimiento,
                    persona.sexo,
                    persona.correo,
                    persona.telefono
                FROM t_usuarios AS usuarios
                INNER JOIN t_cat_roles AS roles 
                    ON usuarios.id_rol = roles.id_rol
                INNER JOIN t_persona AS persona 
                    ON usuarios.id_persona = persona.id_persona
                WHERE usuarios.id_usuario = ?";

        $query = $conexion->prepare($sql);
        $query->bind_param("i", $idUsuario);
        $query->execute();
        $resultado = $query->get_result();

        return $resultado->fetch_array();
    }

    public function actualizarUsuario($datos) {
        $conexion = Conexion::conectar();
        $exitoPersona = self::actualizarPersona($datos);

        if ($exitoPersona) {
            $sql = "UPDATE t_usuarios 
                    SET id_rol = ?, usuario = ?, ubicacion = ? 
                    WHERE id_usuario = ?";
            $query = $conexion->prepare($sql);
            $query->bind_param('issi', 
                $datos['idRol'],
                $datos['usuario'],
                $datos['ubicacion'],
                $datos['idUsuario']
            );
            return $query->execute();
        } else {
            return 0;
        }
    }

    public static function actualizarPersona($datos) {
        $conexion = Conexion::conectar();
        $idPersona = self::obtenerIdPersona($datos['idUsuario']);

        $sql = "UPDATE t_persona 
                SET paterno=?, materno=?, nombre=?, fecha_nacimiento=?, sexo=?, telefono=?, correo=? 
                WHERE id_persona=?";
        $query = $conexion->prepare($sql);
        $query->bind_param('sssssssi', 
            $datos['paterno'],
            $datos['materno'],
            $datos['nombre'],
            $datos['fechaNacimiento'],
            $datos['sexo'],
            $datos['telefono'],
            $datos['correo'],
            $idPersona
        );

        return $query->execute();
    }

    public static function obtenerIdPersona($idUsuario) {
        $conexion = Conexion::conectar();

        $sql = "SELECT persona.id_persona AS idPersona
                FROM t_usuarios AS usuarios
                INNER JOIN t_persona AS persona 
                    ON usuarios.id_persona = persona.id_persona
                WHERE usuarios.id_usuario = ?";

        $query = $conexion->prepare($sql);
        $query->bind_param("i", $idUsuario);
        $query->execute();
        $resultado = $query->get_result();

        return $resultado->fetch_array()['idPersona'];
    }

    public function resetPassword($datos) {
        $conexion = Conexion::conectar();

        $sql = "UPDATE t_usuarios SET password = ? WHERE id_usuario = ?";
        $query = $conexion->prepare($sql);
        $query->bind_param('si', $datos['password'], $datos['idUsuario']);

        return $query->execute();
    }

    public function cambioEstatusUsuario($idUsuario, $estatus) {
        $conexion = Conexion::conectar();
        $estatus = ($estatus == 1) ? 0 : 1;

        $sql = "UPDATE t_usuarios SET activo = ? WHERE id_usuario = ?";
        $query = $conexion->prepare($sql);
        $query->bind_param('ii', $estatus, $idUsuario);

        return $query->execute();
    }

    public static function buscarReportesUsuario($idUsuario) {
        $conexion = Conexion::conectar();

        $sql = "SELECT id_usuario FROM t_reportes WHERE id_usuario = ?";
        $query = $conexion->prepare($sql);
        $query->bind_param("i", $idUsuario);
        $query->execute();
        $resultado = $query->get_result();

        return ($resultado->num_rows > 0) ? 1 : 0;
    }

    public static function buscarAsignacionPersona($idPersona) {
        $conexion = Conexion::conectar();

        $sql = "SELECT id_persona FROM t_asignacion WHERE id_persona = ?";
        $query = $conexion->prepare($sql);
        $query->bind_param("i", $idPersona);
        $query->execute();
        $resultado = $query->get_result();

        return ($resultado->num_rows > 0) ? 1 : 0;
    }

    public function eliminarUsuario($datos) {
        $conexion = Conexion::conectar();

        $reportes = self::buscarReportesUsuario($datos['idUsuario']);
        $asignaciones = self::buscarAsignacionPersona($datos['idPersona']);

        if ($reportes == 0 && $asignaciones == 0) {
            $sql = "DELETE FROM t_usuarios WHERE id_usuario = ?";
            $query = $conexion->prepare($sql);
            $query->bind_param('i', $datos['idUsuario']);
            return $query->execute();
        } else {
            return 0;
        }
    }
}