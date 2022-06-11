<?php
include("includes/header.php");
include("includes/_functions.php");
$displayID = $_GET["id"];


?>


<?php
$result = mysqli_query($con, "SELECT * FROM recipeapp WHERE id = '$displayID'");
?>
<div class="display-container" method="post">
    <?php while ($row = mysqli_fetch_array($result)) : ?>
        <h2><?php echo $row["title"]; ?></h2><br>
        <img src="admin/uploads/display/<?php echo $row["picture"]; ?>">
        <p><b>Description:</b> <?php echo $row["resp_description"]; ?></p>
        <p><b>Author:</b> <?php echo $row["author"]; ?></p>
        <p><b>Difficulty Level:</b> <?php echo $row["difficulty_level"]; ?></p>
        <p><b>Prep Time:</b> <?php echo $row["prep_time"]; ?> Minutes</p>
        <p><b>Cook Time:</b> <?php echo $row["cook_time"]; ?> Minutes</p>
        <p><b>Total Time:</b> <?php echo $row["total_time"]; ?> Minutes</p>
        <p><b>Servings:</b> <?php echo $row["servings"]; ?> Servings</p>
        <p><b>Ingredients:</b><br> <?php echo nl2br($row["ingredients"]); ?></p>
        <p><b>Directions:</b><br> <?php echo nl2br($row["directions"]); ?></p>
        <p><b>Source:</b> <?php echo (MakeClickableLinks($row["source"])); ?></p>

    <?php endwhile; ?>
</div>
<?php
include("includes/footer.php");
?>