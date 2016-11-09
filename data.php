<?php
	//ühendan sessiooniga

	/** @noinspection PhpIncludeInspection */
	require("functions.php");

	require("Helper.class.php");
	$Helper = new Helper($mysqli);

	require("Event.class.php");
	$Event = new Event($mysqli);

	//kui ei ole sisseloginud, suunan login lehele
	if (!isset($_SESSION["userId"])) {
		header("Location: login.php");  //iga headeri järele tuleks lisada exit
		exit();
	}
		
	
	$saveShowError = "";
	
	//kas aadressi real on logout
	if (isset($_GET["logout"])) {
		
		session_destroy();
		
		header("Location: login.php");
		exit();
	}
	
	if ( 	 isset($_POST["show"]) &&
			 isset($_POST["season"]) &&
			 isset($_POST["episode"]) &&
			 !empty($_POST["show"]) &&
			 !empty($_POST["season"]) &&
			 !empty($_POST["episode"]) 
		
		) {

		$Event->saveShow($Helper->cleanInput($_POST["show"]),$Helper->cleanInput($_POST["season"]),$Helper->cleanInput($_POST["episode"]));
			
		} else {
			$saveShowError = "Täida väljad !";
		}

		if (isset($_GET["q"])) {

			$q = $_GET["q"];

		} else {
			//ei otsi
			$q = "";
		}

        //vaikimisi, kui keegi mingit linki ei vajuta
        $sort = "id";
        $order = "ASC";

        if (isset($_GET["sort"]) && isset($_GET["order"])) {
            $sort = $_GET["sort"];
            $order = $_GET["order"];
        }

        $shows = $Event->getAllShows($q, $sort, $order);

		//echo "<pre>";
		//var_dump($shows);
		//echo "</pre>";
		
?>

<h1>Data</h1>

<p>

	Tere tulemast <a href="user.php"><?=$_SESSION["userEmail"];?></a>!
	<a href="?logout=1">Logi Välja</a>

</p>

<html>

<body>

	<h1>Mis sarja vaatasid?</h1>
		<?php echo $saveShowError ; ?> 
		<br>
	<form method="POST">
	
		<label>Sarja nimi:</label> 
		
		<br>
		
		<input name="show" type = "text">
		
		<br><br>
		
		<label>Hooaeg:</label>
		
		<br>
		
		<input name="season" type = "number" >
	
		<br><br>
		
		<label>Episood:</label>
		
		<br>
		
		<input name="episode" type = "number" >
	
		<br><br>
		
		<input type = "submit" value = "SAVE" >
		
	</form>
	
	<h2>Arhiiv</h2>

	<form>
		<input type="search" name="q" value="<?=$q;?>">
		<input type="submit" value="Otsi">

	</form>

<?php
	
	
	$html = "<table>";
	
		$html .= "<tr>";

        $orderId = "ASC";
        if (isset($_GET["order"]) &&
            $_GET["order"] == "ASC" &&
            $_GET["sort"] == "id" ) {

            $orderId = "DESC";
        }

        $orderShow = "ASC";
        if (isset($_GET["order"]) &&
            $_GET["order"] == "ASC" &&
            $_GET["sort"] == "show" ) {

            $orderShow = "DESC";
        }

        $orderSeason = "ASC";
        if (isset($_GET["order"]) &&
            $_GET["order"] == "ASC" &&
            $_GET["sort"] == "season" ) {

            $orderSeason = "DESC";
        }

        $orderEpisode = "ASC";
        if (isset($_GET["order"]) &&
            $_GET["order"] == "ASC" &&
            $_GET["sort"] == "episode" ) {

            $orderEpisode = "DESC";
        }

            $html .= "<th> <a href='?q=".$q."&sort=id&order=".$orderId."'> ID </a> </th>";

            $html .= "<th> <a href='?q=".$q."&sort=show&order=".$orderShow."'> Sari </a> </th>";

            $html .= "<th> <a href='?q=".$q."&sort=season&order=".$orderSeason."'> Hooaeg </a> </th>";

            $html .= "<th> <a href='?q=".$q."&sort=episode&order=".$orderEpisode."'> Episood </a> </th>";

		$html .= "</tr>";
		
		foreach ($shows as $s) {
			
			$html .= "<tr>";
				$html .= "<td>".$s->id."</td>";
				$html .= "<td>".$s->show."</td>";
				$html .= "<td>".$s->season."</td>";
				$html .= "<td>".$s->episode."</td>";
				$html .= "<td><a href='edit.php?id=".$s->id."'>Muuda</a></td>";
			$html .= "</tr>";
			
		}
		
	$html .= "</table>";
	
	echo $html;	
	
	
?>
	

	
</body>	
</html>