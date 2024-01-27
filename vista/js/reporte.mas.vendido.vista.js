var app = {},
  _CLASE = "Venta",
  DT = null;

app.init = function(){
  this.setDOM();
  this.setEventos();
  this.setTemplate();

  app.cargarDatos();
 // app.listar();
};

app.setDOM = function(){
  var DOM = {};

  DOM.listado = $("#listado");
  DOM.resumen = $("#resumen");

  DOM.txtFechaDesde = $("#txtfechadesde");
  DOM.txtFechaHasta = $("#txtfechahasta");
  DOM.chkTodos = $("#chktodos");
  DOM.btnBuscar = $("#btnbuscar");
  DOM.btnExcel = $("#btnexcel");

  DOM.cboSucursal = $("#cbosucursal");

  this.DOM = DOM;
};

app.setEventos  = function(){
  var self = this,
      DOM  = self.DOM;

  DOM.chkTodos.on("change", function(e){
    var chked = this.checked;
    DOM.txtFechaDesde.prop("disabled", chked);
    DOM.txtFechaHasta.prop("disabled", chked);
  });

  DOM.btnBuscar.on("click", function(e){
    e.preventDefault();
    self.listar();
  });

  DOM.btnExcel.on("click", function(e){
     var DOM = self.DOM,
        str = "../controlador/reporte.xls.mas.vendido.php?"+
                    "p_tipo="+DOM.chkTodos[0].checked+"&"+
                    "p_f0="+DOM.txtFechaDesde.val()+"&"+
                    "p_f1="+DOM.txtFechaHasta.val()+"&"+
                    "p_su="+DOM.cboSucursal.val();
        window.open(str,'_blank'); 
  });
};

app.setTemplate = function(){
  var tpl8 = {};
  tpl8.listado = Handlebars.compile($("#tpl8Listado").html());
  tpl8.sucursales = Handlebars.compile($("#tpl8Sucursal").html());

  this.tpl8 = tpl8;
};

app.cargarDatos = function(){
  var DOM = this.DOM,
      tpl8 = this.tpl8,
      fn = function(xhr){
        var datos = xhr.datos;
        if (datos.rpt){
          DOM.cboSucursal.html(tpl8.sucursales(datos.data.sucursales));
        }
      };

  new Ajxur.Api({
    modelo: _CLASE,
    metodo: "obtenerDataReporteMasVendido"
  },fn);
};

app.listar = function(){
  var DOM = this.DOM,
      tpl8 = this.tpl8;
  var fn = function (xhr){
    var datos = xhr.datos;
      if (datos.rpt) {
        if (DT) { DT.fnDestroy(); DT = null; }
        DOM.listado.html(tpl8.listado(datos.data));
        DT = DOM.listado.find("table").dataTable({
          "aaSorting": [[0, "asc"]]
        });

      }else{
        swal("Error", datos.msj, "error");
      }
  };

  new Ajxur.Api({
    modelo: _CLASE,
    metodo: "reporteMasVendido",
    data_out: [DOM.txtFechaDesde.val(), DOM.txtFechaHasta.val(), DOM.chkTodos[0].checked, DOM.cboSucursal.val()]
  },fn);
};

$(document).ready(function(){
  new AccesoAuxiliar(()=>{
    app.init();
  });
});

