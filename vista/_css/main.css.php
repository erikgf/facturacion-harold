
<!-- 

  <link rel="stylesheet" href="../assets/css/jquery-ui-1.10.3.custom.min.css" />
  <link rel="stylesheet" href="../assets/css/jquery.gritter.css" />
-->
  <!-- <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400,300" /> -->
<?php 
    include 'bootstrap.css.php';
    include 'font-awesome.css.php';
    include 'ace.css.php';
    include 'dataTable.css.php'; 

    if (MODO_PRODUCCION == "1"){
      echo '<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css">';
    } else {
      echo '<link rel="stylesheet" href="../assets/css/chosen.min.css" />';
    }
?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;900&display=swap" rel="stylesheet">

    <style>
      body {
        font-family: 'Roboto' !important;
      }

      h1, h2, h3, h4, h5, h6 {
        font-family: 'Roboto' !important;
      }
    </style>
    
    <link rel="stylesheet" href="../assets/alert/dist/sweetalert.css">
    <link rel="stylesheet" type="text/css" href="css/estilos.css">
        <!--
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
-->