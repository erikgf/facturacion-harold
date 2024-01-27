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
  DOM.cboCliente = $("#cbocliente");

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
        str = "../controlador/reporte.xls.ventas.php?"+
                    "p_tipo="+DOM.chkTodos[0].checked+"&"+
                    "p_f0="+DOM.txtFechaDesde.val()+"&"+
                    "p_f1="+DOM.txtFechaHasta.val()+"&"+
                    "p_cl="+DOM.cboCliente.val()+"&"+
                    "p_su="+DOM.cboSucursal.val();
        window.open(str,'_blank'); 
  });
};

app.setTemplate = function(){
  var tpl8 = {};
  tpl8.listado = Handlebars.compile($("#tpl8Listado").html());
  tpl8.resumen = Handlebars.compile($("#tpl8Resumen").html());
  tpl8.sucursales = Handlebars.compile($("#tpl8Sucursal").html());
  tpl8.clientes = Handlebars.compile($("#tpl8Cliente").html());
  tpl8.detalle = Handlebars.compile($("#tpl8DetalleVenta").html());

  this.tpl8 = tpl8;
};

app.cargarDatos = function(){
  var DOM = this.DOM,
      tpl8 = this.tpl8,
      fn = function(xhr){
        var datos = xhr.datos;
        if (datos.rpt){
          DOM.cboSucursal.html(tpl8.sucursales(datos.data.sucursales));
          DOM.cboCliente.html(tpl8.clientes(datos.data.clientes)).chosen();
        }
      };

  new Ajxur.Api({
    modelo: "Venta",
    metodo: "obtenerDataReporte"
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

        DOM.resumen.html(tpl8.resumen(datos.cabecera));
      }else{
        swal("Error", datos.msj, "error");
      }
  };

  new Ajxur.Api({
    modelo: _CLASE,
    metodo: "reporteGeneral",
    data_out: [DOM.txtFechaDesde.val(), DOM.txtFechaHasta.val(), DOM.chkTodos[0].checked, DOM.cboSucursal.val(), DOM.cboCliente.val()]
  },fn);
};


app.verDetalle = function(codTransaccion){
  var self = this,
        DOM = self.DOM,
        fn = function(xhr){
          var datos = xhr.datos;
          if (datos.rpt){
            self.renderVenta(datos.data);
          }
        },
        fnError = function(e){
          swal("Error",e,"error");
        };

    new Ajxur.Api({
              modelo: "Venta",
              metodo: "leerVenta",
              data_in: {
                p_codTransaccion: codTransaccion
              }
            },fn,fnError);
};

 app.renderVenta = function(dataVenta){
    var mdlDetalleVenta = $("#mdlDetalleVenta"),
        cabecera = dataVenta.cabecera;

    mdlDetalleVenta.modal("show");
    mdlDetalleVenta.find("h3").html("Venta: "+cabecera.cod_transaccion+" - "+cabecera.cliente+" - Doc.: "+cabecera.numero_documento);
    mdlDetalleVenta.find(".modal-body").html(app.tpl8.detalle(dataVenta));

    mdlDetalleVenta = null;
};

app.verComprobante = function(codTransaccion){
    var str = "../controlador/imprimir.comprobante.pdf.php?"+
                    "p_t="+codTransaccion;
                    
     window.open(str,'_blank'); 
}

$(document).ready(function(){
  new AccesoAuxiliar(()=>{
    app.init();
  });
});

