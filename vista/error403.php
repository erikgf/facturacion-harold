<?php

include '../datos/local_config_web.php';
$TITULO_PAGINA = "Error 403";

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
        <div class="loader">
            <div>            
                <i class="ace-icon fa fa-spinner fa-spin bigger-225"></i>
            </div>
        </div>
        <div id="navbar" class="navbar navbar-default ace-save-state">
            <div class="navbar-container ace-save-state" id="navbar-container">
                  <div class="navbar-header pull-left">
                      <a href="#" class="navbar-brand">
                          <small>
                              <i class="fa fa-fire"></i>
                              <?php echo NOMBRE_EMPRESA; ?> - <?php echo SW_NOMBRE_COMPLETO; ?> 
                          </small>
                      </a>
                  </div>
              </div><!-- /.navbar-container -->
        </div>

        <div class="main-container ace-save-state" id="main-container">
            <script type="text/javascript">
                try{ace.settings.loadState('main-container')}catch(e){}
            </script>
            <div class="main-content">
              <div class="main-content-inner">

               <div class="breadcrumbs ace-save-state" id="breadcrumbs">
                  <ul class="breadcrumb">
                    <li class="active"><i class="ace-icon fa fa-home home-icon"></i><?php  echo $TITULO_PAGINA; ?></li>
                  </ul><!-- /.breadcrumb -->
               </div>

                <div class="page-content">
               
                  <div class="page-header">
                    <h1>
                      <?php echo $TITULO_PAGINA; ?>
                      <!--
                      <small>
                        <i class="ace-icon fa fa-angle-double-right"></i>
                        Gestión y mantenimiento
                      </small>s
                      -->
                    </h1>
                  </div><!-- /.page-header -->
                  <div class="row">
                    <div class="col-xs-12">
                      <div class="error-container">
                          <div class="well">
                            <h1 class="grey lighter smaller">
                              <span class="blue bigger-125">
                                <i class="ace-icon fa fa-sitemap"></i>
                                403
                              </span>
                              Sin acceso
                            </h1>
                            <hr>
                            <h3 class="lighter smaller">Ha expirado su sesión o No tiene permiso para estar aquí.</h3>
                            <div>

                              <div class="space"></div>
                              <h4 class="smaller">Intenta regresando al inicio:</h4>

                              <ul class="list-unstyled spaced inline bigger-110 margin-15">
                                <li>
                                  <a href="index.php"><i class="ace-icon fa fa-home blue"></i>Inicio</a>
                                </li>
                              </ul>
                            </div>

                            <hr>
                            <div class="space"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div><!-- /.page-content -->
              </div>
            </div><!-- /.main-content -->

           <?php include './partials/_globals/footer.php'; ?>
        </div><!-- /.main-container -->
        <?php  include '_js/main.js.php';?>
        <script type="text/javascript">
            localStorage.removeItem(SESSION_NAME);
        </script>
    </body>

</html>



