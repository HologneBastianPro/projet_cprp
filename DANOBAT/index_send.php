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
					DANOBAT TNC-10 
				</i> </b>
</font>







<BR>
<BR>

			<div style="border: 2px solid black; margin-left: 30%; margin-right: 30%;">
			</br>
				<form enctype="multipart/form-data" action="Alt_send.php" method="POST" target="__URL__">
					File : <input name="file" id="file" type="file" required></input><br><BR>
					Nom : <input name="Nom" id="Nom" type="text" required></input></br><BR>
					Numero : <input name="Nbr" id="Nbr" type="text" minlength="6" maxlength="6" required></input><br><BR>
					
					<?php
						

						if (!isset($_SESSION['username'])) {
						    echo "<script>var session = 0;</script>";
						}else{
							echo "<script>var session = 1;</script>";
						}
	

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
			<button style="float: right;" onclick="window.location.href = 'danobat.php';">Retour</button>
			


			

</center>
<br />

	
  </div> 
</div>



<script type="text/javascript">

	var tab = <?php global $tabnom; echo json_encode($tabnom); ?>; //On récupère notre tableau des noms de fichier dans notre JS
	
	document.getElementById('file') //On sélectionne l'element avec l'ID "file"
	.addEventListener('change', function() { //Quand le fichier de cet element change 
		  
		var name = '';
		var fr=new FileReader(); //On créer un lecteur de fichier
			
		fr.readAsText(this.files[0]); //On lis le fichier en tant que texte

		fr.onload = function()
		{ 
			var contents = this.result; //On recupère le contenu du fichier
					
			var count = 0;
			for (let j = 1; j < contents.length; j++) { //On parcours le fichier
				if (contents.substr(j, 1) != ',' && count === 0) { //Si le programme n'a toujours pas trouvé le character ";" et si la variable "count" est strictement égal à 0
						name += contents.substr(j, 1); //On continu de lire le fichier
				}
				else{
					count++
				}
			}
					document.getElementById("Nom").value = name; //On modifie l'élément avec l'ID 
		}
	})
/////////////////////////////////////////////////////////////////////////////////////////////////////////////

	document.getElementById('Nbr') //On sélectionne l'element avec l'ID "Nbr"
	.addEventListener('input', function() { //Quand on entre un character dans le champ
		
			var data = document.getElementById("Nbr").value; //On récupère la valeur du champ "Nbr"
			
			if(data.length != 6) //Si le nombre characters est strictement inférieur à 6
			{
				document.getElementById("Nbr").style.border = '3px solid red';
			}
			else{
				document.getElementById("Nbr").style.border = '3px solid green';
				var number = "P" + document.getElementById("Nbr").value; //On récupère le nom du fichier
				if(tab.find(element => element === number) === number) //Si le fichier existe déjà
				{
					document.getElementById("send").disabled = true; //On désactive le bouton
					alert("Fichier déjà existant");
				}else{
					document.getElementById("send").disabled = false; //On active le bouton
				}
			}
	})

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