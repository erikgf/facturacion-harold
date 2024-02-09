var app = {},
  _TEMPID = -1,
  _ACCION = "agregar",
  _CLASE = "Cliente",
  DT = null,
  _cache_busqueda;

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
  DOM.blkNumeroDocumento = DOM.frmGrabar.find("#blknumerodocumento");
  DOM.txtNumeroDocumento = DOM.frmGrabar.find("#txtnumerodocumento");
  DOM.txtNombres = DOM.frmGrabar.find("#txtnombres");
  DOM.txtApellidos = DOM.frmGrabar.find("#txtapellidos");
  DOM.txtCelular = DOM.frmGrabar.find("#txtcelular");
  DOM.txtCorreo = DOM.frmGrabar.find("#txtcorreo");
  DOM.cboSexo = DOM.frmGrabar.find("#cbosexo");
  DOM.txtFechaNacimiento = DOM.frmGrabar.find("#txtfechanacimiento");

  DOM.txtRazonSocial = DOM.frmGrabar.find("#txtrazonsocial");
  DOM.txtNumeroContacto = DOM.frmGrabar.find("#txtnumerocontacto");
  DOM.txtDireccion = DOM.frmGrabar.find("#txtdireccion");

  this.DOM = DOM;
};

app.setEventos  = function(){
  var self = this,
      DOM  = self.DOM;

  DOM.modal.on("hidden.bs.modal",function(e){
    self.limpiar();
    DT.fnFilter(_cache_busqueda);
  });

  DOM.modal.on("shown.bs.modal",function(e){
    e.preventDefault();
    _cache_busqueda = $("#DataTables_Table_0_filter").find("input[type=search]").val();
  });

  DOM.cboTipoDocumento.on("change", (e)=>{
    app.cambiarTipoDocumento();
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
  //DOM.txtNumeroDocumento.on("keypress", fSoloNumeros);
  DOM.txtCelular.on("keypress", fSoloNumeros);

  var fSoloLetras = function(e){ return Util.soloLetras(e);};
  DOM.txtNombres.on("keypress", fSoloLetras);
  DOM.txtApellidos.on("keypress", fSoloLetras);
};

app.cambiarTipoDocumento = function(){
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

    let cboTipoDocumento = DOM.cboTipoDocumento.val();

    if (cboTipoDocumento === "6"){
      nd.maxLength = "11";

      DOM.txtRazonSocial.parents(".form-group").show();
      DOM.txtRazonSocial.prop("required", true);
      DOM.txtNumeroContacto.parents(".form-group").show();
      DOM.txtNumeroContacto.prop("required", false);
      DOM.txtDireccion.parents(".form-group").show();
      DOM.txtDireccion.prop("required", true);

      DOM.txtApellidos.parents(".form-group").hide()
      DOM.txtApellidos.prop("required", false);
      DOM.txtNombres.parents(".form-group").hide()
      DOM.txtNombres.prop("required", false);
      DOM.cboSexo.parents(".form-group").hide();
      DOM.cboSexo.prop("required", false);
      DOM.txtFechaNacimiento.parents(".form-group").hide()
      DOM.txtFechaNacimiento.prop("required", false);
      DOM.txtCelular.parents(".form-group").hide();
      DOM.txtCelular.prop("required", false);

    } else {

      DOM.txtRazonSocial.parents(".form-group").hide();
      DOM.txtRazonSocial.prop("required", false);
      DOM.txtNumeroContacto.parents(".form-group").hide();
      DOM.txtNumeroContacto.prop("required", false);
      
      DOM.txtDireccion.parents(".form-group").show();
      DOM.txtDireccion.prop("required", true);
      DOM.txtApellidos.parents(".form-group").show();
      DOM.txtApellidos.prop("required", true);
      DOM.txtNombres.parents(".form-group").show();
      DOM.txtNombres.prop("required", true);
      DOM.cboSexo.parents(".form-group").show();
      DOM.cboSexo.prop("required", true);
      DOM.txtFechaNacimiento.parents(".form-group").show();
      DOM.txtFechaNacimiento.prop("required", false);
      DOM.txtCelular.parents(".form-group").show();
      DOM.txtCelular.prop("required", false);

      if (cboTipoDocumento === "1"){
        nd.maxLength = "8";
      } else {
        nd.maxLength = "12";  
      }
    }
}

app.setTemplate = function(){
  var tpl8 = {};
  tpl8.listado = Handlebars.compile($("#tpl8Listado").html());
  tpl8.combo = Handlebars.compile($("#tpl8Combo").html());

  this.tpl8 = tpl8;
};

app.limpiar = function(){
  var DOM = this.DOM;
  DOM.frmGrabar[0].reset();
  DOM.blkNumeroDocumento.hide();

  app.cambiarTipoDocumento();

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
    const response = await apiAxios.get(`clientes/${cod}`);
    const { data } = response;

    if (data.id_tipo_documento > 0){
      DOM.blkNumeroDocumento.show();
    }

    DOM.cboTipoDocumento.val(data.id_tipo_documento);
    DOM.txtNumeroDocumento.val(data.numero_documento);
    DOM.txtNombres.val(data.nombres);
    DOM.txtApellidos.val(data.apellidos);
    DOM.txtCelular.val(data.celular);
    DOM.txtCorreo.val(data.correo);
    DOM.cboSexo.val(data.sexo);
    DOM.txtFechaNacimiento.val(data.fecha_nacimiento);
    DOM.txtDireccion.val(data.direccion);
    DOM.txtRazonSocial.val(data.nombres);
    DOM.txtNumeroContacto.val(data.numero_contacto);

    app.cambiarTipoDocumento();

  } catch (error) {
      swal("Error", JSON.stringify(error), "error");
      console.error(error);
  }
};


app.eliminar =  function(cod){
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
              await apiAxios.delete(`clientes/${cod}`);
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
    const idTipoDocumento =  DOM.cboTipoDocumento.val();

    const sentData = {
      id_tipo_documento : idTipoDocumento,
      numero_documento: DOM.txtNumeroDocumento.val(),
      nombres : idTipoDocumento == 6 
                  ? DOM.txtRazonSocial.val()
                  : DOM.txtNombres.val(),
      apellidos : idTipoDocumento == 6 
                  ? DOM.txtRazonSocial.val()
                  : DOM.txtApellidos.val(),
      direccion: DOM.txtDireccion.val(),
      celular : DOM.txtCelular.val(),
      correo : DOM.txtCorreo.val(),
      sexo : DOM.cboSexo.val(),
      fecha_nacimiento: DOM.txtFechaNacimiento.val(),
      numero_contacto: DOM.txtNumeroContacto.val(),
    };

    _ACCION === 'agregar' 
          ? await apiAxios.post('clientes', sentData)
          : await apiAxios.put(`clientes/${_TEMPID}`, sentData);

    swal("Exito", "Registrado con éxito.", "success");
    app.listar();
    DOM.modal.modal("hide");

  } catch (error) {
    swal("Error", JSON.stringify(error), "error");
    console.error(error);
  } finally{
    objButtonLoading.finish();
  }
};

app.listar = async function(){
  const DOM = this.DOM,
      tpl8Listado = this.tpl8.listado;
  try {
    const response = await apiAxios.get('clientes');
    const { data } = response;
    if (DT) { DT.fnDestroy(); DT = null; }

    DOM.listado.html(tpl8Listado(data.map(r => {
      return {
        ...r,
        razon_social : r.id_tipo_documento == 6
                      ? r.nombres
                      : `${r.nombres} ${r.apellidos}`,
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

$(document).ready(function(){
  new AccesoAuxiliar(()=>{
    app.init();
  })
});

