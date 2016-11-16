<?php
class Event{

    private $connection;

    // käivitatakse siis kui on = new User(see jõuab siia)
    function __construct($mysqli){
        //this viitab sellele klassile ja selle klassi muutujale
        $this->connection = $mysqli;
    }

    function deleteShow($id){

        $database = "if16_taneotsa_4";

        $stmt = $this->connection ->prepare("
		UPDATE tvshows SET deleted=NOW()
		WHERE id=? AND deleted IS NULL");
        $stmt->bind_param("i",$id);

        // kas õnnestus salvestada
        if($stmt->execute()){
            // õnnestus
            echo "salvestus õnnestus!";
        }

        $stmt->close();

    }



    function getAllShows($q, $sort, $order) {

        $allowedSort = ["id", "showname", "season", "episode"];


        // sort ei kuulu lubatud tulpade sisse
        if(!in_array($sort, $allowedSort)){
            $sort = "id";
        }

        $orderBy = "ASC";

        if($order == "DESC") {
            $orderBy = "DESC";
        }

        //echo "Sorteerin: ".$sort." ".$orderBy." ";

        if ($q != "") {
            //otsin
            echo "otsin: ".$q;

            $stmt = $this->connection->prepare("
              SELECT id, showname, season, episode FROM tvshows Where userid = ? AND deleted IS NULL AND ( showname LIKE ? OR season LIKE ? OR episode like ?) ORDER BY $sort $orderBy
              ");
            $searchWord = "%".$q."%";
            $stmt->bind_param("isss", $_SESSION ["userId"],$searchWord, $searchWord, $searchWord);
        } else {
            //ei otsi
            $stmt = $this->connection->prepare("SELECT id, showname, season, episode FROM tvshows Where userid = ? AND deleted IS NULL ORDER BY $sort $orderBy");

            $stmt->bind_param("i", $_SESSION ["userId"]);
        }

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

    function getSingleShowData($edit_id){

        $stmt = $this->connection->prepare("SELECT showname, season, episode FROM tvshows WHERE id=?  AND deleted IS NULL");

        $stmt->bind_param("i", $edit_id);
        $stmt->bind_result($show, $season, $episode);
        $stmt->execute();

        //tekitan objekti
        $s = new Stdclass();

        //saime ühe rea andmeid
        if($stmt->fetch()){
            // saan siin alles kasutada bind_result muutujaid
            $s->show = $show;
            $s->season = $season;
            $s->episode = $episode;

        }else{
            // ei saanud rida andmeid kätte
            // sellist id'd ei ole olemas
            // see rida võib olla kustutatud
            header("Location: data.php");
            exit();
        }

        $stmt->close();

        return $s;

    }

    function updateShow($id, $show, $season, $episode){

        $stmt = $this->connection->prepare("UPDATE tvshows SET showname=?, season=?, episode=? WHERE id=?  AND deleted IS NULL");
        $stmt->bind_param("siii",$show, $season, $episode, $id);

        // kas õnnestus salvestada
        if($stmt->execute()){
            // õnnestus
            echo "salvestus õnnestus!";
        }

        $stmt->close();

    }

    function saveShow($show,$season,$episode) {

        $stmt = $this->connection ->prepare("INSERT INTO tvshows (showname, season, episode, userid) VALUE(?,?,?,?)");
        echo $this->connection->error;

        $stmt->bind_param("siii", $show, $season, $episode, $_SESSION ["userId"]);

        if($stmt->execute() ) {
            echo "Õnnestus!","<br>";
        } else{
            echo "ERROR".$stmterror;
        }

    }

}
?>