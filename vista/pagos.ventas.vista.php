<?php

include '../datos/local_config_web.php';
$TITULO_PAGINA = "Gestionar Pagos Ventas";
?>

<!DOCTYPE html>
<html leng="es">
    <head>
          <meta charset="utf-8" />
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

                <?php include 'breadcrumb.transacciones.php' ?>

                <div class="page-content">
               
                  <?php include 'ace.settings.php' ?>

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
                          <a data-toggle="tab" href="#tabRegistrarPagosVentas" aria-expanded="true">Registrar Pagos de Ventas</a>
                        </li>

                        <li class="">
                          <a data-toggle="tab" href="#tabListadoPagosVentas" aria-expanded="false">Listado Pagos de Ventas</a>
                        </li>
                      </ul>

                      <div class="tab-content">
                        <div id="tabRegistrarPagosVentas" class="tab-pane active">
                          <?php include '_registrar.pagosventas.vista.php'; ?>
                        </div>
                        <div id="tabListadoPagosVentas" class="tab-pane">
                          <?php include '_listar.pagosventas.vista.php'; ?>
                        </div>
                      </div>
                    </div>
                    </div><!-- /.col -->
                  </div><!-- /.row -->
                </div><!-- /.page-content -->
              </div>
            </div><!-- /.main-content -->
          
            <style type="text/css">
                .detalle-rotulo{
                   font-size: 1.5em;
                   text-align: center;
                }
            </style>

            <div id="mdlDetalleVenta" class="modal fade" tabindex="-1" style="display: none;">
              <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                      <h3 class="smaller lighter blue no-margin"></h3>
                    </div>
                    <div class="modal-body">
                      <script type="handlebars-x" id="tpl8DetalleVenta">
                        <div class="row">
                          <div class="col-xs-12 col-sm-6">
                            <h5><b>Comprobante: </b> {{cabecera.comprobante}}</h5>
                          </div>
                          {{#cabecera.voucher}}
                          <div class="col-xs-12 col-sm-6">
                            <h5><b>Voucher: </b> {{this}}</h5>
                          </div>
                          {{/cabecera.voucher}}
                          </div>

                        <div class="row">
                          <div class="col-xs-12 col-sm-6 col-md-3">
                            <div class="control-group">
                              <label class="control-label"><b>Monto Efectivo</b></label>
                              <p id="txtefectivo_detalle" class="detalle-rotulo">{{cabecera.monto_efectivo}}</p>
                            </div>
                          </div>
                          <div class="col-xs-12 col-sm-6 col-md-3">
                            <div class="control-group">
                              <label class="control-label"><b>Monto Tarjeta</b></label>
                              <p id="txttarjeta_detalle" class="detalle-rotulo">{{cabecera.monto_tarjeta}} {{#cabecera.tipo_tarjeta}} ({{this}}) {{/cabecera.tipo_tarjeta}}</p>
                            </div>
                          </div>
                          <div class="col-xs-12 col-sm-6 col-md-3">
                            <div class="control-group">
                              <label class="control-label"><b>Monto Crédito</b></label>
                              <p id="txtcredito_detalle" class="detalle-rotulo">{{cabecera.monto_credito}}</p>
                            </div>
                          </div>
                          <div class="col-xs-12 col-sm-6 col-md-3">
                            <div class="control-group">
                              <label class="control-label"><b>Fecha Venta</b></label>
                              <p id="txtfechaventa_detalle" class="detalle-rotulo">{{cabecera.fecha_venta}}</p>
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
                                      <th class="width:90px">Cód.</th>
                                      <th class="text-left">Producto</th>
                                      <th class="text-center" style="width:120px">P.U </th>
                                      <th class="text-center" style="width:140px">Cant.</th>
                                      <th class="text-center" style="width:150px">F.Venc.</th>
                                      <th class="text-center" style="width:150px">Lote</th>
                                      <th class="text-center" style="width:120px">Subtotal</th>
                                    </tr>
                                  </thead> 
                                  <tbody id="tbllista_detalle" class="tr-middle-align">    
                                      {{#detalle}}
                                        <tr >
                                          <td class="text-center">{{item}}</td>
                                          <td class="text-left">{{codigo_producto}}</td>
                                          <td class="text-left">{{producto}}</td>
                                          <td class="text-center">{{precio_unitario}}</td>
                                          <td class="text-center">{{cantidad}}</td>
                                          <td class="text-center">{{fecha_vencimiento}}</td>
                                          <td class="text-center">{{lote}}</td>
                                          <td class="text-center">S/ {{subtotal}}</td>
                                        </tr>
                                      {{/detalle}}
                                  </tbody>
                                  <tfoot>
                                    <tr style="font-size:1.1em">
                                      <td class="text-right" colspan="7">SUBTOTAL </td>
                                      <td class="text-center">S/{{cabecera.subtotal}}</td>
                                    </tr>
                                    <tr style="font-size:1.15em;display:none">
                                      <td class="text-right" colspan="7">DESCUENTO GLOBAL</td>
                                      <td class="text-center">S/{{cabecera.descuentos_globales}} {{#cabecera.codigo_descuento}}<br><small>({{this}})</small>{{/cabecera.codigo_descuento}}</td>
                                    </tr>
                                    <tr style="font-size:1.25em">
                                      <td class="text-center" colspan="2">
                                       {{#if_ cabecera.tipo_comprobante '!=' ''}}
                                        <button class="btn btn-sm btn-primary" onclick="app.ListarPagosVentas.verComprobante({{cabecera.x_cod_transaccion}})">
                                          <i class="ace-icon fa fa-file"></i>
                                          VER COMPROBANTE
                                        </button>
                                       {{/if_}}
                                      </td>
                                      <td class="text-right" colspan="5">TOTAL</td>
                                      <td class="text-center">S/ {{cabecera.importe_total_venta}}</td>
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

            <?php include 'footer.php'; ?>
           
        </div><!-- /.main-container -->

        <script id="tpl8Combo" type="handlebars-x">
            <option value="">Todos</option>
            {{#.}}
            <option value='{{codigo}}'>{{nombre}}</option>
            {{/.}}
        </script>
            

        <?php  

          include '_js/main.js.php';
          if (MODO_PRODUCCION == "1"){
              echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>';
            } else {
              echo '<script src="../assets/js/chosen.jquery.min.js"></script>';
            }
        ?>
       <script type="text/javascript">
          var ___ad = '<?php echo $_SESSION["usuario"]["cod_rol"] == "1";?>';
       </script>
       <script src="js/pagos.ventas.vista.js<?php echo '?'.time();?>"></script>
    </body>

</html>



