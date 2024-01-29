<?php

include '../datos/local_config_web.php';
$TITULO_PAGINA = "Reporte de Producto Más Vendido";
$fechaHoy = date('Y-m-d');

?>

<!DOCTYPE html>
<html leng="es">
    <head><meta http-equiv="Content-Type" content="text/html; charset=gb18030">
          
          <title><?php echo $TITULO_PAGINA ?></title>
          <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
          <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />

          <?php  include '_css/main.css.php'; ?>
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
                    <div class="col-xs-6 col-sm-3 col-lg-2">
                      <div class="control-group">
                        <label class="control-label">Desde Fecha</label>
                        <input type="date" value="<?php echo $fechaHoy; ?>" class="form-control" id="txtfechadesde">
                      </div>
                    </div>
                    <div class="col-xs-6 col-sm-3 col-lg-2">
                      <div class="control-group">
                        <label class="control-label">Hasta Fecha</label>
                        <input type="date" value="<?php echo $fechaHoy; ?>" class="form-control" id="txtfechahasta">
                      </div>
                    </div>
                    <div class="col-xs-6 col-sm-3 col-lg-2">
                      <div class="control-group">
                       <br>
                        <div class="checkbox">
                          <label>
                            <input name="chktodos" type="checkbox" id="chktodos" class="ace">
                            <span class="lbl"> TODOS</span>
                          </label>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                     <div class="col-xs-6 col-sm-3 col-lg-2">
                      <div class="control-group">
                        <label class="control-label">Sucursal</label>
                        <select  class="form-control" id="cbosucursal">
                        </select>
                      </div>
                    </div>
                    <div class="col-xs-12 col-sm-3 col-md-2">
                      <div class="control-group">
                        <br><button class="btn btn-info btn-block" id="btnbuscar">BUSCAR</button>
                      </div>
                    </div>
                  </div>

                  <div class="space-6"></div>

                  <h4>Registros: </h4>                  
                  <div class="row">
                        <div class="col-sm-12 col-xs-12"
>                          <div class="table-responsive">           
                              <table id="tbllista" class="small table table-striped table-bordered table-hover dataTable dt-responsive"  cellspacing="0" width="100%">
                                  <thead>
                                    <tr>
                                      <th width="120px">Sucursal</td>
                                      <th width="80px">Código</th>
                                      <th>Producto</th>
                                      <th width="100px">Monto Vendido (S/)</th>
                                      <th width="100px">Monto Gastado (S/)</th>
                                      <th width="100px">Utilidad (S/)</th>
                                      <th width="85px">Unidades Vendidas (unid.)</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                      <script id="tpl8Listado" type="handlebars-x">
                                        {{#.}}
                                          <tr>
                                              <td>{{sucursal}}</td>
                                              <td>{{codigo_generado}}</td>
                                              <td>{{producto}}</td>
                                              <td class="bolder text-right text-primary">{{monto_vendido}}</td>
                                              <td class="bolder text-right text-danger">{{monto_gastado}}</td>
                                              <td class="bolder text-right text-success">{{utilidad}}</td>
                                              <td class="text-right">{{unidades_vendidas}}</td>
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
        <script src="js/reporte.mas.vendido.vista.js" type="text/javascript"></script>
    </body>

</html>



