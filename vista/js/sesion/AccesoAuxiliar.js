const AccesoAuxiliar = function(fnInit){
    this.URL = null;
    this.ITEM_PADRE = "";

    this.setDataUsuario = function(usuario){
        this._usuario = usuario;
    };

    this.dibujarFila = function (item) {
        let $html = "";
        if (!item?.hijos || item?.hijos.length <= 0){
            const active = this.URL === item.url ? 'class="active"' : '';
            $html += `  <li ${active}>
                            <a href="./${item.url}">
                                <i class="menu-icon fa fa-caret-right"></i> ${item.titulo_interfaz}
                            </a>
                        </li>`;                               
        } else {
            if (item.id === 0 ){
                $html += item.hijos.map(value => {
                            return this.dibujarFila(value);
                        }).join(" ");
            } else {
                let tengoAlHijoActivoDentro = false;
                if (this.ITEM_PADRE === ""){
                    tengoAlHijoActivoDentro = item.hijos.find(e=>{
                        return e.url === this.URL;
                    }) != undefined;
                    
                    if (tengoAlHijoActivoDentro){
                        this.ITEM_PADRE = item.titulo_interfaz;
                    }
                }

                const activePadre = tengoAlHijoActivoDentro ? 'class="open"' : '';
                $html += `  <li ${activePadre}>
                                <a href="javascript:void();" class="dropdown-toggle">
                                    <i class="menu-icon fa fa-${item.icono_interfaz}"></i>
                                    <span class="menu-text">${item.titulo_interfaz}</span>
                                    <b class="arrow fa fa-angle-down"></b>
                                </a>
                                <b class="arrow"></b> 
                                <ul class="submenu">
                                    ${item.hijos.map(value => {
                                        return this.dibujarFila(value);
                                    }).join(" ")}
                                </ul>
                            </li>`;
            }
        }

        return $html;
    };

    this.dibujarMenu = function(dataMenu){
        document.getElementById("lst-menu").innerHTML = this.dibujarFila({id: 0, hijos: dataMenu});
    };

    this.dibujarNavBar = function(usuario){
        //document.getElementById("lbl-userinfo").innerHTML = usuario?.nombresApellidos;
        const $html = Boolean(usuario) 
            ? ` <li class="app-color dropdown-modal">
                    <a data-toggle="dropdown" href="#" class="dropdown-toggle">
                        <img class="nav-user-photo"  src="../imagenes/1.png" alt="Img Usuario" />
                        <span class="user-info">
                            <small>Bienvenido,</small>
                            ${usuario?.nombresApellidos}
                        </span>
                        <i class="ace-icon fa fa-caret-down"></i>
                    </a>
                    <ul class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
                        <!-- 
                            <li>
                                <a href="#">
                                    <i class="ace-icon fa fa-cog"></i>
                                    Cambiar Clave
                                </a>
                            </li>
                            <li class="divider"></li>
                        -->
                        <li>
                            <a onclick="Util.cerrarSesion();">
                                <i class="ace-icon fa fa-power-off"></i>
                                Cerrar Sesión
                            </a>
                        </li>
                    </ul>
                </li>`
            : ` <li class="app-color dropdown-modal">
                    <a href="index.php">
                        <i class="ace-icon fa fa-play"></i>
                        ACCEDER
                    </a>
                </li>`;
        document.getElementById("blk-ace-nav").innerHTML = $html;
    }

    this.init = async function(){
        try {
            const { data } = await apiAxios.get(`permisos-usuario`);

            this.URL = window.location.pathname.split("/").pop().replace("#","");
            const USUARIO  = JSON.parse(localStorage.getItem(SESSION_NAME))?.user;
            this.dibujarMenu(data);
            this.dibujarNavBar(USUARIO);

            window.USUARIO = USUARIO;

            if (fnInit){
                fnInit();
            }
            
        } catch (error) {
            console.error(error);
            const { response } = error;
            if (response?.status === 401){
                window.location.href = "./error403.php";
                return;
            }

            if (Boolean(response?.data?.message)){
                swal("Error", response.data.message, "error");
                return;
            }
        }
    };

    return this.init();
};