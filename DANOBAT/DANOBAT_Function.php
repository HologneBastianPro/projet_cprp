<?php
	error_reporting(E_ERROR | E_PARSE);

	session_start();
	
	function user_perm(){
		if(!isset($_SESSION['username']))
		{
			header("location: index.php");
			exit;
		}
	}

	//----------------------------------------------------------------------	

	function Start()
	{
		global $socket ;
		$result; 

		$service_port = 3873; // port de connexion sur le danobat
		$address = "172.19.175.172" ; // @IP du tour danobat
		
		/* Crée un socket TCP/IP. */
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		if ( $socket === false) {
			echo "socket_create() a échoué : raison :  " . socket_strerror(socket_last_error()) . "\n";
		} else {
	
		}
	
		$result = socket_connect($socket, $address, $service_port);
		if ($result === false) {
			echo '<br>';
			echo '<p style="font-size:160%;">';
			echo "Une tentative de connexion a échouée, la Danobat est trés certainement éteinte";
			echo '</p>';
		} else {}
	}

	//----------------------------------------------------------------------		
			
	function Get_Dir(){
		
		global $socket, $result ;
		global $tabnom;
		global $taille;
		$count = 0;
		$z=0;
		
		echo '<br>';
		$tab = array ();
		
		$l=0; //Compteur ligne
		$k=0; // Compteur colonne
				
		
		//Pour chaque trame reçu (par lot de deux commande + ligne) :
		
		for($j = 0; $j < 1000; $j += 12) //NBre de tramme (incrementation 12 en 12)
		{
			
			if ($j < 1)
			{
				//première trame
				$in = chr(0x00).chr(0x10).chr(0x01).chr(0x52).chr(0x2c).chr(0x50).chr(0x52).chr(0x47).chr(0x2c).chr(0x44).chr(0x49).chr(0x52).chr(0x2c).chr(0x50).chr(0x2c).chr(0x30).chr(0x2c).chr(0x03); 
				//<sth> R,PRG,DIR,P,0,
			}
			elseif($j > 1 && $j < 100)
			{
				//Trame de 12 à 96
				$in = chr(0x00).chr(0x11).chr(0x01).chr(0x52).chr(0x2c).chr(0x50).chr(0x52).chr(0x47).chr(0x2c).chr(0x44).chr(0x49).chr(0x52).chr(0x2c).chr(0x50).chr(0x2c).$j.chr(0x2c).chr(0x03); //Si pas trame 1 incrementer 12 en 12
			}
			else
			{
				//Trame de 108 à 996 (Inutile de gerer si plus de ligne)
				$in = chr(0x00).chr(0x12).chr(0x01).chr(0x52).chr(0x2c).chr(0x50).chr(0x52).chr(0x47).chr(0x2c).chr(0x44).chr(0x49).chr(0x52).chr(0x2c).chr(0x50).chr(0x2c).$j.chr(0x2c).chr(0x03);
			}
			
			
			socket_write($socket, $in, strlen($in)); // Envoi de la trame
			
			$out = socket_read($socket, 3873); // Lecture et attribution de retour a out
			// Exemple pour la 1er trame : <sth> S,PRG,DIR,P,0,
			
			
			
			$out2 = socket_read($socket, 3873); // Lecture et attribution du deuxième retour a out2
			
			
			
			//Exemple d'une ligne P000001|<COMMENTAIRE>|170|25/01/16|10:51:08|0x0a
			
			for ($i = 4; $i <= strlen($out2); $i++) //Debut a 4 car 4 premier Quarter -) Taille trame
			{
				if(bin2hex($out2[$i]) == '0a') // Si en Hexa egale a (0a) saut de ligne (DETECTION FIN DE LIGNE)
				{
					$count++; // + 1 caractère
					$l++; // Retour a la ligne (dans le tableau)
					$k=0; // Reprise a la colonne 0 (Debut nouvelle ligne)
				}
				else{
					if($out2[$i] == '<' || $out2[$i] == '>') 
					{ 
						// si ...Ne fais rien
					}
					else{
						if($out2[$i] == '|') {
							$k++;	//Changement de case car "|"
						}
						else{
							$tab[$l][$k]=$tab[$l][$k].$out2[$i]; // On ecrit succéssivement tout les caractères jusqu'a "|"
						}
					}
				}
			}
			
			if($count == 11) //Si 12 lignes
			{
				$count = 0; //Remise a 0 du counter pouyr trame suivante
			}
			else
			{
				$j = 2000; // Si moin de 12 lignes alors dernière trame -> Sortie du while
			}

				
				$k =0; //Chaque fin de trame colone = 0 et ligne + 1
				$l++;
				
		}
		
		for	($i=0; $i<$l; $i++) //pour i inferieur a nb de ligne
		{
			$tabnom[$z]=$tabnom[$z].$tab[$i][0]; //Recuperation des nom de fichier pour le telechargement de ceux-ci
			//on met dans tabnom toute la premier colone de tab
			$z++;
		}
		
		for	($i=0; $i<$l; $i++)
		{
			$taille =$taille + $tab[$i][2]; //Compteur de stockage (colone 3) en octets 
		}
		
		$taille = $taille / 1000; //Passage en koctets
		
		return $tab;

	}

	//----------------------------------------------------------------------

	function Download($ID)
	{
		//Passage du ID en paramètre
		

		
			$name = $ID; //Exemple de ID : P000001| (Id du fichier)
			$name = $name.".PIT"; //Création nom de fichier et Ajout de l'extension .PIT
			global $socket;
			
			$myfile = fopen($name, "w"); //Création du fichier en mode "Ecriture"
			
			fwrite($myfile, '%');	//Écriture du caractère de début de fichier "%"
			
	
			

			//Réalisation de la trame (Sans le "P")
			$in = chr(0x00).chr(0x13).chr(0x01).chr(0x52).chr(0x2c).chr(0x50).chr(0x52).chr(0x47).chr(0x2c).chr(0x54).chr(0x52).chr(0x4e).chr(0x2c).$ID[1].$ID[2].$ID[3].$ID[4].$ID[5].$ID[6].$ID[7].chr(0x2c).chr(0x03);
			//Exemple R,PRG,TRN,000001,

			
			socket_write($socket, $in, strlen($in)); //Envoi de la trame
			
			$out = socket_read($socket, 3873); // Lecteur et attribution a out
			
			//Exemple out : S,PRG,TRN,000001,D,CUADRADO,MX--,
			
			
			$CharCount = 0; //Compteur de virgule (renommé ?)

			for ($i = 0; $i <= strlen($out) - 1; $i++)
			{
				if($out[$i] == ',') //Si c'est une virgule
				{
					if($CharCount >= 5 && $CharCount < 7) //Si entre la virgule 5 et 7
					{
						fwrite($myfile, $out[$i]); //Ecriture de la virgule 5 (nom du fichier) et virgule 7 (droits)
						$CharCount++;
					}
					else
					{
						$CharCount++; //Sinon juste increment du cpt virgule
					}
				}
				else
				{
					if($CharCount >= 5 && $CharCount < 7)
					{
						fwrite($myfile, $out[$i]); //Ecriture du contenu entre la virgule 5 et 7 (Nom et droits)
					}
				}
			}
			
			fwrite($myfile, chr(0x0d));
			fwrite($myfile, "\n"); //Saut de ligne (1er ligne compléter)

			
			
			//ECRITURE DU CONTENU
			$end = false;
			while($end == false) //Tant que pas fin de transmission (0x03)
			{
				$out2 = socket_read($socket, 3873); //Lecture des trames reçu (une par boucle) jusqu'a dernière trame
				
				for($i = 3; $i < strlen($out2); $i++)
				{
					switch($out2[$i])
					{
						case "\x03":
							$end = true; //Si 0x03 (End Of Transmission) end = true
							break;
						case "\x0a":
							fwrite($myfile, chr(0x0d));
							fwrite($myfile, $out2[$i]);
							break;
						case "\x17":
							break; ///Si end of block (0x17) ne rien écrire dans le fichier
						default:
							fwrite($myfile, $out2[$i]); // Cas par défault caractère quelconque on écrit dans le fichier.
							break;
					}
				}	
			};
			fclose($myfile); // Fermeture du fichier
			header("Location: R2.php?id=$name"); //Redirection vers R2
		

	}
		
	//----------------------------------------------------------------------	

	function suppr($ID)
	{
		//Récupération de ID en paramètre
		
		global $socket;
		
		//Création de la trame sans le "P" (début à 1)
		$in = chr(0x00).chr(0x13).chr(0x01).chr(0x53).chr(0x2c).chr(0x50).chr(0x52).chr(0x47).chr(0x2c).chr(0x44).chr(0x45).chr(0x4c).chr(0x2c).$ID[1].$ID[2].$ID[3].$ID[4].$ID[5].$ID[6].$ID[7].chr(0x2c).chr(0x03);
		// Exemple : ???
		
		
		socket_write($socket, $in, strlen($in));//Envoir de la trame
				
		$out = socket_read($socket, 3873); // Lecture du retour et attribution à out
		
	}

	//----------------------------------------------------------------------	

	function Send()
	{
		global $socket, $result;
		global $tabnom;
	
	
		$uploaddir = 'C:\wamp64\tmp'; //Répertoire 
		$uploadfile = $uploaddir . basename($_FILES['file']['name']); // répertoire + nom fichier
	
		if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) { //Si la commande fonctionne
			echo '<BR>';
			echo "<h1 style=\"color : green\">Fichier transféré avec succés</h1>\n"; //Transfert Sur le serveur 
			echo '<BR>';
			echo '<BR>';
		} else {
			echo "Possible file upload attack!\n"; //Exception lévée
		}
		
		$name = $_FILES['file']['name'];
		$name2 = "C:\wamp64\\tmp".$name;
		$file = fopen("C:\wamp64\\tmp".$name, "r"); //Ouverture du fichier en lecture

		$Name = $_POST["Nom"]; //Commentaire
		$Num = $_POST["Nbr"]; //Num prog
		
		
		//---------------------------------------------------------------
		
		
		$var = 0x1a; // Taille de base sans nom
		
		for($i = 0; $i < strlen($Name); $i++)
		{
			$var += 0x01; // Increment taille par rapport a la taille du nom
		}
		
		//Send's Request Construction (OK)
		$in = chr(0x00).chr($var).chr(0x01).chr(0x53).chr(0x2c).chr(0x50).chr(0x52).chr(0x47).chr(0x2c).chr(0x54).chr(0x52).chr(0x4e).chr(0x2c).$Num.chr(0x2c).chr(0x2c).$Name.chr(0x2c).chr(0x4d).chr(0x58).chr(0x2d).chr(0x2d).chr(0x2c).chr(0x17);
		
		socket_write($socket, $in, strlen($in));


		//Initalisation des variables//
		$array = "";
		$array2 = "";
		$data = "";
		$s = 0;
		
		while(false !== ($char = fgetc($file))) { //Tant qu'il y a des caractères
			$array = $array.$char;  //On remplis le tableau du contenu du fichier
		}

		$size = strlen($array);
		
		for ($i=0; $i < $size; $i++)
		{
			if (bin2hex($array[$i]) != '0d')
			{
				$array2 = $array2.$array[$i]; //Suppression des 0d utiles pour la mise en forme mais a supprimer pour l'envois
			}
		}
		
		$i = 0;
		
		$L = false;

		//Detection fin 1er ligne (1er ligne a ne pas envoyé)
		while($L == false) 
		{
			if (bin2hex($array2[$i]) == '0a') //Si ligne fini
			{
				$L = true;
			}
			$i++;
		}
		 
		$size = strlen($array2); //Taille nouveau tableau (nb de case)
		$s = $i; //Variable de parcours
		$size = $size - $s; //Taille deduis de la 1er ligne
		$i =0;
		
		while ($size > 1)
		{
			if ($size >= 1000) // Si la taille est encore supérieur a 1000
			{
				for($i = 1; $i <= 1000; $i++) // Ecrire 999 caracère
				{
					$data=$data.$array2[$s]; //Debut a deuxième ligne puis incrément
					$s++; //Increment variable de parcour du tableau
					$size --; // Décrément taille tableau
				}
				
				while(bin2hex($array2[$s]) != '0a') // Puis ecrire jusqu'a 0x0a
				{
					$data = $data.$array2[$s];
					$s ++;
					$size --;
					$i++;
				}
				
				//Enfin ecriture du 0x0a finale
				$data = $data.$array2[$s];
				$s++;
				$size --;
				$i++;
			}
			else //Sinon dernière trame
			{
				$cpt = $size; 
				for($i = 1; $i <= $cpt; $i++)
				{
					$data=$data.$array2[$s];
					$s++;
					$size --; //Décrement de size MAIS pas de cpt d'ou la copy
				}
			}
			
			//calcul taille trame
			$i = $i+1;
			$t = dechex($i); // Conversion taille en hexa
			$t = str_pad($t, 4, "0", STR_PAD_LEFT ); //Comptement sur 4 caractère hexa exemple t=f4 -> t=00f4
			//exemple : $t = "00f4"  chr(00) et chr (f4)
			
			$in = chr(hexdec(substr($t,0,2))).chr(hexdec(substr($t,2,2))).chr(0x02); // formatage de la taille pour envoyer correctement
			
			if ($i >= 1000)
			{
				
				$send = $in.$data.chr(0x17);
				socket_write($socket, $send, strlen($send));
				
			
				$data = "";
				$send = "";
				time_nanosleep(0, 500000000) ; // 0.5 secondes
				
			}
			else
			{
				
				$send = $in.$data.chr(0x03);
				socket_write($socket, $send, strlen($send));
				

				$data = "";
				$send = "";
				$out = socket_read($socket, 3873);
				time_nanosleep(0, 500000000) ; // 0.5 secondes
				
				
				
			}	
			$i = 0;
		}
		socket_close($socket);	
	}

	//----------------------------------------------------------------------	


?>