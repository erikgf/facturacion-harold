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
  this.$tabRegistrarNotas = $("#tabRegistrarNotas");
  this.$tabListadoNotas = $("#tabListadoNotas");
  /*Modal Detalle*/
  this.tpl8 = Util.Templater($("script[type=handlebars-x]"));

  this.RegistrarNotas = new RegistrarNotas(this.$tabRegistrarNotas, this.tpl8);
  this.ListarNotas = new ListarNotas(this.$tabListadoNotas, this.tpl8);

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

