var app = {},
  _TEMPID = -1,
  _ACCION = "agregar",
  _CLASE = "Cargo",
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

  DOM.txtDescripcion = DOM.frmGrabar.find("#txtdescripcion");
  DOM.cboArea = DOM.frmGrabar.find("#cboarea");
  DOM.cboPeso = DOM.frmGrabar.find("#cbopeso");

  this.DOM = DOM;
};

app.setEventos  = function(){
  var self = this,
      DOM  = self.DOM;

  DOM.modal.on("shown.bs.modal",function(e){
    DOM.txtDescripcion.focus();
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
          $(".confirm").attr('disabled', 'disabled');
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

app.editar = function(cod){
  _ACCION = "editar";
  _TEMPID = cod;
  var DOM = this.DOM,
    fn = function (xhr){
      var datos = xhr.datos;
      if (datos.rpt) {      
        var data = datos.data;
        DOM.txtDescripcion.val(data.descripcion);
      }else{
        console.error(datos.msj);
      }
  };

  DOM.mdlHeader.html((_ACCION+" "+_CLASE).toUpperCase());
  new Ajxur.Api({
    modelo: _CLASE,
    metodo: "leerDatos",
    data_in : {
      p_codCargo: cod
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
          text: "¿Esta seguro que desea eliminar el registro?",
          showCancelButton: true,
          confirmButtonColor: '#3d9205',
          confirmButtonText: 'Si',
          cancelButtonText: "No",
          closeOnConfirm: true,
          closeOnCancel: true,
          imageUrl: "../images/pregunta.png"
        },
        function(isConfirm){ 
          $(".confirm").attr('disabled', 'disabled');
          if (isConfirm){
              new Ajxur.Api({
                modelo: _CLASE,
                metodo: "eliminar",
                data_in : {
                  p_codCargo: cod
                }
              },fn);
          }
      });

};

app.grabar = function(){
  var DOM = this.DOM,
      fn = function(xhr){
        console.log(xhr);
        var datos = xhr.datos;
        if (datos.rpt){
          swal("Exito", datos.msj, "success");
          DOM.modal.modal("hide");
          app.listar();
        } else {
          swal("Error", datos.msj, "error");
        } 
      };

  new Ajxur.Api({
    modelo: _CLASE,
    metodo: _ACCION,
    data_in :  {
      p_descripcion : DOM.txtDescripcion.val(),
      p_codCargo : _TEMPID
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
    metodo: "listar"
  },fn);
};

$(document).ready(function(){
  app.init();
});

