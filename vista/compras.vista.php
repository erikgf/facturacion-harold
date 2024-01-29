<?php

include '../datos/local_config_web.php';
$TITULO_PAGINA = "Gestionar Compras";
?>

<!DOCTYPE html>
<html leng="es">
    <head>
          <meta charset="utf-8" />
          <title><?php echo $TITULO_PAGINA ?></title>
          <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
          <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />

          <?php include '_css/main.css.php'; ?>
          
          <style type="text/css">
              .detalle-rotulo{
                  font-size: 1.2em;
              }
          </style>
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
                    <div class="col-ms-12">
                      <div class="tabbable">
                      <ul class="nav nav-tabs padding-12 tab-color-blue background-blue" id="myTab4">
                        <li class="active">
                          <a data-toggle="tab" href="#tabRegistrarCompras" aria-expanded="true">Registrar Compras</a>
                        </li>

                        <li class="">
                          <a data-toggle="tab" href="#tabListadoCompras" aria-expanded="false">Listado de Compras</a>
                        </li>
                      </ul>

                      <div class="tab-content">
                        <div id="tabRegistrarCompras" class="tab-pane active">
                          <?php include './partials/compras/registrarcompras.partial.php'; ?>
                        </div>
                        <div id="tabListadoCompras" class="tab-pane">
                          <?php include './partials/compras/listacompras.partial.php'; ?>
                        </div>
                      </div>
                    </div>
                    </div><!-- /.col -->
                  </div><!-- /.row -->
                </div><!-- /.page-content -->
              </div>
            </div><!-- /.main-content -->
            <div id="mdlDetalleCompra" class="modal fade" tabindex="-1" style="display: none;">
              <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                      <h3 class="smaller lighter blue no-margin"></h3>
                    </div>
                    <div class="modal-body">
                     <script type="handlebars-x" id="tpl8DetalleCompra">
                      <div class="row">
                        <div class="col-xs-12 col-sm-6">
                          <h5><b>Comprobante: </b> {{numero_comprobante}}</h5>
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-xs-12 col-sm-6 col-md-4">
                          <div class="control-group">
                            <label class="control-label"><b>Tipo Pago</b></label>
                            <p id="txttipopago_detalle" class="detalle-rotulo">{{tipo_pago}} {{#tipo_tarjeta}} ({{this}}) {{/tipo_tarjeta}}</p>
                          </div>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-2">
                          <div class="control-group">
                            <label class="control-label"><b>Fecha Compra</b></label>
                            <p id="txtfechacompra_detalle" class="detalle-rotulo">{{fecha_compra}}</p>
                          </div>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-2">
                          <div class="control-group">
                            <label class="control-label"><b>Hora Compra</b></label>
                            <p id="txhoracompra_detalle" class="detalle-rotulo">{{hora_compra}}</p>
                          </div>
                        </div>
                      </div>
                      <div class="space-6"></div>
                      <div class="row">
                        <div class="col-xs-12">
                            <div  class="table-responsive">    
                               <table class="table tbl-detalle">
                                  <thead>
                                    <tr>
                                      <th style="width:50px">Item</th>
                                      <th class="text-left">Producto</th>
                                      <th class="text-center" style="width:120px">P.U </th>
                                      <th class="text-center" style="width:140px">Cant.</th>
                                      <th class="text-right" style="width:120px">Subtotal</th>
                                    </tr>
                                  </thead> 
                                  <tbody id="tbllista_detalle" class="tr-middle-align">    
                                      {{#detalle}}
                                        <tr >
                                          <td class="text-center">{{item}}</td>
                                          <td class="text-left"">{{producto.nombre}}</td>
                                          <td class="text-center">{{precio_unitario}}</td>
                                          <td class="text-center">{{cantidad}}</td>
                                          <td class="text-right">S/ {{subtotal}}</td>
                                        </tr>
                                      {{/detalle}}
                                  </tbody>
                                  <tfoot>
                                    <tr style="font-size:1.55em">
                                      <td class="text-right" colspan="4">TOTAL</td>
                                      <td class="text-right">S/ {{importe_total}}</td>
                                    </tr>
                                  </tfoot>
                                </table>
                            </div>
                        </div>                                                                               
                      </div>      
                     </script>
                    </div>
                    <div class="modal-footer">
                      <button class="btn btn-sm btn-danger pull-right" data-dismiss="modal">
                        <i class="ace-icon fa fa-times"></i>
                        Cerrar
                      </button>
                    </div>
                  </div>
              </div>
            </div>
            
            <script id="tpl8Combo" type="handlebars-x">
                <option value="">Todos</option>
                {{#.}}
                <option value='{{id}}'>{{nombre}}</option>
                {{/.}}
            </script>   

            <script id="tpl8Sucursal" type="handlebars-x">
                {{#.}}
                <option value='{{id}}'>{{nombre}}</option>
                {{/.}}
            </script>   

            <?php include './partials/_globals/footer.php'; ?>
           
        </div><!-- /.main-container -->


      <?php  include '_js/main.js.php';?>

      <script src="js/compras/RegistrarCompras.js"></script>
      <script src="js/compras/ListarCompras.js"></script>
      <script src="js/compras/compras.vista.js"></script>
    </body>

</html>



