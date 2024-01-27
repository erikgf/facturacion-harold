<?php 

 if (MODO_PRODUCCION == "1"){
                echo '  <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
                		<script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap.min.js"></script>
                		';

            } else {

                echo ' <script src="../assets/plugins/datatables/jquery.dataTables.min.js"></script>
						    <script src="../assets/plugins/datatables/dataTables.bootstrap.js"></script>
						';

            }

             ?>


<!-- <script src="../assets/plugins/datatables/dataTables.responsive.min.js"></script>-->