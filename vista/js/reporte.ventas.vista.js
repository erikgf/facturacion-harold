const ReporteVentas = function(){
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
      blkResumen : $("#blkresumen"),
      txtFechaDesde : $("#txtfechadesde"),
      txtFechaHasta : $("#txtfechahasta"),
      chkTodos : $("#chktodos"),
      btnBuscar : $("#btnbuscar"),
      cboSucursal : $("#cbosucursal"),
      cboCliente : $("#cbocliente"),
      mdlDetalleVenta :$("#mdlDetalleVenta")
    };
  };

  this.setTemplate = function(){
    this._tpl8 = {
      listado : Handlebars.compile($("#tpl8Listado").html()),
      resumen : Handlebars.compile($("#tpl8Resumen").html()),
      sucursales : Handlebars.compile($("#tpl8Sucursal").html()),
      clientes : Handlebars.compile($("#tpl8Cliente").html()),
      detalle : Handlebars.compile($("#tpl8DetalleVenta").html())
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
      this.obtenerVentas();
    });

    DOM.tblLista.on("click", "tr .btn-ver", (e) => {
      e.preventDefault();
      this.verDetalle(e.currentTarget.dataset.id);
    });

    DOM.mdlDetalleVenta.on("click", ".btn-veratencion", (e) => {
      e.preventDefault();
      this.verAtencion(e.currentTarget.dataset.id);
    });

    DOM.mdlDetalleVenta.on("click", ".btn-vercomprobante", (e) => {
      e.preventDefault();
      this.verComprobante(e.currentTarget.dataset.id);
    });
  };
  
  this.obtenerData = function(){
    obtenerClientes();
    obtenerSucursales();
  };

  const obtenerClientes = async () => {
    try {
        const { data } = await apiAxios.get('clientes');
        this._DOM.cboCliente.html(this._tpl8.clientes(data.map(item => {
          return {
            id: item.id,
            descripcion: `${item.numero_documento} | ${item.nombres} ${item.apellidos}`
          }
        }))).chosen();

    } catch (error) {
        swal("Error", "Error al obtener los clientes.", "error");
        console.error(error);
    }
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

  this.obtenerVentas = async function(){
    const DOM = this._DOM;

    try {
      const sentData = {
        todos : DOM.chkTodos[0].checked ? 1 : 0,
        cliente : DOM.cboCliente.val() ?? "",
        sucursal : DOM.cboSucursal.val() ?? "",
        fecha_desde: DOM.txtFechaDesde.val(), 
        fecha_hasta: DOM.txtFechaHasta.val()
      };
      const paramsData = new URLSearchParams(sentData);
      const { data : {cabecera, detalle} } = await apiAxios.get(`ventas-reportes/general?${paramsData.toString()}`);

      this.renderCabecera(cabecera);
      this.renderDetalle(detalle);
    } catch (error) {
        swal("Error", "Ha ocurrido un problema con la consulta.", "error");
        console.error(error);
    }
  };

  this.renderCabecera = function(data){
    this._DOM.blkResumen.html(this._tpl8.resumen(data));
  };

  this.renderDetalle = function(dataVentas){
    if (this._DT) {this._DT.destroy(); this._DT = null;}
    this._DOM.tblLista.find("tbody").html(this._tpl8.listado(dataVentas));
    this._DT = this._DOM.tblLista.DataTable({
        aaSorting: [[0, "desc"]],
        pageLength: 20,
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

  this.verDetalle = async function(idVenta){
    try {
      const { data } = await apiAxios.get(`ventas/${idVenta}`);
      renderVenta(data);
    } catch (error) {
        swal("Error", "Ha ocurrido un problema al obtener el detalle de la venta", "error");
        console.error(error);
    }
  };

  const renderVenta = (dataVenta) => {
    const mdlDetalleVenta = this._DOM.mdlDetalleVenta;

    mdlDetalleVenta.modal("show");
    mdlDetalleVenta.find("h3").html("Venta: "+dataVenta.id+" - "+dataVenta.cliente.nombres_apellidos+" - Doc.: "+dataVenta.cliente.numero_documento);
    mdlDetalleVenta.find(".modal-body").html(this._tpl8.detalle(dataVenta));
  };
  
  this.verAtencion = function(idVenta){
    const sentData = {
      id : idVenta,
      key : JSON.parse(localStorage.getItem(SESSION_NAME)).token
    };

    Util.downloadPDFUsingPost({
      url: "../impresiones/atencion.ticket.pdf.php",
      variableName: "p_data", 
      JSONData: JSON.stringify(sentData)
    });
  };

  this.verComprobante = function(idComprobante){
    const sentData = {
      id : idComprobante,
      key : JSON.parse(localStorage.getItem(SESSION_NAME)).token
    };
    
    Util.downloadPDFUsingPost({
      url: "../impresiones/comprobante.ticket.pdf.php",
      variableName: "p_data", 
      JSONData: JSON.stringify(sentData)
    });
  };

  return this.init();
};

$(document).ready(function(){
  new AccesoAuxiliar(()=>{
    objReporteVentas = new ReporteVentas();
  });
});

