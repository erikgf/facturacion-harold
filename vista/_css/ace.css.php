<?php 

if (MODO_PRODUCCION == "1"){
    echo '  <link rel="stylesheet" href="../assets/css/ace-full.css" class="ace-main-stylesheet" id="main-ace-style" />
            <link rel="stylesheet" href="../assets/css/colors.min.css" />';
} else {
	echo '  <link rel="stylesheet" href="../assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />
          <link rel="stylesheet" href="../assets/css/ace-skins.min.css" />
          <link rel="stylesheet" href="../assets/css/ace-rtl.min.css" />
          <link rel="stylesheet" href="../assets/css/colors.min.css" />';
}

?>

  <!-- 
  <link rel="stylesheet" href="../assets/css/ace.min.css" />
  <link rel="stylesheet" href="../assets/css/ace-responsive.min.css" />
  <link rel="stylesheet" href="../assets/css/ace-skins.min.css" />
  -->

