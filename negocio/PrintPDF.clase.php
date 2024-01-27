<?php

require "../sistema_facturacion/plugins/dompdf/vendor/autoload.php";
use Dompdf\Dompdf;
include "DocumentosHtml.clase.php";

class PrintPDF {

	public function getComprobante($data) {
		/***** FACTURA: DATOS OBLIGATORIOS PARA EL CÓDIGO QR *****/
		/*RUC | TIPO DE DOCUMENTO | SERIE | NUMERO | MTO TOTAL IGV | MTO TOTAL DEL COMPROBANTE | FECHA DE EMISION |TIPO DE DOCUMENTO ADQUIRENTE | NUMERO DE DOCUMENTO ADQUIRENTE |*/
		$id = 0;
		
		$html_documentos = new DocumentosHtml();
		$html = $html_documentos->get_html_comprobante($data);
		return $html["html"];
		/*
		define("DOMPDF_ENABLE_REMOTE", true);
        $dompdf = new Dompdf();
		$dompdf->loadHtml($html['html']);
		$dompdf->setPaper('A4');
		$dompdf->render();
		$dompdf->stream($data["cabecera"]["serie"].'-'.$data["cabecera"]["correlativo"].".pdf");
		*/
	}

	public function getComprobantePDF($data, $rutaPDF, $hash_cpe) {
		/***** FACTURA: DATOS OBLIGATORIOS PARA EL CÓDIGO QR *****/
		/*RUC | TIPO DE DOCUMENTO | SERIE | NUMERO | MTO TOTAL IGV | MTO TOTAL DEL COMPROBANTE | FECHA DE EMISION |TIPO DE DOCUMENTO ADQUIRENTE | NUMERO DE DOCUMENTO ADQUIRENTE |*/
		$html_documentos = new DocumentosHtml();
		$html = $html_documentos->get_html_comprobante_pdf($data, $hash_cpe);
		define("DOMPDF_ENABLE_REMOTE", true);
        $dompdf = new Dompdf();
		$dompdf->loadHtml($html['html']);
		$dompdf->setPaper('A4');
		$dompdf->render();
		$output = $dompdf->output();
    	file_put_contents($rutaPDF, $output);
		//$dompdf->stream();
	}


	public function getCotizacion($data) {
		/***** FACTURA: DATOS OBLIGATORIOS PARA EL CÓDIGO QR *****/
		/*RUC | TIPO DE DOCUMENTO | SERIE | NUMERO | MTO TOTAL IGV | MTO TOTAL DEL COMPROBANTE | FECHA DE EMISION |TIPO DE DOCUMENTO ADQUIRENTE | NUMERO DE DOCUMENTO ADQUIRENTE |*/
		$id = 0;
		
		$html_documentos = new DocumentosHtml();
		$html = $html_documentos->get_html_cotizacion($data);
		return $html["html"];
		/*
		define("DOMPDF_ENABLE_REMOTE", true);
        $dompdf = new Dompdf();
		$dompdf->loadHtml($html['html']);
		$dompdf->setPaper('A4');
		$dompdf->render();
		$dompdf->stream($data["cabecera"]["serie"].'-'.$data["cabecera"]["correlativo"].".pdf");
		*/
	}
}
?>