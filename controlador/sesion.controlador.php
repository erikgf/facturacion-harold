<?php

require_once '../negocio/Sesion.class.php';
require_once '../util/funciones/Funciones.class.php';

$email      = $_POST["txtemail"];
$clave      = $_POST["txtclave"];

if(isset($_POST["chkrecordar"])){
    $recordar   = $_POST["chkrecordar"];
}else{
    $recordar   = "N";
}

$objSesion = new Sesion();
$objSesion->setEmail($email);
$objSesion->setClave($clave);
$objSesion->setRecordar($recordar);

try {
    $resultado = $objSesion->iniciarSesion();
} catch (Exception $exc) {
    //Imprimir el error
    Funciones::mensaje(
            $exc->getMessage(), 
            "e", 
            "../vista/index.php", 
            5);
}

switch ($resultado){
    case 1:
        header("location:../vista/principal.php");
        break;
    case 2:
        Funciones::mensaje(
                "El usuario se encuentra inactivo", 
                "a",
                "../vista/index.php",
                5);
        break;
    default:
        Funciones::mensaje(
                "El email del usuario o la contraseña son incorrectos", 
                "e",
                "../vista/index.php",
                3);
}