<?php
class Helper{

    private $connection;

    // käivitatakse siis kui on = new User(see jõuab siia)
    function __construct($mysqli){
        //this viitab sellele klassile ja selle klassi muutujale
        $this->connection = $mysqli;
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


}
?>