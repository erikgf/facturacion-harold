<?php

include '../datos/local_config_web.php';
$TITULO_PAGINA = "Gestión de Sucursal";
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
                    </h1>
                  </div><!-- /.page-header -->

                  <div class="row">
                    <div class="col-sm-12">
                      <div class="row">
                        <div class="col-xs-offset-9 col-xs-3">
                          <a type="button" class="btn btn-block btn-primary" data-toggle="modal" data-target="#mdlRegistro" onclick="app.agregar()">
                          <i class="fa fa-plus bigger-120 white"></i> NUEVO REGISTRO</a>
                        </div>
                      </div><!-- /.row -->

                      <div class="space-6"></div>

                      <div class="row">
                        <div class="col-xs-12 col-md-8">
                          <div  id="listado" class="table-responsive">           
                            <script id="tpl8Listado" type="handlebars-x">
                              <table class="table table-striped table-bordered table-hover dataTable dt-responsive"  cellspacing="0" width="100%">
                                  <thead>
                                    <tr>
                                      <th width="125px">Acción</th>
                                      <th>Nombre</th>
                                      <th>Dirección</th>
                                      <th width="150px">Teléfono</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                      {{#this}}
                                        <tr>
                                            <td>
                                                <button class="btn btn-xs btn-warning" onclick ="app.editar({{id}})" data-toggle="modal" data-target="#mdlRegistro">
                                                <i class="fa fa-edit bigger-130"></i>
                                                </button>
                                                {{#if_ id '!=' 1}}
                                                  <button class="btn btn-xs btn-danger" onclick ="app.eliminar({{id}})">
                                                  <i class="fa fa-trash bigger-130"></i>
                                                  </button>
                                                {{/if_}}                                              
                                            </td>
                                            <td>{{nombre}}</td>
                                            <td>{{direccion}}</td>
                                            <td>{{telefono}}</td>
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
                              <div class="col-xs-12">
                                <div class="control-group">
                                  <label class="control-label">Nombre: </label>
                                  <input type="text" name="txtnombre" id="txtnombre" class="form-control" placeholder="Nombre..." required="">
                                </div>
                              </div>
                        </div>
                        <div class="row">
                              <div class="col-xs-8">
                                <div class="control-group">
                                  <label class="control-label">Dirección: </label>
                                  <textarea class="form-control" name="txtdireccion" id="txtdireccion" placeholder="Dirección..."></textarea>
                                </div>
                              </div>
                              <div class="col-xs-4">
                                <div class="control-group">
                                  <label class="control-label">Teléfono: </label>
                                  <input type="tel" name="txttelefono" id="txttelefono" maxlength="9" class="form-control" placeholder="Teléfono...">
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
        <script src="js/sucursal.vista.js" type="text/javascript"></script>
    </body>

</html>



