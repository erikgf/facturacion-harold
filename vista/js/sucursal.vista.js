var app = {},
  _TEMPID = -1,
  _ACCION = "agregar",
  _CLASE = "Sucursal",
  DT = null;

app.init = function(){
  this.setDOM();
  this.setEventos();
  this.setTemplate();

  app.listar();
};

app.setDOM = function(){
  var DOM = {};

  DOM.listado = $("#listado");
  DOM.modal = $("#mdlRegistro");
  DOM.frmGrabar = $("#frmgrabar");
  DOM.mdlHeader = DOM.modal.find(".modal-header h3");

  DOM.txtNombre = DOM.frmGrabar.find("#txtnombre");
  DOM.txtDireccion = DOM.frmGrabar.find("#txtdireccion");
  DOM.txtTelefono = DOM.frmGrabar.find("#txttelefono");

  this.DOM = DOM;
};

app.setEventos  = function(){
  var self = this,
      DOM  = self.DOM;

  DOM.modal.on("shown.bs.modal",function(e){
    DOM.txtNombre.focus();
  });

  DOM.modal.on("hidden.bs.modal",function(e){
    self.limpiar();
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
};

app.setTemplate = function(){
  var tpl8 = {};
  tpl8.listado = Handlebars.compile($("#tpl8Listado").html());

  this.tpl8 = tpl8;
};

app.limpiar = function(){
  var DOM = this.DOM;
  DOM.frmGrabar[0].reset();

  _ACCION = "agregar";
  _TEMPID = -1;
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
  try {
    const response = await apiAxios.get(`sucursales/${cod}`);
    const { data } = response;

    DOM.txtNombre.val(data.nombre);
    DOM.txtDireccion.val(data.direccion);
    DOM.txtTelefono.val(data.telefono);

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
              await apiAxios.delete(`sucursales/${cod}`);
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
      nombre : DOM.txtNombre.val(),
      direccion: DOM.txtDireccion.val(),
      telefono: DOM.txtTelefono.val(),
    };

    _ACCION === 'agregar' 
          ? await apiAxios.post('sucursales', sentData)
          : await apiAxios.put(`sucursales/${_TEMPID}`, sentData);

    swal("Exito", "Registrado con éxito.", "success");
    app.listar();
    DOM.modal.modal("hide");

  } catch (error) {
    swal("Error", JSON.stringify(error), "error");
    console.error(error);
  }
};

app.listar = async function(){
  const DOM = this.DOM,
      tpl8Listado = this.tpl8.listado;
  try {
    const response = await apiAxios.get('sucursales');
    const { data } = response;
    if (DT) { DT.fnDestroy(); DT = null; }

    DOM.listado.html(tpl8Listado(data));
    DT = DOM.listado.find("table").dataTable({
      "aaSorting": [[0, "asc"]]
    });

  } catch (error) {
      swal("Error", JSON.stringify(error), "error");
      console.error(error);
  }
};

$(document).ready(function(){
  new AccesoAuxiliar(()=>{
    app.init();
  });
});
