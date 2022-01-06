<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
<title>Transfert de fichier</title>
<meta charset="iso-8859-1">
<link rel="stylesheet" media="screen" type="text/css" title="Design" href="file/style-explorateur.css" />

</head>
<body>
<?php
	session_start();

	if (!isset($_SESSION['username'])) {
	    echo "<script>var session = 0;</script>";
	}else{
		echo "<script>var session = 1;</script>";
	}
?>

<div class="wrapper row1">
	<header id="header" class="clear">
		<div id="hgroup">
			<h1><a href="index.php">Interface de transfert de fichier</a></h1>
		</div>
		<img title="Lycée JB de BAUDRE" src="img/lycee.jpg" style="width: 200px; float:right" />
		<nav>
			<div style="width:75%; float:left; margin:5px;">
				<ul>
					<li><a href="doosan.php">DOOSAN<span><img title="DOOSAN lynx 220ly" src="img/doosan.jpg" style="width: 500px;" /></span></a></li>
					<li><a href="haas.php">HAAS<span><img title="HAAS vf1" src="img/haas.png" style="width: 500px;" /></span></a></li>
					<li><a href="transmab.php">TRANSMAB<span><img title="SOMAB TRANSMAB 200" src="img/transmab.jpg" style="width: 500px;" /></span></a></li>

					<li><a href="DANOBAT/danobat.php">DANOBAT<span><img title="DANOBAT"  title="DANOBAT" src="img/danobat.jpg" style="width: 500px;" /></span></a></li> 
					<li><a href="DMU/dmu.php">DMU<span><img title="DMU"  title="DMU" src="img/dmu.jpg" style="width: 500px;" /></span></a></li>

					<li><a href="User_System/ressources.php">Ressources<span><img title="Ressources"  title="Ressources" src="../img/dmu.jpg" style="width: 500px;" /></span></a></li>
				</ul>
			</div>
			<div id="defaultPage">
				<a href="User_System/login.php">Connexion</a><br>
			</div>
			<div id="adminPage">
				<a href="User_System/logout.php">Deconnexion</a><a style="margin-left: 30px;" href="User_System/test_json.php">Page administrateur</a>
			</div>
		</nav>
	</header>
</div>

<!-- Gestionnaire de fichier -->
<div class="wrapper row2">
   <div id="container">
<br/>

<center>
	<h1> <font color="#229954">Envoyer un programme</font></h1><br/>
	<img src="img/envoi.jpg" style="width: 400px" /><br/><br/>
	
	<form method="post" enctype="multipart/form-data" action="file/fonction_envoi.php">
		<p>
			<input type="file" name="fichier">
			<b><input type="button"" value="Verification"><span><img src="img/envoyer.png" style="width: 400px;" /></span></b>
			<input type="submit" name="upload" value="Envoyer">
			<br/><br/><br/><br/><br/><br/><br/>
		</p>
	</form>
	
	<h1> <font color="#229954">Recevoir un programme</font></h1><br/>
	<img src="img/reception.jpg" style="width: 400px" /><br/><br/>
	
	<form action="file/socket_reception.php" method="post">
		<p>
			Choisissez un nom : <input type="text" name="nom" >
			<input type="submit"" value="Cliquez">
			<c ><input type="button"" value="Recevoir"><span><img src="img/recevoir.png" style="width: 400px;" /></span></c>
		</p>
	</form>
	
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