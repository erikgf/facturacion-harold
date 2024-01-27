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

          <?php  include '_css/main.css.php'; 
            if (MODO_PRODUCCION == "1"){
              echo '<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css">';
            } else {
              echo '<link rel="stylesheet" href="../assets/css/chosen.min.css" />';
            }
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

                <?php include 'breadcrumb.reportes.php' ?>

                <div class="page-content">
               
                  <?php include 'ace.settings.php' ?>

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
                        <br><button class="btn btn-info btn-block btn-lg" id="btnbuscar">BUSCAR</button>
                      </div>
                    </div>
                    <div class="col-xs-12 col-sm-3 col-md-2">
                      <div class="control-group">
                        <br><button class="btn btn-excel btn-block btn-lg" id="btnexcel">EXCEL</button>
                      </div>
                    </div>
                  </div>

                  <div class="space-6"></div>

                  <h4>Registros: </h4>                  
                  <div class="row">
                        <div class="col-sm-12 col-xs-12"
>                          <div  id="listado" class="table-responsive">           
                            <script id="tpl8Listado" type="handlebars-x">
                              <table class="table table-striped table-bordered table-hover dataTable dt-responsive"  cellspacing="0" width="100%">
                                  <thead>
                                    <tr>
                                      <th width="120px">Sucursal</td>
                                      <th width="80px">Código</th>
                                      <th>Producto</th>s
                                      <th width="100px">Monto Vendido</th>
                                      <th width="100px">Monto Gastado</th>
                                      <th width="100px">Utilidad</th>
                                      <th width="85px">Unidades Vendidas</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                      {{#this}}
                                        <tr>
                                            <td>{{sucursal}}</td>
                                            <td>{{codigo_producto}}</td>
                                            <td>{{producto}}</td>
                                            <td class="text-right">{{monto_vendido}}</td>
                                            <td class="text-right">{{monto_gastado}}</td>
                                            <td class="text-right">{{utilidad}}</td>
                                            <td class="text-right">{{unidades_vendidas}}</td>
                                         </tr>
                                      {{/this}}
                                  </tbody>
                              </table>
                           </script>                                    
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
                <option value="{{cod_sucursal}}">{{nombre}}</option>
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
        <script src="js/reporte.mas.vendido.vista.js" type="text/javascript"></script>
    </body>

</html>



