var app = {},
  DT = null,
  alertsW = {},
  seleccionados = [],
  INTERVALO_EMISION_GLOBAL = 3 * 1000,
  _enviandoComprobante = false,
  _modoGlobal = false;

app.init = function(){
  this.setDOM();
  this.setEventos();
  this.setTemplate();
};

app.setDOM = function(){
  var DOM = {};

  DOM.listado = $("#listado");
  DOM.listadoBody = $("#listado-body");

  DOM.txtFechaDesde = $("#txtfechadesde");
  DOM.txtFechaHasta = $("#txtfechahasta");
  DOM.chkTodos = $("#chktodos");
  DOM.cboEstado = $("#cboestado");
  DOM.btnBuscar = $("#btnbuscar");
  DOM.btnExcel = $("#btnexcel");
  DOM.btnGenerar = $("#btngenerarenviar");
  DOM.alertGlobal = $("#alert-blk-global");
  DOM.alert = $("#alert-blk");

  this.DOM = DOM;
};

app.setEventos  = function(){
  const DOM = this.DOM;

  DOM.chkTodos.on("change", function(e){
    const checked = this.checked;
    DOM.txtFechaDesde.prop("disabled", checked);
    DOM.txtFechaHasta.prop("disabled", checked);
  });

  DOM.btnBuscar.on("click", (e) => {
    e.preventDefault();
    this.listar();
  });

  DOM.listadoBody.on("change", "tr td .chkselect", (e) => {
    e.preventDefault();
    this.cambioCheck( e.currentTarget );
  });

  DOM.btnGenerar.on("click", (e) => {
    e.preventDefault();
    if (seleccionados.length){
      this.generarEnviarGlobal();
    }
  });

  DOM.btnExcel.on("click", (e) => {
    e.preventDefault();
    const sentData = {
      tipo: DOM.chkTodos[0].checked,
      fecha_inicio: DOM.txtFechaDesde.val(),
      fecha_fin: DOM.txtFechaHasta.val(),
      key : JSON.parse(localStorage.getItem(SESSION_NAME)).token
    };

    Util.downloadPDFUsingPost({
      url: "../controlador/reporte.xls.comprobantes.php",
      variableName: "p_data", 
      JSONData: JSON.stringify(sentData)
    });
  });
};

app.cambioCheck = function($checkbox){
     var  $tr =  $checkbox.parentElement.parentElement,
          dataset = $tr.dataset,
          tipo = ($checkbox.checked == true ? "+" : "-"),
          $btnGenerar = this.DOM.btnGenerar;

     if (tipo == "+"){
        seleccionados.push({
          id: dataset.id,
          comprobante : dataset.comprobante,
          $tr : $tr
        });
      
        $($tr).addClass("tr-seleccionado");
     } else {
        for( var i = 0; i < seleccionados.length; i++){ 
           if ( seleccionados[i].id === dataset.id) {
             seleccionados.splice(i, 1); 
             $($tr).removeClass("tr-seleccionado");
           }
        }
     }
      
      if (seleccionados.length){
        $btnGenerar.attr("disabled",false);
      } else {
        $btnGenerar.attr("disabled",true);
      }
};

app.setTemplate = function(){
  this.tpl8= {
    listado : Handlebars.compile($("#tpl8Listado").html())
  };
};

app.listar = async function(){
  const DOM = app.DOM;
  try {
    const sentData = {
      todas_fechas: DOM.chkTodos[0].checked ? 1 : 0,
      fecha_inicio: DOM.txtFechaDesde.val(), 
      fecha_fin: DOM.txtFechaHasta.val(),
      estado: DOM.cboEstado.val()
    };
    const paramsData = new URLSearchParams(sentData);
    const { data } = await apiAxios.get(`comprobantes/generacion/listar?${paramsData.toString()}`);
    app.renderLista(data);
  } catch (error) {
    swal("Error", JSON.stringify(error), "error");
    console.error(error);
  }
};

const mapEstadoGenerado = (comprobante) => {
  let estado_generado = {color:'danger', rotulo: 'No Generado', icon: 'close'};
  if (comprobante.fue_generado == 1){
    estado_generado = {
      color: 'success',
      rotulo: 'Generado',
      icon: 'check'
    };
  } 

  if (comprobante.fue_firmado == 1){
    estado_generado.rotulo = "Firmado";
  }

  return {
    ...comprobante,
    estado_generado
  }
};

app.renderLista = function(data){
  const DOM = app.DOM;
  if (DT) { DT.destroy(); DT = null; }
  DOM.listadoBody.html(app.tpl8.listado(data.map(mapEstadoGenerado)));
  DT = DOM.listado.find("table").DataTable({
    "aaSorting": [[0, "asc"]],
    responsive: true,
    dom: 'Bfrtip',
        buttons: [
            {
              extend: 'csv',
              className: "btn btn-secondary",
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
  });
};

const mostrarAlert = function(mensaje, tipo, nombre_comprobante){
  var titulo = (tipo == "e" ? "Error" : (tipo == "w" ? "Procesando" : "OK")),
      classError = (tipo == "e" ? "danger" : (tipo == "w" ? "warning" : "success")),
      icono = (tipo == "e" ? "close" : (tipo == "w" ? "bullhorn" : "check")),
      html = '<div class="col-xs-12"><div class="alert alert-'+classError+'" style="margin:0px">';
      html += '<button type="button" class="close" data-dismiss="alert"><i class="ace-icon fa fa-times"></i></button>';
      html += '<strong> <i class="ace-icon fa fa-'+icono+'"></i> '+titulo+'!</strong>';
      html += ' '+mensaje+'<br>';
      html += '</div></div>';

  if (tipo == "w"){
    const $html = $(html);
    app.DOM.alert.append($html);
    alertsW[nombre_comprobante] = $html;
  } else {
    app.DOM.alert.append(html);
  }
};

app.generarSUNAT = async function(btn, idComprobante, nombre_comprobante){
  if (idComprobante == null || idComprobante == ""){
    return;
  }

  if (_modoGlobal == true){
    return;
  }

  mostrarAlert("Generando y enviando comprobante: "+nombre_comprobante+"....", "w",nombre_comprobante);

  try {
    const response = await apiAxios.post(`comprobantes/generar-firmar-xml/${idComprobante}`);
    const { data } = response;

    alertsW[nombre_comprobante].remove();
    alertsW[nombre_comprobante] = null;
    delete alertsW[nombre_comprobante];

    mostrarAlert(`${nombre_comprobante} - Generación y firma correcta.`, "s");

    const $btn = $(btn);
    const tableRow = DT.row($btn.parents('tr'));
    const {firmado, generado} = data;
    const {datos_comprobante, respuesta, fue_generado} = generado;

    const fueFirmado = firmado.valor_firma.length > 0;
    const fueGenerado = fue_generado == 1;

    const nuevosDatos = {
      "id": datos_comprobante.id,
      "id_tipo_comprobante": datos_comprobante.COD_TIPO_DOCUMENTO,
      "numero_documento_cliente": datos_comprobante.NRO_DOCUMENTO_CLIENTE,
      "descripcion_cliente": datos_comprobante.RAZON_SOCIAL_CLIENTE,
      "id_tipo_moneda": datos_comprobante.COD_MONEDA,
      "total_gravadas": datos_comprobante.TOTAL_GRAVADAS,
      "total_igv": datos_comprobante.TOTAL_IGV,
      "importe_total":  datos_comprobante.TOTAL,
      "xml_filename": `${respuesta.ruta}/${respuesta.xml_filename}`,
      "fue_generado": fueGenerado,
      "fue_firmado": fueFirmado,
      "cdr_estado": null,
      "enviar_a_sunat": "0",
      "comprobante": nombre_comprobante,
      "fecha_emision": datos_comprobante.FECHA_DOCUMENTO
    };

    const rData = $(app.tpl8.listado([nuevosDatos].map(mapEstadoGenerado))).children().toArray().map(td => {
      return td.innerHTML;
    });

    DT.row( tableRow )
      .data(rData);
      
  } catch (error) {
      const { response } = error;
      if (response?.data?.message){
        mostrarAlert(`${nombre_comprobante} - ${response?.data?.message}`, "e");
      }
      console.error(error);
  }
};

app.generarEnviarSUNAT = function(codTransaccion, nombre_comprobante, enviandoDesdeGlobal){
  var self = this,
    fn = function(xhr){
      var datos = xhr.datos;

      alertsW[nombre_comprobante].remove();
      alertsW[nombre_comprobante] = null;
      delete alertsW[nombre_comprobante];

      if (datos.respuesta == "error"){
        mostrarAlert(nombre_comprobante+" - "+datos.mensaje, "e");
      } else {
        mostrarAlert(nombre_comprobante+" - "+datos.msj_sunat, "s");
        if (!enviandoDesdeGlobal){
            self.listar();
        }
      }

      if (enviandoDesdeGlobal){
        for( var i = 0; i < seleccionados.length; i++){ 
          var obj =seleccionados[i], $tr;
           if ( obj.id === codTransaccion) {
             seleccionados.splice(i, 1); 
             $tr = $(obj.$tr);
             $tr.removeClass("tr-seleccionado");
             obj.$tr.children[0].children[0].checked = false;
            _enviandoComprobante = false;
           }
        }

        if (!seleccionados.length){
          self.DOM.alertGlobal.empty();
          _modoGlobal = false;
          _enviandoComprobante = false;
          self.listar();
        }
      }
    };

  if (codTransaccion == null || codTransaccion == ""){
    return;
  }

  if (enviandoDesdeGlobal == undefined){
    enviandoDesdeGlobal  = false;
  }

  if (_modoGlobal == true && enviandoDesdeGlobal == false){
    return;
  }
  mostrarAlert("Generando y enviando comprobante: "+nombre_comprobante+"....", "w",nombre_comprobante);

  new Ajxur.Api({
    modelo: "Comprobante",
    metodo: "generarEnviarSunat",
    data_in: {p_codTransaccion: codTransaccion}
  },fn);
};

app.descargarXML = function(xml_filename){
    const dataurl = xml_filename;
    const filename = xml_filename.split("/").pop();
    const link = document.createElement("a");
    link.href = dataurl;
    link.target = "_blank";
    link.download = filename;
    link.click();
};

app.enviarSUNAT = async function(btn, idComprobante, nombre_comprobante){
  if (idComprobante == null || idComprobante == ""){
    return;
  }

  mostrarAlert("Enviando comprobante: "+nombre_comprobante+"....", "w",nombre_comprobante);

  try {

    const response = await apiAxios.post(`comprobantes/enviar/${idComprobante}`);
    const { data } = response;
    
    if (!Array.isArray(data)){
      mostrarAlert(`${nombre_comprobante} - ${data?.mensaje}.`, "e");
      throw data?.mensaje;
    }

    mostrarAlert(`${nombre_comprobante} - Envío correcto.`, "s");

    const $btn = $(btn);
    const $tr = $btn.parents('tr');
    const [item] = data;

    let $htmlEstado = "";

    if (item.cod_sunat == 0){
      $htmlEstado = `<span class="label label-success">Enviado y Aceptado</span>
                      <br> <small>${item.mensaje}</small>`
    } else {
      $htmlEstado = `<span class="label label-danger">Rechazado</span>
                      <br> <small>${item.mensaje}</small>`
    }

    $tr.find(".btn-generar-xml").remove();
    $tr.find(".td-estado").html($htmlEstado);
    $btn.remove();

  } catch (error) {
      const { response } = error;
      if (response?.data?.message){
        mostrarAlert(`${nombre_comprobante} - ${response?.data?.message}`, "e");
      }
      console.error(error);
  } finally {
    alertsW[nombre_comprobante].remove();
    alertsW[nombre_comprobante] = null;
    delete alertsW[nombre_comprobante];
  }
};


app.verComprobante = function(idComprobante){
  const sentData = {
    id : idComprobante,
    key : JSON.parse(localStorage.getItem(SESSION_NAME)).token
  };
  
  console.log({sentData});

  Util.downloadPDFUsingPost({
    url: "../impresiones/comprobante.ticket.pdf.php",
    variableName: "p_data", 
    JSONData: JSON.stringify(sentData)
  }); 
};

app.generarEnviarGlobal =function(){
  var fnAccion, interval, self= this;

  _modoGlobal = true;
  fnAccion = function(){
        var len = seleccionados.length,
            str = "",
            fnHtmlAlert =  function(txt){
              return '<div class="col-xs-12"><div class="alert alert-success" style="margin:0px"><strong> <i class="ace-icon fa fa-check"></i> Mensaje:</strong> '+txt+'<br></div></div>';        
            },
            primerComprobante = null;

        if (_enviandoComprobante == true){
          return;
        }

        if (_modoGlobal == false || !len ){          
          clearInterval(interval);                    
          return;
        }
        //Generar cadena.
        txt = "Se están procesando los siguientes comprobantes: ";
        for (var i = 0; i < len; i++) {
          var obj = seleccionados[i];
          txt += obj.comprobante + ",";
          if (i == 0){
            primerComprobante = obj;
          }
        };
  
        txt = txt.substring(0, txt.length - 1)+".";      
        self.DOM.alertGlobal.html(fnHtmlAlert(txt));
        /*Enviar ultimo comprobante*/
        _enviandoComprobante = true;
        self.generarEnviarSUNAT(primerComprobante.id, primerComprobante.comprobante, true);
      },
  interval;


  interval = setInterval(fnAccion, INTERVALO_EMISION_GLOBAL);

  fnAccion();

};

$(document).ready(function(){
  new AccesoAuxiliar(()=>{
    app.init();
  })
});

