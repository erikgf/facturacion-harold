<?php 

if (MODO_PRODUCCION == "1"){
    echo ' <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/js/bootstrap.min.js"></script>';
} else {
	echo ' <script src="../assets/js/bootstrap.min.js"></script>>';
}

?>
