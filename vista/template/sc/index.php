<?php	
	require_once("./src/autoload.php");


	
	$company = new \Sunat\Sunat( true, true );
	$ruc = $_GET["documento"];
	//$ruc = "20604522103";
	//$dni = "46856259";
	
	$search1 = $company->search( $ruc );
	//$search2 = $company->search( $dni );
	
	//var_dump($search1);
	//var_dump($search2);
	
	if( $search1->success == true )
	{
		$search1->result->RazonSocial;
	}
	
	/*if( $search2->success == true )
	{
		echo "Persona: " . $search1->result->RazonSocial;
	}*/
	
	// Mostrar en formato XML/JSON
	echo $search1->json();
	//echo $search1->xml('empresa');
	
?>

