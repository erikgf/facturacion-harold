



<?php 

if (MODO_PRODUCCION == "1"){
    echo '  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.19/css/dataTables.bootstrap.min.css" />
    		<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.dataTables.min.css" />
    		';
} else {
	echo '  <link rel="stylesheet" href="../assets/plugins/datatables/dataTables.bootstrap.css" />
 			<link rel="stylesheet" href="../assets/plugins/datatables/dataTables.responsive.css" />';
}

?>
