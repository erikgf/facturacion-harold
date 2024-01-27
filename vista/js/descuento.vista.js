var app = {},
  _TEMPID = -1,
  _ACCION = "agregar",
  _CLASE = "Descuento",
  DT = null,
  _SAVING = false;

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

  DOM.cboFiltroEstado = $("#cbofiltroestado");
  DOM.txtMontoDescuento = DOM.frmGrabar.find("#txtmontodescuento");
  DOM.cboTipoDescuento = DOM.frmGrabar.find("#cbotipodescuento");

  this.DOM = DOM;
};

app.setEventos  = function(){
  var self = this,
      DOM  = self.DOM;

  DOM.modal.on("shown.bs.modal",function(e){
    DOM.cboTipoDescuento.focus();
  });

  DOM.modal.on("hidden.bs.modal",function(e){
    self.limpiar();
  });

  DOM.frmGrabar.on("submit", function(e){
    e.preventDefault();
    swal({
          title: "Confirme",
          text: "¿Está seguro de grabar los datos ingresados?",
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
            if (_SAVING == true){
              return;
            }
            self.grabar();
          }
      });
  });

  DOM.cboFiltroEstado.on("change", function(e){
    self.listar();
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

app.editar = function(cod){
  _ACCION = "editar";
  _TEMPID = cod;
  var DOM = this.DOM,
    fn = function (xhr){
      var datos = xhr.datos;
      if (datos.rpt) {      
        var data = datos.data;
        DOM.cboTipoDescuento.val(data.tipo_descuento);
        DOM.txtMontoDescuento.val(data.monto_descuento);
      }else{
        console.error(datos.msj);
      }
  };

  DOM.mdlHeader.html((_ACCION+" "+_CLASE).toUpperCase());
  new Ajxur.Api({
    modelo: _CLASE,
    metodo: "leerDatos",
    data_in : {
      p_codDescuento: cod
    }
  },fn);
};


app.eliminar = function(cod){
  var DOM = this.DOM,
    fn = function (xhr){
      var datos = xhr.datos;
      if (datos.rpt) {      
         swal("Exito", datos.msj, "success");
         app.listar();
      }else{
        console.error(datos.msj);
      }
  };

  swal({
          title: "Confirme",
          text: "���Esta seguro que desea eliminar el registro?",
          showCancelButton: true,
          confirmButtonColor: '#3d9205',
          confirmButtonText: 'Si',
          cancelButtonText: "No",
          closeOnConfirm: true,
          closeOnCancel: true,
          imageUrl: "../images/pregunta.png"
        },
        function(isConfirm){ 
          if (isConfirm){
              new Ajxur.Api({
                modelo: _CLASE,
                metodo: "eliminar",
                data_in : {
                  p_codDescuento: cod
                }
              },fn);
          }
      });

};

app.grabar = function(){
  var DOM = this.DOM,
      fn = function(xhr){
        var datos = xhr.datos;        
        if (datos.rpt){
          swal("Exito", datos.msj, "success");
          DOM.modal.modal("hide");
          app.listar();
        } else {
          swal("Error", datos.msj, "error");
        } 

        _SAVING = false;
      };

  _SAVING = true;

  new Ajxur.Api({
    modelo: _CLASE,
    metodo: _ACCION,
    data_in :  {
      p_tipoDescuento : DOM.cboTipoDescuento.val(),
      p_montoDescuento : DOM.txtMontoDescuento.val(),
      p_codDescuento : _TEMPID
    }
  },fn);
};

app.listar = function(){
  var DOM = this.DOM,
      tpl8Listado = this.tpl8.listado;
  var fn = function (xhr){
    var datos = xhr.datos;
      if (datos.rpt) {
        if (DT) { DT.fnDestroy(); DT = null; }
        DOM.listado.html(tpl8Listado(datos.data));
        DT = DOM.listado.find("table").dataTable({
          "aaSorting": [[0, "asc"]]
        });
      }else{
        swal("Error", datos.msj, "error");
      }
  };

  new Ajxur.Api({
    modelo: _CLASE,
    metodo: "listar",
    data_out: [DOM.cboFiltroEstado.val()]
  },fn);
};

$(document).ready(function(){
  app.init();
});

