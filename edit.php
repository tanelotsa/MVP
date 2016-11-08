<?php

    require("functions.php");

    if(isset($_POST["update"])){

        $Event->updateShow($Helper->cleanInput($_POST["id"]), $Helper->cleanInput($_POST["show"]), $Helper->cleanInput($_POST["season"]), $Helper->cleanInput($_POST["episode"]));

        header("Location: edit.php?id=".$_POST["id"]."&success=true");
        exit();

    }

    //saadan kaasa id
    $s = $Event->getSingleShowData($_GET["id"]);
    var_dump($s);


?>

<br><br>
<a href="data.php"> tagasi </a>

<h2>Muuda sisestust</h2>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" >

    <input type="hidden" name="id" value="<?=$_GET["id"];?>" >

    <label for="show" >Sari</label><br>
    <input id="show" name="show" type="text" value="<?php echo $s->show;?>" ><br><br>

    <label for="season" >Hooaeg</label><br>
    <input id="season" name="season" type="number" value="<?=$s->season;?>"><br><br>

    <label for="episode" >Episood</label><br>
    <input id="episde" name="episode" type="number" value="<?=$s->episode;?>"><br><br>

    <input type="submit" name="update" value="Salvesta">
</form>
<a href="?id=<?=$_GET["id"];?>&delete=true">kustuta</a>