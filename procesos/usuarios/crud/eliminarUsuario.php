<?php

include "../../../clases/Usuarios.php";

if(isset($_POST['idUsuario']) && isset($_POST['idPersona'])){

    $Usuarios = new Usuarios();

    $datos = array(
        "idUsuario" => $_POST['idUsuario'],
        "idPersona" => $_POST['idPersona']
    );

    echo $Usuarios->eliminarUsuario($datos);

} else {
    echo "Error: datos incompletos";
}
?>