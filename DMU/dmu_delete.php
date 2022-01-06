<?php
	include 'DMU_Function.php';

	$arbo = recup_arbo();
	$arbo_back = $arbo;
	$data = $_GET['name'];

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

			naviguer_repertoire_uni($arbo, $socket);
			delete($socket, $data);
			
			//------------------------------------------------------------------------

		 	$in = "\x00\x00\x00\x00A_LO" ; 		// Log Out
			envoyer_et_afficher($in,$socket) ; 	// T_OK

			//------------------------------------------------------------------------
		}
		
	socket_close($socket);

    header('Location: ./dmu.php');
?>
