<?php 

 if (MODO_PRODUCCION == "1"){
                echo '  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
                		<script src="../assets/js/jquery-ui.custom.min.js"></script>
                		<script src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.1.0/handlebars.min.js"></script>
                		';

            } else {

                echo ' <script src="../assets/js/jquery-2.1.4.min.js"></script>
						<script src="../assets/js/jquery-ui.custom.min.js"></script>
						<script src="../assets/js/handlebars.min.js"></script>
						';

            }

             ?>
		<!--
		<script src="assets/js/jquery.ui.touch-punch.min.js"></script>
		<script src="assets/js/jquery.easypiechart.min.js"></script>
		<script src="assets/js/jquery.sparkline.index.min.js"></script>
		<script src="assets/js/jquery.flot.min.js"></script>
		<script src="assets/js/jquery.flot.pie.min.js"></script>
		<script src="assets/js/jquery.flot.resize.min.js"></script>
 -->		