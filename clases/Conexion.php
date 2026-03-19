<?php
    class Conexion {
        public function conectar() {
            $servidor = "localhost";
            $usuario = "root";
            $password = "autodidacta";
            $db = "helpdesk";
            $conexion = mysqli_connect($servidor, $usuario, $password, $db);
            return $conexion;
        }
    }