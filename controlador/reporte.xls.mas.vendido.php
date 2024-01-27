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

        $dataReporte = $objVenta->reporteMasVendido($f0,$f1, $tipo, $codSucursal);

        $detalle = $dataReporte["data"];

		$objPHPExcel->getProperties()->setCreator($empresa)
									 ->setTitle("Reporte Mas Vendidos")
									 ->setSubject("Reporte de Mas Vendidos");

		$objPHPExcel->setActiveSheetIndex(0);

		$tituloStyle = array('font' => array('bold' => true,'size' => 15),'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
		$subtituloStyle = array('font' => array('size' => 10),'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
		$fechaHoraStyle = array('font' => array('bold' => true, 'name' => 'Arial','size' => 8),'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT));

		if ($tipo == "false"){
			$cadenaFecha = 'Reporte de';
			$cadenaFecha .= ($f1 == $f0) ? ' '.Funciones::fechear($f0) : 'l '.Funciones::fechear($f0).' al '.Funciones::fechear($f1);
		} else {
			$cadenaFecha = "Todas las ventas";
		}
		

		$objPHPExcel->getActiveSheet()
					->setCellValue('A1', $empresa)
					->mergeCells('A2:G2')
					->mergeCells('A3:G3')
					->setCellValue('E1', 'Fecha: '.date('d-m-Y').' Hora: '.date('H:i:s'))
					->mergeCells('E1:G1')
					->setCellValue('A2', 'REPORTE DE PRODUCTOS MAS VENDIDOS')
					->setCellValue('A3', $cadenaFecha);

		$filaInit = 5;

		$sheet = $objPHPExcel->getActiveSheet();

		$sheet->getStyle('E1')->applyFromArray($fechaHoraStyle);
		$sheet->getStyle('A1')->applyFromArray($fechaHoraStyle);
		$sheet->getStyle('A2:G2')->applyFromArray($tituloStyle);
		$sheet->getStyle('A3:G3')->applyFromArray($subtituloStyle);

		/*Inicio tabla CABECERA: A6-D6*/
		$filaI = $filaInit;

			$objPHPExcel->getActiveSheet()
					->setCellValue('A'.$filaI, 'Código')
					->setCellValue('B'.$filaI, 'Producto')
					->setCellValue('C'.$filaI, 'Monto Vendido')
					->setCellValue('D'.$filaI, 'Monto Gastado')
					->setCellValue('E'.$filaI, 'Utilidad')
					->setCellValue('F'.$filaI, 'Unidades Vendidas')
					->setCellValue('G'.$filaI, 'Sucursal');


			$filaI++;

			$primerFila = $filaI;
			$sheet->getColumnDimension('A')->setWidth(12);
			$sheet->getColumnDimension('B')->setWidth(30);
			$sheet->getColumnDimension('C')->setWidth(18);
			$sheet->getColumnDimension('D')->setWidth(18);
			$sheet->getColumnDimension('E')->setWidth(18);
			$sheet->getColumnDimension('F')->setWidth(18);
			$sheet->getColumnDimension('G')->setWidth(20);
	
			foreach ($detalle as $_ => $value) {
					$objPHPExcel->getActiveSheet()
						->setCellValue('A'.$filaI, $value["codigo_producto"] )
						->setCellValue('B'.$filaI, $value["producto"] )
						->setCellValue('C'.$filaI, number_format($value["monto_vendido"],2))
						->setCellValue('D'.$filaI, number_format($value["monto_gastado"],2))
						->setCellValue('E'.$filaI, number_format($value["utilidad"],2))
						->setCellValue('F'.$filaI, $value["unidades_vendidas"])
						->setCellValue('G'.$filaI, substr($value["sucursal"], 0, -1));

					$filaI++;
			}

			$ultimaFila = $filaI;

			$sheet->getStyle('C'.$primerFila.':C'.$ultimaFila)->getNumberFormat()->setFormatCode('#,##0.00');
			$sheet->getStyle('D'.$primerFila.':D'.$ultimaFila)->getNumberFormat()->setFormatCode('#,##0.00');
			$sheet->getStyle('E'.$primerFila.':E'.$ultimaFila)->getNumberFormat()->setFormatCode('#,##0.00');

		$headerTablaStyle = array('font' => array('bold' => true,),'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
		$sheet->getStyle('A'.$filaInit.':G'.$filaInit)->applyFromArray($headerTablaStyle);
		$sheet->setTitle('Reporte Más Vendido');
		 
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="reporte-masvendido-'.time().'.xlsx"');
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
    	Funciones::imprimeJSON(500, "ERROR", $exc->getMessage())
    }   
  } else {
  	Funciones::imprimeJSON(500,  "Faltan parámetros", "")
  }
    

