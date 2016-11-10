<?php
class Interest
{

    private $connection;

    // käivitatakse siis kui on = new User(see jõuab siia)
    function __construct($mysqli)
    {
        //this viitab sellele klassile ja selle klassi muutujale
        $this->connection = $mysqli;
    }

    function getAllInterests()
    {

        $stmt = $this->connection->prepare("SELECT id, interest	FROM interests");
        echo $this->connection->error;

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

        return $result;
    }

    function getUserInterests()
    {

        $stmt = $this->connection->prepare("
		SELECT interest 
		FROM interests
		JOIN user_interests 
		ON user_interests.interest_id=interests.id
		WHERE user_interests.user_id=?
	");

        echo $this->connection->error;

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

        return $result;
    }

    function saveInterest($interest)
    {

        $stmt = $this->connection->prepare("INSERT INTO interests (interest) VALUES (?)");

        echo $this->connection->error;

        $stmt->bind_param("s", $interest);

        if ($stmt->execute()) {
            echo "salvestamine õnnestus";
        } else {
            echo "ERROR " . $stmt->error;
        }

        $stmt->close();

    }

    function saveUserInterest($interest)
    {

        $stmt = $this->connection->prepare("SELECT id FROM user_interests WHERE user_id=? AND interest_id=?");

        $stmt->bind_param("ii", $_SESSION ["userId"], $interest);

        $stmt->execute();

        if ($stmt->fetch()) {

            //oli olemas
            echo "juba olemas";
            return;
            //pärast returni enam koodi ei vaadata

            $stmt->close();

        }

        $stmt = $this->connection->prepare("INSERT INTO user_interests (user_id, interest_id) VALUES (?,?)");

        echo $this->connection->error;

        $stmt->bind_param("ii", $_SESSION ["userId"], $interest);

        if ($stmt->execute()) {
            echo "salvestamine õnnestus";
        } else {
            echo "ERROR " . $stmt->error;
        }

        $stmt->close();
    }






}
?>