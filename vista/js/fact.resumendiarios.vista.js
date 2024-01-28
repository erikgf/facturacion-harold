
const app = {
  init: function(){
    this._DT = null;
    this._data = [];
    this._registroSeleccionado = null;
    this._$trSeleccionado = null;

    this.setDOM();
    this.setEventos();
    this.setTemplate();
    return this;
  },
  setTemplate: function(){
    this._tpl8= {
      listado : Handlebars.compile($("#tpl8Listado").html()),
      listadoRegistroBoletas : Handlebars.compile($("#tpl8ListadoRegistroBoletas").html()),
      leerRegistro : Handlebars.compile($("#tpl8LeerRegistro").html())
    };
  },
  setDOM : function(){
    this._DOM = {
      txtFechaDesde : $("#txtfechadesde"),
      txtFechaHasta : $("#txtfechahasta"),
      btnBuscar : $("#btnbuscar"),
      btnNuevo: $("#btn-nuevo"),
      alert : $("#alert-blk"),
      tblListado : $("#tbl-listado"),
      mdlRegistro: $("#mdl-registro"),
      frmRegistro : $("#frm-registro"),
      txtRegistroFechaEmision : $("#txt-registro-fechaemision"),
      tblRegistroBoletas : $("#tbl-registro-boletas"),
      mdlLeer: $("#mdl-leer")
    };
  },
  setEventos : function(){
    const DOM = this._DOM;
  
    DOM.btnBuscar.on("click", (e) => {
      e.preventDefault();
      this.listar();
    });

    DOM.btnNuevo.on("click", (e)=>{
      e.preventDefault();
      this.nuevoRegistro();
    });
  
    DOM.tblListado.on("click", "tbody tr .btn-ver", (e) => {
      e.preventDefault();
      const $tr = $(e.currentTarget).parents("tr"); 
      const id = $tr.data("id");
      this.leer(id, $tr);
    });

    DOM.mdlRegistro.on("shown.bs.modal", () => {
      DOM.txtRegistroFechaEmision.val(null);
      DOM.tblRegistroBoletas.find("tbody").empty();
    });

    DOM.frmRegistro.on("submit", (e)=>{
      e.preventDefault();
      this.guardar();
    });

    DOM.txtRegistroFechaEmision.on("focusout", (e)=>{
      const fechaEmision = e.currentTarget.value;

      if (e.currentTarget.disabled === true){
        return;
      }

      if (!Boolean(fechaEmision) || Date.parse(fechaEmision) == NaN){
        DOM.tblRegistroBoletas.find("tbody").empty();
        return;
      }

      this.consultarComprobantesParaRegistro(fechaEmision);
    });


    DOM.mdlLeer.on("click", ".btn-enviarsunat", (e)=>{
      e.preventDefault();
      this.enviarSUNAT($(e.currentTarget));
    });


    DOM.mdlLeer.on("click", ".btn-consultarticket", (e)=>{
      e.preventDefault();
      this.consultarTicket($(e.currentTarget));
    });

  },
  fnObtenerEstado : (item) => {
    const estado = {};
    if (!Boolean(item.ticket)){
      estado.color = 'grey';
      estado.rotulo = 'PENDIENTE';
    } else {
      if (!Boolean(item.cdr_estado)){
        estado.color = 'primary';
        estado.rotulo = 'TICKET';
      } else {
        if (item.cdr_estado == '0'){
          estado.color = 'success';
          estado.rotulo = 'ACEPTADO';
        } else {
          estado.color = 'danger';
          estado.rotulo = 'RECHAZADO';
        }
      }
    }
    return estado;
  },
  listar: async function(){
    const DOM = this._DOM;
    DOM.btnBuscar.prop("disabled", true).addClass("shine");

    try {
      const sentData = {
        fecha_inicio: DOM.txtFechaDesde.val(), 
        fecha_fin: DOM.txtFechaHasta.val(),
      };
      const paramsData = new URLSearchParams(sentData);

      const { data } = await apiAxios.get(`resumenes-diarios?${paramsData.toString()}`);
      this.renderListar(data.map(item => {
        const estado = this.fnObtenerEstado(item);
        return {
          ...item,
          ticket: Boolean(item.ticket) ? item.ticket : '-',
          estado
        }
      }));

    } catch (error) {
      console.error(error);
    } finally{
      DOM.btnBuscar.prop("disabled", false).removeClass("shine");
    }
  },
  renderListar: function(data){
    const DOM = this._DOM;
    if (this._DT) { this._DT.destroy(); this._DT = null; }
    DOM.tblListado.find("tbody").html(this._tpl8.listado(data));
    this._DT = DOM.tblListado.DataTable({
      "aaSorting": [[0, "asc"]]
    });
    this._data = data;
  },
  leer : async function(id, $tr = null){
    try {
      const { data } = await apiAxios.get(`resumenes-diarios/${id}`);
      this._DOM.mdlLeer.modal("show");
      this.renderLeer({
        ...data, 
        estado: this.fnObtenerEstado(data)
      }, $tr);
      this._$trSeleccionado = $tr;
    } catch (error) {
      console.error(error);
    }
  },
  renderLeer : function(data){
    console.log({data});

    this._DOM.mdlLeer.find(".modal-content").html(this._tpl8.leerRegistro(data));
    this._registroSeleccionado = data;
  },
  nuevoRegistro: function(){
    this._DOM.mdlRegistro.modal("show");
  },
  consultarComprobantesParaRegistro : async function(fechaEmision){
    const $txtFechaEmision = this._DOM.txtRegistroFechaEmision;
    const $tbody = this._DOM.tblRegistroBoletas.find("tbody");

    $txtFechaEmision.prop("disabled", true);
    $tbody.html("<tr><td>Cargando...</td></tr>");

    try {
      const { data } = await apiAxios.get(`resumenes-diarios/comprobantes-fecha/${fechaEmision}`);
      this._DOM.tblRegistroBoletas.find("tbody").html(this._tpl8.listadoRegistroBoletas(data));
      
    } catch (error) {
      console.error(error);
      $tbody.empty();
    } finally{
      $txtFechaEmision.prop("disabled", false);
    }
  },
  guardar: async function(){
    const fechaEmision = this._DOM.txtRegistroFechaEmision.val();
    const comprobantes = this._DOM.tblRegistroBoletas.find("tbody tr td .chkselect:checked").toArray().map($chkSelect => {
      return {id: $chkSelect.dataset.id};
    });

    if (comprobantes.length <= 0){
      swal("Error", "No se ha seleccionado ningún comprobante.", "error");
      return;
    }

    const $btnGuardar = this._DOM.frmRegistro.find("button[type=submit]");
    $btnGuardar.prop("disabled", true).addClass("shine");

    try {
      const sentData = {
        fecha_emision :fechaEmision,
        status : "1",
        comprobantes
      };
      const { data } = await apiAxios.post(`resumenes-diarios`, sentData);
      this._DOM.mdlRegistro.modal("hide");

      swal("Éxito", "Registrado correctamente!", "success");

      setTimeout(()=>{
        this.leer(data.id);
      }, 300)
      
    } catch (error) {
      swal("Error", "Ha ocurrido un problema al registrar el resumen.", "error");
      console.error(error);
    } finally{
      $btnGuardar.prop("disabled", false).removeClass("shine");
    }
  },
  anular: function(){

  },
  enviarSUNAT : async function($btn){
    const id = $btn.data("id");

    $btn.prop("disabled", true).addClass("shine");
    try {
      const { data } = await apiAxios.post(`resumenes-diarios/enviar/${id}`);
    
      if (!Array.isArray(data)){
        throw data?.mensaje;
      }

      const nuevoTicket = data[0].cod_ticket;
      const registroActualizado = {
        ...this._registroSeleccionado,
        ticket: nuevoTicket,
        enviar_a_sunat : Boolean(nuevoTicket)
      }

      this.renderLeer({
        ...registroActualizado,
        estado: this.fnObtenerEstado(registroActualizado)
      });
      
    } catch (error) {
      swal("Error", "Ha ocurrido un problema al enviar a SUNAT el resumen.", "error");
      console.error(error);
    } finally{
      $btn.prop("disabled", false).removeClass("shine");
    }

  },
  consultarTicket : async function($btn){
    const id = $btn.data("id");
    $btn.prop("disabled", true).addClass("shine");

    try {
      const { data } = await apiAxios.post(`resumenes-diarios/ticket/${id}`);
      if (!Array.isArray(data)){
        throw data?.mensaje;
      }

      const respuesta = data[0];
      const registroActualizado = {
        ...this._registroSeleccionado,
        cdr_descripcion: respuesta.mensaje,
        cdr_estado : respuesta.cod_sunat
      }

      this.renderLeer({
        ...registroActualizado,
        estado: this.fnObtenerEstado(registroActualizado)
      });

    } catch (error) {
      swal("Error", "Ha ocurrido un problema al enviar a SUNAT el resumen.", "error");
      console.error(error);
    } finally{
      $btn.prop("disabled", false).removeClass("shine");
    }
  }
};

$(document).ready(function(){
  new AccesoAuxiliar(()=>{
    app.init();
  })
});

