<?php 

/** Incluye PHPExcel */
require_once '../util/Classes/PHPExcel.php';
require_once '../negocio/util/Funciones.php';           
require_once '../datos/local_config_web.php';
require_once MODELO . "/Almacen.clase.php";

$objPHPExcel = new PHPExcel();    
$objAlmacen = new Almacen();

  if ( isset($_GET['p_su'])) {  
    try {
        $codSucursal  = isset($_GET['p_su']) ? $_GET["p_su"] : null;
        $empresa = NOMBRE_EMPRESA;

        $dataReporte = $objAlmacen->reporteAlmacenStock($codSucursal);

        $data = $dataReporte["data"];

		$objPHPExcel->getProperties()->setCreator($empresa)
									 ->setTitle("Reporte de Stock")
									 ->setSubject("Reporte de Stock Productos");

		$objPHPExcel->setActiveSheetIndex(0);

		$tituloStyle = array('font' => array('bold' => true,'size' => 15),'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
		$subtituloStyle = array('font' => array('size' => 10),'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
		$fechaHoraStyle = array('font' => array('bold' => true, 'name' => 'Arial','size' => 8),'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT));

		$objPHPExcel->getActiveSheet()
					->setCellValue('A1', $empresa)
					->mergeCells('A2:F2')
					->mergeCells('A3:F3')
					->setCellValue('D1', 'Fecha: '.date('d-m-Y').' Hora: '.date('H:i:s'))
					->mergeCells('D1:F1')
					->setCellValue('A2', 'REPORTE DE STOCK PRODUCTOS');

		$filaStart = 3;

		$sheet = $objPHPExcel->getActiveSheet();

		$sheet->getStyle('D1')->applyFromArray($fechaHoraStyle);
		$sheet->getStyle('A1')->applyFromArray($fechaHoraStyle);
		$sheet->getStyle('A2:F2')->applyFromArray($tituloStyle);
		$sheet->getStyle('A3:F3')->applyFromArray($subtituloStyle);

		/*Inicio tabla CABECERA: A6-D6*/
		$filaInit = $filaStart + 2;
		$filaI = $filaInit;

			$objPHPExcel->getActiveSheet()
					->setCellValue('A'.$filaI, 'Cod. Prod.')
					->setCellValue('B'.$filaI, 'Producto')
					->setCellValue('C'.$filaI, 'Categoría Producto')
					->setCellValue('D'.$filaI, 'Tipo Categoría')
					->setCellValue('E'.$filaI, 'Stock')
					->setCellValue('F'.$filaI, 'Sucursal');
			$filaI++;

			$primerFila = $filaI;

			$sheet->getColumnDimension('A')->setWidth(15);
			$sheet->getColumnDimension('B')->setWidth(50);
			$sheet->getColumnDimension('C')->setWidth(33);
			$sheet->getColumnDimension('D')->setWidth(20);
			$sheet->getColumnDimension('E')->setWidth(10);
			$sheet->getColumnDimension('F')->setWidth(20);
		
			foreach ($data as $_ => $value) {
					$objPHPExcel->getActiveSheet()
						->setCellValue('A'.$filaI, $value["codigo_producto"] )
						->setCellValue('B'.$filaI, $value["producto"] )
						->setCellValue('C'.$filaI, $value["categoria"])
						->setCellValue('D'.$filaI, $value["tipo"])
						->setCellValue('E'.$filaI, $value["stock"])
						->setCellValue('F'.$filaI, $value["sucursal"]);

					$filaI++;
			}

		$ultimaFila = $filaI;

		$headerTablaStyle = array('font' => array('bold' => true,),'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
		$sheet->getStyle('A'.$filaInit.':F'.$filaInit)->applyFromArray($headerTablaStyle);
		$sheet->setTitle('Reporte de Stock');
		 
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="reporte-stock-'.time().'.xlsx"');
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
    	var_dump($exc->getMessage());
    }   
  } else {
  	var_dump("Faltan parámetros");
  }
    

