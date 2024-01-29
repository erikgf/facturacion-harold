<?php

include '../datos/local_config_web.php';
$TITULO_PAGINA = "Reporte de Stock";
$fechaHoy = date('Y-m-d');

?>

<!DOCTYPE html>
<html leng="es">
    <head>
          <meta charset="utf-8" />
          <title><?php echo $TITULO_PAGINA ?></title>
          <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
          <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />

          <?php  include '_css/main.css.php';  ?>
          <?php  include '_css/dataTableButtons.css.php'; ?>
    </head>
    <body class="no-skin">
        <?php include './partials/_globals/navbar.php'; ?>

        <div class="main-container ace-save-state" id="main-container">
            <?php include './partials/_globals/menu.php'; ?>

            <div class="main-content">
              <div class="main-content-inner">

                <?php include './partials/_globals/breadcrumb.reportes.php' ?>

                <div class="page-content">
               
                  <?php include './partials/_globals/ace.settings.php' ?>

                  <div class="page-header">
                    <h1>
                      <?php echo $TITULO_PAGINA; ?>
                    </h1>
                  </div><!-- /.page-header -->
                  <div class="row">
                     <div class="col-xs-12 col-sm-6 col-md-3">
                      <div class="control-group">
                        <label class="control-label">Sucursal</label>
                        <select  class="form-control" id="cbosucursal">
                        </select>
                      </div>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-2">
                      <div class="control-group">
                        <br><button class="btn btn-info btn-block" id="btnbuscar">BUSCAR</button>
                      </div>
                    </div>
                  </div>
                  <div class="space-6"></div>
                  <h4>Registros: </h4>                  
                  <div class="row">
                        <div class="col-sm-12 col-xs-12">
                          <div class="table-responsive">           
                              <table id="tbllista" class="table table-striped table-bordered table-hover dataTable dt-responsive"  cellspacing="0" width="100%">
                                  <thead>
                                    <tr>
                                      <th>Producto</th>
                                      <th>Lote</th>
                                      <th>Categoría de Producto</th>
                                      <th>Tipo de Categoría</th>
                                      <th class="text-center">Stock</th>
                                      <th class="text-center">Costo (Prom.)</th>
                                      <th class="text-center">Total</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <script id="tpl8Listado" type="handlebars-x">
                                      {{#.}}
                                        <tr>
                                          <td>{{producto}}</td>
                                          <td>{{lote}}</td>
                                          <td>{{categoria}}</td>
                                          <td>{{tipo}}</td>
                                          <td class="text-right">{{stock}}</td>
                                          <td class="text-right">{{precio_entrada_promedio}}</td>
                                          <td class="text-right">{{total}}</td>
                                         </tr>
                                      {{/.}}
                                    </script>                                    
                                  </tbody>
                              </table>
                          </div>  <!-- table-responsive --> 
                        </div> 
                      </div>
                    </div><!-- /.col -->
                  </div><!-- /.row -->
                </div><!-- /.page-content -->
              </div>
            </div><!-- /.main-content -->


            <script id="tpl8Sucursal" type="handlebars-x">
              <option value="">Todas</option>
              {{#.}}
                <option value="{{id}}">{{nombre}}</option>
              {{/.}}
            </script>

            <?php include './partials/_globals/footer.php'; ?>
        </div><!-- /.main-container -->

        <?php  include '_js/main.js.php'; ?>
        <?php  include '_js/dataTableButtons.js.php'; ?>
        <script src="js/reporte.stock.vista.js" type="text/javascript"></script>
    </body>

</html>



