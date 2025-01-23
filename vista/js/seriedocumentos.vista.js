var app = {},
  _TEMPID = -1,
  _ACCION = "agregar",
  _CLASE = "Serie Documentos",
  DT = null;

app.init = function(){
  this.setDOM();
  this.setEventos();
  this.setTemplate();

  app.obtenerTipoComprobantes();
  app.obtenerSucursales();
  app.listar();
};

app.setDOM = function(){
  var DOM = {};

  DOM.listado = $("#listado");
  DOM.modal = $("#mdlRegistro");
  DOM.frmGrabar = $("#frmgrabar");
  DOM.mdlHeader = DOM.modal.find(".modal-header h3");

  DOM.cboTipoComprobante = DOM.frmGrabar.find("#cbotipocomprobante");
  DOM.txtSerie = DOM.frmGrabar.find("#txtserie");
  DOM.txtCorrelativo = DOM.frmGrabar.find("#txtcorrelativo");
  DOM.cboSucursal = DOM.frmGrabar.find("#cbosucursal");

  this.DOM = DOM;
};

app.setEventos  = function(){
  var self = this,
      DOM  = self.DOM;

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
          $(".confirm").attr('disabled', 'disabled');
          if (isConfirm){
            self.grabar();
          }
      });
  });

  const fSoloNumeros = function(e){ return Util.soloNumeros(e);};
  DOM.txtCorrelativo.on("keypress", fSoloNumeros);
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
    const response = await apiAxios.get(`serie-documentos/${cod}`);
    const { data } = response;

    DOM.cboTipoComprobante.val(data.id_tipo_comprobante);
    DOM.txtSerie.val(data.serie);
    DOM.txtCorrelativo.val(data.correlativo);
    DOM.cboSucursal.val(data.id_sucursal);
  } catch (error) {
    swal("Error", error?.response?.data?.message || JSON.stringify(error?.response?.data), "error");
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
          $(".confirm").attr('disabled', 'disabled');
          if (isConfirm){
            try {
              await apiAxios.delete(`serie-documentos/${cod}`);
              swal("Exito", "Eliminado con éxito.", "success");
              app.listar();
            
            } catch (error) {
              swal("Error", error?.response?.data?.message || JSON.stringify(error?.response?.data), "error");
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
      id_tipo_comprobante : DOM.cboTipoComprobante.val(),
      serie: DOM.txtSerie.val(),
      correlativo :  DOM.txtCorrelativo.val(),
      id_sucursal : DOM.cboSucursal.val()
    };

    _ACCION === 'agregar' 
          ? await apiAxios.post('serie-documentos', sentData)
          : await apiAxios.put(`serie-documentos/${_TEMPID}`, sentData);

    swal("Exito", "Registrado con éxito.", "success");
    app.listar();
    DOM.modal.modal("hide");

  } catch (error) {
    swal("Error", error?.response?.data?.message || JSON.stringify(error?.response?.data), "error");
    console.error(error);
  } finally {
    objButtonLoading.finish();
  }
};

app.listar = async function(){
  const DOM = this.DOM,
      tpl8Listado = this.tpl8.listado;

  try {
    const response = await apiAxios.get('serie-documentos');
    const { data } = response;
    if (DT) { DT.fnDestroy(); DT = null; }

    DOM.listado.html(tpl8Listado(data));
    DT = DOM.listado.find("table").dataTable({
      "aaSorting": [[0, "asc"]]
    });

  } catch (error) {
      swal("Error", error?.response?.data?.message || JSON.stringify(error?.response?.data), "error");
      console.error(error);
  }
};

app.obtenerTipoComprobantes = async function(){
  try {
      const { data } = await apiAxios.get('tipo-comprobantes');
      let html = "";

      for (let i = 0, len = data.length; i < len; i++) {
          const item = data[i];
          html += '<option value="' + item.id + '">'+ item.nombre + '</option>';
      };

      this.DOM.cboTipoComprobante.html(html);
  } catch (error) {
      swal("Error", error?.response?.data?.message || JSON.stringify(error?.response?.data), "error");
      console.error(error);
  }
}

app.obtenerSucursales = async function(){
  try {
      const { data } = await apiAxios.get('sucursales');
      let html = "";

      for (let i = 0, len = data.length; i < len; i++) {
          const item = data[i];
          html += '<option value="' + item.id + '">'+ item.nombre + '</option>';
      };

      this.DOM.cboSucursal.html(html);
  } catch (error) {
      swal("Error", error?.response?.data?.message || JSON.stringify(error?.response?.data), "error");
      console.error(error);
  }
}

$(document).ready(function(){
  new AccesoAuxiliar(()=>{
    app.init();
  });
});

