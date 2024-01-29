const ReporteStock = function(){
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
      cboSucursal : $("#cbosucursal"),
      btnBuscar : $("#btnbuscar"),
    };
  };

  this.setTemplate = function(){
    this._tpl8 = {
      listado : Handlebars.compile($("#tpl8Listado").html()),
      sucursales : Handlebars.compile($("#tpl8Sucursal").html()),
    };
  };

  this.setEventos  = function(){
    const DOM = this._DOM;
  
    DOM.btnBuscar.on("click", (e) =>{
      e.preventDefault();
      this.obtenerStock();
    });

  };
  
  this.obtenerData = function(){
    obtenerSucursales();
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

  this.obtenerStock = async function(){
    const DOM = this._DOM;

    try {
      const sentData = {
        sucursal : DOM.cboSucursal.val() ?? "",
      };
      const paramsData = new URLSearchParams(sentData);
      const { data } = await apiAxios.get(`almacen-reportes/stock?${paramsData.toString()}`);

      this.renderLista(data.map(item => {
        return {
          ...item,
          precio_entrada_promedio: parseFloat(item.precio_entrada_promedio).toFixed(2),
          total: parseFloat(item.precio_entrada_promedio * item.stock).toFixed(2)
        }
      }));
    } catch (error) {
        swal("Error", "Ha ocurrido un problema con la consulta.", "error");
        console.error(error);
    }
  };

  this.renderLista = function(data){
    if (this._DT) {this._DT.destroy(); this._DT = null;}
    this._DOM.tblLista.find("tbody").html(this._tpl8.listado(data));
    this._DT = this._DOM.tblLista.DataTable({
        aaSorting: [[0, "desc"]],
        pageLength: 50,
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
              extend: 'csv',
              className: "btn btn-secondary",
              exportOptions: {
                columns: ':not(.notexport)'
              }
            },
            {
              extend: 'pdf',
              className: "btn btn-danger",
              exportOptions: {
                columns: ':not(.notexport,.notexportpdf)'
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
    objReporteStock = new ReporteStock();
  });
});