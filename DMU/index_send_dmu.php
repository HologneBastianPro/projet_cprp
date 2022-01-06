<!DOCTYPE html>
<html lang="fr" dir="ltr">
	<head>
		<title>Transfert de fichier</title>
		<meta charset="iso-8859-1">
		<link rel="stylesheet" media="screen" type="text/css" title="Design" href="../file/style-explorateur.css" />
		<script language="javascript" type="text/javascript" src="../file/javascript.js"></script>
	</head>


	<body>
		<?php
			include 'DMU_Function.php';

			session_start(); //On démarre la session
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
		</font>

		<div class="wrapper row2">
	   		<div id="container" class="clear">
				<br/>
				<center>

				<?php
					function getBrowser() { 
					  $u_agent = $_SERVER['HTTP_USER_AGENT'];
					  $bname = 'Unknown';
					 
					  if(preg_match('/Trident/i',$u_agent)){
					    $bname = 'IE';
					    $ub = "MSIE";
					  }
					  else
					  {
						   $bname = '0';
							$ub = "0";
					  }

					  return array(
					    'userAgent' => $u_agent,
					    'name'      => $bname,

					  );
					} 
				?>

				<font face="arial" size="7" color="BLUE">
								<b> <i>
									DMU 40
								</i> </b>
				</font>


				<BR>
				<BR>

				<?php
					$arbo = recup_arbo(); //On récupère l'arbosescence
					$arbo_back = $arbo; //On duplique la variable

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

						    init_transfert_dmu($socket) ; //On initialise la connection

							$repertoire_courant = lire_repertoire_courant($socket) ; //On récupère le répertoire courant

							if (sizeof($arbo) == 0) //Si l'arborescence est égale à 0
							{
								echo ("repertoire courant : ".$repertoire_courant) ;
								echo("<BR>\n<br>") ;
							}

							if (sizeof($arbo) != 0)
							{
								get_rep($arbo, $socket); //On navigue jusqu'au répertoire cible
							}
						
							
							//------------------------------------------------------------------------

						 	$in = "\x00\x00\x00\x00A_LO" ; 		// Log Out
							envoyer_et_afficher($in,$socket) ; 	// T_OK

							//------------------------------------------------------------------------
						}
						
					socket_close($socket); //On ferme le socket
				?>

				<div style="border: 2px solid black; margin-left: 30%; margin-right: 30%;">
				</br>
					<form enctype="multipart/form-data" <?php echo 'action="Alt_send_dmu.php?tree='.$_GET['tree'].'"'; ?> method="POST" target="__URL__">
						File : <input name="file" id="file" type="file" required></input><br><BR>
						
						
						<?php
							$ua=getBrowser();
							

							if ($ua['name'] == 'IE')
							{
								
								echo'<p style="color:#FF0000";>Internet explorer non compatible</p>';
								echo '<BR>';
							}
							else{
								echo'<input type="submit" value="Envoyer" name="send" id="send">';
							}
						?>
					</form>
				</div>
				

				
				
				</div>
				<button style="float: right;" onclick="window.location.href = 'dmu.php';">Retour</button>
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