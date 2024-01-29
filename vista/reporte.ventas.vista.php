<?php

include '../datos/local_config_web.php';
$TITULO_PAGINA = "Reporte de Ventas";
$fechaHoy = date('Y-m-d');

?>

<!DOCTYPE html>
<html leng="es">
    <head>
      <meta http-equiv="Content-Type" content="text/html; charset=gb18030">
      <title><?php echo $TITULO_PAGINA ?></title>
      <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
      <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
      <?php  include '_css/main.css.php'; ?>
      <?php  include '_css/dataTableButtons.css.php'; ?>
      
      <style type="text/css">
          .detalle-rotulo{
              font-size: 1.5em;
              text-align: center;
          }
      </style>

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
                      <!--
                      <small>
                        <i class="ace-icon fa fa-angle-double-right"></i>
                        Gestión y mantenimiento
                      </small>
                      -->
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
                    <div class="col-xs-12 col-sm-6 col-lg-4">
                        <div class="control-group">
                          <label class="control-label">Cliente</label>
                          <select  class="form-control" id="cbocliente">
                          </select>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-3 col-md-2">
                      <div class="control-group">
                        <br><button class="btn btn-info btn-block " id="btnbuscar">BUSCAR</button>
                      </div>
                    </div>
                  </div>

                  <div class="space-6"></div>

                  <h3>Datos resumen: </h3>   
                  <div class="row" id="blkresumen">
                    <script id="tpl8Resumen" type="handlebars-x">
                        <div class="col-xs-12 col-sm-12">
                            <div class="control-group">
                              <label class="control-label">Total Ventas</label>
                              <h4><b>S/ {{total}}</b> (Efectivo: S/ {{monto_efectivo}} | Tarjetas: S/ {{monto_tarjeta}} | Yape : S/ {{monto_yape}} | Plin: S/ {{monto_plin}} | Transf.: S/ {{monto_transferencia}} | Crédito: S/ {{monto_credito}})</h4>
                            </div>
                        </div>
                    </script>
                  </div>

                  <h4>Registros: </h4>                  
                  <div class="row">
                        <div class="col-sm-12 col-xs-12">
                          <div class="table-responsive">           
                              <table   id="tbllista"  class="table small table-striped table-bordered table-hover dataTable dt-responsive"  cellspacing="0" width="100%">
                                  <thead>
                                    <tr>
                                      <th class="notexport" width="100px">Opc.</th>
                                      <th width="125px">Comprobante</th>
                                      <th class="notexportpdf">Cliente</th>
                                      <th width="140px">Fecha Venta</th>
                                      <th width="125px">Efectivo</th>
                                      <th width="125px">Tarjeta</th>
                                      <th width="125px">Yape</th>
                                      <th width="125px">Plin</th>
                                      <th width="125px">Transf.</th>
                                      <th width="125px">Crédito</th>
                                      <th width="125px">Dsctos.</th>
                                      <th width="150px">Total</th>
                                      <th class="notexportpdf" width="150px">Sucursal</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <script id="tpl8Listado" type="handlebars-x">
                                      {{#.}}
                                        <tr>
                                            <td class="text-center">
                                              <button type="button" class="btn btn-xs btn-ver" data-id="{{id}}" title="Ver Detalle">
                                                  <i class="fa fa-eye bigger-130"></i>
                                              </button>
                                             </td>
                                            <td>{{comprobante}}</td>
                                            <td>{{cliente.nombres}} {{cliente.apellidos}}</td>
                                            <td>{{fecha_venta}}</td>
                                            <td class="text-right">{{monto_efectivo}}</td>
                                            <td class="text-right">{{monto_tarjeta}}</td>
                                            <td class="text-right">{{monto_yape}}</td>
                                            <td class="text-right">{{monto_plin}}</td>
                                            <td class="text-right">{{monto_transferencia}}</td>
                                            <td class="text-right">{{monto_credito}}</td>
                                            <td class="text-right text-danger bolder">- {{monto_descuento}}</td>
                                            <td class="text-right">{{monto_total_venta}}</td>
                                            <td>{{sucursal.nombre}}</td>
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
                                        <button data-id="{{id}}" type="button" class="btn btn-sm btn-primary btn-veratencion">
                                          <i class="ace-icon fa fa-file"></i>
                                          VER TICKET
                                        </button>
                                        
                                       {{#if_ id_tipo_comprobante '!=' '00'}}
                                        <button data-id="{{id}}" type="button" class="btn btn-sm btn-secondary btn-vercomprobante">
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

            <script id="tpl8Sucursal" type="handlebars-x">
              <option value="">Todas</option>
              {{#.}}
                <option value="{{id}}">{{nombre}}</option>
              {{/.}}
            </script>

            <script id="tpl8Cliente" type="handlebars-x">
              <option value="">Todos</option>
              {{#.}}
                <option value="{{id}}">{{descripcion}}</option>
              {{/.}}
            </script>

            <?php include './partials/_globals/footer.php'; ?>
        </div><!-- /.main-container -->

        <?php  include '_js/main.js.php'; ?>
        <?php  include '_js/dataTableButtons.js.php'; ?>
        <script src="js/reporte.ventas.vista.js" type="text/javascript"></script>
    </body>

</html>



