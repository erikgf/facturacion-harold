var Util = {
	TFloat : function (valor, digitos){
		return parseFloat(valor).toFixed(!digitos ? 2 : digitos);
	},
	alert: function($alertPlacer, opt){
		var ds, $html;
		if (!opt.tipo){
			opt.tipo = 'e';
		}
		switch(opt.tipo){
			case "s" : 
				ds  = {icon: "fa fa-check", type: "success", color: "green"};
				break;
			case "w":
				ds = {icon: "fa fa-info-circle", type: "warning", color: "yellow"};
				break;
			case "e":
				ds = {icon: "fa fa-ban", type: "danger", color: "red"};
				break;
		}

        $html = `<div class="alert alert-block alert-`+ds.type+`"><i class="ace-icon `+ds.icon+` `+ds.color+`"></i> `+opt.mensaje+`</div>`;
		$alertPlacer.html($html);
		setTimeout(function(){
			$alertPlacer.empty();
		}, opt.tiempo || 3000);                        
	},
  completarNumero:  function(valor, cantidad){
      var tmp = ("000000000000000"+valor);            
      return (tmp).substr(tmp.length  - cantidad,cantidad);
  },
	notificacion: function(colorName, text, placementFrom, placementAlign, animateEnter, animateExit, delay){
	    if (colorName === null || colorName === '') { colorName = 'bg-black'; }
	    if (text === null || text === '') { text = 'Turning standard Bootstrap alerts'; }
	    if (animateEnter === null || animateEnter === '') { animateEnter = 'animated fadeInDown'; }
	    if (animateExit === null || animateExit === '') { animateExit = 'animated fadeOutUp'; }
	    if (delay === null || delay === '') { delay = 5000; }
	    var allowDismiss = true;

	    $.notify({
	        message: text
	    },
	        {
	            type: colorName,
	            allow_dismiss: allowDismiss,
	            newest_on_top: true,
	            timer: 1000,
	            delay: delay,
	            placement: {
	                from: placementFrom,
	                align: placementAlign
	            },
	            animate: {
	                enter: animateEnter,
	                exit: animateExit
	            },
	            template: '<div data-notify="container" class="bootstrap-notify-container alert alert-dismissible {0} ' + (allowDismiss ? "p-r-35" : "") + '" role="alert">' +
	            '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
	            '<span data-notify="icon"></span> ' +
	            '<span data-notify="title">{1}</span> ' +
	            '<span data-notify="message">{2}</span>' +
	            '<div class="progress" data-notify="progressbar">' +
	            '<div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>' +
	            '</div>' +
	            '<a href="{3}" target="{4}" data-notify="url"></a>' +
	            '</div>'
	        });
	},
	cerrarSesion: async function(){
		if (!confirm("¿Desea cerrar sesión?")){
			return ;
		}

    try {
      await apiAxios.post(`sesion/cerrar`);
      localStorage.removeItem(SESSION_NAME);
    } catch (error) {
      console.error(error);
    }finally{
      location.href = '../';
    }
	},
   soloNumerosDecimales: function (evento) {
        var tecla = (evento.which) ? evento.which : evento.keyCode;
        if (((tecla >= 48 && tecla <= 57) || tecla == 46)) {
            return true;
        }
        return false;
    }, 
    soloDecimal: function(evento, cadena,mostrar){
        var tecla = (evento.which) ? evento.which : evento.keyCode;
        var key = cadena.length;
        var posicion = cadena.indexOf('.');
        var contador = 0;
        var numero = cadena.split(".");
        var resultado1 = numero[0];
        var suma = resultado1.length+mostrar; 

        while (posicion != -1) { 
            contador++;             
            posicion = cadena.indexOf('.', posicion + 1);

        }

        if ( (tecla>=48 && tecla<=57) || (tecla==46) ) {    
            if ( key == 0 &&  tecla == 46 ) { // SOLO PERMITE ENTRE 0 AL 9
                return false;
            }
            
            if (contador != 0 && tecla == 46) { //NO SE REPITA EL PUNTO                
                return false;
            }

            if ( cadena == '0') { // EL SIGUIENTE ES PUNTO   
                if ( tecla>=48 && tecla<=57 ) {
                    return false;
                }
                return true;                
            }      
            
            if (!(key <= suma)) {
                return false;
            }
            return true;            
        }
        return false;
    },
    soloLetras: function (evento, espacio=null) {
        var tecla = (evento.which) ? evento.which : evento.keyCode;
        if ( espacio != null ) {
            if ((tecla >= 65 && tecla <= 90) || (tecla >= 97 && tecla <= 122) || (tecla==241) || (tecla==209) || (tecla == 180) ) {
                return true;
            }    
        }else{
            if ((tecla >= 65 && tecla <= 90) || (tecla >= 97 && tecla <= 122) || (tecla==241) || (tecla==209) || (tecla>=32 && tecla <=40) || (tecla==8)  || (tecla==46)  || (tecla == 180)  ) {
                return true;
            } 
        }
        return false;
    },
    soloNumeros: function (evento) {
        var tecla = (evento.which) ? evento.which : evento.keyCode;
        if ((tecla >= 48 && tecla <= 57)) {
            return true;
        }
        return false;
    },
    confirm : function (fnAccion, titulo, texto, isHTML){
    	swal({
	        title: (titulo  || "¿Está seguro de completar esta acción?"),
	        html : isHTML || false,
	        text : texto || "",
	        type: "warning",
	        showCancelButton: true,
	        confirmButtonColor: "#4CAF50 ",
	        confirmButtonText: "Sí",
	        cancelButtonText: "No",
	        closeOnConfirm: true
	    }, fnAccion);
    },
	Templater : function(scriptDOM){
	    var objTpl8 = {};
	    $.each(scriptDOM, function(i,o){
	        var id = o.id, idName = id.substr(4);
	        if (id.length > 0){
	            objTpl8[idName] = Handlebars.compile(o.innerHTML);
	        }
	    });
	    return objTpl8;
	},
	XHR : function(objXHR, fn){
	   new Ajxur.Api(objXHR, function(xhr){
	        if (xhr.estado = 200){
	            fn(xhr.datos);
	        }
	   });
	},
	getHTML : function ( url, callback ) {

		// Feature detection
		if ( !window.XMLHttpRequest ) return;

		// Create new request
		var xhr = new XMLHttpRequest();

		// Setup callback
		xhr.onload = function() {
			if ( callback && typeof( callback ) === 'function' ) {
				callback( this.responseXML );
			}
		}

		// Get the HTML
		xhr.open( 'GET', url );
		xhr.responseType = 'document';
		xhr.send();
	},
	llenarCombo: function(rotulo_inicial, combo, datos){
		var html = '<option value="" selected>'+rotulo_inicial+'</option>';
          	$.each(datos, function (i, item) { 
            	html += '<option value="' + item.id + '">'+ item.descripcion + '</option>';
         	});

        combo.html(html);
	},
	preDOM2DOM: function($contenedor, listaDOM){
	    /*Función que recibe un contenedor donde buscar elementos DOM, una lista con sus respectivos nombres de id y los objetos en que se convertirán, la
	        lista debe estar en el orden adecuado para que se asigne automáticamente. 
	      Devuelve el DOM.*/
	    var DOM = {}, preDOM, cadenaFind = "", numeroDOMs = listaDOM.length,
	        tmpEntries = [], tmpObjectName = [];

	    for (var i = numeroDOMs - 1; i >= 0; i--) {
	        tmpEntries = Object.entries(listaDOM[i]);
	        cadenaFind += (tmpEntries[0][1]+",");
	        tmpObjectName[i] = tmpEntries[0][0];
	    };

	    cadenaFind = cadenaFind.substr(0,cadenaFind.length-1);

	    preDOM = $contenedor.find(cadenaFind);

	    for (var i = numeroDOMs - 1; i >= 0; i--) {
	       DOM[tmpObjectName[i]] = preDOM.eq(i);
	    };

	    return DOM;
	},
  downloadPDFUsingPost : ({url, variableName, JSONData}) => {
    const $form = $(`<form target="_blank" method="post" action="${url}">
                        <input type="hidden" name="${variableName}" value=\'${JSONData}\'/>
                    </form>`);

    $("body").append($form);

    setTimeout(()=>{
      $form.submit();
      $form.remove();
    }, 330);
    //$form.remove();  
  },
  STR_CARGANDO: `<i class="fa fa-spin fa-spinner"></i> <span> CARGANDO...</span>`,
  completarCeros: ( numero, cantidadCeros) => {
    return numero.lpad("0", cantidadCeros);
  }
};

var ArrayUtils = {
            conseguirPID :  function(array, propiedadNombre, valorPropiedad) {
              //var prop = "id";
              for (var i = 0, len = array.length; i < len; i++) {        
                  if (array[i][propiedadNombre] == valorPropiedad){
                      return {i: i, o: array[i]};
                  }
              }            
              return {i: -1, o: null};
            },
            conseguir :  function(array, propiedadNombre, valorPropiedad) {
              //var prop = "id";
              for (var i = 0, len = array.length; i < len; i++) {        
                  if (array[i][propiedadNombre] == valorPropiedad){
                      return array[i];
                  }
              }            
              return -1;
            },
            conseguirTodos :  function(array, propiedadNombre, valorPropiedad) {
            //var prop = "id";
            var arrayRet = [];
            for (var i = 0, len = array.length; i < len; i++) {   
              var item = array[i];
                if (item[propiedadNombre] == valorPropiedad){
                   arrayRet.push(item);
                }
            }            
            return arrayRet;
            },              
            remover :  function(array, obj, propiedadNombre) {
            //var prop = "id";
            var t_array = $.grep( array, function( n ) {
                        return n[propiedadNombre] !== obj[propiedadNombre];                 
                        //return n > 0;
                  }); 
            return t_array;
            },         
            eliminar :  function(array, propiedadNombre, valorPropiedad) {
              var arNuevo = [];       
                  for (var i = 0, len = array.length; i < len; i++) {   
                    var item = array[i];
                    if (item[propiedadNombre] != valorPropiedad){
                      arNuevo.push(item);
                    }
                  };

              return arNuevo;
            },     
            eliminarObj :  function(array, objParams) {
            //var prop = "id";
            var propiedadNombre = Object.keys(objParams)[0],
                    valor = objParams[propiedadNombre];            
                var t_array = $.grep( array, function( n ) {
                        return n[propiedadNombre] !== valor;                 
                        //return n > 0;
                  }); 
            return t_array;
            },
            diferencia: function(array1,array2,propiedadNombre){
               var self = this;
               for (var i = 0, lenI = array2.length; i < lenI; i++) {     
                   for (var j = 0, lenJ = array1.length; j < lenJ; j++) {                        
                        if (array2[i][propiedadNombre] === array1[j][propiedadNombre]){
                           array1 = self.remover(array1,array1[j],propiedadNombre);
                           break;     
                        }
                    }
                }     
                return array1;
            },
            union : function(array1,array2,propiedadNombre){
               var ret = array1, bol;
               for (var i = 0, lenI = array2.length; i < lenI; i++) {     
                   bol = true;
                   for (var j = 0, lenJ = array1.length; j < lenJ; j++) {        
                        if (array2[i][propiedadNombre] === array1[j][propiedadNombre]){
                            //tiene el mismo ID.
                           bol = false;                           
                           break;     
                        }
                    }
                   if (bol){
                       ret.push(array2[i]);
                   }                                         
                }  
                return ret; 
            },
            interseccion : function(array1,array2,propiedadNombre){
               var self = this, ret  = [], bol;
               for (var i = 0, lenI = array1.length; i < lenI; i++) {     
                   bol = false;
                   for (var j = 0, lenJ = array2.length; j < lenJ; j++) {        
                        if (array1[i][propiedadNombre] === array2[j][propiedadNombre]){
                            //tiene el mismo ID.
                           array2 = self.remover(array2,array2[j],propiedadNombre);
                           bol = true;                           
                           break;     
                        }
                    }
                   if (bol){
                       ret.push(array1[i]);
                   }                                         
                }  
                return ret; 
            },
            exclusion: function(array1, array2, propiedadNombre){ //obj => {prop_name}
                //Para este "for" usaremos "grep", grep te devuelve un array con objetos que no 
                //cumplen una regla booleana.        
               var self = this;
               return $.grep(array1, function(i)
                {         
                    var o = self.objEnArray(i,array2,propiedadNombre);
                    return !o;
                });
            },
            objEnArray : function (obj,array,propiedadNombre){
                 for (var i = 0, len = array.length; i < len; i++) {    
                        if (array[i][propiedadNombre] === obj[propiedadNombre]){
                           return true;
                        }
                 }
                 return false;
            },
            buscar :  function(array, parametrosBusqueda) {
              for (var i = 0, len = array.length; i < len; i++) {
                var item = array[i];
                var boolCumple = true;
                for (var j = parametrosBusqueda.length - 1; j >= 0; j--) {
                     var  itemParametro = parametrosBusqueda[j],
                      mayusculas = itemParametro.mayusculas!= null  ? itemParametro.mayusculas : false,
                      exactitud = itemParametro.exactitud != null ? itemParametro.exactitud : true,
                      tmpValorObtenido = item[itemParametro.propiedad],
                      tmpValorBuscado = itemParametro.valor;

                  if (tmpValorBuscado == "" || tmpValorBuscado == null){
                    boolCumple = boolCumple && true;
                    continue;
                  }

                  if (mayusculas == true){
                    tmpValorObtenido = tmpValorObtenido.toUpperCase();
                    tmpValorBuscado = tmpValorBuscado.toUpperCase();
                  }

                  if (exactitud == true){
                      boolCumple = boolCumple && (tmpValorObtenido == tmpValorBuscado);
                  } else {
                     boolCumple = boolCumple && (tmpValorObtenido.includes(tmpValorBuscado));
                  }
                };

                if (boolCumple){
                  return item;
                }
              }           
              return -1;
            },
            buscarTodos :  function(array, parametrosBusqueda, numResultados){
              //parametrosBusqueda => {propiedad, valor, tipo (exactitud = true, mayusculas = false)} [arreglo]
              var arrayRet = [],
                  unSoloRegistro;
              numResultados = numResultados ? numResultados : 0;
              unSoloRegistro = numResultados == 1,
              todosRegistros = numResultados == 0;

              if (!array){
                return unSoloRegistro ? null : [];
              }

              for (var i = 0, len = array.length; i < len; i++) {
                var item = array[i];
                var boolCumple = true;
                for (var j = parametrosBusqueda.length - 1; j >= 0; j--) {
                      var itemParametro = parametrosBusqueda[j];
                      var mayusculas = itemParametro.mayusculas != null ? itemParametro.mayusculas : false,
                      exactitud = itemParametro.exactitud != null ? itemParametro.exactitud : true,
                      tmpValorObtenido = item[itemParametro.propiedad],
                      tmpValorBuscado = itemParametro.valor;

                  if (tmpValorBuscado == "" || tmpValorBuscado == null){
                    boolCumple = boolCumple && true;
                    continue;
                  }

                  if (mayusculas == true){
                    tmpValorObtenido = tmpValorObtenido.toUpperCase();
                    tmpValorBuscado = tmpValorBuscado.toUpperCase();
                  }

                  if (exactitud == true){
                      boolCumple = boolCumple && (tmpValorObtenido == tmpValorBuscado);
                  } else {
                      boolCumple = boolCumple && (tmpValorObtenido.includes(tmpValorBuscado));
                  }
                };

                if (boolCumple){
                  if (unSoloRegistro){
                    return {i: i, item: item};
                  }

                  if (todosRegistros){
                    arrayRet.push(item);
                  }               

                  if (arrayRet.length >= numResultados && numResultados > 0){
                    break;
                  }
                }
              }

              if (unSoloRegistro){
                return null;
              }            
              return arrayRet;
            }       
};

if (window["Handlebars"]){
  Handlebars.registerHelper("indexer", function(index) {
    return index + 1;
  });

  Handlebars.registerHelper("ceros", function(n) { 
    return Util.completarCeros(n,6);
  });

  Handlebars.registerHelper('if_', function (v1, operator, v2, options) {
    switch (operator) {
      case '==':
      return (v1 == v2) ? options.fn(this) : options.inverse(this);
      case '===':
      return (v1 === v2) ? options.fn(this) : options.inverse(this);
      case '!=':
      return (v1 != v2) ? options.fn(this) : options.inverse(this);
      case '!==':
      return (v1 !== v2) ? options.fn(this) : options.inverse(this);
      case '<':
      return (v1 < v2) ? options.fn(this) : options.inverse(this);
      case '<=':
      return (v1 <= v2) ? options.fn(this) : options.inverse(this);
      case '>':
      return (v1 > v2) ? options.fn(this) : options.inverse(this);
      case '>=':
      return (v1 >= v2) ? options.fn(this) : options.inverse(this);
      case '&&':
      return (v1 && v2) ? options.fn(this) : options.inverse(this);
      case '||':
      return (v1 || v2) ? options.fn(this) : options.inverse(this);
      default:
      return options.inverse(this);
    }
  });
}

const Storager = function() {
  const KEY_STORAGE = "andreitababykids_store";
  let objStorager;

  this.init = () => {
    const storager = window.localStorage.getItem(KEY_STORAGE);
    try {
      objStorager = Boolean(storager) 
        ? JSON.parse(storager)
        : {};  
    } catch (error) {
      objStorager = {};
    }
  };

  this.setValue = (key, value) => {
    if (value === null){
      delete objStorager[key];
      window.localStorage.setItem(KEY_STORAGE, JSON.stringify(objStorager));
      return;
    }

    objStorager = {...objStorager, [key] : value};
    window.localStorage.setItem(KEY_STORAGE, JSON.stringify(objStorager));
  };

  this.getValue = (key) => {
    const res = objStorager[key];
    return res === undefined ? null : objStorager[key];
  };

  this.reset = () => {
    window.localStorage.removeItem(KEY_STORAGE);
    objStorager = null;
  };

  return this.init();
}