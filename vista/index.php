<?php
    include '../datos/local_config_web.php';
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <meta charset="utf-8" />
        <title>Login</title>

        <meta name="description" content="User login page" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />

        <?php 
            if (MODO_PRODUCCION == "1"){
                echo '  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/css/bootstrap.min.css" />
                        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css" />
                        <link rel="stylesheet" href="../assets/css/fonts.googleapis.com.css" />
                        <link rel="stylesheet" href="../assets/css/ace.min.css" />
                        <link rel="stylesheet" href="../assets/css/ace-rtl.min.css" />
                        <link rel="stylesheet" href="../assets/css/colors.min.css" />';

            } else {

                echo '  <link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
                        <link rel="stylesheet" href="../assets/font-awesome/4.5.0/css/font-awesome.min.css" />
                        <link rel="stylesheet" href="../assets/css/fonts.googleapis.com.css" />
                        <link rel="stylesheet" href="../assets/css/ace.min.css" />
                        <link rel="stylesheet" href="../assets/css/ace-rtl.min.css" />
                        <link rel="stylesheet" href="../assets/css/colors.min.css" />';

            }
         ?>
        <!-- bootstrap & fontawesome -->

        <!--[if lte IE 9]>
          <link rel="stylesheet" href="../assets/css/ace-ie.min.css" />
        <![endif]-->

        <!-- HTML5shiv and Respond.js for IE8 to support HTML5 elements and media queries -->

        <!--[if lte IE 8]>
        <script src="../assets/js/html5shiv.min.js"></script>
        <script src="../assets/js/respond.min.js"></script>
        <![endif]-->
        <link rel="stylesheet" href="css/estilos.css" />
    </head>


    <body class="login-layout light-login">
        <div class="loader"> <div>   <i class="ace-icon fa fa-spinner fa-spin bigger-225"></i> </div> </div>
        <div class="main-container">
            <div class="main-content">
                <div class="row">
                    <div class="col-sm-10 col-sm-offset-1">
                        <div class="login-container">
                            <div class="center">
                                <h1>
                                    <?php echo SW_NOMBRE; ?>
                                </h1>
                                <h4 class="app-color" id="id-company-text">&copy; <?php echo NOMBRE_EMPRESA; ?></h4>

                                <img src="../imagenes/logo.jpeg" style="width:100%">
                            </div>

                            <div class="space-6"></div>

                            <div class="position-relative">
                                <div id="login-box" class="login-box visible widget-box no-border">
                                    <div class="widget-body">
                                        <div class="widget-main">
                                            <h4 class="header app-color lighter bigger">
                                                <i class="ace-icon fa fa-coffee "></i>
                                                Ingreses sus credenciales
                                            </h4>

                                            <div class="space-6"></div>

                                            <form id="frminiciar">
                                                <fieldset>
                                                    <label class="block clearfix">
                                                        <span class="block input-icon input-icon-right">
                                                            <input type="text" class="form-control"  name="txtdni" id="txtdni"  value="" placeholder="Usuario" />
                                                            <i class="ace-icon fa fa-user"></i>
                                                        </span>
                                                    </label>

                                                    <label class="block clearfix">
                                                        <span class="block input-icon input-icon-right">
                                                            <input type="password" class="form-control" name="txtclave" id="txtclave" placeholder="Clave" />
                                                            <i class="ace-icon fa fa-lock"></i>
                                                        </span>
                                                    </label>

                                                    <div class="space"></div>

                                                    <div class="clearfix">
                                                        <!-- <label style="display:none;">-->
                                                        <button type="submit" class="width-35 pull-right btn btn-sm btn-primary">
                                                            <i class="ace-icon fa fa-key"></i>
                                                            <span class="bigger-110">Acceder</span>
                                                        </button>
                                                    </div>

                                                    <div class="space-4"></div>
                                                </fieldset>
                                                <div id="blkalert"></div>
                                            </form>
                                        </div><!-- /.widget-main -->
                                    </div><!-- /.widget-body -->
                                </div><!-- /.login-box -->

                            </div><!-- /.position-relative -->
                            <!--
                            <div class="navbar-fixed-top align-right">
                                <br />
                                &nbsp;
                                <a id="btn-login-dark" href="#">Dark</a>
                                &nbsp;
                                <span class="blue">/</span>
                                &nbsp;
                                <a id="btn-login-blur" href="#">Blur</a>
                                &nbsp;
                                <span class="blue">/</span>
                                &nbsp;
                                <a id="btn-login-light" href="#">Light</a>
                                &nbsp; &nbsp; &nbsp;
                            </div>
                            -->
                        </div>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.main-content -->
        </div><!-- /.main-container -->

        <!-- basic scripts -->

        <!--[if !IE]> -->

         <?php 
            if (MODO_PRODUCCION == "1"){
                echo '  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
                        <script type="text/javascript">
                            if(\'ontouchstart\' in document.documentElement) document.write("<script src=\'https://cdnjs.cloudflare.com/ajax/libs/jquery-mobile/1.4.5/jquery.mobile.min.js\'>"+"<"+"/script>");
                        </script>';
            } else {
                echo '  <script src="../assets/js/jquery-2.1.4.min.js"></script>
                        <script type="text/javascript">
                            if(\'ontouchstart\' in document.documentElement) document.write("<script src=\'../assets/js/jquery.mobile.custom.min.js\'>"+"<"+"/script>");
                        </script>';

            }
         ?>

        <!-- inline scripts related to this page -->
        <script type="text/javascript">
            jQuery(function($) {
             $(document).on('click', '.toolbar a[data-target]', function(e) {
                e.preventDefault();
                var target = $(this).data('target');
                $('.widget-box.visible').removeClass('visible');//hide others
                $(target).addClass('visible');//show target
             });
            });        
        </script>

        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
        <script type="text/javascript" src="js/_env.js"></script>
        <script type="text/javascript" src="js/axios/apiLoad.js"></script>
        <script type="text/javascript" src="js/Util.js"></script>
        <script type="text/javascript" src="js/login.js"></script>
    </body>
</html>
