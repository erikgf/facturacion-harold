var app = {},
  _TEMPID = -1,
  _ROTULO = "",
  _ACCION = "agregar",
  _CLASE = "Venta",
  DT = null,
  _POSTREGISTRADO = false,
  _MSJ = "",
  _SAVING = false;

app.init = function(){
  this.$tabRegistrarVentas = $("#tabRegistrarVentas");
  this.$tabListadoVentas = $("#tabListadoVentas");

  this.mdlVoucher = $("#mdlVoucher");
  this.frmVoucher = $("#frmgrabarvoucher");


  this.mdlComisionar = $("#mdlComisionar");
  this.frmComisionar = $("#frmgrabarcomisionar");
  this.cboComisionista = $("#cbocomisionista");
  this.tblListaComisionar = $("#tbllistacomisionista");
  this.lblComisionarTotal = $("#lblComisionarTotal");

  /*Modal Detalle*/

  this.tpl8 = Util.Templater($("script[type=handlebars-x]"));

  this.RegistrarVentas = new RegistrarVentas(this.$tabRegistrarVentas, this.tpl8);
  this.ListarVentas = new ListarVentas(this.$tabListadoVentas, this.tpl8);

  this.setEventos();
//  this.initRegistrar();
 // this.initLista();
};

app.setEventos  = function(){
  var self = this;
      txtNumeroVoucher = $("#txtnumerovoucher");

      txtNumeroVoucher.on("keypress", function(e){
        if (!Util.soloNumeros(e)){
          e.preventDefault(); return;
        }
        return;
      });

      self.mdlVoucher.on("hidden.bs.modal", function(e){
        _TEMPID = -1;
        self.frmVoucher[0].reset();
        if (_POSTREGISTRADO == true){
          /*Ir a otra tab y actualizar ventas*/
           $('.nav-tabs a[href="#tabListadoVentas"]').tab('show');
           self.ListarVentas.obtenerVentas(); /* lista ventas*/
           self.RegistrarVentas.limpiarVenta();
           swal("Exito", _MSJ, "success");
           _MSJ = "";
          _POSTREGISTRADO = false;
        }
      });

      self.frmVoucher.on("submit", function(e){
        /*Ejcutar ajxur*/
        e.preventDefault();
        if (_TEMPID == -1){
          self.mdlVoucher.modal("hide");
          swal("Error", "Código de venta no válido.", "error");
          return;
        }
        var fn = function(xhr){
          var datos = xhr.datos;
          if (datos.rpt){
            self.mdlVoucher.modal("hide");
            $('.nav-tabs a[href="#tabListadoVentas"]').tab('show');
            app.ListarVentas.obtenerVentas(); /* lista ventas*/
            swal("Exito", datos.msj, "success");
          } else {
            swal("Error", datos.msj, "error");
          }
        };

        new Ajxur.Api({
          modelo: "Venta",
          metodo: "registrarVoucher",
          data_in: {
            p_numeroVoucher: txtNumeroVoucher.val(),
            p_codVenta: _TEMPID
          }
        }, fn);
      });

      self.mdlComisionar.on("hidden.bs.modal", function(e){
        _TEMPID = -1;
        self.cboComisionista.val("");
        self.tblListaComisionar.html('<tr class="tr-null "><td class="text-center" colspan="10"> Sin comisionista seleccionado. </td></tr>');
        self.lblComisionarTotal.html("0.00");
      });

      self.tblListaComisionar.on("change", "tr .tipo_comision select", function(e){
        cambiarComision(this.parentElement.parentElement);
      });

      self.tblListaComisionar.on("change", "tr .monto_comision input", function(e){
        cambiarComision(this.parentElement.parentElement);
      });

      self.tblListaComisionar.on("change", "tr .comisionar input", function(e){
        /*reclcular total*/
        var totalActual = self.lblComisionarTotal.html(),
            arregloTD = [].slice.call(this.parentElement.parentElement.children),
            $comisionado = arregloTD[5].innerHTML;

        retorno = parseFloat(parseFloat(totalActual) + ($comisionado * (this.checked == true ? 1 : -1))).toFixed(2);
        self.lblComisionarTotal.html(retorno);
      });

      self.cboComisionista.on("change", function(e){
        var codComisionista = this.value,
            fn = function(xhr){
              var datos = xhr.datos;
              if( datos.rpt){
                self.tblListaComisionar.html(self.tpl8.ListaComisionista(datos.data.detalle));
                self.lblComisionarTotal.html(datos.data.total);
              }
            };

        if (_TEMPID == -1){
          return;
        }

        if (codComisionista == ""){
          self.tblListaComisionar.html('<tr class="tr-null "><td class="text-center" colspan="10"> Sin comisionista seleccionado. </td></tr>');
          return;
        }

        new Ajxur.Api({
          modelo: "Venta",
          metodo: "obtenerDatosComisionista",
          data_in: {
            p_codVenta : _TEMPID,
            p_codComisionista: codComisionista
          }
        }, fn);
      });

      var cambiarComision = function($tr){
        var arregloTD = [].slice.call($tr.children),
            $total_detalle = arregloTD[2].dataset.total,
            $tipo_comision = arregloTD[3].children[0].value,
            $valor_comision = arregloTD[4].children[0].value,
            $comisionar = arregloTD[6].children[0].checked,
            viejoMontoComisionado = arregloTD[5].innerHTML,
            nuevoMontoComisionado = app.calcularComisionista($total_detalle, $tipo_comision, $valor_comision),
            totalComisionar = parseFloat(self.lblComisionarTotal.html()),
            diferenciaMontos = nuevoMontoComisionado - viejoMontoComisionado;

            arregloTD[5].innerHTML = parseFloat(nuevoMontoComisionado).toFixed(2);

            if ($comisionar){
              if (diferenciaMontos >= 0){
                self.lblComisionarTotal.html( parseFloat(totalComisionar + parseFloat(diferenciaMontos)).toFixed(2));
              } else {
                self.lblComisionarTotal.html( parseFloat(totalComisionar -  Math.abs(diferenciaMontos) ).toFixed(2));
              }
            }
      };

      self.frmComisionar.on("submit", function(e){
        e.preventDefault();
        var fn, arregloComisionista = [], arregloTR,
            codComisionista, registroOK = true;

        codComisionista = self.cboComisionista.val();

        if (_TEMPID == -1){
          self.mdlComisionar.modal("hide");
          swal("Error", "Código de venta no válido.", "error");
          return;
        }

        if (codComisionista == ""){
          Util.alert($("#blkalertcomisionar"), {tipo: "e", mensaje: "Debe seleccionar un comisionista."});
          return;
        }

        fn = function(xhr){
          var datos = xhr.datos;
          if (datos.rpt){
            self.mdlComisionar.modal("hide");
            swal("Exito", datos.msj, "success");
          } else {
            swal("Error", datos.msj, "error");
          }
        };

        /*Obtener TRs*/
        arregloTR= self.tblListaComisionar.find("tr:not(.tr-null)").toArray();

        for (var i = 0, len = arregloTR.length; i < len; i++) {
          var $tr = arregloTR[i],
            $tds = [].slice.call($tr.children),
            item =  $tds[0].innerHTML,
            cod_producto = $tds[1].dataset.producto,
            tipo_comision = $tds[3].children[0].value,
            valor_comision = $tds[4].children[0].value,
            comisionar = $tds[6].children[0].checked;

            if (valor_comision == "" || valor_comision <= 0 && comisionar){
              var $$tds = $($tds);
              var fnCleanClass= function(){
                $$tds.removeClass("bg-danger");
                $$tds = null;
              };
              $$tds.addClass("bg-danger");
              setTimeout(fnCleanClass, 1000);
              Util.alert($("#blkalertcomisionar"), {tipo: "e", mensaje:"No se ha registrado monto de comisión."});
              registroOK = false;
              return;
            }

            if (comisionar){
              arregloComisionista.push({
                item : item,
                cod_producto : cod_producto,
                tipo_comision : tipo_comision,
                valor_comision : valor_comision
              });
            }
        };

        if (!registroOK){
          return;
        }

        if (arregloComisionista.length <= 0){
          Util.alert($("#blkalertcomisionar"), {tipo: "e", mensaje:"No hay productos que comisionar."});
          return;
        }

        new Ajxur.Api({
          modelo: "Venta",
          metodo: "registrarComisionista",
          data_in: {
            p_codComisionista : self.cboComisionista.val(),
            p_codVenta: _TEMPID
          },
          data_out: [JSON.stringify(arregloComisionista)]
        }, fn);
      });
  
  /*
    var mdlDetalleVenta = $("#mdlDetalleVenta");
    $mdlDetalleVenta.on("shown.bs.modal", function(e){

    });
  */


};

app.calcularComisionista = function(totalDetalle, tipoComision, valorComision) {
    var resultado = 0;
    if (tipoComision == "P"){
      resultado = (valorComision / 100) * totalDetalle;
    } else {
      resultado = (valorComision == "" ? 0 : valorComision);
    }

    return parseFloat(resultado).toFixed(2);
};

$(document).ready(function(){
  Handlebars.registerHelper("calcularComisionista", app.calcularComisionista);
  new AccesoAuxiliar(()=>{
    window.___ad = USUARIO?.idRol == 1;
    app.init();
  });
});

