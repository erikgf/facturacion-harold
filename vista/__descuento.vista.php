<?php

include '../datos/local_config_web.php';
$TITULO_PAGINA = "Gestión de Descuentos";
?>

<!DOCTYPE html>
<html leng="es">
    <head>
          <meta charset="utf-8" />
          <title><?php echo $TITULO_PAGINA ?></title>
          <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
          <link rel="icon" type="image/jpeg" href="../imagenes/logo_peque.jpg" />
          <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />

          <?php  include '_css/main.css.php'; ?>
    </head>
    <body class="no-skin">
        <?php include 'navbar.php'; ?>

        <div class="main-container ace-save-state" id="main-container">
             <?php include 'menu.php'; ?>

            <div class="main-content">
              <div class="main-content-inner">

                <?php include 'breadcrumb.transacciones.php' ?>

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
                    <div class="col-sm-12">
                      <!-- PAGE CONTENT BEGINS -->
                      <!-- USARE ESTE ALERT PARA DESPUES
                      <div class="alert alert-block alert-success">
                        <button type="button" class="close" data-dismiss="alert">
                          <i class="ace-icon fa fa-times"></i>
                        </button>

                        <i class="ace-icon fa fa-check green"></i>

                        Welcome to
                        <strong class="green">
                          Ace
                          <small>(v1.4)</small>
                        </strong>, лёгкий, многофункциональный и простой в использовании шаблон для админки на bootstrap 3.3.6. Загрузить исходники с <a href="https://github.com/bopoda/ace">github</a> (with minified ace js/css files).
                      </div>
                     -->

                      <div class="row">
                        <div class="col-xs-offset-9 col-xs-3">
                          <a type="button" class="btn btn-block btn-primary" data-toggle="modal" data-target="#mdlRegistro" onclick="app.agregar()">
                          <i class="fa fa-plus bigger-120 white"></i> NUEVO REGISTRO</a>
                        </div>
                      </div><!-- /.row -->

                      <div class="space-6"></div>

                      <div class="row">
                        <div class="col-xs-6 col-sm-4">
                          <div class="control-group">
                            <label>Filtrar por Estado</label>
                            <select id="cbofiltroestado" value="form-control">
                                <option value="*">Ambos</option>
                                <option value="1">Utilizados</option>
                                <option value="0" selected>No utilizados</option>
                            </select>
                          </div>

                        </div>
                      </div>

                      <div class="row">
                        <div class="col-xs-12 col-md-10">
                          <div  id="listado" class="table-responsive">           
                            <script id="tpl8Listado" type="handlebars-x">
                              <table class="table table-striped table-bordered table-hover dataTable dt-responsive"  cellspacing="0" width="100%">
                                  <thead>
                                    <tr>
                                      <th width="100px">Acción</th>
                                      <th>Código </th>
                                      <th>Tipo</th>
                                      <th>Monto</th>
                                      <th>Utilizado por</th>
                                      <th>Estado</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                      {{#this}}
                                        <tr>
                                            <td>
                                              {{^fecha_hora_uso}}
                                                <button class="btn btn-xs btn-warning" onclick ="app.editar({{cod_descuento}})" data-toggle="modal" data-target="#mdlRegistro">
                                                <i class="fa fa-edit bigger-130"></i>
                                                </button>
                                                <button class="btn btn-xs btn-danger" onclick ="app.eliminar({{cod_descuento}})">
                                                <i class="fa fa-trash bigger-130"></i>
                                                </button>
                                              {{/fecha_hora_uso}}
                                            </td>
                                            <td class="text-center">{{codigo_generado}}</td>
                                            <td class="text-center">{{tipo_descuento}}</td>
                                            <td class="text-center">{{monto_descuento}}</td>
                                            <td class="text-center">{{usuario_uso}} <br> {{fecha}} {{hora}}</td>
                                            <td class="text-center"><span class="badge badge-{{color_estado}}">{{estado}}</span></td>
                                         </tr>
                                      {{/this}}
                                  </tbody>
                              </table>
                           </script>                                    
                          </div>  <!-- table-responsive --> 
                        </div> 
                      </div>

                      <!-- PAGE CONTENT ENDS -->
                    </div><!-- /.col -->
                  </div><!-- /.row -->
                </div><!-- /.page-content -->
              </div>
            </div><!-- /.main-content -->

            <?php include 'footer.php'; ?>
        </div><!-- /.main-container -->

        <div id="mdlRegistro" class="modal fade" tabindex="-1" style="display: none;">
            <div class="modal-dialog">
              <form id="frmgrabar">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h3 class="smaller lighter blue no-margin">Mantenimiento</h3>
                      </div>

                      <div class="modal-body">
                        <div class="row">
                              <div class="col-xs-6">
                                <div class="control-group">
                                  <label class="control-label">Tipo Descuento: </label>
                                  <select class="form-control" id="cbotipodescuento" name="cbotipodescuento" required>
                                    <option value="">Seleccionar</option>
                                    <option value="P">PORCENTAJE</option>
                                    <option value="M">MONTO FIJO</option>
                                  </select>
                                </div>
                              </div>
                              <div class="col-xs-6">
                                <div class="control-group">
                                  <label class="control-label">Monto Descuento: </label>
                                  <input type="text" name="txtmontodescuento" id="txtmontodescuento" class="form-control" placeholder="Monto descuento..." required="">
                                </div>
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

        <?php  include '_js/main.js.php';?>
        <script src="js/descuento.vista.js" type="text/javascript"></script>
    </body>

</html>



