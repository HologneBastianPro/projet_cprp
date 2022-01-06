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

		include 'DANOBAT_Function.php';

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



 


		<BR>
		<center>
		<?php
		//Gestion par scénario (3 possibles)
			if(isset ($_GET['Download'])) // SI téléchagement (Variable download "is set" et récupérable en GET)
			{
				Start(); //Connexion
				Download($_GET['Download']);	// Appelle de la fonction Download en passant par paramètre GET Download
			}
			elseif (isset ($_GET['Suppr']))  // SI suppréssion (Variable Suppr "is set" et récupérable en GET)
			{
				Start(); //Connexion
				Suppr($_GET['Suppr']); // Appelle de la fonction Suppr en passant par paramètre GET Suppr
				header("Location: danobat.php"); // Redirection page principale (Scnénario 3) - Actualiser nouvelle arboréscence 
				
			}
			else //Scnéraio 3 le nominal
			{
				if (!isset($_SESSION['username'])) {
				    echo "<script>var session = 0;</script>";
				}else{
					echo "<script>var session = 1;</script>";
				}

				Start(); // Connexion
				$tabdef=Get_Dir(); //Affection de return de Getdir (tableau) à tabdef 
				array_map('unlink', glob("C:\wamp64\www\*.PIT")); //Suppression des .PIT sur le serveur
			}
			
			$size = sizeof($tabdef); //Récuperation taille reperoire (nombre de ligne)
		?>
		</center>

	 <table>
		<tr>
		
		<!--Partie Graphique HighCHarts -->
		<td>
			<figure>
				<div id="container-speed" style=" width: 200px; height: 180px;"></div>
			</figure>
			
			<script type="text/javascript">
				var gaugeOptions = {
					chart: {
						type: 'solidgauge',
						backgroundColor: 'rgba(0,0,0,0)',
					},

					title: null,

					pane: {
						center: ['50%', '85%'],
						size: '100%',
						startAngle: -90,
						endAngle: 90,
						background: {
							backgroundColor:
								Highcharts.defaultOptions.legend.backgroundColor || '#EEE',
							innerRadius: '60%',
							outerRadius: '100%',
							shape: 'arc'
						}
					},

					exporting: {
						enabled: false
					},

					tooltip: {
						enabled: false
					},

					// the value axis
					yAxis: {
						stops: [
							[0.1, '#55BF3B'], // green
							[0.5, '#DDDF0D'], // yellow
							[0.9, '#DF5353'] // red
						],
						lineWidth: 0,
						tickWidth: 0,
						minorTickInterval: null,
						tickAmount: 2,
						title: {
							y: -70
						},
						labels: {
							y: 16
						}
					},

					plotOptions: {
						solidgauge: {
							dataLabels: {
								y: 5,
								borderWidth: 0,
								useHTML: true
							}
						}
					}
				};

				var chartSpeed = Highcharts.chart('container-speed', Highcharts.merge(gaugeOptions, {
					yAxis: {
						min: 0,
						max: 783.728, //Stoockage max en KOCTETS
						title: {
							text: 'Stockage'
						}
					},

					credits: {
						enabled: false
					},

					series: [{
						name: 'Speed',
						data: 
						<?php 
						

						global $taille; //recuparation de taille (calculer dans la fonction getdir)
						echo '[';
						echo $taille; //graphique remplis de taille
						echo ']'; 
						
						
						?>,
						dataLabels: {
							format:
								'<div style="text-align:center">' +
								'<span style="font-size:15px">{y}</span><br/>' +
								'<span style="font-size:8px;opacity:0.4">KBytes</span>' +
								'</div>'
						},
						tooltip: {
							valueSuffix: ' KBytes'
						}
					}]

				}));



			</script>
		</td>
		<!-- Fin partie graphique -->
		<td Align="center">

			<font face="arial" size="7" >
				<b><i>DANOBAT TNC-10</i> </b>
			</font>
			<BR>
			<BR>
			<center>
					<button class="Envoi" onclick="window.location.href = 'index_send.php';"> Envoi de fichier </button>
			</center>
			<BR>
		</td>
		</tr>	
		</table>
		<BR>
		<button id="button" class="Suppr" onclick="masquer_div(' <?php echo($size) ; ?> ');"> Activer la suppression </button>
		
	</div>
	<center>
	<BR> 
		<table cellpadding=4 cellspacing=2>
			<tr>	
				<th align="center">Identifiant</th>
				<th align="center">Nom</th>
				<th align="center">Taille</th>
				<th align="center">Date</th>
				<th align="center">Heure</th>
				<th align="center">Droit</th>
			</tr>
			<?php
				global $tabnom; // Récuperation tableau des identifiants
				for ($j=0;$j<sizeof($tabdef);$j++) // Pour i inferieur a la taille de direcory
				{
					echo '<tr>';
			
						for($k=0;$k<6;$k++) //Remplissage de la 1er ligne
						{
							echo '<td align="center">';
							echo $tabdef[$j][$k]; // Seul K varie durant la boucle
							echo '</td>';
							
						}
						
					
					
						//Creation case download
						echo '<td> ';
						echo '<a href="?Download=';
						echo $tabnom[$j]; //Redirection vers la même page + ?Download=ID (Donc download devient set exemple ?Dowload = P000478°)
						echo '">';
						echo '<img title="Telecharger" src="../img/download.png">';
						echo '<a>';
						echo '</td>';
						echo "\n" ;
						
						
						

						//Creation case supprimer
						echo '<td>';
						echo '<div id="a_masquer';
						echo $j; //Creation d'un div avec id unique (Utile pour la fonction masquer_div)
						echo'"  style="display:none;">';
						echo '<a href="?Suppr=';
						echo $tabnom[$j]; //Redirection vers la même page + ?Suppr=ID (Donc Suppr devient set exemple ?Suppr = P000478)
						echo '">';
						echo '<img title="Supprimer" src="../img/delete.gif">';
						echo '</a>';
						echo '</div>';
						echo '</td>';
						
						echo '</tr>';
						
				}
				
			?>
		

			
	
			</table>
	</center>
	<BR>
	

</div>
</body>

	<script type="text/javascript">

		function masquer_div(size)
		{
			
		

			for (i = 0; i <= size; i++) {
				  if (document.getElementById("a_masquer"+i).style.display == 'none')
				  {
					   document.getElementById("a_masquer"+i).style.display = 'block';
					   document.getElementById("button").innerHTML = "Désactiver la suppression";
					 
				  }
				  else
				  {
					   document.getElementById("a_masquer"+i).style.display = 'none';
						document.getElementById("button").innerHTML = "Activer la suppression";
				  }
			}
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
</html>