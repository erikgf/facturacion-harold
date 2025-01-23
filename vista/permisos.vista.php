<?php

include '../datos/local_config_web.php';
$TITULO_PAGINA = "Permisos por Rol";
?>

<!DOCTYPE html>
<html leng="es">
    <head>
          <meta charset="utf-8" />
          <title><?php echo $TITULO_PAGINA ?></title>
          <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
          <link rel="icon" type="image/jpeg" href="../imagenes/logo_peque.jpg" />
          <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />

          <?php  include '_css/main.css.php'; ?>
    </head>
    <body class="no-skin">
        <?php include './partials/_globals/navbar.php'; ?>

        <div class="main-container ace-save-state" id="main-container">
            <?php include './partials/_globals/menu.php'; ?>

            <div class="main-content">
              <div class="main-content-inner">

                <?php include './partials/_globals/breadcrumb.mantenimiento.php' ?>

                <div class="page-content">
               
                  <?php include './partials/_globals/ace.settings.php' ?>

                  <div class="page-header">
                    <h1>
                      <?php echo $TITULO_PAGINA; ?>
                      <!--
                      <small>
                        <i class="ace-icon fa fa-angle-double-right"></i>
                        Gestión y mantenimiento
                      </small>
                      -->
                    </h1>
                  </div><!-- /.page-header -->


                  <div class="row">
                    <div class="col-xs-6 col-md-4">
                      <label class="control-label">Selección de ROL</label>
                      <select class="form-control" id="cborol">
                      </select>
                    </div>
                  </div>

                 <div class="row">
                  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="row clearfix">
                         <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
                                    <div class="card">
                                        <div class="header bg-green">
                                            <h2>Permiso activo</h2>
                                        </div>
                                        <div id="listar-permisos-activos" style="height:350px;overflow: scroll;"></div> 
                                    </div>
                          </div>
                         <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12" style="height:410px;display:flex;justify-content:center;align-items: center;">
                                    <div style="width: 200px;">
                                        <button type="button" disabled id="btn-izquierda" class="btn btn-block btn-lg btn-success waves-effect" onclick="agregar()">&#60;</button>
                                        <button type="button" disabled  id="btn-derecha" class="btn btn-block btn-lg btn-success waves-effect" onclick="quitar()">&#62;</button>
                                    </div>
                                </div>
                           <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
                                    <div class="card">
                                        <div class="header bg-grey">
                                            <h2>Permiso inactivo</h2>
                                        </div>
                                        <div id="listar-permisos-inactivos" style="height:350px;overflow: scroll;"></div>                                        
                                    </div>
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
        <script src="js/permisos.vista.js" type="text/javascript"></script>
    </body>

</html>



