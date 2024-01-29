<?php

include '../datos/local_config_web.php';
$TITULO_PAGINA = "Gestión de Personal";
?>

<!DOCTYPE html>
<html leng="es">
    <head>
          <meta charset="utf-8" />
          <title><?php echo $TITULO_PAGINA ?></title>
          <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
          <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />

          <?php  include '_css/main.css.php'; ?>
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
                        <div class="col-sm-offset-6 col-sm-3">
                          <a type="button" class="btn btn-block btn-success" onclick="app.cambiarClave()">
                          <i class="fa fa-key bigger-120"></i> CAMBIAR CLAVE </a>
                        </div>
                        <div class="col-sm-3">
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
                                      <th width="100px">N.D.</th>
                                      <th>Nombres y Apellidos</th>
                                      <th width="120px">Celular</th>
                                      <th width="200px">Correo</th>
                                      <th width="170px">Cargo</th>
                                      <th width="100x">Estado</th>
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
                                            <td>{{numero_documento}}</td>
                                            <td>{{nombres_apellidos}}</td>
                                            <td>{{celular}}</td>
                                            <td>{{email}}</td>
                                            <td>{{rol.nombre}}</td>
                                            <td><span class="badge badge-{{color_estado}}">{{descripcion_estado}}</span></td>
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
                                <div class="control-group">
                                  <label class="control-label">DNI: </label>
                                  <input type="text" name="txtdni" id="txtdni" class="form-control" minlength="8" maxlength="8" placeholder="DNI..." required="">
                                </div>
                              </div>                            
                        </div>
                        <div class="row">
                              <div class="col-xs-12">
                                <div class="control-group">
                                  <label class="control-label">Nombres: </label>
                                  <input type="text" name="txtnombres" id="txtnombres" class="form-control" placeholder="Nombres..." required="">
                                </div>
                              </div>
                              <!--
                              <div class="col-xs-6">
                                <div class="control-group">
                                  <label class="control-label">Apellidos: </label>
                                  <input type="text" name="txtapellidos" id="txtapellidos" class="form-control" placeholder="Apellidos..." required="">
                                </div>
                              </div>
                              -->
                        </div>
                        <div class="row">
                            <div class="col-xs-4">
                                <div class="control-group">
                                  <label class="control-label">Celular: </label>
                                  <input type="text" name="txtcelular" minlength="9" maxlength="9" id="txtcelular" class="form-control" placeholder="Celular..." >
                                </div>
                            </div>
                            <div class="col-xs-offset-2 col-xs-6">
                                <div class="control-group">
                                  <label class="control-label">Correo: </label>
                                  <input type="email" name="txtcorreo" id="txtcorreo" class="form-control" placeholder="Correo..." >
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-4">
                                <div class="control-group">
                                  <label class="control-label">Sexo: </label>
                                  <select name="cbosexo" id="cbosexo" class="form-control" required="">
                                    <option value="M">Masculino</option>
                                    <option value="F">Femenino</option>
                                  </select>
                                </div>
                            </div>
                            <div class="col-xs-4">
                                <div class="control-group">
                                  <label class="control-label">Fecha Nacimiento: </label>
                                  <input type="date" name="txtfechanacimiento" id="txtfechanacimiento" class="form-control" >
                                </div>
                            </div>
                            <div class="col-xs-4">
                                <div class="control-group">
                                  <label class="control-label">Fecha Ingreso: </label>
                                  <input type="date" name="txtfechaingreso" id="txtfechaingreso" class="form-control" >
                                </div>
                            </div>
                        </div>

                        <div class="row">
                          <!--
                            <div class="col-xs-4">
                                <div class="control-group">
                                  <label class="control-label">Cargo: </label>
                                  <select name="cbocargo" id="cbocargo" class="form-control" required=""></select>
                                </div>
                            </div>
                          -->
                            <div class="col-xs-4">
                                <div class="control-group">
                                  <label class="control-label">Rol: </label>
                                  <select name="cborol" id="cborol" class="form-control" required=""></select>
                                </div>
                            </div>
                            <div class="col-xs-4">
                                <div class="control-group">
                                  <label class="control-label">Acceso Sistema: </label>
                                  <br><input type="checkbox" name="chkacceso" id="chkacceso" style="width: 100%;height: 20px;"/>
                                </div>
                            </div>
                            <div class="col-xs-4">
                                  <div class="control-group">
                                    <label class="control-label">Estado: </label>
                                    <select name="cboestado" id="cboestado" class="form-control" required="">
                                      <option value='A'>ACTIVO</option>
                                      <option value='I'>INACTIVO</option>
                                    </select>
                                  </div>
                            </div>
                        </div>

                        <div class="row">
                          <!-- 
                            <div class="col-xs-4">
                                  <div class="control-group">
                                    <label class="control-label">Sucursal: </label>
                                    <select name="cbosucursal" id="cbosucursal" class="form-control" required=""></select>
                                  </div>
                            </div>
                          -->
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

        <script id="tpl8DniClave" type="handlebars-x">
            <option value="">Seleccionar personal</option>
            {{#this}}
                <option value='{{id}}'>{{numero_documento}} - {{nombres_apellidos}}</option>
            {{/this}}
        </script>                                


        <div id="mdlCambiarClave" class="modal fade" tabindex="-1" style="display: none;">
            <div class="modal-dialog">
              <form id="frmgrabarclave">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h3 class="smaller lighter blue no-margin">Cambio de Clave Personal</h3>
                      </div>

                      <div class="modal-body">
                        <div class="row">
                              <div class="col-xs-12">
                                <div class="control-group">
                                  <label class="control-label">DNI: </label>
                                  <select name="txtdniclave" id="txtdniclave"class="form-control" required></select>
                                </div>
                              </div>                     
                        </div>
                        <br>
                        <div class="row">
                              <div class="col-xs-6">
                                <div class="control-group">
                                  <label class="control-label">Nueva Clave: </label>
                                  <span class="input-icon input-icon-right">
                                    <input type="password" name="txtnuevaclave" maxlength="30" minlength="6" id="txtnuevaclave" class="form-control" required="">
                                    <i onmouseover="app.verClave();" title="Mostrar Contraseña" onmouseout="app.esconderClave();" class="ace-icon fa fa-eye gray"></i>
                                  </span>
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

        <?php  include '_js/main.js.php'; ?>
        <script src="js/personal.vista.js"></script>
    </body>

</html>



