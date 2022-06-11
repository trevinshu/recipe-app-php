<?php
include("../includes/mysql_connect.php");

$recipeID = $_GET["id"];

if (is_numeric($recipeID)) {
    mysqli_query($con, "delete from recipeapp where id = $recipeID") or die(mysqli_error($con));
    header("Location:edit.php");
}
