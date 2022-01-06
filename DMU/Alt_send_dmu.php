<!DOCTYPE html>
<html lang="fr" dir="ltr">
	<head>
		<title>Transfert de fichier</title>
		<meta charset="iso-8859-1">
		<link rel="stylesheet" media="screen" type="text/css" title="Design" href="../file/style-explorateur.css" />
		<script language="javascript" type="text/javascript" src="../file/javascript.js"></script>
	</head>

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
	</style>	

	<body>
		<?php
			include 'DMU_Function.php';

			//session_start();
			
		?>
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

		<!-- Gestionnaire de fichier -->
		<div class="wrapper row2">
		   <div id="container" class="clear">
			<br/>

			<center>
				<font face="arial" size="7" color="BLUE">
					<b> <i>
						DANOBAT TNC-10 
					</i> </b>
				</font>
				
					<div style="margin-left: 30%; margin-right: 30%;">
						
					</div>

					<?php
							$arbo = recup_arbo(); //On récupère l'arbosescence
							$arbo_back = $arbo; //On duplique la variable

							$file = get_file_data(); //On récupère les données du fichier

							$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
							if ($socket === false) 
								{
							    	//echo "socket_create() à échoué : raison :  " . socket_strerror(socket_last_error()) . "\n";
								}	 
							else 
							 	{
							    	//echo "OK pour la création du socket.<BR>\n";
								}

							//echo "Essai de connexion à '$address' sur le port '$service_port'... <BR>\n";
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

									//var_dump($_GET['dl']);
									naviguer_repertoire_uni($arbo, $socket);
									Send($socket, $file);
									//var_dump($data);
									//Create_file($data, $_GET['dl']);
									
									
									//------------------------------------------------------------------------

								 	$in = "\x00\x00\x00\x00A_LO" ; 		// Log Out
									envoyer_et_afficher($in,$socket) ; 	// T_OK

									//------------------------------------------------------------------------
								}
								
							socket_close($socket); //On ferme la session
					?>
					
					<button class="Envoi" onclick="window.location.href = 'dmu.php';">Retour</button>
				</center>
				<br/>
			</div> 
		</div>

		<script>
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