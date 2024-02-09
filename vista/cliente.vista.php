<?php

include '../datos/local_config_web.php';
$TITULO_PAGINA = "Gestión de Clientes";
?>

<!DOCTYPE html>
<html leng="es">
    <head>
          <meta charset="utf-8" />
          <title><?php echo $TITULO_PAGINA ?></title>
          <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
          <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />

          <?php include '_css/main.css.php'; ?>
    </head>
    <body class="no-skin">
        <?php include './partials/_globals/navbar.php'; ?>
        <div class="main-container ace-save-state" id="main-container">
            <?php include './partials/_globals/menu.php'; ?>
            <div class="main-content">
              <div class="main-content-inner">
                <?php include './partials/_globals/breadcrumb.mantenimiento.php' ?>
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
                    <div class="col-xs-12">
                      <div class="row">
                        <div class="col-sm-offset-9 col-sm-3">
                          <a type="button" class="btn btn-block btn-primary" data-toggle="modal" data-target="#mdlRegistro" onclick="app.agregar()">
                          <i class="fa fa-plus bigger-120 white"></i> NUEVO REGISTRO</a>
                        </div>
                      </div><!-- /.row -->

                      <div class="space-6"></div>

                      <div class="row">
                        <div class="col-xs-12">
                          <div  id="listado" class="table-responsive">           
                            <script id="tpl8Listado" type="handlebars-x">
                              <table class="table table-striped table-bordered table-hover dataTable dt-responsive"  cellspacing="0" width="100%">
                                  <thead>
                                    <tr>
                                      <th width="100px">Acción</th>
                                      <th width="100px">Documento</th>
                                      <th>Razón Social</th>
                                      <th width="120px">Celular / Núm. Contacto</th>
                                      <th width="200px">Correo</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                      {{#this}}
                                        <tr>
                                            <td>
                                                <button title="Editar" class="btn btn-xs btn-warning" onclick ="app.editar({{id}})" data-toggle="modal" data-target="#mdlRegistro">
                                                <i class="fa fa-edit bigger-130"></i>
                                                </button>
                                                <button title="Eliminar" class="btn btn-xs btn-danger" onclick ="app.eliminar({{id}})">
                                                <i class="fa fa-trash bigger-130"></i>
                                                </button>
                                            </td>
                                            <td class="text-center">{{#if_ id_tipo_documento '==' '0'}} SIN DOCUMENTO {{else}} {{tipo_documento.abrev}} - [{{numero_documento}}]{{/if_}}</td>
                                            <td>{{razon_social}}</td>
                                            <td>{{celular}}</td>
                                            <td>{{correo}}</td>
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

           <?php include './partials/_globals/footer.php'; ?>
           
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
                              <div class="col-xs-4">
                                <div class="form-group">
                                  <label class="control-label">Tipo Documento: </label>
                                  <select name="cbotipodocumento" id="cbotipodocumento" class="form-control" required="">
                                    <option value="0">S/D</option>
                                    <option value="1">DNI</option>
                                    <option value="4">CARNET EXT.</option>
                                    <option value="6">RUC</option>
                                    <option value="7">PASAPORTE</option>
                                  </select>
                                </div>
                              </div>  
                              <div class="col-xs-8" id="blknumerodocumento" style="display:none;">
                                <div class="form-group">
                                  <label class="control-label">Número documento: </label>
                                  <input type="text" name="txtnumerodocumento" id="txtnumerodocumento" class="form-control" minlength="8" maxlength="12" placeholder="Número documento...">
                                </div>
                              </div>                            
                        </div>
                        <div class="row">
                              <div class="col-xs-6">
                                <div class="form-group">
                                  <label class="control-label">Nombres: </label>
                                  <input type="text" name="txtnombres" id="txtnombres" class="form-control" placeholder="Nombres..." required="">
                                </div>
                              </div>
                              <div class="col-xs-6">
                                <div class="form-group">
                                  <label class="control-label">Apellidos: </label>
                                  <input type="text" name="txtapellidos" id="txtapellidos" class="form-control" placeholder="Apellidos..." required="">
                                </div>
                              </div>

                              <div class="col-xs-12 col-sm-8">
                                <div class="form-group" style="display:none;">
                                  <label class="control-label">Razón Social: </label>
                                  <input type="text" name="txtrazonsocial" id="txtrazonsocial" class="form-control" placeholder="Razón Social..." >
                                </div>
                              </div>

                              <div class="col-xs-12 col-sm-4">
                                <div class="form-group" style="display:none;">
                                  <label class="control-label">Núm. Contacto: </label>
                                  <input type="text" name="txtnumerocontacto" id="txtnumerocontacto" class="form-control" placeholder="Número Contacto...">
                                </div>
                              </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-6">
                                <div class="form-group">
                                  <label class="control-label">Celular: </label>
                                  <input type="text" name="txtcelular" minlength="9" maxlength="9" id="txtcelular" class="form-control" placeholder="Celular..." >
                                </div>
                                <div class="form-group" style="display:none;">
                                  <label class="control-label">Dirección: </label>
                                  <textarea name="txtdireccion"  id="txtdireccion" class="form-control" placeholder="Dirección..." ></textarea>
                                </div>
                            </div>

                            <div class=" col-xs-6">
                                <div class="form-group">
                                  <label class="control-label">Correo: </label>
                                  <input type="email" name="txtcorreo" id="txtcorreo" class="form-control" placeholder="Correo..." >
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-4">
                                <div class="form-group">
                                  <label class="control-label">Sexo: </label>
                                  <select name="cbosexo" id="cbosexo" class="form-control" required="">
                                    <option value="M">Masculino</option>
                                    <option value="F">Femenino</option>
                                  </select>
                                </div>
                            </div>
                            <div class="col-xs-offset-2 col-xs-4">
                                <div class="form-group">
                                  <label class="control-label">Fecha Nacimiento: </label>
                                  <input type="date" name="txtfechanacimiento" id="txtfechanacimiento" class="form-control" >
                                </div>
                            </div>
                        </div>
                      </div>
                      <script id="tpl8Combo" type="handlebars-x">
                          <option value="">Seleccionar {{rotulo}}</option>
                        {{#opciones}}
                          <option value='{{codigo}}'>{{descripcion}}</option>
                        {{/opciones}}
                      </script>                                

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

        <?php  include '_js/main.js.php'; ?>
        <script src="js/cliente.vista.1.js" type="text/javascript"></script>
    </body>
</html>



