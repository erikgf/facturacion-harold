var app = {},
  _CLASE = "Almacen",
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

  DOM.btnBuscar = $("#btnbuscar");
  DOM.btnExcel = $("#btnexcel");
  DOM.cboSucursal = $("#cbosucursal");

  this.DOM = DOM;
};

app.setEventos  = function(){
  var self = this,
      DOM  = self.DOM;

  DOM.btnBuscar.on("click", function(e){
    e.preventDefault();
    self.listar();
  });

  DOM.btnExcel.on("click", function(e){
     var DOM = self.DOM,
        str = "../controlador/reporte.xls.stock.php?"+
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
    metodo: "obtenerDataReporteStock"
  },fn);
};

app.listar = function(){
  var DOM = this.DOM,
      tpl8 = this.tpl8;
  var fn = function (xhr){
    var datos = xhr.datos;
      if (datos.rpt) {
        if (DT) { DT.fnDestroy(); DT = null; }
        DOM.listado.html(tpl8.listado(datos.data.map(r=>{
          return {
                  ...r, 
                  precio_entrada: parseFloat(r.precio_entrada).toFixed(2),
                  total: parseFloat(r.stock * r.precio_entrada).toFixed(2)
                }
        })));
        DT = DOM.listado.find("table").dataTable({
          "aaSorting": [[0, "asc"]]
        });

      }else{
        swal("Error", datos.msj, "error");
      }
  };

  new Ajxur.Api({
    modelo: _CLASE,
    metodo: "reporteAlmacenStock",
    data_out: [DOM.cboSucursal.val()]
  },fn);
};

$(document).ready(function(){
  new AccesoAuxiliar(()=>{
    app.init();
  });
});

