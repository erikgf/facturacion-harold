<?php 

/** Incluye PHPExcel */
require_once '../util/Classes/PHPExcel.php';
require_once '../negocio/util/Funciones.php';           
require_once '../datos/local_config_web.php';
require_once MODELO . "/Venta.clase.php";

$objPHPExcel = new PHPExcel();    
$objVenta = new Venta();


  $_GET['p_modo'] = 0;
  if ( isset($_GET['p_tipo'])) {  
    try {
        $tipo  = $_GET['p_tipo'];
        $f0 = isset($_GET['p_f0']) ? $_GET["p_f0"] : null;
        $f1 = isset($_GET['p_f1']) ? $_GET["p_f1"] : null;
        $codSucursal  = isset($_GET['p_su']) ? $_GET["p_su"] : null;
        $empresa = NOMBRE_EMPRESA;

        $dataReporte = $objVenta->reporteGeneralComprobantes($f0,$f1, $tipo, $codSucursal);

        $detalle = $dataReporte["data"];

		$objPHPExcel->getProperties()->setCreator($empresa)
									 ->setTitle("Reporte Comprobantes")
									 ->setSubject("Reporte de Comprobantes");

		$objPHPExcel->setActiveSheetIndex(0);

		$tituloStyle = array('font' => array('bold' => true,'size' => 15),'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
		$subtituloStyle = array('font' => array('size' => 10),'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
		$fechaHoraStyle = array('font' => array('bold' => true, 'name' => 'Arial','size' => 8),'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT));

		if ($tipo == "false"){
			$cadenaFecha = 'Reporte de';
			$cadenaFecha .= ($f1 == $f0) ? ' '.Funciones::fechear($f0) : 'l '.Funciones::fechear($f0).' al '.Funciones::fechear($f1);
		} else {
			$cadenaFecha = "Todos los comprobantes";
		}

		$objPHPExcel->getActiveSheet()
					->setCellValue('A1', $empresa)
					->mergeCells('A2:M2')
					->mergeCells('A3:M3')
					->setCellValue('D1', 'Fecha: '.date('d-m-Y').' Hora: '.date('H:i:s'))
					->mergeCells('D1:M1')
					->setCellValue('A2', 'REPORTE DE COMPROBANTES')
					->setCellValue('A3', $cadenaFecha);

		$filaStart = 5;

		$sheet = $objPHPExcel->getActiveSheet();
		$sheet->getStyle('D1')->applyFromArray($fechaHoraStyle);
		$sheet->getStyle('A1')->applyFromArray($fechaHoraStyle);
		$sheet->getStyle('A2:M2')->applyFromArray($tituloStyle);
		$sheet->getStyle('A3:M3')->applyFromArray($subtituloStyle);
		/*

		$sheet->getStyle('B'.$filaStart)->getNumberFormat()->setFormatCode('#,##0.00');
		$sheet->getStyle('F'.$filaStart)->getNumberFormat()->setFormatCode('#,##0.00');
		$sheet->getStyle('H'.$filaStart)->getNumberFormat()->setFormatCode('#,##0.00');
		$sheet->getStyle('J'.$filaStart)->getNumberFormat()->setFormatCode('#,##0.00');
		*/
	
		$filaInit = $filaStart + 0;
		$filaI = $filaInit;

			$objPHPExcel->getActiveSheet()
					->setCellValue('A'.$filaI, 'Código')
					->setCellValue('B'.$filaI, 'Serie')
					->setCellValue('C'.$filaI, 'Correlativo')
					->setCellValue('D'.$filaI, 'Documento')
					->setCellValue('E'.$filaI, 'Cliente')
					->setCellValue('F'.$filaI, 'Fecha Venta')
					->setCellValue('G'.$filaI, 'Importe total Sin IGV')
					->setCellValue('H'.$filaI, 'IGV')
					->setCellValue('I'.$filaI, 'Importe total')
					->setCellValue('J'.$filaI, 'Fecha Envío')
					->setCellValue('K'.$filaI, 'CDR')
					->setCellValue('L'.$filaI, 'Hash CDR')
					->setCellValue('M'.$filaI, 'Sucursal');
			$filaI++;

			$primerFila = $filaI;

			$sheet->getColumnDimension('A')->setWidth(15);
			$sheet->getColumnDimension('B')->setWidth(13);
			$sheet->getColumnDimension('C')->setWidth(15);
			$sheet->getColumnDimension('D')->setWidth(18);
			$sheet->getColumnDimension('E')->setWidth(35);
			$sheet->getColumnDimension('F')->setWidth(15);
			$sheet->getColumnDimension('G')->setWidth(22);
			$sheet->getColumnDimension('H')->setWidth(15);
			$sheet->getColumnDimension('I')->setWidth(16);
			$sheet->getColumnDimension('J')->setWidth(16);
			$sheet->getColumnDimension('K')->setWidth(22);
			$sheet->getColumnDimension('L')->setWidth(16);
			$sheet->getColumnDimension('M')->setWidth(16);
	
			foreach ($detalle as $_ => $value) {
					$objPHPExcel->getActiveSheet()
						->setCellValue('A'.$filaI, $value["codigo"] )
						->setCellValue('B'.$filaI, $value["serie"] )
						->setCellValue('C'.$filaI, $value["correlativo"])
						->setCellValue('D'.$filaI, $value["numero_documento"])
						->setCellValue('E'.$filaI, strtoupper($value["cliente"]))
						->setCellValue('F'.$filaI, $value["fecha_venta"])
						->setCellValue('G'.$filaI, number_format($value["total_gravadas"],2))
						->setCellValue('H'.$filaI, number_format($value["sumatoria_igv"],2))
						->setCellValue('I'.$filaI, number_format($value["importe_total"],2))
						->setCellValue('J'.$filaI, $value["fecha_envio"])
						->setCellValue('K'.$filaI, $value["cdr"])
						->setCellValue('L'.$filaI, $value["hash_cdr"])
						->setCellValue('M'.$filaI, $value["sucursal"]);

					$filaI++;
			}

			$ultimaFila = $filaI;

			$sheet->getStyle('G'.$primerFila.':G'.$ultimaFila)->getNumberFormat()->setFormatCode('#,##0.00');
			$sheet->getStyle('H'.$primerFila.':H'.$ultimaFila)->getNumberFormat()->setFormatCode('#,##0.00');
			$sheet->getStyle('I'.$primerFila.':I'.$ultimaFila)->getNumberFormat()->setFormatCode('#,##0.00');

		$headerTablaStyle = array('font' => array('bold' => true,),'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
		$sheet->getStyle('A'.$filaInit.':M'.$filaInit)->applyFromArray($headerTablaStyle);
		$sheet->setTitle('Reporte Comprobante Ventas');
		 
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="reporte-comprobantes-'.time().'.xlsx"');
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
    	Funciones::imprimeJSON(500, "ERROR", $exc->getMessage());
    }   
  } else {
  	Funciones::imprimeJSON(500,  "Faltan parámetros", "");
  }
    

