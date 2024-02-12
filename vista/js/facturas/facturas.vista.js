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
  this.$tabRegistrarFacturas = $("#tabRegistrarFacturas");
  this.$tabListadoFacturas = $("#tabListadoFacturas");
  /*Modal Detalle*/
  this.tpl8 = Util.Templater($("script[type=handlebars-x]"));

  this.RegistrarFacturas = new RegistrarFacturas(this.$tabRegistrarFacturas, this.tpl8);
  this.ListarFacturas = new ListarFacturas(this.$tabListadoFacturas, this.tpl8);

  this.setEventos();
//  this.initRegistrar();
 // this.initLista();
};

app.setEventos  = function(){

};

$(document).ready(function(){
  new AccesoAuxiliar(()=>{
    window.___ad = USUARIO?.idRol == 1;
    app.init();
  });
});

