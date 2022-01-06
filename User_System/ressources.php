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

				$homepage = file_get_contents('ressources_path.json');
				echo " var data = ".$homepage.";";

				if (!isset($_SESSION['username'])) {
				    echo "var session = 0;";
				}else{
					echo "var session = 1;";
				}
			?>
			var data2 = data;

			console.log(data);
			console.log(data[0].ressources[0].nom);
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

							<li><a href="../User_System/ressources.php">Ressources<span><img title="Ressources"  title="Ressources" src="../img/dmu.jpg" style="width: 500px;" /></span></a></li>
						</ul>
					</div>
					<div id="defaultPage">
						<a href="../login.php">Connexion</a><br>
					</div>
					<div id="adminPage">
						<a href="../logout.php">Deconnexion</a><a style="margin-left: 30px;" href="../test_json.php">Page administrateur</a>
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

					<font face="arial" size="7" color="BLUE">
									<b> <i>
										Ressources disponibles 
									</i> </b>
					</font>
					<BR>
					<BR>

					<div style="border: 2px solid black; margin-left: 30%; margin-right: 30%;">
					</br>
						<form enctype="multipart/form-data" action="Alt_send.php" method="POST" target="__URL__">
							<select id="select2">
								<option>Sélectionnez une ressource</option>
							</select><br><BR>
						</form>
						<button onclick="update()" style="margin-bottom: 5%;">Télécharger la documentation</button>
					</div>
				</div>
				<button style="float: right;" onclick="window.location.href = '../index.php';">Retour</button>
			</center>
			<br />
		  </div> 
		</div>



		<script type="text/javascript">

			var sel = document.getElementById('select2');

			window.addEventListener("load",function(){
				for (var key in data[0].ressources) {
				  if (data[0].ressources.hasOwnProperty(key)) {
				    var opt = document.createElement('option');
				    opt.innerHTML = data[0].ressources[key].nom;
				    opt.value = data[0].ressources[key].nom;
				    sel.appendChild(opt);
				  }
				}
			},false);

			function update() {
				var position = (sel.selectedIndex) - 1;
				window.open(data[0].ressources[position].path, '_blank');
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