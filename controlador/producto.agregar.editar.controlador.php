<?php

require_once '../datos/local_config.php';
require_once MODELO_FUNCIONES;
require_once '../negocio/Producto.clase.php';

if (!isset($_POST["p_array_datos"]) && !isset($_POST["p_tipo_accion"]) && !($_POST["p_imagen_check"])) {
    Funciones::imprimeJSON(500, "Faltan parametros", "");
    exit();
}


try {
    $obj = new Producto();
    $datosFormulario = array();
    parse_str($_POST["p_array_datos"], $datosFormulario);

    $tipo_accion = $_POST["p_tipo_accion"];
    $obj->setCodProducto($tipo_accion == "agregar" ? NULL : $_POST["p_cod_producto"]);

    $obj->setCodigo($datosFormulario["txtcodigo"]);
    $obj->setNombre($datosFormulario["txtnombre"]);
    $obj->setDescripcion($datosFormulario["txtdescripcion"] == "" ? NULL : $datosFormulario["txtdescripcion"]);    
    $obj->setPrecioUnitario($datosFormulario["txtpreciounitario"]);
    $obj->setCodUnidadMedida($datosFormulario["cbounidadmedida"]);
    $obj->setCodCategoriaProducto($datosFormulario["cbocategoria"]);
    $obj->setNumeroImagenPrincipal($datosFormulario["cboimagenprincipal"]);
    $obj->setCodMarca($datosFormulario["cbomarca"]);
    //$obj->setCodPresentacion($datosFormulario["cbopresentacion"]);
    $obj->setCodPresentacion($datosFormulario["cbopresentacion"] === "" ? 0 : $datosFormulario["cbopresentacion"]);

    //$obj->modoActualizarImg = $imagen_check;
    $imgUrl = array();

    if ($_FILES){
        $i = 0;
        foreach ($_FILES as $key => $file) {
            $i = substr($key,10);
            $tipo = $file["type"];
            if ($tipo != "image/png" && $tipo != "image/jpeg"){
                Funciones::imprimeJSON(200, "OK", array("rpt"=>false, "msj"=>"Imagen N° (".($i)."): Solo se aceptan formatos JPEG, JPG y PNG en imagenes de producto."));
                exit; 
            }      
            array_push($imgUrl, ["i"=>$i,"file"=>$file, "check"=>$_POST["p_imagen_check_".$i]]);
        }
    }

    for ($i=1; $i <= 10 ; $i++) { 
        $existeIMGCheck = isset($_POST["p_img_url_".$i]);
        if ($existeIMGCheck){
            array_push($imgUrl, ["i"=>$i,"file"=>NULL, "check"=>$_POST["p_imagen_check_".$i]]);
        }
    }

    if (count($imgUrl) <= 0){
        $obj->setImgUrl(NULL);
    } else {
        $obj->setImgUrl($imgUrl);
    }    

    Funciones::imprimeJSON(200, "OK", $tipo_accion == "agregar" ? $obj->agregar() : $obj->editar());
    
} catch (Exception $exc) {
    Funciones::imprimeJSON(500, $exc->getMessage(), "");
}




