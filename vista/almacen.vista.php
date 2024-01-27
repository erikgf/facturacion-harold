<?php

include '../datos/local_config_web.php';
$TITULO_PAGINA = "Gestionar Almacén";
?>

<!DOCTYPE html>
<html leng="es">
    <head>
          <meta charset="utf-8" />
          <title><?php echo $TITULO_PAGINA ?></title>
          <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
          <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />

          <?php  
            if (MODO_PRODUCCION == "1"){
              echo '<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css">';
            } else {
              echo '<link rel="stylesheet" href="../assets/css/chosen.min.css" />';
            }
            include '_css/main.css.php'; 
          ?>

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

                <?php include 'breadcrumb.transacciones.php' ?>

                <div class="page-content">
               
                  <?php include 'ace.settings.php' ?>

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
                          <?php include '_stockproductos.almacen.vista.php'; ?>
                        </div>
                        <div id="tabHistorialMovimientos" class="tab-pane">
                          <?php include '_historialproductos.almacen.vista.php'; ?>
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

            <?php include 'footer.php'; ?>
           
        </div><!-- /.main-container -->


        <?php  include '_js/main.js.php';
          if (MODO_PRODUCCION == "1"){
              /*echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>';*/
              echo '<script src="../assets/js/chosen.jquery.min.js"></script>';
            } else {
              echo '<script src="../assets/js/chosen.jquery.min.js"></script>';
            }
        ?>

        <script src="js/almacenes/HistorialMovimientos.js" type="text/javascript"></script>
        <script src="js/almacenes/StockProductos.js" type="text/javascript"></script>
        <script src="js/almacenes/almacenes.vista.js" type="text/javascript"></script>
    </body>

</html>



