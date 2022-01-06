<!DOCTYPE html>
<html lang="fr" dir="ltr">
	<head>
		<title>Transfert de fichier</title>
		<meta charset="iso-8859-1">
		<link rel="stylesheet" media="screen" type="text/css" title="Design" href="../file/style-explorateur.css" />
		
		<script language="javascript" type="text/javascript" src="../file/javascript.js"></script>
		
		<script src="../../highcharts/code/highcharts.js"></script>
		<script src="../../highcharts/code/highcharts-more.js"></script>
		<script src="../../highcharts/code/modules/solid-gauge.js"></script>
		<script src="../../highcharts/code/modules/exporting.js"></script>
		<script src="../../highcharts/code/modules/export-data.js"></script>
		<script src="../../highcharts/code/modules/accessibility.js"></script>

		
		<style type="text/css">
			.highcharts-figure .chart-container {
			width: 300px;
			height: 200px;
			float: left;
			}

			.highcharts-figure, .highcharts-data-table table {
			width: 600px;
			margin: 0 auto;
			}

			.highcharts-data-table table {
			font-family: Verdana, sans-serif;
			border-collapse: collapse;
			border: 1px solid #EBEBEB;
			margin: 10px auto;
			text-align: center;
			width: 100%;
			max-width: 500px;
			}
			.highcharts-data-table caption {
			padding: 1em 0;
			font-size: 1.2em;
			color: #555;
			}
			.highcharts-data-table th {
			font-weight: 600;
			padding: 0.5em;
			}
			.highcharts-data-table td, .highcharts-data-table th, .highcharts-data-table caption {
			padding: 0.5em;
			}
			.highcharts-data-table thead tr, .highcharts-data-table tr:nth-child(even) {
			background: #f8f8f8;
			}
			.highcharts-data-table tr:hover {
			background: #f1f7ff;
			}

			@media (max-width: 600px) {
				.highcharts-figure, .highcharts-data-table table {
					width: 100%;
				}
				.highcharts-figure .chart-container {
					width: 300px;
					float: none;
					margin: 0 auto;
				}
			}

		</style>

		<style>
			.Envoi {
			  transition-duration: 0.4s;
			  background-color: #FFFFFF;
			  border: 2px solid #4CAF50; /* Green */
			  font-size: 18px;
			  border-radius: 8px;
			}

			.Envoi:hover {
			  background-color: #4CAF50; /* Green */
			  color: white;
			}

			
			.Suppr {
			  transition-duration: 0.4s;
			  background-color: #FFFFFF;
			  border: 2px solid #BB0B0B;
			  font-size: 12px;
			  border-radius: 8px;
			  float: right;
			  margin-right : 10%;
			
			}

			.Suppr:hover {
			  background-color: #BB0B0B; 
			  color: white;
			}
			
			
			.outer-div {
		     padding: 30px;
			}
			.inner-div {
			     margin: 0 auto;
			     width: 100px; 
			}
		</style>
	</head>



		<body>

				<?php
					include 'DMU_Function.php';

					//session_start();

					$arbo = recup_arbo(); //On récupère l'arborescence
					$arbo_back = $arbo; //Duplication de l'arborescence pour la fonction retour en arrière
				?>

				<?php

					$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
					if ($socket === false) 
						{
					    	//echo "socket_create() à échoué : raison :  " . socket_strerror(socket_last_error()) . "\n";
						}	 
					else 
					 	{
					    	//echo "OK pour la création du socket.<BR>\n";
						}

					$result = socket_connect($socket, $address, $service_port);
						
					if ($socket === false) 
					{
				    	//echo "socket_connect() a échoué : raison : ($result) " . socket_strerror(socket_last_error($socket)) . "\n";
					} 
					else 
						{
						    //echo "OK pour la connexion socket <BR>\n";

						    init_transfert_dmu($socket) ;

							$repertoire_courant = lire_repertoire_courant($socket) ;

							if(isset($_GET['dl']))
							{
								naviguer_repertoire_uni_dl($arbo, $socket); //On navigue jusqu'au répertoire cible
								telecharger_fichier($_GET['dl'], $socket); //On télécharge le fichier cible
							}else{

								echo '
									<div class="wrapper row1">
									  <header id="header" class="clear">
									    <div id="hgroup">
									      <h1><a href="../index.php">Interface de transfert de fichier</a></h1>
									    </div>
										<img title="Lycée JB de BAUDRE" src="../img/lycee.jpg" style="width: 200px; float:right" />
									    <nav>
											<div style="width:75%; float:left; margin:5px;">
												<ul>
													<li><a href="../doosan.php">DOOSAN<span><img title="DOOSAN lynx 220ly" src="../img/doosan.jpg" style="width: 500px;" /></span></a></li>
													<li><a href="../haas.php">HAAS<span><img title="HAAS vf1" src="../img/haas.png" style="width: 500px;" /></span></a></li>
													<li><a href="../transmab.php">TRANSMAB<span><img title="SOMAB TRANSMAB 200" src="../img/transmab.jpg" style="width: 500px;" /></span></a></li>

													<li><a href="../DANOBAT/danobat.php">DANOBAT<span><img title="DANOBAT"  title="DANOBAT" src="../img/danobat.jpg" style="width: 500px;" /></span></a></li> 
													<li><a href="../DMU/dmu.php">DMU<span><img title="DMU"  title="DMU" src="../img/dmu.jpg" style="width: 500px;" /></span></a></li>

													<li><a href="../User_System/ressources.php">Ressources<span><img title="Ressources"  title="Ressources" src="../img/dmu.jpg" style="width: 500px;" /></span></a></li>
												</ul>
											</div>
											<div id="defaultPage">
												<a href="../User_System/login.php">Connexion</a><br>
											</div>
											<div id="adminPage">
												<a href="../User_System/logout.php">Deconnexion</a><a style="margin-left: 30px;" href="../User_System/test_json.php">Page administrateur</a>
											</div>
									    </nav>
									  </header>
									</div>
									

									<div class="wrapper row2">
										<div id="container" class="clear">
											<center>
								';


								echo '<tr><BR>
										<td Align="center">
										<font face="arial" size="7" >
										<b> <i>DMU 40</i> </b>
										</font><BR>	<BR>
										<div style="align-content: center;  display: table;">
											<div style="width:50%; float:left; margin:5px;">
												<button class="Envoi" onclick="fun_send();">Envoi de fichier</button>
											</div>
											<div style="width:40%; float:left; margin:5px;">
												<button class="Suppr" id="Suppr" onclick="Hide();">Activer la suppression</button>
											</div>
										</div>
										<BR><BR><BR></td></tr></table>';

								if (sizeof($arbo) == 0) //Si l'arborescence se situe au fichier racine
								{
									echo ("repertoire courant : ".$repertoire_courant) ;
									echo("<BR>\n") ;
								}

								$tab = lire_fichiers_dossiers($socket); //On affiche le contenu du dossier

								if (sizeof($arbo) != 0)
								{
									naviguer_repertoire_uni($arbo, $socket);
								}else{
									affichage_repertoire($tab);
								}

								array_map('unlink', glob("C:\wamp64\www\cprp\DMU\*.H")); //Suppression des .H sur le serveur
							}
							
							//------------------------------------------------------------------------

						 	$in = "\x00\x00\x00\x00A_LO" ; 		// Log Out
							envoyer_et_afficher($in,$socket) ; 	// T_OK

							//------------------------------------------------------------------------
						}
						
					socket_close($socket); //On ferme le socket
				?>
				<BR>
				<BR>
			</center>
		</div>
	</div>

	<script>
		var suppr = 0; //Variable nécéssaire à la fonction "supprimer"

		function nav_rep(Noms) {
		  fname = Noms;
		  window.location.replace("./dmu.php?name=" + fname + "&tree=" + <?php echo json_encode($arbo); ?>  + "," + fname); //Création de la requête web
		};

		function back() {
			<?php
				if(sizeof($arbo_back) != 0){ //Si l'arborescence est non null
					$Prev = $arbo_back[sizeof($arbo_back) - 2];
					array_pop($arbo_back); //On retire le dossier où l'on se situe
					echo "taille = ".sizeof($arbo_back).";\n";
					echo "fname = '".$Prev."';\n";
				}
			?>
			if(taille > 1){
			  window.location.replace("./dmu.php?name=" + fname + "&tree=" + <?php echo json_encode($arbo_back); ?>); //Création de la requête web
			}else{
			  window.location.replace("./dmu.php"); //Création de la requête web
			}
		};

		function fun_send(){
			window.location.replace("./index_send_dmu.php?" + "&tree=" + <?php echo json_encode($arbo); ?>); //Création de la requête web
		}

		function Hide()
		{
			var els = document.getElementsByClassName("delete"); //On récupère l'élément avec la class "delete"
			if(suppr == 0) //Si la suppression est activée
			{
				document.getElementById("Suppr").innerText = "Désactiver la suppresion";
				for (var i=0; i<els.length; i++) {
           	 		els[i].style.display = "block";
        		}
				suppr = 1;
			}else{
				document.getElementById("Suppr").innerText = "Activer la suppression";
				for (var i=0; i<els.length; i++) {
           			els[i].style.display = "none";
        		}
				suppr = 0;
			}
		}

		function Delete(Noms, DName){
			dname = DName; //variable qui va contenir l'arborescence 
			fname = Noms; //Variable qui va contenir le nom du fichier à supprimer
			if(dname = 0) //Si l'arborescence se situe à la racine
			{
				window.location.replace("./dmu_delete.php?name=" + fname + "&tree=" + <?php echo json_encode($arbo); ?> + "&name=" + fname); //Création de la requête web
			}else{
				window.location.replace("./dmu_delete.php?name=" + fname + "&tree=" + <?php echo json_encode($arbo); ?> + "&name=" + fname); //Création de la requête web
			}
		}

		function Download(Noms, DName) {
		  dname = DName; //variable qui va contenir l'arborescence 
		  fname = Noms; //Variable qui contenir le nom du fichier à télécharger
		  if(dname = 0) //Si l'arborescence se situe à la racine
		  {
		  	window.location.replace("./dmu.php?name=" + fname + "&tree=" + <?php echo json_encode($arbo); ?> + "&dl=" + fname); //Création de la requête web
		  }else{
		  	window.location.replace("./dmu.php?name=" + fname + "&tree=" + <?php echo json_encode($arbo); ?> + "&dl=" + fname); //Création de la requête web
		  }
		};

		if(session === 0)
		{
			document.getElementById("adminPage").style.display = "none";
			document.getElementById("defaultPage").style.display = "block";
		}else{
			document.getElementById("adminPage").style.display = "block";
			document.getElementById("defaultPage").style.display = "none";
		}
	</script>
</body>
</html>