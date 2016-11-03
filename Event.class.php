<?php
class Event{

    private $connection;

    // käivitatakse siis kui on = new User(see jõuab siia)
    function __construct($mysqli){
        //this viitab sellele klassile ja selle klassi muutujale
        $this->connection = $mysqli;
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

    function getAllShows () {

        $stmt = $this->connection->prepare("SELECT id, showname, season, episode FROM tvshows Where userid = ?");

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



}
?>