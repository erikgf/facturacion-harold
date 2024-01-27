var app = {},
  _TEMPID = -1,
  _ACCION = "agregar",
  _CLASE = "Compra",
  DT = null,
  _SAVING = false;


app.init = function(){
  this.$tabRegistrarCompras = $("#tabRegistrarCompras");
  this.$tabListadoCompras = $("#tabListadoCompras");
  this.tpl8 = Util.Templater($("script[type=handlebars-x]"));

  this.RegistrarCompras = new RegistrarCompras(this.$tabRegistrarCompras, this.tpl8);
  this.ListarCompras = new ListarCompras(this.$tabListadoCompras, this.tpl8);
};

app.setDOM = function(){
};

app.setEventos  = function(){
};

app.setTemplates = function(){
};

app.limpiar = function(){
};

$(document).ready(function(){
  new AccesoAuxiliar(()=>{
    window.___ad = USUARIO?.idRol === 1;
    app.init();
  });
});

