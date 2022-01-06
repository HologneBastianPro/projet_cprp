<?php
	//header ('Content-Type: text/html; charset=windows-1252'); // permet d'afficher correctement avec l'utilisation de l'ASCII etendu (comme sur la machine)
	session_start();	

	/* port du service. */
	$service_port = 19000 ;

	/* Adresse IP du serveur de destination */
	$address = "172.19.175.170" ;


	//------------------------------------------------------------------------
	//-----
	//------------------------------------------------------------------------
	function telecharger_fichier($name, $socket)
	{
		$myfile = fopen($name, "w"); //On ouvre un nouveau fichier nommé suivant la variable $name

		$data = ""; //On initialise la variable qui permettra de récupérer les données

		$taille = strlen($_GET['dl']); //On calcul la taille du nom du fichier
		$taille = $taille + 1;

		if($taille < 255) //Si la taille est infèrieur à 255
		{
			$dat = dechex($taille);
			$in = "\x00\x00\x00";
			$in = $in.chr($taille);
		}

		$in = $in."R_FL".$name."\x00"; //On forge la requête
		$param = 0;

		$out = '';

		while($out != "\x00\x00\x00\x00T_FD" || $out != "\x00\x00\x00\x00T_ER") //Tant que la réponse de la machine ne correspond pas à "T_FD" ou "T_ER"
		{
			envoyer_simple($in,$socket); //On envoi la requête
			$out = lire_data($socket); //On récupère la réponse
			
			if($out == "\x00\x00\x00\x00T_FD" || $out == "\x00\x00\x00\x00T_ER") //Si la réponse de la machine correspond pas à "T_FD" ou "T_ER"
			{
				$param = 10000000000;
				break;
			}

			$out = substr($out , (strlen($out ) - (strlen($out ) - 8) ) );

			$out = str_replace("\x00", "\n", $out);

			$data = $data.$out;

			$in = "\x00\x00\x00\x00T_OK" ;	// On répond OK car on a bien reçu

			$param++;
		}

		$data = str_replace("\x0a", "\x0d\x0a", $data);

		fwrite($myfile, $data);

		$in = "\x00\x00\x00\x00T_OK" ;	// On répond OK car on a bien reçu
		envoyer_simple($in,$socket) ; // $recup contient la réponse de la machine

		header("Location: ./R2.php?id=".$name);
		//return $data;
	}

	
	if(!isset($_SESSION['username'])) {
    	echo "<script>var session = 0;</script>";
	}else{
		echo "<script>var session = 1;</script>";
	}

	function user_perm(){
		if(!isset($_SESSION['username']))
		{
			header("location: index.php"); //On redirige vers la page principal
			exit;
		}
	}

	function envoyer_et_afficher($donnees_a_transmettre,$la_socket)
	{
		socket_write($la_socket, $donnees_a_transmettre, strlen($donnees_a_transmettre));
		$out = socket_read($la_socket, 65535) ;
		return $out ;		
	}

	//------------------------------------------------------------------------
	//-----
	//------------------------------------------------------------------------
	function envoyer_simple($donnees_a_transmettre,$la_socket)
	{
		socket_write($la_socket, $donnees_a_transmettre, strlen($donnees_a_transmettre));
	}

	//------------------------------------------------------------------------
	//-----
	//------------------------------------------------------------------------
	function envoyer_data($donnees_a_transmettre,$la_socket)
	{
		socket_write($la_socket, $donnees_a_transmettre, strlen($donnees_a_transmettre));
		$out = socket_read($la_socket, 65535);
		return $out ;		
	}

	//------------------------------------------------------------------------
	//-----
	//------------------------------------------------------------------------
	function lire_data($la_socket)
	{
		$out = socket_read($la_socket, 65535) ;
		return $out ;		
	}

	//------------------------------------------------------------------------
	//-----
	//------------------------------------------------------------------------
	function init_transfert_dmu($la_socket)
	{
		$in = "\x00\x00\x00\x08A_LGINSPECT\x00" ; // bascule en mode config machine ?
		envoyer_data($in,$la_socket) ;

		$in = "\x00\x00\x00\x00R_VR" ;
		$version_machine = envoyer_data($in,$la_socket) ;

		$in = "\x00\x00\x00\x00R_PR" ;
		envoyer_data($in,$la_socket) ;

		$in = "\x00\x00\x00\x02C_CC\x00\x03" ;
		envoyer_data($in,$la_socket) ;

		$in = "\x00\x00\x00\x02C_CC\x00\x06" ;
		envoyer_data($in,$la_socket) ;

		$in = "\x00\x00\x00\x02C_CC\x00\x13" ;
		envoyer_data($in,$la_socket) ;

		$in = "\x00\x00\x00\x05A_LGFILE\x00" ; // bascule en mode fichiers machine ?
		envoyer_data($in,$la_socket) ;

		$in = "\x00\x00\x00\x00R_ST" ;
		envoyer_data($in,$la_socket) ;

		$in = "\x00\x00\x00\x01R_VR\x05" ; // version soft ?
		envoyer_data($in,$la_socket) ;
	}

	//------------------------------------------------------------------------
	//-----
	//------------------------------------------------------------------------
	function lire_repertoire_courant($la_socket)
	{
		$in = "\x00\x00\x00\x00R_DI" ; // requète pour lire le répertoire courant
		$data = envoyer_data($in,$la_socket) ;

		$i=0 ;
		$repertoire = "" ;

		while($data[strlen($data)-2-$i] != "\x00")	// isole le répertoire
		{
		 	$repertoire = $data[strlen($data)-2-$i].$repertoire ;
		 	$i++ ;
		}

		return $repertoire ; 
	}


	function recup_arbo()
	{
		if(isset($_GET['tree'])) //Si le parametre "tree" est passe dans l'url
		{
			$receive = $_GET['tree']; //Reception de la variable "tree" passee dans l'url
			$arbo = array(); //Creation du tableau
			$value = ""; //Variable permettant la récupération des noms 

			for($i = 0; $i < strlen($receive); $i++) //Tant qu'il reste des donnees a traiter
			{
				
				if($receive[$i] == ',') //Si le character reçu est une virgule
				{
					array_push($arbo, $value); //On ajoute les données de value dans le tableau
					$value = ""; //On réinitialise la variable de récupération des noms
				}else{
					$value = $value.$receive[$i]; //On ajoute un character au nom
				}
			}
			array_push($arbo, $value); //On ajoute les données de value dans le tableau

			return $arbo; //Renvoi du tableau d'arbo
		}else{
			$arbo = array(); //Creation d'un tableau vide
			return $arbo; //Renvoi du tableau d'arbo
		}
	}

	function get_rep($nom, $socket)
	{
		for($i = 1; $i < sizeof($nom); $i++) //Tant que la variable contient des noms
		{
			$taille = strlen($nom[$i]);
			$taille = $taille + 1; //On calcul la taille du nom du fichier

			if($taille < 255)
			{
				$dat = dechex($taille);
				$in2 = "\x00\x00\x00";
				$in2 = $in2.chr($taille);
			}

			$in2 = $in2."C_DC".$nom[$i]."\x00"; //On forge la requête

			if($i != sizeof($nom) - 1) //Si le répertoire n'est pas le dernier
			{

				//On navigue dans un répertoire sans en afficher le contenu

				envoyer_et_afficher($in2,$socket);

				$repertoire_courant = lire_repertoire_courant($socket) ;

				$tab = lire_fichiers_dossiers($socket) ;

				$in = "\x00\x00\x00\x00R_ST" ;
				envoyer_et_afficher($in,$socket) ;

			}else{

				//On navigue dans le répertoire final répertoire et on affiche le contenu

				envoyer_et_afficher($in2,$socket);

				$repertoire_courant = lire_repertoire_courant($socket) ;

				echo ("repertoire courant : ".$repertoire_courant) ;
				echo("<BR>\n<br>") ;

				$tab = lire_fichiers_dossiers($socket) ;

				$in = "\x00\x00\x00\x00R_ST" ;
				envoyer_et_afficher($in,$socket) ;
			}
		}
	}

	//------------------------------------------------------------------------
	//-----
	//------------------------------------------------------------------------

	function naviguer_repertoire_uni($nom, $socket)
	{
		for($i = 1; $i < sizeof($nom); $i++) //Tant que la variable contient des noms
		{
			$taille = strlen($nom[$i]);
			$taille = $taille + 1; //On calcul la taille du nom du fichier

			if($taille < 255)
			{
				$dat = dechex($taille);
				$in2 = "\x00\x00\x00";
				$in2 = $in2.chr($taille);
			}

			$in2 = $in2."C_DC".$nom[$i]."\x00"; //On forge la requête

			if($i != sizeof($nom) - 1) //Si le répertoire n'est pas le dernier
			{

				//On navigue dans un répertoire sans en afficher le contenu

				envoyer_et_afficher($in2,$socket);

				$repertoire_courant = lire_repertoire_courant($socket) ;

				$tab = lire_fichiers_dossiers($socket) ;

				$in = "\x00\x00\x00\x00R_ST" ;
				envoyer_et_afficher($in,$socket) ;

			}else{

				//On navigue dans le répertoire final répertoire et on affiche le contenu

				envoyer_et_afficher($in2,$socket);

				$repertoire_courant = lire_repertoire_courant($socket) ;

				echo ("repertoire courant : ".$repertoire_courant) ;
				echo("<BR>\n") ;

				$tab = lire_fichiers_dossiers($socket) ;
				affichage_repertoire($tab) ;

				$in = "\x00\x00\x00\x00R_ST" ;
				envoyer_et_afficher($in,$socket) ;
			}
		}
	}

	function naviguer_repertoire_uni_dl($nom, $socket)
	{
		for($i = 1; $i < sizeof($nom); $i++) //Tant que la variable contient des noms
		{
			$taille = strlen($nom[$i]); //On calcul la taille du nom
			$taille = $taille + 1;

			if($taille < 255) //Si la taille du nom est infèrieure à 255
			{
				$dat = dechex($taille);
				$in2 = "\x00\x00\x00";
				$in2 = $in2.chr($taille);
			}

			$in2 = $in2."C_DC".$nom[$i]."\x00"; //On forge la requête

			if($i != sizeof($nom) - 1)
			{
				envoyer_et_afficher($in2,$socket);

				$repertoire_courant = lire_repertoire_courant($socket) ;

				$tab = lire_fichiers_dossiers($socket) ;

				$in = "\x00\x00\x00\x00R_ST" ;
				envoyer_et_afficher($in,$socket) ;

			}else{
				envoyer_et_afficher($in2,$socket);
				$repertoire_courant = lire_repertoire_courant($socket);

				$tab = lire_fichiers_dossiers($socket);

				$in = "\x00\x00\x00\x00R_ST" ;
				envoyer_et_afficher($in,$socket) ;
			}
		}
	}

	function get_file_data()
	{
		$uploaddir = 'C:\wamp64\tmp';
		$uploadfile = $uploaddir . basename($_FILES['file']['name']); //On crée le chemin du fichier
	
		if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) { //On vérifie si le fichier est valide et on le déplace
			echo '<BR>';
			echo "<h1 style=\"color : green\">Fichier transféré avec succés</h1>\n";
			echo '<BR>';
			echo '<BR>';
		} else {
			echo "Possible file upload attack!\n";
		}
		
		$name = $_FILES['file']['name'];

		$name2 = "C:\wamp64\\tmp".$name;
		$file = fopen("C:\wamp64\\tmp".$name, "r"); //On ouvre le fichier en mode lecture

		$array = '';

		while(false !== ($char = fgetc($file))) { //Tant que l'on récupère des données sur le fichier
			$array = $array.$char; //On insère dans le tableau 
		}

		return $array; //On retourne le tableau
	}

	function Send($la_socket, $array)
	{
		//global $socket, $result;
		//global $tabnom;
		
		$name = $_FILES['file']['name']; //On récupère le fichier du formulaire
		
		$taille = strlen($name); //On calcule la taille de son nom
		$taille = $taille + 1;

		if($taille < 255) //Si sa taille est inèrieure à 255
		{
			$dat = dechex($taille);
			$in = "\x00\x00\x00";
			$in = $in.chr($taille);
		}

		$in = $in."R_FI".$name."\x00"; //On forge la requête
		envoyer_simple($in, $la_socket); //On envoi la requête

		$in = "\x00\x00\x00".chr($taille)."C_FL".$name."\x00"; //On forge la requête
		envoyer_simple($in, $la_socket); //On envoi la requête
		$out = lire_data($la_socket, 19000); //On récupère la réponse de la machine
		sleep(5);

		$size = strlen($array); //On calcul la taille du fichier
		
		$Cmpt = 0;
		$Cmpt_Check = 0;

		$data = '';

		if($size < 3000) //Si la taille est infèrieure à 3000
		{
			$data_len = strlen($array);
			$dat = dechex($taille);

			if($data_len <= 255)
			{
				$in = "\x00\x00\x00";
				$in = $in.bin2hex($data_len + 1);
			}else{
				if($data_len >= 65535)
				{
					$dat = dechex($data_len);
					$in = "\x00";
					$in = $in.bin2hex($data_len + 1);	
				}else{
					$dat = dechex($data_len);
					$in = "\x00\x00";
					$in = $in.bin2hex($data_len + 1);
				}
				
			}

			$in = $in."S_FL".$array."\x00"; //On forge la requête

			envoyer_simple($in, $la_socket);
			time_nanosleep(0, 500);

		}else{
			while($Cmpt <= $size) //Tant que le compteur est infèrieure à la taille
			{
				$data = '';
				$Cmpt_Check = $Cmpt_Check + 3050;
				$condition = false;
				$test = 0;
				for($i = 0; $i <= 750; $i++)
				{
					if(($Cmpt_Check + $test) < $size)
					{
						if(bin2hex($array[$Cmpt_Check + $test]) == '0a')
						{
							$Cmpt_Check = $Cmpt_Check + $test;
							$i = 1000;
							break;
						}else{
							if(bin2hex($array[$Cmpt_Check - $test]) == '0a')
							{
								$Cmpt_Check = $Cmpt_Check - $test;
								$i = 1000;
								break;
							}
						}
					}

					$test++;
				}

				for($Cmpt; $Cmpt <= $Cmpt_Check; $Cmpt++)
				{
					if($Cmpt <= $size)
					{
						$data = $data.$array[$Cmpt];
					}
				}

				var_dump($data);

				$data = str_replace("\x0d\x0a", "\x00", $data);

				var_dump($data);

				$data_len = strlen($data);
				$dat = dechex($taille);

				$dat = dechex($data_len + 1);

				if($data_len > 255)
				{
					$datlen = strlen($dat);
					$in = "\x00\x00";
					$encode = 0;
				}else{
					$datlen = strlen($dat);
					$in = "\x00\x00\x00";
					$encode = 0;
				}

				if($datlen % 2 != 0){
					$dat = '0'.$dat;
				}

				$final_data= '';

				for($i = $datlen; $i >= 0 ; $i = $i - 2)
				{
					if($dat[$i - 1] != '0')
					{
						$encode = $dat[$i - 1].$dat[$i];
					}else{
						$encode = $dat[$i];
					}
					
					$final_data = chr(hexdec($encode)).$final_data;
					$encode = '';
				}


				$in = $in.$final_data;
				$in = $in."S_FL".$data."\x00";


				
					envoyer_simple($in, $la_socket);
					time_nanosleep(0, 500);
				
			}
		}

		$in = "\x00\x00\x00\x00"."T_FD";

		envoyer_simple($in, $la_socket);

		$out = lire_data($la_socket, 19000);
		
		if($out == "\x00\x00\x00\x00T_FD")
		{
			$in = "\x00\x00\x00\x00R_ST" ;
			envoyer_simple($la_socket,$socket) ;
		}
	}

	function delete($la_socket, $file)
	{
		$in = "\x00\x00\x00".chr(strlen($file) + 1)."C_FD".$file."\x00";
		$out = envoyer_et_afficher($in, $la_socket);
	}
	

	function lire_fichiers_dossiers($la_socket)
	{
		$tableau_contenu = array ();

		$in="\x00\x00\x00\x01R_DR\x01" ; // pour demander le répertoire
		$recup = envoyer_data($in,$la_socket) ; // $recup contient la réponse de la machine

		$date_fichier = new DateTime();
		
		$i = 8 ; // pointeur sur le premier fichier / dossier

		$indice_tableau = 0 ;
		while($i<strlen($recup))
		{
			$taille_fichier = ord($recup[$i+0])*16777216 
							+ ord($recup[$i+1])*65536 
							+ ord($recup[$i+2])*256 
							+ ord($recup[$i+3])*1 ;
			$timeStamp_fichier = ord($recup[$i+4])*16777216 
								+ ord($recup[$i+5])*65536 
								+ ord($recup[$i+6])*256 
								+ ord($recup[$i+7])*1 ;
			$date_fichier->setTimestamp($timeStamp_fichier); // on formate le TimeStamp en DateTime
			$type_de_fichier = ord($recup[$i+11]) ; // 
			$marqueur = "" ; 
			if (($type_de_fichier & 0x01) == 0x01) { $marqueur = $marqueur."E" ; }
			if (($type_de_fichier & 0x02) == 0x02) { $marqueur = $marqueur."M" ; }
			if (($type_de_fichier & 0x04) == 0x04) { $marqueur = $marqueur."S" ; }
			if (($type_de_fichier & 0x08) == 0x08) { $type_hide = true ; } 
			else { $type_hide = false ; }
			if (($type_de_fichier & 0x40) == 0x40) { $type_dossier = true ; } 
			else { $type_dossier = false ; }
			
			$nom_du_fichier = "" ;
			$i = $i + 12 ; // pointe sur la première lettre du fichier, texte à 0x00 terminal
			while(ord($recup[$i]) != 0x00)
			{
				$nom_du_fichier = $nom_du_fichier.$recup[$i] ;
				$i++ ;
			}
			$i++ ; // pour passer le 0x00 terminal du texte

			if ($type_hide == false ) // si fichier/dossier non caché alors on compte
			{
				if ($type_dossier == true) 
					{
						array_push ($tableau_contenu,[
							"nom" 		=> $nom_du_fichier,
							"taille" 	=> 0,
							"attributs"	=> 0,
							"type"		=> "DIR",
							"date"		=> $date_fichier ]) ;
					}
				else
					{
						array_push ($tableau_contenu,[
							"nom" 		=> $nom_du_fichier,
							"taille" 	=> $taille_fichier,
							"attributs"	=> $marqueur,
							"type"		=> "",
							"date"		=> $date_fichier ]) ;
					}
			}
		}
		
		$in = "\x00\x00\x00\x00T_OK" ;	// On répond OK car on a bien reçu

		envoyer_data($in,$la_socket) ; // juste envoyer la reponse sans afficher

		return ($tableau_contenu) ;
	}

	//------------------------------------------------------------------------
	//-----  
	//------------------------------------------------------------------------
	function affichage_repertoire($tableau_a_afficher) 
	{
		$tableau_a_afficher = tri_répertoire($tableau_a_afficher); //On tri le tableau

		//print_r($tableau_a_afficher);
		echo ("<br><dutexte style=\"color: #0000ff; font-weight: bold; \" onclick=\"back()\" >Retour en arrière</dutexte><br><br>");
		echo ("<table style=\"width: 60%; \">\n") ;
		echo ("<tr>\n") ;
		echo ("	<th>nom</th> 
				<th>taille</th> 
				<th>attributs</th> 
				<th>type</th> 
				<th>date</th> 
				<th>action</th> \n") ;
		echo ("</tr>\n") ;

		for ($i = 0; $i < sizeof($tableau_a_afficher); $i++ )
		{
			if ($tableau_a_afficher[$i]["type"]=="DIR") //Si l'élément est un dossier
			{
				if($tableau_a_afficher[$i]["nom"] != "..") //Vérifie si le dossier est le dossier "Retour arrière"
				{
					if($tableau_a_afficher[$i]["nom"] != "tncguide" && $tableau_a_afficher[$i]["nom"] != "mdna" && $tableau_a_afficher[$i]["nom"] != "SOFTWARE" && $tableau_a_afficher[$i]["nom"] != "System Volume Information") //Vérifie si le dossier ne fait pas partie des dossiers à cacher
					{
						$name = $tableau_a_afficher[$i]["nom"];
						echo ("<tr>\n") ;
						echo ("<td><dutexte style=\"color: #0000ff; font-weight: bold; \" onclick=\"nav_rep('".$name."')\" ><img src=\"../img/file.png\"> ".$tableau_a_afficher[$i]["nom"]."</dutexte></td>") ;
						echo ("<td>".""."</td>") ;
						echo ("<td>".""."</td>") ;
						echo ("<td>".$tableau_a_afficher[$i]["type"]."</td>") ;
						echo ("<td>".""."</td>") ;
						//echo ("<td>".$tableau_a_afficher[$i]["date"]."</td>") ;	
					}
				}
			}
			else
			{
				if((substr($tableau_a_afficher[$i]["nom"], -1) == "I" || substr($tableau_a_afficher[$i]["nom"], -1) == "H" || substr($tableau_a_afficher[$i]["nom"], -1) == "h" ||substr($tableau_a_afficher[$i]["nom"], -1) == "T" || substr($tableau_a_afficher[$i]["nom"], -3) == "TCH") && substr($tableau_a_afficher[$i]["nom"], -3) != "CDT") //Vérifie si le dossier ne fait pas partie des dossiers à cacher
				{
					echo ("<tr style=\"color: #000000;\" >\n") ;
					echo ("<td><dutexte><img src=\"../img/fichier.gif\"> ".$tableau_a_afficher[$i]["nom"]."</dutexte></td>") ;
					echo ("<td>".$tableau_a_afficher[$i]["taille"]."</td>") ;
					echo ("<td>".$tableau_a_afficher[$i]["attributs"]."</td>") ;
					echo ("<td>".$tableau_a_afficher[$i]["type"]."</td>") ;
					//echo ("<td>".$tableau_a_afficher[$i]["date"]."</td>") ;
					echo ("<td>".""."</td>") ;
					if(isset($_GET['dl']))
					{
						echo ('<td><img style="cursor: pointer;" onclick="Download(\''.$tableau_a_afficher[$i]["nom"]."\', \'".$_GET['name'].'\')" title="Telecharger" src="../img/download.png"></td>');
						echo ('<td><img style="cursor: pointer;" class="delete" onclick="Delete(\''.$tableau_a_afficher[$i]["nom"]."\', \'".$_GET['name'].'\')  title="Telecharger" src="../img/delete.gif" hidden></td>');
					}else{
						echo ('<td><img style="cursor: pointer;" onclick="Download(\''.$tableau_a_afficher[$i]["nom"].'\', 0)" title="Telecharger" src="../img/download.png"></td>');
						echo ('<td><img style="cursor: pointer;" class="delete" onclick="Delete(\''.$tableau_a_afficher[$i]["nom"].'\', 0)" title="Telecharger" src="../img/delete.gif" hidden></td>');
					}
				}

			}
						 
			echo ("</tr>\n") ;
		}
		echo ("</table>\n") ;
	}

	function tri_répertoire($tableau_a_traiter)
	{
		$tableau_dossier = array(); //On créer un tableau pour les dossiers
		$tableau_fichier = array(); //On créer un tableau pour les fichiers

		for ($i = 0; $i < sizeof($tableau_a_traiter); $i++ ) //On parcour le tableau
		{
			if ($tableau_a_traiter[$i]["type"]=="DIR") //Si c'est un dossier
			{
				array_push($tableau_dossier, $tableau_a_traiter[$i]);
			}
			else
			{
				array_push($tableau_fichier, $tableau_a_traiter[$i]);
			}
		}

		asort($tableau_dossier);
		asort($tableau_fichier);

		$tableau_a_traiter = array_merge($tableau_dossier, $tableau_fichier);

		return $tableau_a_traiter; //On renvoie les tableau
	}
?>