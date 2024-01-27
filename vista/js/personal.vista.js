var app = {},
  _TEMPID = -1,
  _ACCION = "agregar",
  _CLASE = "Personal",
  DT = null;

app.init = function(){
  this.setDOM();
  this.setEventos();
  this.setTemplate();

  app.llenarCargosRoles();
  app.listar();
};

app.setDOM = function(){
  var DOM = {};

  DOM.listado = $("#listado");
  DOM.modal = $("#mdlRegistro");
  DOM.modalCambiarClave = $("#mdlCambiarClave");
  DOM.frmGrabar = $("#frmgrabar");
  DOM.mdlHeader = DOM.modal.find(".modal-header h3");

  DOM.txtDni = DOM.frmGrabar.find("#txtdni");
  DOM.txtNombres = DOM.frmGrabar.find("#txtnombres");
  //DOM.txtApellidos = DOM.frmGrabar.find("#txtapellidos");
  DOM.txtCelular = DOM.frmGrabar.find("#txtcelular");
  DOM.txtCorreo = DOM.frmGrabar.find("#txtcorreo");
  //DOM.cboCargo = DOM.frmGrabar.find("#cbocargo");
  DOM.cboRol = DOM.frmGrabar.find("#cborol");
  //DOM.cboSucursal = DOM.frmGrabar.find("#cbosucursal");
  DOM.cboEstado = DOM.frmGrabar.find("#cboestado");
  DOM.cboSexo = DOM.frmGrabar.find("#cbosexo");
  DOM.txtFechaNacimiento = DOM.frmGrabar.find("#txtfechanacimiento");
  DOM.txtFechaIngreso = DOM.frmGrabar.find("#txtfechaingreso");
  DOM.chkAcceso = DOM.frmGrabar.find("#chkacceso");

  DOM.frmGrabarClave = $("#frmgrabarclave");
  DOM.txtDniClave = DOM.frmGrabarClave.find("#txtdniclave");
  DOM.txtPersonalClave = DOM.frmGrabarClave.find("#txtpersonalclave");
  DOM.txtNuevaClave = DOM.frmGrabarClave.find("#txtnuevaclave");

  this.DOM = DOM;
};

app.setEventos  = function(){
  var self = this,
      DOM  = self.DOM;

  DOM.modal.on("hidden.bs.modal",function(e){
    self.limpiar();
  });

  DOM.modalCambiarClave.on("hidden.bs.modal",function(e){
    self.limpiarClave();
  });

  DOM.modalCambiarClave.on("shown.bs.modal",function(e){
    DOM.txtDniClave.chosen("destroy").chosen({allow_single_deselect:true});
  });

  DOM.cboRol.on("change", function(e){
    DOM.cboSucursal[0].disabled = (this.value == "1");
  });

  DOM.frmGrabar.on("submit", function(e){
    e.preventDefault();
    swal({
          title: "Confirme",
          text: "¿Esta seguro de grabar los datos ingresados?",
          showCancelButton: true,
          confirmButtonColor: '#3d9205',
          confirmButtonText: 'Si',
          cancelButtonText: "No",
          closeOnConfirm: false,
          closeOnCancel: true,
          imageUrl: "../images/pregunta.png"
        },
        function(isConfirm){ 
          if (isConfirm){
            self.grabar();
          }
      });
  });

  DOM.frmGrabarClave.on("submit", function(e){
    e.preventDefault();
    swal({
          title: "Confirme",
          text: "¿Esta seguro de cambiar clave al usuario?",
          showCancelButton: true,
          confirmButtonColor: '#3d9205',
          confirmButtonText: 'Si',
          cancelButtonText: "No",
          closeOnConfirm: false,
          closeOnCancel: true,
          imageUrl: "../images/pregunta.png"
        },
        function(isConfirm){ 
          if (isConfirm){
            self.grabarCambioClave();
          }
      });
  });

  var fSoloNumeros = function(e){ return Util.soloNumeros(e);};
  DOM.txtDni.on("keypress", fSoloNumeros);
  DOM.txtCelular.on("keypress", fSoloNumeros);
  DOM.txtDniClave.on("keypress", fSoloNumeros);

  var fSoloLetras = function(e){ return Util.soloLetras(e);};

  DOM.txtNombres.on("keypress", fSoloLetras);
  //DOM.txtApellidos.on("keypress", fSoloLetras);
};

app.setTemplate = function(){
  var tpl8 = {};
  tpl8.listado = Handlebars.compile($("#tpl8Listado").html());
  tpl8.combo = Handlebars.compile($("#tpl8Combo").html());
  tpl8.dni_clave = Handlebars.compile($("#tpl8DniClave").html());

  this.tpl8 = tpl8;
};

app.limpiar = function(){
  var DOM = this.DOM;
  DOM.frmGrabar[0].reset();

  _ACCION = "agregar";
  _TEMPID = -1;
};

app.limpiarClave = function(){
  var DOM = this.DOM;
  DOM.frmGrabarClave[0].reset();
};

app.agregar = function(){
  _ACCION = "agregar";
  _TEMPID = -1;
  this.DOM.mdlHeader.html((_ACCION+" "+_CLASE).toUpperCase());
};

app.editar = async function(cod){
  _ACCION = "editar";
  _TEMPID = cod;
  const DOM = this.DOM;
  DOM.mdlHeader.html((_ACCION+" "+_CLASE).toUpperCase());

  try {
    const response = await apiAxios.get(`clientes/${cod}`);
    const { data } = response;

    DOM.txtDni.val(data.numero_documento);
    DOM.txtNombres.val(data.nombres_apellidos);
    //DOM.txtApellidos.val(data.apellidos);
    DOM.txtCelular.val(data.celular);
    DOM.txtCorreo.val(data.correo);
    DOM.txtFechaIngreso.val(data.fecha_ingreso);
    DOM.cboSexo.val(data.sexo);
    DOM.txtFechaNacimiento.val(data.fecha_nacimiento);
    DOM.cboRol.val(data.id_rol);
    DOM.cboEstado.val(data.estado);
    DOM.chkAcceso[0].checked = data.acceso_sistema == "1";

  } catch (error) {
      swal("Error", JSON.stringify(error), "error");
      console.error(error);
  }

};


app.eliminar = function(cod){
  swal({
          title: "Confirme",
          text: "¿Esta seguro que desea eliminar el registro?",
          showCancelButton: true,
          confirmButtonColor: '#3d9205',
          confirmButtonText: 'Si',
          cancelButtonText: "No",
          closeOnConfirm: true,
          closeOnCancel: true,
          imageUrl: "../images/pregunta.png"
        },
        async function(isConfirm){ 
          if (isConfirm){
            try {
              await apiAxios.delete(`usuarios/${cod}`);
              swal("Exito", "Eliminado con éxito.", "success");
              app.listar();
            
            } catch (error) {
              swal("Error", JSON.stringify(error), "error");
              console.error(error);
            }
          }
      });

};

app.grabar = async function(){
  const DOM = this.DOM;
  try {

    const sentData = {
      numero_documento : DOM.txtDni.val(),
      nombres_apellidos : DOM.txtNombres.val(),
      celular : DOM.txtCelular.val(),
      email : DOM.txtCorreo.val(),
      sexo : DOM.cboSexo.val(),
      id_rol : DOM.cboRol.val(),
      fecha_ingreso: DOM.txtFechaIngreso.val(),
      fecha_nacimiento: DOM.txtFechaNacimiento.val(),
      acceso_sistema : DOM.chkAcceso[0].checked,
      estado_activo : DOM.cboEstado.val(),
    };

    _ACCION === 'agregar' 
          ? await apiAxios.post('usuarios', sentData)
          : await apiAxios.put(`usuarios/${_TEMPID}`, sentData);

    swal("Exito", "Registrado con éxito.", "success");
    app.listar();
    DOM.modal.modal("hide");

  } catch (error) {
    swal("Error", JSON.stringify(error), "error");
    console.error(error);
  }

};

app.grabarCambioClave = async function(){
  const DOM = this.DOM;

  try {
    const sentData = {
      id_usuario : DOM.txtDniClave.val(),
      nueva_clave: DOM.txtNuevaClave.val()
    };

    await apiAxios.post(`usuarios/cambiar-clave`, sentData);
    swal("Exito", "Clave cambiada con éxito.", "success");
    DOM.modalCambiarClave.modal("hide");
  
  } catch (error) {
    swal("Error", JSON.stringify(error), "error");
    console.error(error);
  }


};

app.listar = async function(){
  const DOM = this.DOM,
      tpl8Listado = this.tpl8.listado,
      tpl8DniClave = this.tpl8.dni_clave;

  try {
    const response = await apiAxios.get('usuarios');
    const { data } = response;
    if (DT) { DT.fnDestroy(); DT = null; }
    DOM.txtDniClave.html(tpl8DniClave(data));
    DOM.listado.html(tpl8Listado(data.map(r => {
      return {
        ...r,
        color_estado: r.estado_activo == 'A' ? 'success' : 'danger',
        descripcion_estado: r.estado_activo == 'A' ? 'ACTIVO' : 'INACTIVO'
      }
    })));
    DT = DOM.listado.find("table").dataTable({
      "aaSorting": [[0, "asc"]]
    });

  } catch (error) {
      swal("Error", JSON.stringify(error), "error");
      console.error(error);
  }
};

app.llenarCargosRoles = async function(){
  const DOM = this.DOM,
      tpl8 = this.tpl8.combo;

  try {
      const response = await apiAxios.get('roles');
      const { data } = response;

      DOM.cboRol.html(tpl8({opciones: data.map(r => {
        return {
          codigo: r.id,
          descripcion: r.nombre
        }
      }), rotulo: "rol"}));

  } catch (error) {
      swal("Error", JSON.stringify(error), "error");
      console.error(error);
  }
};

app.verClave = function(){
  this.DOM.txtNuevaClave[0].type = "text";
};

app.esconderClave = function(){
  this.DOM.txtNuevaClave[0].type = "password";
};

app.cambiarClave = function(){
  this.DOM.modalCambiarClave.modal("show");
}

$(document).ready(function(){
  new AccesoAuxiliar(()=>{
    app.init();
  });
});

