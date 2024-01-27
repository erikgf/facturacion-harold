var app = {}; 

app.init = function(){
  this.setDOM();
  this.setEventos();
};

app.setDOM = function(){
  var DOM = {};

  DOM.frmIniciar = $("#frminiciar");
  DOM.txtDni = $("#txtdni");
  DOM.txtClave = $("#txtclave");
  DOM.blkAlert = $("#blkalert");

  this.DOM = DOM;
};

app.setEventos  = function(){
  var self = this;

  self.DOM.frmIniciar.on("submit", function(e){
    e.preventDefault();
    self.iniciarSesion();
  });
};

app.limpiar = function(){
  this.DOM.frmIniciar[0].reset();
};

app.iniciarSesion = async function(){
  const DOM = this.DOM;
  try {
    const sentData = {
      username:  DOM.txtDni.val(),
      password:  DOM.txtClave.val()
    };
    const {data} = await apiAxiosPublic.post(`sesion/iniciar`, sentData);
    localStorage.setItem(SESSION_NAME, JSON.stringify(data));

    window.location.href = "principal.vista.php";
  } catch (error) {
    const { response } = error;
    if (Boolean(response?.data?.message)){
      Util.alert(DOM.blkAlert, {tipo: "e", "mensaje": response.data.message});
      return;
    }

    this.limpiar();
    console.error(error);
  }

};

$(document).ready(function(){
  const user = JSON.parse(localStorage.getItem(SESSION_NAME))?.user;
  if (Boolean(user)){
    window.location.href = './principal.vista.php';
    return;
  }
  app.init();
});

