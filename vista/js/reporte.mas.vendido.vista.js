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
      txtFechaDesde : $("#txtfechadesde"),
      txtFechaHasta : $("#txtfechahasta"),
      chkTodos : $("#chktodos"),
      btnBuscar : $("#btnbuscar"),
      cboSucursal : $("#cbosucursal")
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
  
    DOM.chkTodos.on("change", (e) => {
      const checked = e.currentTarget.checked;
      DOM.txtFechaDesde.prop("disabled", checked);
      DOM.txtFechaHasta.prop("disabled", checked);
    });
  
    DOM.btnBuscar.on("click", (e) =>{
      e.preventDefault();
      this.obtenerMasVendidos();
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

  this.obtenerMasVendidos = async function(){
    const DOM = this._DOM;

    try {
      const sentData = {
        todos : DOM.chkTodos[0].checked ? 1 : 0,
        sucursal : DOM.cboSucursal.val() ?? "",
        fecha_desde: DOM.txtFechaDesde.val(), 
        fecha_hasta: DOM.txtFechaHasta.val()
      };
      const paramsData = new URLSearchParams(sentData);
      const { data } = await apiAxios.get(`ventas-reportes/mas-vendido?${paramsData.toString()}`);

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
        aaSorting: [[0, "desc"]],
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
    objReporteMasVendido = new ReporteMasVendido();
  });
});
  

