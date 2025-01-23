const ListarFacturas = function($contenedor, _tpl8){
    var DT = null;
  
    this.init = function(){
      this.setDOM();
      this.setEventos();
      this.obtenerFacturas();
    };
  
    this.setDOM = function(){
      var DOM = Util.preDOM2DOM($contenedor, 
                      [{"cboTipocomprobante": "#cbotipocomprobante-listar"},
                        {"txtFechaInicio": "#txtfechainicio"},
                        {"txtFechaFin": "#txtfechafin"},
                        {"btnBuscar":"#btnbuscarfecha"},
                        {"tblLista" : "#tbllista"}
                        ]);  
        this.DOM = DOM;
    };
  
    this.setEventos = function(){
      const DOM = this.DOM;
  
      DOM.btnBuscar.on("click", (e) => {
        e.preventDefault();
        this.obtenerFacturas();
      });
    };
  
    this.obtenerFacturas = async function(){
      const DOM = this.DOM;
      const objButtonLoader = new ButtonLoading({$: DOM.btnBuscar[0]});

      objButtonLoader.start();
      try {
        const sentData = {
          id_tipo_comprobante : DOM.cboTipocomprobante.val() ?? 1,
          fecha_inicio: DOM.txtFechaInicio.val(), 
          fecha_fin: DOM.txtFechaFin.val()
        };
        const paramsData = new URLSearchParams(sentData);
        const { data } = await apiAxios.get(`comprobantes?${paramsData.toString()}`);
        this.listarFacturas(data);
      } catch (error) {
        swal("Error", error?.response?.data?.message || JSON.stringify(error?.response?.data), "error");
        console.error(error);
      } finally {
        objButtonLoader.finish();
      }
    };
  
    this.listarFacturas = function(dataFacturas){
      if (DT) {DT.fnDestroy(); DT = null;}
      this.DOM.tblLista.html(_tpl8.ListaFacturas({admin : ___ad == 1 ? 1 : null, data: dataFacturas}));
      if (dataFacturas.length > 0){
        DT = $(".tablalista").dataTable({
                "aaSorting": [[0, "desc"]],
                responsive: true
              });
      }
    };
  
    this.anular =  function(idComprobante, $btn){
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
      }, async (respuesta)=>{
          if (!respuesta){
            return;
          }

          try {
            await apiAxios.delete(`comprobantes/${idComprobante}`);
            swal("Éxito", "Comprobante eliminado con éxito.", "success");

            const index = $btn.closest("tr")[0];
            DT.fnDeleteRow(DT.fnGetPosition(index));
          } catch (error) {
            const { response } = error;
            if (Boolean(response?.data?.message)){
              swal("Error", response.data.message, "error");
              return;
            }
            swal("Error", error?.response?.data?.message || JSON.stringify(error?.response?.data), "error");
            console.error(error);
          }
      });
    };
  
    this.verDetalle = async function(idComprobante){
      try {
        const { data } = await apiAxios.get(`comprobantes/${idComprobante}`);
        renderFactura(data);
      } catch (error) {
        swal("Error", error?.response?.data?.message || JSON.stringify(error?.response?.data), "error");
        console.error(error);
      }
    };
  
    const renderFactura = function(dataComprobante){
      const mdlDetalleFactura = $("#mdlDetalleFactura");
  
      mdlDetalleFactura.modal("show");
      mdlDetalleFactura.find("h3").html(`Doc.: ${dataComprobante.numero_documento_cliente} | Comprobante: ${dataComprobante.descripcion_cliente}`);
      mdlDetalleFactura.find(".modal-body").html(_tpl8.DetalleFactura(dataComprobante));
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
  