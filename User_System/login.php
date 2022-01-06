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
			session_start();
			
			if (!isset($_SESSION['username'])) {
			    echo "<script>var session = 0;</script>";
			}else{
				echo "<script>var session = 1;</script>";
			}

			if(!isset($_SESSION['username']))
			{
				if (isset($_POST['pass'])) {

				    $name = $_POST['Nom'];

				    $pass = $_POST['pass'];

			    	if($name == "Admin" && $pass == "CPRP")
			    	{
			    		

			    		$_SESSION['valid'] = true;
				        $_SESSION['timeout'] = time();
				        $_SESSION['username'] = 'Admin';

						header("location: ../index.php");
						exit;
					}else{
						echo '<script>window.alert("Nom d\'utilisateur ou mot de passe incorrect")</script>';
					}

				}
			}else{
				header("location: ../index.php");
				exit;
			}
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


		<div class="wrapper row2">
		   <div id="container" class="clear">
				<br/>
				<center>

					<font face="arial" size="7" color="BLUE">
									<b> <i>
										Connexion 
									</i> </b>
					</font>

					<BR>
					<BR>

					<div style="border: 2px solid black; margin-left: 30%; margin-right: 30%;">
					</br>
						<form action="login.php" method="POST" target="__URL__">
							Nom d'utilisateur : <input name="Nom" id="Nom" type="text" required></input><br><BR>
							Mot de passe : <input name="pass" id="pass" type="password" required></input></br><BR>
							<input type="submit" value="Envoyer" name="submit" id="submit">
						</form>
					</div>
					<button style="float: right;" onclick="window.location.href = '../index.php';">Retour</button>
				</center>
				<br/>
		  </div> 
		</div>



		<script type="text/javascript">

			function update() {
				window.location.replace("./edit_json.php?json=" + JSON.stringify(data2));
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