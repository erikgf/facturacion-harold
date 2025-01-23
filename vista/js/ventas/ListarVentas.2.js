const ListarVentas = function($contenedor, _tpl8){
    var DT = null;
  
    this.init = function(){
      this.setDOM();
      this.setEventos();
      this.obtenerVentas();
    };
  
    this.setDOM = function(){
      var DOM = Util.preDOM2DOM($contenedor, 
                      [{"cboSucursal": "#cbosucursal"},
                        {"txtFechaInicio": "#txtfechainicio"},
                        {"txtFechaFin": "#txtfechafin"},
                        {"btnBuscar":"#btnbuscarfecha"},
                        {"tblLista" : "#tbllista"}
                        ]);  
  
        this.DOM = DOM;
    };
  
    this.setEventos = function(){
      var self = this,
          DOM = self.DOM;
  
      DOM.cboSucursal.on("change", function(e){
        e.preventDefault();
        self.obtenerVentas();
      });
  
      DOM.btnBuscar.on("click", function(e){
        e.preventDefault();
        self.obtenerVentas();      
      });
    };
  
    this.obtenerVentas = async function(){
      const DOM = this.DOM;
      const objButtonLoader = new ButtonLoading({$: DOM.btnBuscar[0]});

      objButtonLoader.start();
      try {
        const sentData = {
          id_sucursal : DOM.cboSucursal.val() ?? 1,
          fecha_inicio: DOM.txtFechaInicio.val(), 
          fecha_fin: DOM.txtFechaFin.val()
        };
        const paramsData = new URLSearchParams(sentData);
        const { data } = await apiAxios.get(`ventas?${paramsData.toString()}`);
        this.listarVentas(data);
      } catch (error) {
        swal("Error", error?.response?.data?.message || JSON.stringify(error?.response?.data), "error");
        console.error(error);
      } finally {
        objButtonLoader.finish();
      }
    };
  
    this.listarVentas = function(dataVentas){
      if (DT) {DT.fnDestroy(); DT = null;}
      this.DOM.tblLista.html(_tpl8.ListaVentas({admin : ___ad == 1 ? 1 : null, data: dataVentas}));
      if (dataVentas.length > 0){
        DT = $(".tablalista").dataTable({
                "aaSorting": [[0, "desc"]],
                responsive: true
              });
      }
    };
  
    this.gestionarVoucher = function(cod_venta,  rotulo){
      _TEMPID = cod_venta;
      app.mdlVoucher.modal("show");
      app.mdlVoucher.find(".rotuloVenta").html(rotulo);
      app.frmVoucher[0].reset();
    };
  
  
    this.gestionarComisionista = function(cod_venta, rotulo ){
      _TEMPID = cod_venta;
      app.mdlComisionar.modal("show");
      app.mdlComisionar.find(".rotuloVenta").html(rotulo);
    };
  
    this.anular =  function(idVenta, $btn){
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
            await apiAxios.delete(`ventas/${idVenta}`);
            swal("Éxito", "Venta anulada con éxito.", "success");

            const index = $btn.closest("tr")[0];
            DT.fnDeleteRow(DT.fnGetPosition(index));
          } catch (error) {
              swal("Error", error?.response?.data?.message || JSON.stringify(error?.response?.data), "error");
              console.error(error);
          }
      });
    };
  
    this.verDetalle = async function(idVenta){
      try {
        const { data } = await apiAxios.get(`ventas/${idVenta}`);
        renderVenta(data);
      } catch (error) {
          swal("Error", error?.response?.data?.message || JSON.stringify(error?.response?.data), "error");
          console.error(error);
      }
    };
  
    const renderVenta = function(dataVenta){
      const mdlDetalleVenta = $("#mdlDetalleVenta");
  
      mdlDetalleVenta.modal("show");
      mdlDetalleVenta.find("h3").html("Venta: "+dataVenta.id+" - "+dataVenta.cliente.nombres_apellidos+" - Doc.: "+dataVenta.cliente.numero_documento);
      mdlDetalleVenta.find(".modal-body").html(_tpl8.DetalleVenta(dataVenta));
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
  