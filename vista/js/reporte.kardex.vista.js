const ReporteMasVendido = function(){
  this.init = function(){
    this._DT = null;

    this.setTemplate();
    this.setDOM();
    this.setEventos();
  
    this.obtenerData();
  };
  
  this.setDOM = function(){
    this._DOM = {
      tblLista : $("#tbllista"),
      cboProducto : $("#cboproducto"),
      btnBuscar : $("#btnbuscar"),
      cboSucursal : $("#cbosucursal")
    };
  };

  this.setTemplate = function(){
    this._tpl8 = {
      listado : Handlebars.compile($("#tpl8Listado").html()),
      sucursales : Handlebars.compile($("#tpl8Sucursal").html()),
      productos : Handlebars.compile($("#tpl8Producto").html()),
    };
  };

  this.setEventos  = function(){
    const DOM = this._DOM;
  
    DOM.btnBuscar.on("click", (e) =>{
      e.preventDefault();
      this.obtenerKardex();
    });

  };
  
  this.obtenerData = function(){
    obtenerSucursales();
    obtenerProductos();
  };

  const obtenerSucursales = async () => {
    try {
        const { data } = await apiAxios.get('sucursales');
        this._DOM.cboSucursal.html(this._tpl8.sucursales(data));
    } catch (error) {
        swal("Error",  "Error al obtener las sucursales.", "error");
        console.error(error);
    }
  };

  const obtenerProductos = async () => {
    try {
        const { data } = await apiAxios.get('productos');
        this._DOM.cboProducto.html(this._tpl8.productos(data)).chosen();
    } catch (error) {
        swal("Error",  "Error al obtener los productos.", "error");
        console.error(error);
    }
  };

  this.obtenerKardex = async function(){
    const DOM = this._DOM;

    try {
      const sentData = {
        producto : DOM.cboProducto.val() ?? "",
        sucursal : DOM.cboSucursal.val() ?? "",
      };

      const paramsData = new URLSearchParams(sentData);
      const { data } = await apiAxios.get(`almacen-reportes/kardex?${paramsData.toString()}`);

      this.renderLista(data);
    } catch (error) {
        swal("Error", "Ha ocurrido un problema con la consulta.", "error");
        console.error(error);
    }
  };

  this.renderLista = function(data){
    if (this._DT) {this._DT.destroy(); this._DT = null;}
    this._DOM.tblLista.find("tbody").html(this._tpl8.listado(data));
    this._DT = this._DOM.tblLista.DataTable({
        //aaSorting: [[0, "desc"]],
        pageLength: 30,
        dom: 'Bfrtip',
        buttons: [
            {
              extend: 'copy',
              className: "btn btn-primary",
              exportOptions: {
                columns: ':not(.notexport)'
              }
            },
            {
              extend: 'excel',
              className: "btn btn-success",
              exportOptions: {
                columns: ':not(.notexport)'
              }
            },
        ],
        responsive: true
    });
  };

  return this.init();
};

$(document).ready(function(){
  new AccesoAuxiliar(()=>{
    objReporteMasVendido = new ReporteMasVendido();
  });
});