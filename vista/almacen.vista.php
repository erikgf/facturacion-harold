<?php

include '../datos/local_config_web.php';
$TITULO_PAGINA = "Gestionar Almacén";

$fechaHoy = date('Y-m-d');
?>

<!DOCTYPE html>
<html leng="es">
    <head>
          <meta charset="utf-8" />
          <title><?php echo $TITULO_PAGINA ?></title>
          <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
          <link rel="icon" type="image/jpeg" href="../imagenes/logo_peque.jpg" />
          <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />

          <?php  include '_css/main.css.php';  ?>
    </head>
    <body class="no-skin">
        <?php include './partials/_globals/navbar.php'; ?>

        <div class="main-container ace-save-state" id="main-container">
            <?php include './partials/_globals/menu.php'; ?>

            <div class="main-content">
              <div class="main-content-inner">

                <?php include './partials/_globals/breadcrumb.transacciones.php' ?>

                <div class="page-content">
               
                  <?php include './partials/_globals/ace.settings.php' ?>

                  <div class="page-header">
                    <h1>
                      <?php echo $TITULO_PAGINA; ?>
                    </h1>
                  </div><!-- /.page-header -->

                  <div class="row">
                      <div class="col-xs-12 col-sm-4">
                        <div class="control-group">
                          <label class="control-label">Sucursal</label>
                          <select id="cbosucursal" class="control-form"></select>
                        </div>
                      </div>
                  </div>

                  <div class="row">
                    <div class="col-ms-12">
                      <div class="tabbable">
                      <ul class="nav nav-tabs padding-12 tab-color-blue background-blue" id="myTab4">
                        <li class="active">
                          <a data-toggle="tab" href="#tabStockProductos" aria-expanded="true">Stock de productos</a>
                        </li>

                        <li class="">
                          <a data-toggle="tab" href="#tabHistorialMovimientos" aria-expanded="false">Historial Movimientos</a>
                        </li>
                      </ul>

                      <div class="tab-content">
                        <div id="tabStockProductos" class="tab-pane active">
                          <?php include './partials/almacen/stockproductos.partial.php'; ?>
                        </div>
                        <div id="tabHistorialMovimientos" class="tab-pane">
                          <?php include './partials/almacen/historialproductos.partial.php'; ?>
                        </div>
                      </div>
                    </div>
                    </div><!-- /.col -->
                  </div><!-- /.row -->
                </div><!-- /.page-content -->
              </div>
            </div><!-- /.main-content -->

            <script id="tpl8Sucursal" type="handlebars-x">
                {{#.}}
                  <option value='{{id}}'>{{nombre}}</option>
                {{/.}}
            </script> 

            <script id="tpl8Combo" type="handlebars-x">
                <option value=''>Todos</option>
                {{#.}}
                <option value='{{id}}'>{{nombre}}</option>
                {{/.}}
            </script>  

            <?php include './partials/_globals/footer.php'; ?>
           
        </div><!-- /.main-container -->

        <?php  include '_js/main.js.php'; ?>
        <script src="js/almacenes/HistorialMovimientos.js" type="text/javascript"></script>
        <script src="js/almacenes/StockProductos.js" type="text/javascript"></script>
        <script src="js/almacenes/almacenes.vista.js" type="text/javascript"></script>
    </body>

</html>



