<?php
	require("/home/taneotsa/config.php");
	
	//see fail peab olema seotud kõigiga, kus tahame sessiooni kasutada
	//saab kasutada nüüd $_SESSION muutujat
	
	session_start();


	$database = "if16_taneotsa_4";

    $mysqli = new mysqli($serverHost, $serverUsername, $serverPassword, $database);

?>