<?php
session_start();
if (isset($_SESSION['auyfgigafa'])) {
    // echo "Logged in";
} else {
    // echo "Not logged In";
    header("Location: login.php");
}
?>
<?php
include("../includes/header.php");

$pageID = $_GET['id'];
if (!isset($pageID)) {
    $tmp = mysqli_query($con, "Select id from recipeapp limit 1");
    while ($row = mysqli_fetch_array($tmp)) {
        $pageID = $row['id'];
    }
}

$title = trim($_POST['title']);
$author = trim($_POST['author']);
$prepTime = trim($_POST['prepTime']);
$cookTime = trim($_POST['cookTime']);
$servings = trim($_POST['servings']);
$difficulty = ($_POST['difficulty']);
$instructions = trim($_POST['instructions']);
$ingredients = trim($_POST['ingredients']);
$webSource = trim($_POST['source']);
$description = trim($_POST['description']);

if (isset($_POST['submit'])) {
    $valid = 1;
    $msgPreError = "\n<div class=\"alert alert-danger\" role=\"alert\">";
    $msgPreSuccess = "\n<div class=\"alert alert-primary\" role=\"alert\">";
    $msgPost = "\n</div>";
    $totalTime = $prepTime + $cookTime;

    if ((strlen($title) < 10) || (strlen($title) > 200)) {
        $valid = 0;
        $valTitleMsg .= "Please enter a title between 10 to 200 characters.";
    }

    if ((strlen($author) < 10) || (strlen($author) > 200)) {
        $valid = 0;
        $valAuthorMsg .= "Please enter an author's name that's between 10 to 200 characters.";
    }

    if ($prepTime < 0 || $prepTime > 60) {
        $valid = 0;
        $valPrepTimeMsg .= "Please enter a preperation time that's between 0 to 60 minutes.";
    }

    if ($cookTime < 0 || $cookTime > 60) {
        $valid = 0;
        $valCookTimeMsg .= "Please enter a cook time that's between 1 & 60 minutes.";
    }

    if ($servings < 1 || $servings > 100) {
        $valid = 0;
        $valServingsMsg .= "Please enter a servings ammount that's between 1 to 100 servings.";
    }

    if ((strlen($description) < 10) || (strlen($description) > 400)) {
        $valid = 0;
        $valDescriptionMsg .= "Please enter a description between 10 to 400 characters.";
    }

    if ((strlen($ingredients) < 10)) {
        $valid = 0;
        $valIngredientsMsg .= "Please enter some ingredients (minimum of 10).";
    }

    if ((strlen($instructions) < 10)) {
        $valid = 0;
        $valInstructionsMsg .= "Please enter some instructions (minimum of 10).";
    }

    if (!filter_var($webSource, FILTER_VALIDATE_URL)) {
        $valid = 0;
        $valSourceMsg .= "Please enter a valid source. Ex: https://website.com";
    }

    $webSource = filter_var($webSource, FILTER_SANITIZE_URL);

    if ($difficulty == "") {
        $valid = 0;
        $valDifficultyMsg .= "Please select a difficulty.";
    }

    if ($totalTime > 60) {
        $valid = 0;
        $valTotalTimeMsg = "Please enter a recipe that takes less than 60 minutes to prep and cook.";
    }

    if ($valid == 1) {
        mysqli_query($con, "UPDATE recipeapp set title = '$title', author = '$author', difficulty_level = '$difficulty', prep_time = '$prepTime', cook_time = '$cookTime', total_time = '$totalTime', servings = '$servings', ingredients = '$ingredients', directions = '$instructions', resp_description = '$description', source = '$webSource'")  or die(mysqli_error($con));
        $msgSuccess = "Success.The recipe has been updated.";
    }
}

$result = mysqli_query($con, "Select * from recipeapp");
?>
<div class="jumbotron clearfix">
    <h1 class="text-center">Edit Recipe</h1>
</div>
<?php
if ($msgSuccess) {
    echo $msgPreSuccess . $msgSuccess . $msgPost;
}
?>
<div class="box">
    <?php while ($row = mysqli_fetch_array($result)) : ?>
        <div class="edit">
            <a href="edit.php?id=<?php echo $row['id'] ?> ">
                <img src="uploads/editthumbs/<?php echo $row["picture"]; ?>">
            </a>
            <h4 class="edit-h4"><?php echo substr($row["title"], 0, 10) ?></h4>
        </div>
    <?php endwhile; ?>
</div>
<?php

$result = mysqli_query($con, "Select * from recipeapp where id='$pageID'");
while ($row = mysqli_fetch_array($result)) {
    $title = $row['title'];
    $description = $row['resp_description'];
    $author = $row['author'];
    $prepTime = $row['prep_time'];
    $cookTime = $row['cook_time'];
    $servings = $row['servings'];
    $difficulty = $row['difficulty_level'];
    $instructions = $row['directions'];
    $ingredients = $row['ingredients'];
    $webSource = $row['source'];
}
?>
<form id="myform" name="myform" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
    <div class=" form-group row">
        <div class="col-md-6">
            <label for="title">Title:</label>
            <input type=" text" name="title" class="form-control" value="<?php echo $title ?>">
            <?php
            if ($valTitleMsg) {
                echo $msgPreError . $valTitleMsg . $msgPost;
            }
            ?>

            <label for="author">Author:</label>
            <input type="text" name="author" class="form-control" value="<?php echo $author ?>">
            <?php
            if ($valAuthorMsg) {
                echo $msgPreError . $valAuthorMsg . $msgPost;
            }
            ?>

            <label for="description">Description:</label>
            <textarea name="description" rows="5" class="form-control"><?php if ($description) {
                                                                            echo $description;
                                                                        } ?></textarea>
            <?php
            if ($valDescriptionMsg) {
                echo $msgPreError . $valDescriptionMsg . $msgPost;
            }
            ?>
        </div>
        <div class="col-md-6">
            <label for="prepTime">Preperation Time:</label>
            <input type="number" name="prepTime" class="form-control" min="0" value="<?php echo $prepTime ?>">
            <?php
            if ($valPrepTimeMsg) {
                echo $msgPreError . $valPrepTimeMsg . $msgPost;
            }
            if ($valTotalTimeMsg) {
                echo $msgPreError . $valTotalTimeMsg . $msgPost;
            }
            ?>
            <label for="cookTime">Cook Time:</label>
            <input type="number" name="cookTime" class="form-control" min="0" max="60" value="<?php echo $cookTime ?>">
            <?php
            if ($valCookTimeMsg) {
                echo $msgPreError . $valCookTimeMsg . $msgPost;
            }
            if ($valTotalTimeMsg) {
                echo $msgPreError . $valTotalTimeMsg . $msgPost;
            }
            ?>
            <label for="servings">Servings:</label>
            <input type="number" name="servings" class="form-control" min="1" max="100" value="<?php echo $servings ?>">
            <?php
            if ($valServingsMsg) {
                echo $msgPreError . $valServingsMsg . $msgPost;
            }
            ?>

            <br>
            <select name="difficulty" class="form-control">
                <option value="" class="text-center" disabled selected>--- Please select a difficulty ---</option>
                <option value="Easy" <?php if (isset($difficulty) && $difficulty == "Easy") {
                                            echo "selected";
                                        } ?>>Easy</option>
                <option value="Intermediate" <?php if (isset($difficulty) && $difficulty == "Intermediate") {
                                                    echo "selected";
                                                } ?>>Intermediate</option>
                <option value="Hard" <?php if (isset($difficulty) && $difficulty == "Hard") {
                                            echo "selected";
                                        } ?>>Hard</option>
            </select>
            <?php
            if ($valDifficultyMsg) {
                echo $msgPreError . $valDifficultyMsg . $msgPost;
            }
            ?>
        </div>
        <div class="col-md-12">
            <label for="ingredients">Ingredients:</label>
            <textarea name="ingredients" rows="10" class="form-control"><?php if ($ingredients) {
                                                                            echo $ingredients;
                                                                        } ?></textarea>
            <?php
            if ($valIngredientsMsg) {
                echo $msgPreError . $valIngredientsMsg . $msgPost;
            }
            ?>
            <br>
        </div>
        <div class="col-md-12">
            <label for="instructions">Instructions:</label>
            <textarea name="instructions" rows="10" class="form-control"><?php if ($instructions) {
                                                                                echo $instructions;
                                                                            } ?></textarea>
            <?php
            if ($valInstructionsMsg) {
                echo $msgPreError . $valInstructionsMsg . $msgPost;
            }
            ?>
            <br>
        </div>
        <div class="col-md-12">
            <label for="source">Source:</label>
            <input type="source" name="source" class="form-control" value="<?php echo $webSource ?>">
            <?php
            if ($valSourceMsg) {
                echo $msgPreError . $valSourceMsg . $msgPost;
            }
            ?>
        </div>
        <div class="col text-center">
            <br>
            <label for="submit">&nbsp;</label>
            <input type="submit" name="submit" class="btn btn-info" value="Submit">
            <a href="delete.php?id=<?php echo $pageID ?>" onclick="return confirm('Are you sure you want to delete this recipe?')" class="btn btn-danger">Delete Recipe</a>
        </div>
    </div>
</form>
<?php
include("../includes/footer.php");
?>