var DOM = {};

$(document).ready(function () {
    new AccesoAuxiliar(()=>{
        setDOM();
        setEventos();
        cargarRol();
    })
});

function setDOM() {
    DOM.cboRol = $("#cborol");
    DOM.btnIzquierda = $("#btn-izquierda");
    DOM.btnDerecha = $("#btn-derecha");

    DOM.lstPermisosInactivos = $("#listar-permisos-inactivos");
    DOM.lstPermisosActivos = $("#listar-permisos-activos");

    DOM.blkAlert = $("#blk-alert");
}

function setEventos() {

    DOM.cboRol.change(function(e){
        if (this.value == ""){
            $('#listar-permisos-inactivos').empty();
            $('#listar-permisos-activos').empty();
            resetBotones();
            return;    
        }
        
        listarPermisos();
        //listarActivos();
        //listarInactivos();
        resetBotones();
    });

    DOM.lstPermisosInactivos.on('click', '.list-group-item', function(e) {
        $('.list-group-item').removeClass("active");        
        $(this).addClass("active");
        DOM.btnIzquierda.attr("disabled",false);
        DOM.btnDerecha.attr("disabled",true);
    });

    DOM.lstPermisosActivos.on('click', '.list-group-item', function(e) {
        $('.list-group-item').removeClass("active");        
        $(this).addClass("active");
        DOM.btnIzquierda.attr("disabled",true);
        DOM.btnDerecha.attr("disabled",false);
    });
}


const renderPermisos = ({$lista, data}) => {
    let html = '<div class="list-group">'; 
    data.forEach(item=>{
        html += `<a href="#" class="list-group-item" data-id="${item.id}"><small><b>${item.superior}</b></small><p>${item.titulo_interfaz}</p></a>`;
    });
    html += '</div>';                
    $lista.html(html);
};

const listarPermisos = async function(){
    try {
        const idRol = DOM.cboRol.val();
        const response = await apiAxios.get(`permisos-rol/${idRol}`);
        const { data } = response;

        console.log({data})

        renderPermisos({
            $lista : DOM.lstPermisosActivos,
            data: data.permisosActivos
        });

        renderPermisos({
            $lista : DOM.lstPermisosInactivos,
            data: data.permisosInactivos
        });

    } catch (error) {
        swal("Error", error?.response?.data?.message || JSON.stringify(error?.response?.data), "error");
        console.error(error);
    }
};

function agregar(){
    if ( DOM.cboRol.val() === null ) {
        Util.alert(DOM.blkAlert,{tipo:"e",mensaje:"Debe seleccionar un rol para agregar el permiso"});
        return;
    }

    if ( validar($("#listar-permisos-inactivos .list-group")) ) {
        Util.alert(DOM.blkAlert,{tipo:"e",mensaje:"Debe seleccionar un permiso para para agregar al rol"});
        return;
    }

    $("#listar-permisos-inactivos .list-group").find(".list-group-item").each(async function(){
        const encontro = this.classList.contains('active');
        if ( encontro ) {
            const idPermiso = this.dataset.id;

            try {
                const idRol = DOM.cboRol.val();
                const sentData = {
                    id_rol : idRol,
                    id_permiso : idPermiso
                };
                await apiAxios.post(`permisos-rol/agregar`, sentData);
                listarPermisos();
                resetBotones();
            } catch (error) {
                swal("Error", error?.response?.data?.message || JSON.stringify(error?.response?.data), "error");
                console.error(error);
            }
        }
    });
}

function quitar(){
    if ( DOM.cboRol.val() === null ) {
        Util.alert(DOM.blkAlert,{tipo:"e",mensaje:"Debe seleccionar un rol para quitar el permiso"});
        return;
    }

    if ( validar($("#listar-permisos-activos .list-group")) ) {
        Util.alert(DOM.blkAlert,{tipo:"e",mensaje:"Debe seleccionar un permiso para para quitar al rol"});
        return;
    }

    $("#listar-permisos-activos .list-group").find(".list-group-item").each(async function(){
        const encontro = this.classList.contains('active');
        if ( encontro ) {
            const idPermiso = $(this)[0].children[1].dataset.id;

            try {
                const idRol = DOM.cboRol.val();
                const sentData = {
                    id_rol : idRol,
                    id_permiso : idPermiso
                };
                await apiAxios.post(`permisos-rol/quitar`, sentData);
                listarPermisos();
                resetBotones();
            } catch (error) {
                swal("Error", error?.response?.data?.message || JSON.stringify(error?.response?.data), "error");
                console.error(error);
            }
        }
    });


}

function validar(parametro){
    var c = 0; 
    parametro.find(".list-group-item").each(function(){
        var encontro = this.classList.contains('active');
        if ( encontro ) {
            c++;
        }
    });
    if ( c >= 1 ) {
        return false;
    }
    return true; 
}

async function cargarRol(){
    try {
        const response = await apiAxios.get('roles');
        const { data } = response;

        let html = '<option value="" selected>Seleccionar rol</option>';
        for (let i = 0, len = data.length; i < len; i++) {
            const item = data[i];
            html += '<option value="' + item.id + '">'+ item.nombre + '</option>';
        };
        $("#cborol").html(html);

    } catch (error) {
        swal("Error", error?.response?.data?.message || JSON.stringify(error?.response?.data), "error");
        console.error(error);
    }
}

function resetBotones(){
    DOM.btnIzquierda.attr("disabled",true);
    DOM.btnDerecha.attr("disabled",true);
};