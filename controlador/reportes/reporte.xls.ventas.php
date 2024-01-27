<?php 

/** Incluye PHPExcel */
require_once '../../util/Classes/PHPExcel.php';
require_once '../../negocio/util/Funciones.php';           
require_once '../../datos/local_config.php';
require_once '../'.MODELO . "/Venta.clase.php";
require_once MODELO_WEBSERVICE;

$objPHPExcel = new PHPExcel();    
$objVenta = new Venta();


  $_GET['p_modo'] = 0;
  if ( isset($_GET['p_tipo'])) {  
    try {
        $tipo  = $_GET['p_tipo'];
        $f0 = isset($_GET['p_f0']) ? $_GET["p_f0"] : null;
        $f1 = isset($_GET['p_f1']) ? $_GET["p_f1"] : null;
        $codCliente  = isset($_GET['p_cl']) ? $_GET["p_cl"] : null;
        $codSucursal  = isset($_GET['p_su']) ? $_GET["p_su"] : null;
        $empresa = "Vaping Shop Perú";


        var_dump($f0,$f1, $tipo, $codSucursal, $codCliente); exit;
        $dataReporte = $objVenta->reporteGeneral($f0,$f1, $tipo, $codSucursal, $codCliente);

        $cabecera = $dataReporte["cabecera"];
        $detalle = $dataReporte["data"];

		$objPHPExcel->getProperties()->setCreator($empresa)
									 ->setTitle("Reporte Ventas")
									 ->setSubject("Reporte de Ventas");

		$objPHPExcel->setActiveSheetIndex(0);

		$tituloStyle = array('font' => array('bold' => true,'size' => 15),'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
		$subtituloStyle = array('font' => array('size' => 10),'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
		$fechaHoraStyle = array('font' => array('bold' => true, 'name' => 'Arial','size' => 8),'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT));

		$cadenaFecha = 'Reporte de';
		$cadenaFecha .= ($f1 == $f0) ? ' '.Funciones::fechear($f0) : 'l '.Funciones::fechear($f0).' al '.Funciones::fechear($f1);

		$objPHPExcel->getActiveSheet()
					->setCellValue('A1', $empresa)
					->mergeCells('A2:K2')
					->mergeCells('A3:K3')
					->setCellValue('D1', 'Fecha: '.date('d-m-Y').' Hora: '.date('H:i:s'))
					->mergeCells('D1:K1')
					->setCellValue('A2', 'REPORTE DE VENTAS')
					->setCellValue('A3', $cadenaFecha);

		$filaStart = 4;

		$objPHPExcel->getActiveSheet()			
					->setCellValue('A'.$filaStart, 'Total sin descuentos')
					->setCellValue('B'.$filaStart, !isset($cabecera["subtotal"]) ? "0.00" : $cabecera["subtotal"])
					->setCellValue('D'.$filaStart, 'Descuentos')
					->setCellValue('E'.$filaStart, !isset($cabecera["total_produccion"]) ? "0.00" : $cabecera["total_descuentos"])
					->setCellValue('F'.$filaStart, 'Importe Efectivo')
					->setCellValue('G'.$filaStart, !isset($cabecera["monto_efectivo"]) ? "0.00" : $cabecera["monto_efectivo"])
					->setCellValue('H'.$filaStart, 'Importe Tarjetas')
					->setCellValue('I'.$filaStart, !isset($cabecera["monto_tarjeta"]) ? "0.00" : $cabecera["monto_tarjeta"]);

		$objPHPExcel->getActiveSheet()->getStyle('D1')->applyFromArray($fechaHoraStyle);
		$objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($fechaHoraStyle);
		$objPHPExcel->getActiveSheet()->getStyle('A2:F2')->applyFromArray($tituloStyle);
		$objPHPExcel->getActiveSheet()->getStyle('A3:F3')->applyFromArray($subtituloStyle);

		/*Inicio tabla CABECERA: A6-D6*/
		$filaInit = $filaStart + 2;
		$filaI = $filaInit;

			$objPHPExcel->getActiveSheet()
					->setCellValue('A'.$filaI, 'Código')
					->setCellValue('B'.$filaI, 'Comprobante')
					->setCellValue('C'.$filaI, 'Voucher')
					->setCellValue('D'.$filaI, 'Cliente')
					->setCellValue('E'.$filaI, 'Monto Efectivo')
					->setCellValue('F'.$filaI, 'Monto Tarjeta')
					->setCellValue('G'.$filaI, 'Fecha Venta')
					->setCellValue('H'.$filaI, 'Subtotal')
					->setCellValue('I'.$filaI, 'Total descuentos')
					->setCellValue('J'.$filaI, 'Importe total')
					->setCellValue('K'.$filaI, 'Sucursal');
			$filaI++;

			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(16);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(16);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(35);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(16);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(16);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(16);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(16);
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(16);
			$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(16);
			$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(16);

			foreach ($cuerpo as $_ => $detalle) {
					$objPHPExcel->getActiveSheet()
						->setCellValue('A'.$filaI, $value["codigo"] )
						->setCellValue('B'.$filaI, $value["comprobante"] )
						->setCellValue('C'.$filaI, $value["voucher"])
						->setCellValue('D'.$filaI, $value["cliente"])
						->setCellValue('E'.$filaI, $value["monto_efectivo"])
						->setCellValue('F'.$filaI, $value["monto_tarjeta"])
						->setCellValue('G'.$filaI, $value["fecha_venta"])
						->setCellValue('H'.$filaI, $value["subtotal"])
						->setCellValue('I'.$filaI, $value["total_descuentos"])
						->setCellValue('J'.$filaI, $value["importe_total"])
						->setCellValue('K'.$filaI, $value["sucursal"]);

					$filaI++;
			}


		$headerTablaStyle = array('font' => array('bold' => true,),'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
		$objPHPExcel->getActiveSheet()->getStyle('A'.$filaInit.':G'.$filaInit)->applyFromArray($headerTablaStyle);
		$objPHPExcel->getActiveSheet()->setTitle('Reporte Ventas');
		 
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="reporte-ventas-'.time().'.xlsx"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		exit;

    } catch (Exception $exc) {
        $objWebServices = new WebServices(500, "ERROR", $exc->getMessage());
    }   
  } else {
        $objWebServices = new WebServices(400, "Faltan parámetros", "");
  }
    

