

<?php 

if (MODO_PRODUCCION == "1"){
  	echo '  <link href="../assets/css/bootstrap.min.css" rel="stylesheet" />';
} else {
	echo '  <link href="../assets/css/bootstrap.min.css" rel="stylesheet" />
  			<!-- <link href="../assets/css/bootstrap-responsive.min.css" rel="stylesheet" />-->';
}

?>
