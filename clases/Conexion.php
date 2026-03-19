<?php
class Conexion {

    public static function conectar() {
        $servidor = "localhost";
        $usuario = "root";
        $password = "";
        $db = "helpdesk";

        $conexion = mysqli_connect($servidor, $usuario, $password, $db);

        if (!$conexion) {
            die("Error de conexión: " . mysqli_connect_error());
        }

        return $conexion;
    }
}