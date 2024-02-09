var app = {},
  _TEMPID = -1,
  _ACCION = "agregar",
  _CLASE = "Proveedor",
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

  DOM.cboTipoDocumento = DOM.frmGrabar.find("#cbotipodocumento");
  DOM.txtNumeroDocumento = DOM.frmGrabar.find("#txtnumerodocumento");
  DOM.txtDireccion = DOM.frmGrabar.find("#txtdireccion");
  DOM.blkNumeroDocumento = DOM.frmGrabar.find("#blknumerodocumento");
  DOM.txtRazonSocial = DOM.frmGrabar.find("#txtrazonsocial");
  DOM.txtCorreo = DOM.frmGrabar.find("#txtcorreo");

  DOM.txtCelularContacto = DOM.frmGrabar.find("#txtcelularcontacto");
  DOM.txtNombreContacto = DOM.frmGrabar.find("#txtnombrecontacto");

  this.DOM = DOM;
};

app.setEventos  = function(){
  var self = this,
      DOM  = self.DOM;

  DOM.modal.on("hidden.bs.modal",function(e){
    self.limpiar();
  });

  DOM.cboTipoDocumento.on("change", function(e){
    self.setNumeroDocumento();
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

  var fSoloNumeros = function(e){ return Util.soloNumeros(e);};
  DOM.txtNumeroDocumento.on("keypress", fSoloNumeros);
  DOM.txtCelularContacto.on("keypress", fSoloNumeros);

  var fSoloLetras = function(e){ return Util.soloLetras(e);};
  //DOM.txtRazonSocial.on("keypress", fSoloLetras);
  //DOM.txtDireccion.on("keypress", fSoloLetras);
  DOM.txtNombreContacto.on("keypress", fSoloLetras);
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

  DOM.mdlHeader.html((_ACCION+" "+_CLASE).toUpperCase());

  try {
    const response = await apiAxios.get(`proveedores/${cod}`);
    const { data } = response;

    if (data.id_tipo_documento > 0){
      DOM.blkNumeroDocumento.show();
    }

    DOM.cboTipoDocumento.val(data.id_tipo_documento);
    DOM.txtNumeroDocumento.val(data.numero_documento);
    DOM.txtRazonSocial.val(data.razon_social);
    DOM.txtDireccion.val(data.direccion);
    DOM.txtCorreo.val(data.correo);

    DOM.txtNombreContacto.val(data.nombre_contacto);
    DOM.txtCelularContacto.val(data.celular_contacto);

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
              await apiAxios.delete(`proveedores/${cod}`);
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
  const objButtonLoading = new ButtonLoading({$: DOM.frmGrabar.find("button[type=submit]")[0]});

  try {

    objButtonLoading.start();

    const sentData = {
      id_tipo_documento : DOM.cboTipoDocumento.val(),
      numero_documento: DOM.txtNumeroDocumento.val(),
      razon_social :  DOM.txtRazonSocial.val(),
      direccion: DOM.txtDireccion.val(),
      correo : DOM.txtCorreo.val(),
      nombre_contacto : DOM.txtNombreContacto.val(),
      celular_contacto : DOM.txtCelularContacto.val(),
    };

    _ACCION === 'agregar' 
          ? await apiAxios.post('proveedores', sentData)
          : await apiAxios.put(`proveedores/${_TEMPID}`, sentData);

    swal("Exito", "Registrado con éxito.", "success");
    app.listar();
    DOM.modal.modal("hide");

  } catch (error) {
    swal("Error", JSON.stringify(error), "error");
    console.error(error);
  } finally {
    objButtonLoading.finish();
  }
};

app.listar = async function(){
  const DOM = this.DOM,
      tpl8Listado = this.tpl8.listado;

  try {
    const response = await apiAxios.get('proveedores');
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
app.setNumeroDocumento = function(){
  var DOM = this.DOM,
      nd = DOM.txtNumeroDocumento[0];
    if (DOM.cboTipoDocumento.val() == "0"){
      DOM.blkNumeroDocumento.hide();
      nd.value = "";
      nd.required = false;
    } else {
      DOM.blkNumeroDocumento.show();
      nd.required = true;
    }

    nd.maxLength = DOM.cboTipoDocumento.val() == "6" ? "11" : "8";
}

$(document).ready(function(){
  new AccesoAuxiliar(()=>{
    app.init();
  });
});

