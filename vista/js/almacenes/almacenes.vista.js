var app = {};
const storager = new Storager();

app.init = async function(){
  this.tpl8 = Util.Templater($("script[type=handlebars-x]"));

  this.$tabStockProductos = $("#tabStockProductos");
  this.$tabHistorialMovimientos = $("#tabHistorialMovimientos");
  this.cboSucursal = $("#cbosucursal");

  this.cboSucursal.on("change", function(e){
    storager.setValue("sucursal", $("#cbosucursal").val());
    app.obtenerDataSoloProductos(this.value);
  });

  try {
    const { data } = await apiAxios.get(`sucursales`);

    if (!data.length){
      swal("Error", "No hay sucursales activa en el sistema.", "error");
      return;
    }

    this.cboSucursal.html(this.tpl8.Sucursal(data));
    const cachedSucursal = storager.getValue("sucursal");
    if (cachedSucursal){
      this.cboSucursal.val(cachedSucursal);
    }

    this.objStockProductos = new StockProductos({$: this.$tabStockProductos, tpl8: this.tpl8, sucursales: data})
    this.objHistorialMovimientos = new HistorialMovimientos({$: this.$tabHistorialMovimientos, tpl8: this.tpl8, sucursales: data})

  } catch (error) {
    console.error(error);
  }
};

app.obtenerData = function(inicial){
     var self = this,
          fn = function (xhr){
              var datos = xhr.datos,
                  data;
                if (datos.rpt) {
                  var data = datos.data;

                  if (inicial){
                    var sucursalesHTML =self.tpl8.Sucursal(data.sucursales);
                    self.cboSucursal.html(sucursalesHTML);
                    self.StockProductos = new StockProductos( self.$tabStockProductos,
                                                              { tipos: data.tipo,
                                                                categorias: data.categoria,
                                                                producto_stock: data.producto_stock},
                                                              self.tpl8);
                    self.HistorialMovimientos = new HistorialMovimientos(self.$tabHistorialMovimientos,
                                                                    {tipos: data.tipo,
                                                                     categorias: data.categoria,
                                                                     lista_productos: data.lista_productos,
                                                                     historial_productos: data.historial_productos
                                                                    },
                                                                    self.tpl8);

                    self.HistorialMovimientos.DOM.cboSucursalActual.html(sucursalesHTML);
                    self.HistorialMovimientos.DOM.mdlTransferencia.find("#cboalmacenorigen").html(sucursalesHTML);
                    self.HistorialMovimientos.DOM.mdlTransferencia.find("#cboalmacendestino").html(sucursalesHTML);
                    return;
                  } 

                  self.StockProductos.setData({ tipos: data.tipo,
                                                categorias: data.categoria,
                                                producto_stock: data.producto_stock});

                  self.HistorialMovimientos.setData({ tipos: data.tipo,
                                                    categorias: data.categorias,
                                                    lista_productos: data.lista_productos,
                                                    historial_productos: data.historial_productos});


                }else{
                  console.error(datos.msj);
                }
          };

      new Ajxur.Api({
        modelo: "Almacen",
        metodo: "obtenerDataInterfaz"
      },fn);
};

app.obtenerDataSoloProductos = function(idSucursal){
  this.objHistorialMovimientos.actualizarListaProductos({
    idSucursal
  });
  this.objStockProductos.actualizarListaProductos({
    idSucursal
  });
};

$(document).ready(function(){
  new AccesoAuxiliar(()=>{
    app.init();
  });
});

