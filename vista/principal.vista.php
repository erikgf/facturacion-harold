<?php

include '../datos/local_config_web.php';
$TITULO_PAGINA = "Inicio";

?>

<!DOCTYPE html>
<html leng="es">
    <head>
          <meta charset="utf-8" />
          <title><?php echo $TITULO_PAGINA ?></title>
          <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
          <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />

          <?php  include '_css/main.css.php'; ?>
    </head>
    <body class="no-skin">
        <?php include 'navbar.php'; ?>

        <div class="main-container ace-save-state" id="main-container">
            <script type="text/javascript">
                try{ace.settings.loadState('main-container')}catch(e){}
            </script>

            <?php include 'menu.php'; ?>

            <div class="main-content">
              <div class="main-content-inner">

               <div class="breadcrumbs ace-save-state" id="breadcrumbs">
                  <ul class="breadcrumb">
                    <li class="active"><i class="ace-icon fa fa-home home-icon"></i><?php  echo $TITULO_PAGINA; ?></li>
                  </ul><!-- /.breadcrumb -->
               </div>

                <div class="page-content">
               
                  <?php include 'ace.settings.php' ?>

                  <div class="page-header">
                    <h1>
                      Bienvenido!
                      <!--
                      <small>
                        <i class="ace-icon fa fa-angle-double-right"></i>
                        Gestión y mantenimiento
                      </small>s
                      -->
                    </h1>
                  </div><!-- /.page-header -->

                  <div class="text-center img">
                    <img src="../imagenes/logo_grande.jpeg" alt="">
                  </div>
          
                </div><!-- /.page-content -->
              </div>
            </div><!-- /.main-content -->

           <?php include 'footer.php'; ?>


        </div><!-- /.main-container -->

        <?php  include '_js/main.js.php';?>
        <script type="text/javascript" src="js/principal.vista.js"></script>
    </body>

</html>



