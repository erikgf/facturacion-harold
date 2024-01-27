<?php

include '../datos/local_config_web.php';
$TITULO_PAGINA = "Gestionar Cotizaciones";
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
                    <div class="col-ms-12">
                      <div class="tabbable">
                      <ul class="nav nav-tabs padding-12 tab-color-blue background-blue" id="myTab4">
                        <li class="active">
                          <a data-toggle="tab" href="#tabRegistrarCotizaciones" aria-expanded="true">Registrar Cotizaciones</a>
                        </li>

                        <li class="">
                          <a data-toggle="tab" href="#tabListadoCotizaciones" aria-expanded="false">Listado de Cotizaciones</a>
                        </li>
                      </ul>

                      <div class="tab-content">
                        <div id="tabRegistrarCotizaciones" class="tab-pane active">
                          <?php include '_registrarcotizaciones.cotizaciones.vista.php'; ?>
                        </div>
                        <div id="tabListadoCotizaciones" class="tab-pane">
                          <?php include '_listacotizaciones.cotizaciones.vista.php'; ?>
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

            <div id="mdlDetalleCotizacion" class="modal fade" tabindex="-1" style="display: none;">
              <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                      <h3 class="smaller lighter blue no-margin"></h3>
                    </div>
                    <div class="modal-body">
                     <script type="handlebars-x" id="tpl8DetalleCotizacion">
                      <div class="row">
                        <div class="col-xs-12 col-sm-3">
                          <h5><b>Cotización: </b> {{cabecera.comprobante}}</h5>
                        </div>
                        <div class="col-xs-12 col-sm-3">
                          <h5><b>Fecha: </b> {{cabecera.fecha_cotizacion}}</h5>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                          <h5><b>Correo Cliente: </b> {{cabecera.correo_envio}}</h5>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-xs-12 col-sm-3">
                          <h5><b>Días Crédito: </b> {{cabecera.condicion_dias_credito}}</h5>
                        </div>
                        <div class="col-xs-12 col-sm-3">
                          <h5><b>Días Validez: </b> {{cabecera.condicion_dias_validez}}</h5>
                        </div>
                        <div class="col-xs-12 col-sm-3">
                          <h5><b>Días Entrega: </b> {{cabecera.condicion_dias_entrega}}</h5>
                        </div>
                        <div class="col-xs-12 col-sm-3">
                          <h5><b>Delivery: </b> {{cabecera.condicion_delivery}}</h5>
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
                                      <th class="text-left">Marca</th>
                                      <th class="text-center" style="width:140px">Cant.</th>
                                      <th class="text-center" style="width:120px">P.U </th>
                                      <th class="text-center" style="width:120px">Monto</th>
                                    </tr>
                                  </thead> 
                                  <tbody id="tbllista_detalle" class="tr-middle-align">    
                                      {{#detalle}}
                                        <tr >
                                          <td class="text-center">{{item}}</td>
                                          <td class="text-left"">{{codigo_producto}}</td>
                                          <td class="text-left"">{{producto}}</td>
                                          <td class="text-left"">{{marca}}</td>
                                          <td class="text-center">{{cantidad}}</td>
                                          <td class="text-center">{{precio_unitario}}</td>
                                          <td class="text-center">S/ {{subtotal}}</td>
                                        </tr>
                                      {{/detalle}}
                                  </tbody>
                                  <tfoot>
                                    <tr style="font-size:1.1em">
                                      <td class="text-right" colspan="6">SUBTOTAL </td>
                                      <td class="text-center">S/{{cabecera.subtotal}}</td>
                                    </tr>
                                    <tr style="font-size:1.1em">
                                      <td class="text-right" colspan="6">IGV 18.00% </td>
                                      <td class="text-center">S/{{cabecera.monto_igv}}</td>
                                    </tr>
                                    <tr style="font-size:1.25em">
                                      <td class="text-center" colspan="2">
                                       {{#if_ cabecera.tipo_comprobante '!=' ''}}
                                        <button class="btn btn-sm btn-primary" onclick="app.ListarCotizaciones.verComprobante({{cabecera.x_cod_transaccion}})">
                                          <i class="ace-icon fa fa-file"></i>
                                          VER COTIZACION
                                        </button>
                                       {{/if_}}
                                      </td>
                                      <td class="text-right" colspan="4">TOTAL</td>
                                      <td class="text-center">S/ {{cabecera.importe_total}}</td>
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
                <option value='{{codigo}}'>{{nombre}}</option>
                {{/.}}
            </script>   

            <?php include 'footer.php'; ?>
           
        </div><!-- /.main-container -->


        <?php  include '_js/main.js.php';
          if (MODO_PRODUCCION == "1"){
              echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>';
            } else {
              echo '<script src="../assets/js/chosen.jquery.min.js"></script>';
            }
        ?>
       <script type="text/javascript">
          var ___ad = '<?php echo $_SESSION["usuario"]["cod_rol"] == "1";?>';
       </script>
       <script src="js/cotizaciones.vista.js<?php echo '?'.time();?>"></script>
    </body>

</html>



+