<?php

include '../datos/local_config_web.php';

$TITULO_PAGINA = "Generación - Envío Comprobantes";
$fechaHoy = date('Y-m-d');
$fechaHaceSemana = date("Y-m-d", strtotime("- 7 days"));

?>

<!DOCTYPE html>
<html leng="es">
    <head>
          <meta charset="utf-8" />
          <title><?php echo $TITULO_PAGINA ?></title>
          <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
          <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />

          <?php include '_css/main.css.php'; ?>
          <?php  include '_css/dataTableButtons.css.php'; ?>
          
          <style type="text/css">
              .detalle-rotulo{
                  font-size: 1.5em;
                  text-align: center;
              }
              .tr-seleccionado{
                background-color: #cedae8 !important;
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
                      <!--
                      <small>
                        <i class="ace-icon fa fa-angle-double-right"></i>
                        Gestión y mantenimiento
                      </small>
                      -->
                    </h1>
                  </div><!-- /.page-header -->

                  <div class="row">     
                    <div class="col-xs-6 col-sm-3 col-md-2">
                      <div class="control-group">
                        <label class="control-label">Desde Fecha</label>
                        <input type="date" value="<?php echo $fechaHaceSemana; ?>" class="form-control input-sm" id="txtfechadesde">
                      </div>
                    </div>
                    <div class="col-xs-6 col-sm-3 col-md-2">
                      <div class="control-group">
                        <label class="control-label">Hasta Fecha</label>
                        <input type="date" value="<?php echo $fechaHoy; ?>" class="form-control input-sm" id="txtfechahasta">
                      </div>
                    </div>
                    
                    <div class="col-xs-6 col-sm-3 col-md-2">
                      <div class="control-group">
                       <br>
                        <div class="checkbox">
                          <label>
                            <input name="chktodos" type="checkbox" id="chktodos" class="ace">
                            <span class="lbl"> TODAS LAS FECHAS</span>
                          </label>
                        </div>
                      </div>
                    </div>

                    <div class="col-xs-6 col-sm-3 col-md-2">
                      <div class="control-group">
                        <label class="control-label">T. Comprobante</label>
                        <select  class="form-control" id="cbotipocomprobante">
                          <option value="*" selected>TODOS</option>
                          <option value="01">FACTURAS</option>
                          <option value="03">BOLETAS</option>
                          <option value="07">N. CRÉDITOS</option>
                          <option value="08">N. DÉBITOS</option>
                        </select>
                      </div>
                    </div>
                    
                    <div class="col-xs-6 col-sm-3 col-md-2">
                      <div class="control-group">
                        <label class="control-label">Estado Envío</label>
                        <select  class="form-control" id="cboestado">
                          <option value="P">PENDIENTES</option>
                          <option value="F">GENERADOS Y FIRMADOS</option>
                          <option value="A">ENVIADOS Y ACEPTADOS</option>
                          <option value="R">ENVIADOS Y RECHAZADOS</option>
                          <option value="T">TODOS</option>
                        </select>
                      </div>
                    </div>
                    
                    <div class="col-xs-12 col-sm-3 col-md-2">
                      <div class="control-group">
                        <br><button class="btn btn-info btn-block" id="btnbuscar">BUSCAR</button>
                      </div>
                    </div>
                  </div>
                  <hr>
                  <div class="row" style="display:none">
                    <div class="col-xs-6 col-sm-3 col-md-3">
                        <button class="btn btn-danger btn-block" id="btngenerarenviar" disabled>GENERAR Y ENVIAR SUNAT</button>
                    </div>
                  </div>

                  <div class="space-6"></div>
                  <div class="row" id="alert-blk-global">
                    <div class="col-xs-12"></div>
                  </div>
                  <div class="row" id="alert-blk">
                    <div class="col-xs-12"></div>
                  </div>

                  <div class="space-6"></div>

                  <h4>Lista de Comprobantes: </h4>    
                  <div class="row">
                        <div class="col-sm-12 col-xs-12">
                          <div  id="listado" class="table-responsive">   
                             <table class="table table-striped table-bordered table-hover dataTable dt-responsive"  cellspacing="0" width="100%">
                                  <thead>
                                    <tr>
                                      <th class="notexport" width="120px">Opc.</th>
                                      <th width="130px">Comprobante</th>
                                      <th >Cliente</th>
                                      <th class="text-center" width="135px">Fecha Emisión</th>
                                      <th class="text-center" width="160px">Importe Gravadas</th>
                                      <th class="text-center" width="160px">Importe IGV</th>
                                      <th class="text-center" width="160px">Importe Total</th>
                                      <th class="text-center" width="70px">Generado</th>
                                      <th class="text-center" width="125px">Estado</th>
                                    </tr>
                                  </thead>    
                                  <tbody id="listado-body">
                                    <script id="tpl8Listado" type="handlebars-x">                             
                                              {{#this}}
                                                <tr data-id="{{id}}" data-comprobante="{{comprobante}}"> 
                                                    <td>
                                                      {{#if_ enviar_a_sunat '==' '0'}} 
                                                        <input type="checkbox" class="chkselect">                                                
                                                      {{/if_}}
                                                      <div class="btn-group">
                                                        <button data-toggle="dropdown" class="btn btn-inverse btn-xs dropdown-toggle" aria-expanded="false">
                                                          Opciones
                                                          <span class="ace-icon fa fa-caret-down icon-on-right"></span>
                                                        </button>

                                                        <ul data-id="{{id}}" class="dropdown-menu dropdown-inverse">
                                                          {{#if_ enviar_a_sunat '==' '0'}} 
                                                          <li class="btn-generar-xml">
                                                            <a href="#" onclick="app.generarSUNAT(this, {{id}},'{{comprobante}}')">Generar XML</a>
                                                          </li>      
                                                          {{/if_}}
                                                          <li>
                                                            <a href="#" onclick="app.verComprobante({{id}})">Ver comprobante</a>
                                                          </li>
                                                          {{#if_ fue_firmado '==' '1'}} 
                                                          <li>
                                                            <a href="#" onclick="app.descargarXML('{{xml_filename}}')">Descargar XML</a>
                                                          </li>
                                                            {{#if_ enviar_a_sunat '==' '0'}} 
                                                            <li>
                                                              <a href="#" onclick="app.enviarSUNAT(this, {{id}},'{{comprobante}}')"><b class="text-success">Enviar SUNAT<b></a>
                                                            </li>
                                                            {{/if_}}
                                                          {{/if_}}
                                                        </ul>
                                                      </div>
                                                    </td>
                                                    <td>{{comprobante}}</td>
                                                    <td>{{descripcion_cliente}}</td>
                                                    <td class="text-center">{{fecha_emision}}</td>
                                                    <td class="text-right">S/{{total_gravadas}}</td>
                                                    <td class="text-right">S/{{total_igv}}</td>
                                                    <td class="text-right">S/{{importe_total}}</td>
                                                    <td class="text-center">
                                                      <span class="label label-{{estado_generado.color}}">
                                                        <i class="ace-icon fa fa-{{estado_generado.icon}} bigger-120"></i>
                                                        {{estado_generado.rotulo}}
                                                      </span>
                                                    </td>
                                                    <td class="text-center td-estado">
                                                      {{#if_ enviar_a_sunat '==' '0'}}
                                                        <span class="label">
                                                          No Enviado
                                                        </span>
                                                      {{else}}
                                                        {{#if_ cdr_estado '==' '0'}}
                                                          <span class="label label-success">
                                                            Enviado y Aceptado
                                                          </span>
                                                          {{#cdr_descripcion}}<br> <small>{{this}}</small>{{/cdr_descripcion}} 
                                                        {{/if_}}
                                                        {{#if_ cdr_estado '!=' '0'}}
                                                          <span class="label label-danger">
                                                            Rechazado
                                                          </span>
                                                          {{#cdr_descripcion}}<br> <small>{{this}}</small>{{/cdr_descripcion}} 
                                                        {{/if_}}
                                                      {{/if_}}
                                                    </td>
                                                </tr>
                                              {{/this}}
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
            <?php include './partials/_globals/footer.php'; ?>
        </div><!-- /.main-container -->

        <?php  include '_js/main.js.php'; ?>
        <?php  include '_js/dataTableButtons.js.php'; ?>
        <script src="js/fact.comprobantes.vista.js" type="text/javascript"></script>
    </body>

</html>



