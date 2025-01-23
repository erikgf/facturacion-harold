<?php

include '../datos/local_config_web.php';

$TITULO_PAGINA = "Resumenes Diarios";
$fechaHoy = date('Y-m-d');
$fechaHaceSemana = date("Y-m-d", strtotime("- 7 days"));

?>

<!DOCTYPE html>
<html leng="es">
    <head>
          <meta charset="utf-8" />
          <title><?php echo $TITULO_PAGINA ?></title>
          <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
          <link rel="icon" type="image/jpeg" href="../imagenes/logo_peque.jpg" />
          <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />

          <?php include '_css/main.css.php';  ?>
          <style>
            .table-responsive{
              max-height: 350px;
            }
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
                    
                    <div class="col-xs-12 col-sm-3 col-md-2">
                      <div class="control-group">
                        <br><button class="btn btn-info btn-block" id="btnbuscar">BUSCAR</button>
                      </div>
                    </div>
                  </div>

                  <div class="space-6"></div>
                  <div class="row" id="alert-blk">
                    <div class="col-xs-12"></div>
                  </div>

                  <h4>Lista de Resúmenes: 
                    <div class="pull-right">
                      <button type="button" class="btn btn-block btn-primary" id="btn-nuevo">
                      <i class="fa fa-plus bigger-120 white"></i> NUEVO REGISTRO</button>
                    </div>
                  </h4>    

                  <div class="row">
                        <div class="col-sm-12 col-xs-12">
                          <div class="table-responsive">   
                             <table id="tbl-listado" class="table table-striped table-bordered table-hover dataTable dt-responsive"  cellspacing="0" width="100%">
                                  <thead>
                                    <tr>
                                      <th width="60px">Opc.</th>
                                      <th>Nombre Comprobante</th>
                                      <th class="text-center" width="140px">Fecha Emisión</th>
                                      <th class="text-center" width="140px">Fecha Generación</th>
                                      <th class="text-center" width="160px">Ticket</th>
                                      <th class="text-center" width="120px">Estado</th>
                                    </tr>
                                  </thead>    
                                  <tbody>
                                    <script id="tpl8Listado" type="handlebars-x">                             
                                      {{#.}}
                                        <tr data-id="{{id}}" data-comprobante="{{nombre_resumen}}"> 
                                            <td>
                                              <button class="btn btn-xs btn-warning btn-ver">
                                                <i class="fa fa-eye bigger-130"></i>
                                              </button>
                                            </td>
                                            <td>{{nombre_resumen}}</td>
                                            <td class="text-center">{{fecha_emision}}</td>
                                            <td class="text-center">{{fecha_generacion}}</td>
                                            <td class="text-center">{{ticket}}</td>
                                            <td class="text-center">
                                              <span class="label label-{{estado.color}}">
                                                {{estado.rotulo}}
                                              </span>
                                              <br><small>{{cdr_descripcion}}</small>
                                            </td>
                                        </tr>
                                      {{/.}}
                                    </script>   
                                </tbody>
                            </table>                                 
                          </div>  <!-- table-responsive --> 
                        </div> 
                  </div>

                  <div id="mdl-registro" class="modal fade" tabindex="-1" style="display: none;">
                    <div class="modal-dialog">
                      <form id="frm-registro">
                          <div class="modal-content">
                            <div class="modal-header">
                              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                              <h3 class="smaller lighter blue no-margin">Nuevo Resumen Diario</h3>
                            </div>

                            <div class="modal-body">
                              <div class="row">
                                  <div class="col-xs-6 col-sm-3">
                                    <div class="control-group">
                                        <label class="control-label">Fecha Emisión: </label>
                                        <input type="date" name="txt-registro-fechaemision" id="txt-registro-fechaemision"  class="form-control"/>
                                    </div>
                                  </div>
                                  <div class="col-xs-6 col-sm-3">
                                    <div class="control-group">
                                        <label class="control-label">Fecha Generación: </label>
                                        <input type="date" readonly name="txt-registro-fechageneracion" value="<?php echo $fechaHoy?>"  class="form-control"/>
                                    </div>
                                  </div>
                                  <div class="col-xs-6 col-sm-3">
                                    <div class="control-group">
                                        <label class="control-label">Status: </label>
                                        <select name="cbo-status" id="cbo-status" class="form-control">
                                          <option value="1" selected>1: Registro</option>
                                        </select>
                                    </div>
                                  </div>
                              </div>

                              <div class="row">
                                <div class="col-xs-12">
                                <div class="table-responsive">   
                                    <table id="tbl-registro-boletas" class=" small table table-striped table-bordered table-hover dataTable dt-responsive"  cellspacing="0" width="100%">
                                          <thead>
                                            <tr>
                                              <th style="width:50px">Sel.</th>
                                              <th>Comprobante</th>
                                              <th style="width:120px">ND. Cliente</th>
                                              <th style="width:110px" class="text-center" >Importe Gravadas</th>
                                              <th style="width:110px">Importe IGV</th>
                                              <th class="text-center" style="width:110px">Importe Total</th>
                                            </tr>
                                          </thead>    
                                          <tbody>
                                            <script id="tpl8ListadoRegistroBoletas" type="handlebars-x">                             
                                              {{#.}}
                                                <tr data-id="{{id}}"> 
                                                    <td class="text-center">
                                                      <input type="checkbox" data-id="{{id}}" class="chkselect" checked>
                                                    </td>
                                                    <td>{{id_tipo_comprobante}} | {{serie}}-{{correlativo}}</td>
                                                    <td>{{numero_documento_cliente}}</td>
                                                    <td class="text-right">{{id_tipo_moneda}} {{total_gravadas}}</td>
                                                    <td class="text-right">{{id_tipo_moneda}} {{total_igv}}</td>
                                                    <td class="text-right">{{id_tipo_moneda}} {{importe_total}}</td>
                                                </tr>
                                              {{/.}}
                                          </script>   
                                        </tbody>
                                    </table>                                 
                                  </div>  <!-- table-responsive --> 
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
                          </div><!-- /.modal-content -->
                      </form>
                    </div>
                  </div>

                  <div id="mdl-leer" class="modal fade" tabindex="-1" style="display: none;">
                    <div class="modal-dialog">
                        <div class="modal-content">
                          <script id="tpl8LeerRegistro" type="handlebars-x">
                            <div class="modal-header">
                              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                              <h3 class="smaller lighter blue no-margin">Resumen Diario: {{nombre_resumen}}</h3>
                            </div>
                            <div class="modal-body">
                              <div class="row">
                                  <div class="col-xs-6 col-sm-3">
                                    <div class="form-group">
                                        <label class="control-label">Fecha Emisión: </label>
                                        <input type="date" readonly name="txt-leer-fechaemision" value="{{fecha_emision}}" class="form-control"/>
                                    </div>
                                  </div>
                                  <div class="col-xs-6 col-sm-3">
                                    <div class="form-group">
                                        <label class="control-label">Fecha Generación: </label>
                                        <input type="date" readonly name="txt-leer-fechageneracion" value="{{fecha_generacion}}" class="form-control"/>
                                    </div>
                                  </div>
                                  <div class="col-xs-6 col-sm-3">
                                    <div class="form-group">
                                        <label class="control-label">Estado: </label>
                                        <span class="label label-{{estado.color}} label-lg">{{estado.rotulo}}</span>
                                    </div>
                                  </div>
                              </div>

                              <div class="row">
                                {{#if_ enviar_a_sunat '==' '0'}}
                                <div class="col-sm-6">
                                  <div class="form-group">
                                    <button type="button" class="btn btn-success btn-enviarsunat" data-id="{{id}}">
                                      <i class="fa fa-send"></i>
                                      <span>ENVIAR A SUNAT</span>
                                    </button>
                                  </div>
                                </div>
                                {{/if_}}
                                {{#ticket}}
                                <div class="col-sm-6">
                                  <div class="form-group">
                                    <strong>Ticket: {{this}} </strong>
                                  </div>
                                </div>
                                {{/ticket}}
                              </div>

                              <div class="row">
                                {{#if_ enviar_a_sunat '==' '1'}}
                                <div class="col-sm-6">
                                  <div class="form-group">
                                    <button type="button" class="btn btn-primary btn-consultarticket" data-id="{{id}}">
                                      <i class="fa fa-file"></i>
                                      <span>CONSULTAR TICKET</span>
                                    </button>
                                  </div>
                                </div>
                                {{/if_}}
                                {{#cdr_descripcion}}
                                <div class="col-sm-6">
                                  <div class="form-group">
                                    <strong>CDR: {{this}}</strong>
                                  </div>
                                </div>
                                {{/cdr_descripcion}}
                              </div>
                           

                              <div class="row">
                                <div class="col-xs-12">
                                <div class="table-responsive">   
                                    <table id="tbl-leer-boletas" class=" small table table-striped table-bordered table-hover dataTable dt-responsive"  cellspacing="0" width="100%">
                                          <thead>
                                            <tr>
                                              <th>Comprobante</th>
                                              <th style="width:50px">Status</th>
                                              <th style="width:120px">ND. Cliente</th>
                                              <th style="width:120px" class="text-center" >Importe Gravadas</th>
                                              <th style="width:120px">Importe IGV</th>
                                              <th class="text-center" style="width:120px">Importe Total</th>
                                            </tr>
                                          </thead>    
                                          <tbody>
                                              {{#detalle}}
                                                <tr data-id="{{id}}"> 
                                                    <td>{{id_tipo_comprobante}} | {{serie_comprobante}}-{{correlativo_comprobante}}</td>
                                                    <td class="text-center">{{status}}</td>
                                                    <td>{{numero_documento_cliente}}</td>
                                                    <td class="text-right">{{id_tipo_moneda}} {{importe_gravadas}}</td>
                                                    <td class="text-right">{{id_tipo_moneda}} {{importe_igv}}</td>
                                                    <td class="text-right">{{id_tipo_moneda}} {{importe_total}}</td>
                                                </tr>
                                              {{/detalle}}
                                        </tbody>
                                    </table>                                 
                                  </div>  <!-- table-responsive --> 
                                </div>
                              </div>
                            </div>
                            <div class="modal-footer">
                              <button class="btn btn-sm btn-danger pull-right" data-dismiss="modal">
                                <i class="ace-icon fa fa-times"></i>
                                Cerrar
                              </button>
                            </div>
                          </script>
                        </div><!-- /.modal-content -->
                    </div>
                  </div>
                </div><!-- /.page-content -->
              </div><!-- /main-content-inner -->
            </div><!-- /.main-content -->


          <?php include './partials/_globals/footer.php'; ?>
        </div><!-- /.main-container -->

        <?php  include '_js/main.js.php'; ?>
        <script src="js/fact.resumendiarios.vista.js" type="text/javascript"></script>
    </body>

</html>



