<?php
include '../datos/local_config_web.php';
$TITULO_PAGINA = "Gestionar Ventas";
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
                          <a data-toggle="tab" href="#tabRegistrarVentas" aria-expanded="true">Registrar Ventas</a>
                        </li>

                        <li class="">
                          <a data-toggle="tab" href="#tabListadoVentas" aria-expanded="false">Listado de Ventas</a>
                        </li>
                      </ul>

                      <div class="tab-content">
                        <div id="tabRegistrarVentas" class="tab-pane active">
                          <?php include './partials/ventas/registrarventas.partial.php'; ?>
                        </div>
                        <div id="tabListadoVentas" class="tab-pane">
                          <?php include './partials/ventas/listaventas.partial.php'; ?>
                        </div>
                      </div>
                    </div>
                    </div><!-- /.col -->
                  </div><!-- /.row -->
                </div><!-- /.page-content -->
              </div>
            </div><!-- /.main-content -->

            
            <div id="mdlVoucher" class="modal fade" tabindex="-1" style="display: none;">
              <div class="modal-dialog modal-sm">
                <form id="frmgrabarvoucher">
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                      <h3 class="smaller lighter blue no-margin">Registro de Voucher</h3>
                    </div>
                    <div class="modal-body">
                      <p>Ingrese el número de voucher de la venta. Esta ventana se activa si y solo sí se trata de una venta con pago con tarjeta sin voucher registrado.</p>
                      <h4><b>Venta: <span class="rotuloVenta"></span></b></h4>
                      <div class="row">
                        <div class="col-xs-12">
                          <div class="control-group">
                            <label class="control-label">Número de Voucher</label>
                            <input class="form-control" id="txtnumerovoucher" type="text"/>
                          </div>
                        </div>
                      </div>
                      <div class="space-6"></div>
                      <div class="row">
                        <div class="col-xs-12">
                          <div id="blkalertvoucher"></div>
                        </div>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button class="btn btn-sm btn-danger pull-right" data-dismiss="modal">
                        <i class="ace-icon fa fa-times"></i>
                        Cancelar
                      </button>
                      <button type="submit"  class="btn btn-sm btn-primary pull-right">
                        <i class="ace-icon fa fa-save"></i>
                        Guardar
                      </button>
                    </div>
                  </div>
                </form>
              </div>
            </div>


            <div id="mdlComisionar" class="modal fade" tabindex="-1" style="display: none;">
              <div class="modal-dialog modal-lg">
                <form id="frmgrabarcomisionar">
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                      <h3 class="smaller lighter blue no-margin">Registro de Comisionista</h3>
                    </div>
                    <div class="modal-body">
                      <h4><b>Venta: <span class="rotuloVenta"></span></b></h4>
                      <div class="row">
                        <div class="col-xs-12 col-sm-8 col-md-6">
                          <div class="control-group">
                            <label class="control-label">Comisionista</label>
                            <select class="form-control" id="cbocomisionista">
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="space-6"></div>
                      <div class="row">
                        <div class="col-xs-12">
                          <div id="blkalertcomisionar"></div>
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
                                      <th class="text-center" style="width:120px">Precio </th>
                                      <th class="text-center" style="width:140px">Tipo Comisión</th>
                                      <th class="text-center" style="width:120px">Monto Comisión</th>
                                      <th class="text-center" style="width:120px">Comisionado S/ </th>
                                      <th class="text-center" style="width:50px">¿Comisionar?</th>
                                    </tr>
                                  </thead> 
                                  <tbody id="tbllistacomisionista" class="tr-middle-align">    
                                    <tr class="tr-null ">
                                          <td class="text-center" colspan="7"> Sin comisionista seleccionado. </td>
                                    </tr>
                                    <script type="handlebars-x" id="tpl8ListaComisionista">
                                      {{#.}}
                                        <tr >
                                          <td class="text-center">{{item}}</td>
                                          <td class="text-left" data-producto="{{cod_producto}}">{{producto}}</td>
                                          <td class="text-center" data-total="{{total_detalle}}"><strong>S/{{total_detalle}}</strong><br>S/{{precio}} ({{cantidad}})</td>
                                          <td class="text-center  tipo_comision">
                                            <select  class="form-control">
                                              <option value="M" {{#if_ tipo_comision '==' 'M'}}selected{{/if_}}>MONTO FIJO</option>
                                              <option value="P" {{#if_ tipo_comision '==' 'P'}}selected{{/if_}}>PORCENTAJE</option>
                                            </select>
                                          </td>
                                          <td class="text-center monto_comision">
                                            <input type="numeric" class="form-control text-center" value="{{valor_comision}}"/></td>
                                          <td class="text-center">{{calcularComisionista total_detalle tipo_comision valor_comision}}</td>
                                          <td class="text-center comisionar"><input type="checkbox" {{#if_ comisionar '==' '1'}}checked{{/if_}}/></td>
                                        </tr>
                                      {{else}}
                                        <tr class="tr-null ">
                                          <td class="text-center" colspan="7"> Sin productos comisionables. </td>
                                        </tr>
                                      {{/.}}
                                    </script>
                                  </tbody>
                                  <tfoot>
                                    <tr style="font-size:1.55em">
                                      <td class="text-right" colspan="5">TOTAL COMISIONADO S/</td>
                                      <td class="text-center" id="lblComisionarTotal">0.00</td>
                                      <td></td>
                                    </tr>
                                  </tfoot>
                                </table>
                            </div>
                        </div>
                      </div>                      
                    </div>
                    <div class="modal-footer">
                      <button class="btn btn-sm btn-danger pull-right" data-dismiss="modal">
                        <i class="ace-icon fa fa-times"></i>
                        Cerrar
                      </button>
                      <button type="submit"  class="btn btn-sm btn-primary pull-right">
                        <i class="ace-icon fa fa-save"></i>
                        Guardar
                      </button>
                    </div>
                  </div>
                </form>
              </div>
            </div>

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
                        <div class="col-xs-12 col-sm-4">
                          <h5><b>Comprobante: </b> {{serie}}-{{correlativo}}</h5>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-6">
                          <h5><b>Fecha Venta: </b> {{fecha_venta}} {{hora_venta}}</h5>
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-xs-12 col-sm-6 col-md-2">
                          <div class="control-group">
                            <label class="control-label"><b>Monto Efectivo</b></label>
                            <p id="txtefectivo_detalle" class="detalle-rotulo">S/{{monto_efectivo}}</p>
                          </div>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-2">
                          <div class="control-group">
                            <label class="control-label"><b>Monto Tarjeta</b></label>
                            <p id="txttarjeta_detalle" class="detalle-rotulo">S/{{monto_tarjeta}}</p>
                          </div>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-2">
                          <div class="control-group">
                            <label class="control-label"><b>Monto YAPE</b></label>
                            <p id="txtyape_detalle" class="detalle-rotulo">S/{{monto_yape}}</p>
                          </div>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-2">
                          <div class="control-group">
                            <label class="control-label"><b>Monto PLIN</b></label>
                            <p id="txtplin_detalle" class="detalle-rotulo">S/{{monto_plin}}</p>
                          </div>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-2">
                          <div class="control-group">
                            <label class="control-label"><b>Monto Banco</b></label>
                            <p id="txttransferencia_detalle" class="detalle-rotulo">S/{{monto_transferencia}}</p>
                          </div>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-2">
                          <div class="control-group">
                            <label class="control-label"><b>Monto Crédito</b></label>
                            <p id="txtcredito_detalle" class="detalle-rotulo">S/{{monto_credito}}</p>
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
                                      <th class="text-center" style="width:100px">P.U </th>
                                      <th class="text-center" style="width:70px">Cant.</th>
                                      <th class="text-center" style="width:100px">Lote</th>
                                      <!-- 
                                      <th class="text-center" style="width:150px">Descuento</th>
                                      <th class="text-center" style="width:150px">Monto Sin IGV</th>
                                      <th class="text-center" style="width:150px">IGV</th>
                                      -->
                                      <th class="text-center" style="width:120px">Subtotal</th>
                                    </tr>
                                  </thead> 
                                  <tbody id="tbllista_detalle" class="tr-middle-align">    
                                      {{#detalle}}
                                        <tr >
                                          <td class="text-center">{{item}}</td>
                                          <td class="text-left">{{producto}}</td>
                                          <td class="text-center">S/ {{precio_unitario}}</td>
                                          <td class="text-center">{{cantidad}}</td>
                                          <td class="text-center">{{lote}}</td>
                                          <!-- 
                                          <td class="text-center">S/ {{descuento}} {{#codigo_descuento}}<br><small>({{this}})</small>{{/codigo_descuento}}</td>
                                          <td class="text-center">S/ {{valor_venta}}</td>
                                          <td class="text-center">S/ {{monto_igv}}</td> 
                                          -->
                                          <td class="text-right">S/ {{subtotal}}</td>
                                        </tr>
                                      {{/detalle}}
                                  </tbody>
                                  <tfoot>
                                    <tr style="font-size:1.1em">
                                      <td class="text-right" colspan="5">SUBTOTAL </td>
                                      <td class="text-right">S/{{subtotal}}</td>
                                    </tr>
                                    <tr style="font-size:1.15em;">
                                      <td class="text-right" colspan="5">DESCUENTO GLOBAL</td>
                                      <td class="text-right text-danger bolder">- S/{{monto_descuento}}</td>
                                    </tr>
                                    <tr style="font-size:1.25em">
                                      <td class="text-left" colspan="2">
                                        <button class="btn btn-sm btn-primary" onclick="app.ListarVentas.verAtencion({{id}})">
                                          <i class="ace-icon fa fa-file"></i>
                                          VER TICKET
                                        </button>
                                        
                                       {{#if_ id_tipo_comprobante '!=' '00'}}
                                        <button class="btn btn-sm btn-secondary" onclick="app.ListarVentas.verComprobante({{comprobante.id}})">
                                          <i class="ace-icon fa fa-file"></i>
                                          VER COMPROBANTE
                                        </button>
                                       {{/if_}}
                                      </td>
                                      <td class="text-right" colspan="3">TOTAL</td>
                                      <td class="text-right">S/ {{monto_total_venta}}</td>
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
        
        <script id="tpl8Series" type="handlebars-x">
            {{#.}}
            <option value="{{serie}}" data-correlativo="{{correlativo}}"">{{serie}}</option>
            {{/.}}
        </script> 

        <?php include './partials/_globals/footer.php'; ?>
           
        </div><!-- /.main-container -->
       <?php  include '_js/main.js.php'; ?>
       <script src="js/ventas/RegistrarVentas.3.js"></script>
       <script src="js/ventas/ListarVentas.2.js"></script>
       <script src="js/ventas/ventas.vista.2.js"></script>
    </body>

</html>



