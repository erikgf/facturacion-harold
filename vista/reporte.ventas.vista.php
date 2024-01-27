<?php

include '../datos/local_config_web.php';
$TITULO_PAGINA = "Reporte de Ventas";
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

                  <h3>Datos resumen: </h3>   
                  <div class="row" id="resumen">
                    <script id="tpl8Resumen" type="handlebars-x">
                        <div class="col-xs-12 col-sm-6">
                            <div class="control-group">
                              <label class="control-label">Total Ventas</label>
                              <h4><b>S/ {{total}}</b> (Efectivo: S/ {{monto_efectivo}} | Tarjetas: S/ {{monto_tarjeta}} | Crédito: S/ {{monto_credito}})</h4>
                            </div>
                        </div>
                    </script>
                  </div>

                  <h4>Registros: </h4>                  
                  <div class="row">
                        <div class="col-sm-12 col-xs-12">
                          <div  id="listado" class="table-responsive">           
                            <script id="tpl8Listado" type="handlebars-x">
                              <table class="table table-striped table-bordered table-hover dataTable dt-responsive"  cellspacing="0" width="100%">
                                  <thead>
                                    <tr>
                                      <th width="125px">Código</th>
                                      <th width="125px">Comprobante</th>
                                      <th>Cliente</th>
                                      <th width="140px">Fecha Venta</th>
                                      <th width="125px">M. Efectivo</th>
                                      <th width="125px">M. Tarjeta</th>
                                      <th width="125px">M. Crédito</th>
                                      <th width="125px">Importe sin IGV</th>
                                      <th width="125px">IGV</th>
                                      <th width="125px">Importe Total</th>
                                      <th width="150px">Sucursal</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                      {{#this}}
                                        <tr>
                                            <td>{{codigo}}
                                              <button class="btn btn-xs" title="Ver Detalle" onclick ="app.verDetalle({{cod_transaccion}})">
                                                <i class="fa fa-eye bigger-130"></i>
                                             </button>
                                             </td>
                                            <td>{{comprobante}}</td>
                                            <td>{{cliente}}</td>
                                            <td>{{fecha_venta}}</td>
                                            <td>{{monto_efectivo}}</td>
                                            <td>{{monto_tarjeta}}</td>
                                            <td>{{monto_credito}}</td>
                                            <td>{{total_gravadas}}</td>
                                            <td>{{sumatoria_igv}}</td>
                                            <td>{{importe_total}}</td>
                                            <td>{{sucursal}}</td>
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
                        <div class="col-xs-12 col-sm-6 col-md-4">
                          <div class="control-group">
                            <label class="control-label"><b>Monto Efectivo</b></label>
                            <p id="txtefectivo_detalle" class="detalle-rotulo">{{cabecera.monto_efectivo}}</p>
                          </div>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-4">
                          <div class="control-group">
                            <label class="control-label"><b>Monto Tarjeta</b></label>
                            <p id="txttarjeta_detalle" class="detalle-rotulo">{{cabecera.monto_tarjeta}} {{#cabecera.tipo_tarjeta}} ({{this}}) {{/cabecera.tipo_tarjeta}}</p>
                          </div>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-4">
                          <div class="control-group">
                            <label class="control-label"><b>Monto Crédito</b></label>
                            <p id="txtcredito_detalle" class="detalle-rotulo">{{cabecera.monto_credito}}</p>
                          </div>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-4">
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
                                      <th class="width:90px">Cod.</th>
                                      <th class="text-left">Producto</th>
                                      <th class="text-left">F.V.</th>
                                      <th class="text-left">Lote</th>
                                      <th class="text-center" style="width:120px">P.U </th>
                                      <th class="text-center" style="width:140px">Cant.</th>
                                      <th class="text-center" style="width:120px">Subtotal</th>
                                    </tr>
                                  </thead> 
                                  <tbody id="tbllista_detalle" class="tr-middle-align">    
                                      {{#detalle}}
                                        <tr >
                                          <td class="text-center">{{item}}</td>
                                          <td class="text-left">{{codigo_producto}}</td>
                                          <td class="text-left">{{producto}}</td>
                                          <td class="text-left">{{fecha_vencimiento}}</td>
                                          <td class="text-left">{{lote}}</td>
                                          <td class="text-center">{{precio_unitario}}</td>
                                          <td class="text-center">{{cantidad}}</td>
                                          <td class="text-center">S/ {{subtotal}}</td>
                                        </tr>
                                      {{/detalle}}
                                  </tbody>
                                  <tfoot>
                                    <tr style="font-size:1.15em">
                                      <td class="text-right" colspan="7">SUBTOTAL </td>
                                      <td class="text-center">S/{{cabecera.subtotal}}</td>
                                    </tr>
                                    <tr style="font-size:1.55em">
                                      <td class="text-center" colspan="2">
                                       {{#if_ cabecera.tipo_comprobante '!=' ''}}
                                       <!--
                                        <button class="btn btn-sm btn-primary" onclick="app.verComprobante({{cabecera.x_cod_transaccion}})">
                                          <i class="ace-icon fa fa-file"></i>
                                          VER COMPROBANTE
                                        </button>
                                        -->
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

            <script id="tpl8Sucursal" type="handlebars-x">
              <option value="">Todas</option>
              {{#.}}
                <option value="{{cod_sucursal}}">{{nombre}}</option>
              {{/.}}
            </script>

            <script id="tpl8Cliente" type="handlebars-x">
              <option value="">Todos</option>
              {{#.}}
                <option value="{{cod_cliente}}">{{nombres}}</option>
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
        <script src="js/reporte.ventas.vista.js" type="text/javascript"></script>
    </body>

</html>



