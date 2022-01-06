<?php
	session_start();

	if (!isset($_SESSION['username'])) { //Si la session existe
	    echo "<script>var session = 0;</script>";
	}else{
		echo "<script>var session = 1;</script>";
	}

	// On détruit la machine.

	if (session_destroy()) {

	    // On redirige le client

	    header("Location: ../index.php");

	    exit;
	}
?>

