<?php 

/** Incluye PHPExcel */
require_once '../negocio/util/Funciones.php';           
require_once '../datos/local_config.php';
require_once MODELO . "/Transaccion.clase.php";
require_once MODELO . "/PrintPDF.clase.php";

$objTransaccion = new Transaccion();

  if ( isset($_GET['p_t'])) { //cod_transaccion  
    try {
        $codTransaccion  = $_GET['p_t'];
        $objTransaccion->setCodTransaccion($codTransaccion);
        $dataComprobante = $objTransaccion->obtenerComprobanteData();

        if ($dataComprobante["rpt"] == false){
        	print($dataComprobante["msj"]);
        	exit;
        }

        $dataComprobante = $dataComprobante["data"];
        $objPrintpdf = new PrintPDF();
       	echo $objPrintpdf->getComprobante($dataComprobante);
      //echo $objPrintpdf->getComprobante($dataComprobante);
    } catch (Exception $exc) {
    	Funciones::imprimeJSON(500, "ERROR",$exc->getMessage());
    }   
  } else {
  	Funciones::imprimeJSON(500, "Faltan parámetros","");
  }
    

