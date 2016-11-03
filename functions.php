<?php
	require("../../config.php");
	
	//see fail peab olema seotud kõigiga, kus tahame sessiooni kasutada
	//saab kasutada nüüd $_SESSION muutujat
	
	session_start();


	$database = "if16_taneotsa_4";
	
	function signup($email, $password) {
		
		//loon ühenduse 
		
		
		
		$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
		
		$stmt = $mysqli ->prepare("INSERT INTO user_sample (email, password, gender, birthdate) VALUE(?,?,?,?)");
		echo $mysqli->error;
		//asendan küsimärgid
		//iga märgikohta tuleb lisada üks täht ehk mis tüüpi muutuja on
		//	s - string
		//	i - int,arv
		//  d - double
		$stmt->bind_param("ssss", $email, $password, $_POST["gender"], $_POST["birthdate"]);
		
		
		//täida käsku 
		if($stmt->execute() ) {
			echo "Õnnestus!";			
		} else{
			echo "ERROR".$stmterror;
		}
		
	}
	
	function login ($email, $password) {
	
		$notice = "";
			
	
		$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
		
		$stmt = $mysqli ->prepare("
		
					SELECT id, email, password, created, gender, birthdate
					FROM user_sample
					WHERE email = ? 
					
		");	
		echo $mysqli->error;
	
		//asendan ?
		
		$stmt->bind_param("s", $email);
		
		//rea kohta tulba väärtus
		$stmt->bind_result($id, $emailFromDb, $passwordFromDb, $created, $gender, $birthdate);
		
		$stmt->execute();
		//ainult SELECT'i puhul
		if($stmt->fetch()) {
			//oli olemas, rida käes
			$hash = hash("sha512", $password);
			
			if ($hash == $passwordFromDb) {
				echo "Kasutaja $id logis sisse";
				
				$_SESSION ["userId"] = $id;
				$_SESSION ["userEmail"] = $emailFromDb;
				
				header("Location: data.php");
				
				
				
				
			} else {
				$notice = "Parool vale !";
			}
		
		} else {	
			//ei olnud ühtegi rida
			$notice = "Sellise e-mailiga ".$email." kasutajat ei ole olemas!";
		}
		
		return $notice;
	
	}
	
	function saveShow($show,$season,$episode) {
		
		$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
		
		$stmt = $mysqli ->prepare("INSERT INTO tvshows (showname, season, episode, userid) VALUE(?,?,?,?)");
		echo $mysqli->error;
		
		$stmt->bind_param("siii", $show, $season, $episode, $_SESSION ["userId"]);
	
		if($stmt->execute() ) {
			echo "Õnnestus!","<br>";			
		} else{
			echo "ERROR".$stmterror;
		}
	
	}
	
	function getAllShows () {
		
		$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
		
		$stmt = $mysqli->prepare("SELECT id, showname, season, episode FROM tvshows Where userid = ?");
		
		$stmt->bind_param("i", $_SESSION ["userId"]);
		$stmt->bind_result($id, $show, $season, $episode);
		$stmt->execute ();
		
		$results = array();
		
		//tsükli sisu tehakse niimitu korda , mitu rida sql lausega tuleb
		while($stmt->fetch()) {
			
			$tvshow = new StdClass();
			$tvshow->id = $id;
			$tvshow->show = $show;
			$tvshow->season = $season;
			$tvshow->episode = $episode;
			
			//echo $color."<br>";
			array_push($results,$tvshow);
		}
		
		return $results;
	}

	function cleanInput($input) {
		
		//eemaldab tühikud ümbert
		$input = trim($input);
 		
		//eemaldab teistpidised kaldkriipsud \\
		$input = stripslashes($input);
		
		//html asendab , nt "<" muutub "&lt;"
		$input = htmlspecialchars($input);
		
		return $input;
	}

		function saveInterest ($interest) {
		
		$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);

		$stmt = $mysqli->prepare("INSERT INTO interests (interest) VALUES (?)");
	
		echo $mysqli->error;
		
		$stmt->bind_param("s", $interest);
		
		if($stmt->execute()) {
			echo "salvestamine õnnestus";
		} else {
		 	echo "ERROR ".$stmt->error;
		}
		
		$stmt->close();
		$mysqli->close();
		
	}
	
		function saveUserInterest ($interest) {
			
			$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
			
			$stmt = $mysqli->prepare("SELECT id FROM user_interests WHERE user_id=? AND interest_id=?");
			
			$stmt->bind_param("ii", $_SESSION ["userId"], $interest);
			
			$stmt->execute();
			
		if ($stmt->fetch()) {
			
			//oli olemas
			echo "juba olemas";
			return; 
			//pärast returni enam koodi ei vaadata
			
		$stmt->close();
		
		}
		
		$stmt = $mysqli->prepare("INSERT INTO user_interests (user_id, interest_id) VALUES (?,?)");
	
		echo $mysqli->error;
		
		$stmt->bind_param("ii", $_SESSION ["userId"], $interest);
		
		if($stmt->execute()) {
			echo "salvestamine õnnestus";
		} else {
		 	echo "ERROR ".$stmt->error;
		}
		
		$stmt->close();
		$mysqli->close();
		
	}
	
	function getAllInterests() {
		
		$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
		
		$stmt = $mysqli->prepare("
			SELECT id, interest
			FROM interests
		");
		echo $mysqli->error;
		
		$stmt->bind_result($id, $interest);
		$stmt->execute();
		
		
		//tekitan massiivi
		$result = array();
		
		// tee seda seni, kuni on rida andmeid
		// mis vastab select lausele
		while ($stmt->fetch()) {
			
			//tekitan objekti
			$i = new StdClass();
			
			$i->id = $id;
			$i->interest = $interest;
		
			array_push($result, $i);
		}
		
		$stmt->close();
		$mysqli->close();
		
		return $result;
	}

	function getUserInterests() {
	
	$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
	
	$stmt = $mysqli->prepare("
		SELECT interest 
		FROM interests
		JOIN user_interests 
		ON user_interests.interest_id=interests.id
		WHERE user_interests.user_id=?
	");

	echo $mysqli->error;

	$stmt->bind_param("i", $_SESSION["userid"]);

	$stmt->bind_result($interest);
	$stmt->execute();
	
	
	//tekitan massiivi
	$result = array();
	
	// tee seda seni, kuni on rida andmeid
	// mis vastab select lausele
	while ($stmt->fetch()) {
		
		//tekitan objekti
		$i = new StdClass();
		
		$i->interest = $interest;
	
		array_push($result, $i);
	}
	
	$stmt->close();
	$mysqli->close();
	
	return $result;
}	
	
	/*
	function hello ($firstname, $lastname) {
		return "Tere tulemast ".$firstname." ".$lastname."!";		
	}

	echo hello ("Tanel","Otsa");
	*/
	
?>