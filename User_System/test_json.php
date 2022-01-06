<!DOCTYPE html>
<html lang="fr" dir="ltr">
	<head>
		<title>Transfert de fichier</title>
		<meta charset="iso-8859-1">
		<link rel="stylesheet" media="screen" type="text/css" title="Design" href="../file/style-explorateur.css" />
		<script language="javascript" type="text/javascript" src="../file/javascript.js"></script>
	</head>

	<body>
		<script>
			<?php

				session_start();
				
				if(!isset($_SESSION['username']))
				{
					header("location: ../index.php");
					exit;
				}

				$homepage = file_get_contents('file.json'); //On lit le fichier JSON
				echo " var data = ".$homepage.";"; //On convertit la variable du PHP au JS

				if (!isset($_SESSION['username'])) {
				    echo "var session = 0;";
				}else{
					echo "var session = 1;";
				}
			?>
			var data2 = data;

			//console.log(data);
			//console.log(data[0].machine[0].ip);
		</script>

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
				<br />
				<center>

					<?php
					function getBrowser() { 
					  $u_agent = $_SERVER['HTTP_USER_AGENT']; //On récupère les renseignement sur le naviguateur du client
					  $bname = 'Unknown';
					 
					  if(preg_match('/Trident/i',$u_agent)){ //Si celui-ci correspond à Internet Explorer
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
										Paramètres des Machines 
									</i> </b>
					</font>
					<BR>
					<BR>

					<div style="border: 2px solid black; margin-left: 30%; margin-right: 30%;">
					</br>
						<form>
							<select id="select2">
								<option>Sélectionnez une machine</option>
							</select><br><BR>
							Adresse IP : <input name="file" id="ip" type="text" required></input><br><BR>
							Nom : <input name="Nom" id="Nom" type="text" required></input></br><BR>
						</form>
						<button onclick="update()">Mettre à jour</button> 
					</div>
				</div>
				<button style="float: right;" onclick="window.location.href = '../index.php';">Retour</button>
			</center>
			<br />
		  </div> 
		</div>



		<script type="text/javascript">

			var sel = document.getElementById('select2'); //On sélectionne l'élément avec l'id "select2"

			window.addEventListener("load",function(){ //Lorsque la page charge
				for (var key in data[0].machine) { // On va récupérer chaque élément de notre objet JSON
				  if (data[0].machine.hasOwnProperty(key)) {
				    var opt = document.createElement('option'); //On créer une option
				    opt.innerHTML = data[0].machine[key].nom;
				    opt.value = data[0].machine[key].nom;
				    sel.appendChild(opt); //On va ajouter cette option au sélecteur
				  }
				}
			},false);

			document.getElementById('ip')
				.addEventListener('change', function() { //Lorsque l'élément avec l'id "file" change
				var position = (sel.selectedIndex) - 1;
				if(position >= 0)
				{
					data2[0].machine[position].ip = document.getElementById("ip").value; //On modifie l'objet JSON
					console.log(data);
				}
			})

			document.getElementById('Nom')
				.addEventListener('change', function() { //Lorsque l'élément avec l'id "Nom" change
				var position = (sel.selectedIndex) - 1;
				if(position >= 0)
				{
					data2[0].machine[position].nom = document.getElementById("Nom").value; //On modifie l'objet JSON
					console.log(data);
				}
			})


			document.getElementById('select2')
				.addEventListener('change', function() { //On sélectionne un élément depuis le sélecteur avec l'id "select2"
					var data = document.getElementById("select2").value;
					var position = (sel.selectedIndex) - 1;
					if(position >= 0) //Si l'utilisateur à choisis une machine
					{
						document.getElementById("ip").value = data2[0].machine[position].ip;
						document.getElementById("Nom").value = data2[0].machine[position].nom;
					}else{
						document.getElementById("ip").value = '';
						document.getElementById("Nom").value = '';
					}
			})

			function update() {
				window.location.replace("./edit_json.php?json=" + JSON.stringify(data2)); //On exécute la requête permettant de traiter le fichier JSON
			}

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