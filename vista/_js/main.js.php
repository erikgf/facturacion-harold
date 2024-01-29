<script type="text/javascript" src="js/_env.js"></script>
<?php 
	include 'jquery.js.php';
	include 'bootstrap.js.php';
	include 'dataTable.js.php';
	include 'ace.js.php';

  if (MODO_PRODUCCION == "1"){
    echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>';
  } else {
    echo '<script src="../assets/js/chosen.jquery.min.js"></script>';
  }
 ?>


<script src="../assets/js/ace-extra.min.js"></script>
<script src="../assets/alert/dist/sweetalert.js"></script>


  <!--***********************************************ALERTA*****************************************************-->
<!--
  <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/font-awesome-4.3.0/css/font-awesome.min.css" rel="stylesheet">
  <script src="../assets/javascripts/jquery.js"></script>

  <script src="../util/alert/dist/sweetalert.js"></script>
  <link rel="stylesheet" href="../util/alert/dist/sweetalert.css"> -->
<script src="js/Util.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="js/axios/apiLoad.js"></script>
<script src="js/sesion/AccesoAuxiliar.js"></script>
<script src="../assets/Ajxur.js"></script>