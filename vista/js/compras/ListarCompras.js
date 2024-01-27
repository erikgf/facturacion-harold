const ListarCompras = function($contenedor, _tpl8){
    var _Util = Util,
        _Ajxur = Ajxur,
        DT = null;
  
    this.init = function(){
      this.setDOM();
      this.setEventos();
      this.obtenerCompras();
    };

    this.getDT = function(){
      return DT;
    }
  
    this.setDOM = function(){
      var DOM = _Util.preDOM2DOM($contenedor, 
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
        self.obtenerCompras();
      });
  
      DOM.btnBuscar.on("click", function(e){
        e.preventDefault();
        self.obtenerCompras();      
      });
    };

    this.obtenerCompras = async function(){
      const DOM = this.DOM;
      try {
        const sentData = {
          id_sucursal : DOM.cboSucursal.val() ?? 1,
          fecha_inicio: DOM.txtFechaInicio.val(), 
          fecha_fin: DOM.txtFechaFin.val()
        };
        const paramsData = new URLSearchParams(sentData);
        const { data } = await apiAxios.get(`compras?${paramsData.toString()}`);
        this.listarCompras(data);
      } catch (error) {
          swal("Error", JSON.stringify(error), "error");
          console.error(error);
      }
    };
  
    this.listarCompras = function(dataCompras){
      if (DT) {DT.fnDestroy(); DT = null;}
      this.DOM.tblLista.html(_tpl8.ListaCompras({admin : ___ad == 1 ? 1 : null, data: dataCompras}));
      if (dataCompras.length > 0){
        DT = $(".tablalista").dataTable({
                "aaSorting": [[0, "desc"]],
                responsive: true
              });
      }
    };
  
    this.anular =  function(idCompra, $btn){
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
            await apiAxios.delete(`compras/${idCompra}`);
            swal("Éxito", "Compra anulada con éxito.", "success");

            const index = $btn.closest("tr")[0];
            DT.fnDeleteRow(DT.fnGetPosition(index));
          } catch (error) {
              const { response } = error;
              if (Boolean(response?.data?.message)){
                swal("Error", JSON.stringify(response.data.message), "error");
                return;
              }
              console.error(error);
          }
      });
    };
  
    this.verDetalle = async function(idCompra){
      try {
        const { data } = await apiAxios.get(`compras/${idCompra}`);
        renderCompra(data);
      } catch (error) {
        swal("Error", JSON.stringify(error), "error");
        console.error(error);
      }
    };
  
    const renderCompra = function(dataCompra){
      const mdlDetalleCompra = $("#mdlDetalleCompra");
  
      mdlDetalleCompra.modal("show");
      mdlDetalleCompra.find("h3").html("Compra: "+dataCompra.id+" - "+dataCompra.proveedor.razon_social);
      mdlDetalleCompra.find(".modal-body").html(_tpl8.DetalleCompra({
        ...dataCompra,
        tipo_tarjeta : dataCompra.tipo_tarjeta 
                          ? (dataCompra.tipo_tarjeta === 'C'
                              ? 'CRÉDITO'
                              : 'DÉBITO')
                          : null,
        tipo_pago : (dataCompra.tipo_pago == 'T'  ? 'TARJETA' : 'EFECTIVO' )
      }));
    };
  
  
    return this.init();
  };
  