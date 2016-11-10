<?php
	//ühendan sessiooniga

	/** @noinspection PhpIncludeInspection */
	require("../functions.php");

	require("../class/Helper.class.php");
	$Helper = new Helper($mysqli);

	require("../class/Event.class.php");
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

<?php require("../header.php"); ?>
<div class="container">
	<div class="row">


		<h3>
		Tere tulemast <?=$_SESSION["userEmail"];?>!
		<a href="?logout=1">Logi Välja</a>
		</h3>

		<div class="col-sm-4 col-md-4">
			<html>


				<h1>Mis sarja vaatasid?</h1>
					<?php echo $saveShowError ; ?>
					<br>
				<form method="POST">

					<label>Sarja nimi:</label>

					<div class="form-group">
					<input class="form-control" name="show" type = "text">
					</div>

					<label>Hooaeg:</label>

					<div class="form-group">
					<input class="form-control" name="season" type = "number" >
					</div>

					<label>Episood:</label>

					<div class="form-group">
					<input class="form-control" name="episode" type = "number" >
					</div>

					<input class="btn-lg btn-success btn-sm hidden-xs" type = "submit" value = "SAVE" >
					<input class="btn btn-success btn-sm btn-block visible-xs-block" type = "submit" value = "SAVE" >
				</form>

		</div>

		<div class="col-sm-4 col-sm-offset-2 col-md-4 col-md-offset-3">
			<h2>Arhiiv</h2>

			<form>
				<div class="form-group">
				<input class="form-control" type="search" name="q" value="<?=$q;?>">
				</div>

				<input class="btn-lg btn-primary btn-sm hidden-xs" type="submit" value="Otsi">
				<input class="btn btn-primary btn-sm btn-block visible-xs-block" type = "submit" value = "Otsi" >
			</form>



			<?php


				$html = "<table class='table table-bordered table-condensed'>";

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

						$html .= "<th> Muuda </a> </th>";

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
		</div>

	</div>

</div>
	<?php require("../footer.php"); ?>