<?php
include '../datos/local_config_web.php';
$TITULO_PAGINA = "Gestionar Notas";
$fechaHoy = date('Y-m-d');
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
                  font-size: 1.25em;
              }
          </style>

    </head>
    <body class="no-skin">
        <?php include './partials/_globals/navbar.php'; ?>

        <div class="main-container ace-save-state" id="main-container">
            <?php include './partials/_globals/menu.php'; ?>

            <div class="main-content">
              <div class="main-content-inner">

                <?php include './partials/_globals/breadcrumb.facturacion.php' ?>

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
                          <a data-toggle="tab" href="#tabRegistrarNotas" aria-expanded="true">Registrar Notas</a>
                        </li>

                        <li class="">
                          <a data-toggle="tab" href="#tabListadoNotas" aria-expanded="false">Listado de Notas</a>
                        </li>
                      </ul>

                      <div class="tab-content">
                        <div id="tabRegistrarNotas" class="tab-pane active">
                          <?php include './partials/notas/registrarnotas.partial.php'; ?>
                        </div>
                        <div id="tabListadoNotas" class="tab-pane">
                          <?php include './partials/notas/listarnotas.partial.php'; ?>
                        </div>
                      </div>
                    </div>
                    </div><!-- /.col -->
                  </div><!-- /.row -->
                </div><!-- /.page-content -->
              </div>
            </div><!-- /.main-content -->

          <div id="mdlDetalleNota" class="modal fade" tabindex="-1" style="display: none;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h3 class="smaller lighter blue no-margin"></h3>
                  </div>
                  <div class="modal-body">
                    <script type="handlebars-x" id="tpl8DetalleNota">
                    <div class="row">
                      <div class="col-xs-12 col-sm-3">
                        <h5><b>Comprobante: </b> {{serie}}-{{correlativo}}</h5>
                      </div>
                      <div class="col-xs-12 col-sm-3">
                        <h5><b>Comprobante Modifcado: </b> {{serie}}-{{correlativo}}</h5>
                      </div>
                      <div class="col-xs-12 col-sm-3">
                        <h5><b>Fecha Emisión: </b> {{fecha_emision}} {{hora_emision}}</h5>
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
                                    <th class="text-right" style="width:100px">P.U </th>
                                    <th class="text-right" style="width:70px">Cant.</th>
                                    <th class="text-right" style="width:120px">Subtotal</th>
                                  </tr>
                                </thead> 
                                <tbody id="tbllista_detalle" class="tr-middle-align">    
                                    {{#detalle}}
                                      <tr >
                                        <td class="text-center">{{item}}</td>
                                        <td class="text-left">{{producto}}</td>
                                        <td class="text-right">{{../id_tipo_moneda}} {{precio_unitario}}</td>
                                        <td class="text-right">{{cantidad}}</td>
                                        <td class="text-right">{{../id_tipo_moneda}} {{subtotal}}</td>
                                      </tr>
                                    {{/detalle}}
                                </tbody>
                                <tfoot>
                                  <tr style="font-size:1.25em">
                                    <td class="text-left" colspan="2">
                                      <button class="btn btn-sm btn-secondary" onclick="app.ListarNotas.verComprobante({{id}})">
                                        <i class="ace-icon fa fa-file"></i>
                                        VER COMPROBANTE
                                      </button>
                                    </td>
                                    <td class="text-right" colspan="2">TOTAL</td>
                                    <td class="text-right">{{id_tipo_moneda}} {{importe_total}}</td>
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

        <script id="tpl8Series" type="handlebars-x">
            {{#.}}
            <option value="{{serie}}" data-correlativo="{{correlativo}}"">{{serie}}</option>
            {{/.}}
        </script> 

        <?php include './partials/_globals/footer.php'; ?>
           
        </div><!-- /.main-container -->
       <?php  include '_js/main.js.php'; ?>
       <script src="js/notas/RegistrarNotas.js"></script>
       <script src="js/notas/ListarNotas.js"></script>
       <script src="js/notas/notas.vista.js"></script>
    </body>

</html>



