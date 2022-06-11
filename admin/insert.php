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

$originalsFolder = "uploads/originals/";
$thumbsFolder = "uploads/thumbs/";
$thumbEditfolder = "uploads/editthumbs/";
$displayFolder = "uploads/display/";

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
$filename =  $_FILES['file']['name'];
$imageFileType = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

function createSquareImageCopy($file, $folder, $newWidth)
{
	global $imageFileType;
	$thumb_width = $newWidth;
	$thumb_height = $newWidth;

	list($width, $height) = getimagesize($file);

	$original_aspect = $width / $height;
	$thumb_aspect = $thumb_width / $thumb_height;

	if ($original_aspect >= $thumb_aspect) {
		$new_height = $thumb_height;
		$new_width = $width / ($height / $thumb_height);
	} else {
		$new_width = $thumb_width;
		$new_height = $height / ($width / $thumb_width);
	}

	if ($imageFileType == "jpg" || $imageFileType == "jpeg") {
		$source = imagecreatefromjpeg($file);
	} else if ($imageFileType == "png") {

		$source = imagecreatefrompng($file);
	}

	$thumb = imagecreatetruecolor($thumb_width, $thumb_height);

	imagecopyresampled(
		$thumb,
		$source,
		0 - ($new_width - $thumb_width) / 2,
		0 - ($new_height - $thumb_height) / 2,
		0,
		0,
		$new_width,
		$new_height,
		$width,
		$height
	);

	$newFileName = $folder . "/" . basename($file);

	if ($imageFileType == "jpg" || $imageFileType == "jpeg") {
		imagejpeg($thumb, $newFileName, 80);
	} else if ($imageFileType == "png") {
		imagepng($thumb, $newFileName, 2);
	}
}

if (isset($_POST['submit'])) {
	$valid = 1;
	$msgPreError = "\n<div class=\"alert alert-danger\" role=\"alert\">";
	$msgPreSuccess = "\n<div class=\"alert alert-primary\" role=\"alert\">";
	$msgPost = "\n</div>";
	$allowed_extensions = array("image/png", "image/jpeg");

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
		$valCookTimeMsg .= "Please enter a cook time that's between 0 & 60 minutes.";
	}

	if ($servings < 1 || $servings > 100) {
		$valid = 0;
		$valServingsMsg .= "Please enter a servings ammount that's between 1 to 400 servings.";
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

	if ($_FILES['file']['size'] / 1024 / 1024 > 10) {
		$valid = 0;
		$valMaxSizeMsg .= "File too large. Upload lesser than 10MB";
	}

	if ($imageFileType != "jpg" && $imageFileType != "jpeg" && $imageFileType != "png") {
		$valid = 0;
		$valFileTypeMsg .= "Only JPG, JPEG & PNG images are allowed";
	}

	if ($filename == "") {
		$valid = 0;
		$valNoFileMsg = "You have not selected a file. Please select a file.";
	}

	if ($totalTime > 60) {
		$valid = 0;
		$valTotalTimeMsg = "Please enter a recipe that takes less than 60 minutes to prep and cook.";
	}



	if ($valid == 1) {
		$temp = explode(".", $_FILES["file"]["name"]);
		$renameFile = uniqid() . '.' . end($temp);
		$totalTime = $prepTime + $cookTime;
		if (move_uploaded_file(($_FILES['file']['tmp_name']), $uploads . $originalsFolder . $renameFile)) {
			$thisFile =  $uploads . $originalsFolder . $renameFile;
			createSquareImageCopy($thisFile, $thumbEditfolder, 50);
			createSquareImageCopy($thisFile, $thumbsFolder, 150);
			createSquareImageCopy($thisFile, $displayFolder, 600);

			mysqli_query($con, "INSERT INTO recipeapp(title, author, difficulty_level, prep_time, cook_time, total_time, servings, ingredients, directions, resp_description, source, picture) VALUES('$title', '$author', '$difficulty', '$prepTime', '$cookTime', '$totalTime', '$servings', '$ingredients', '$instructions', '$description', '$webSource', '$renameFile')") or die(mysqli_error($con));
			$msgSuccess = "Success. A new recipe has been added.";
		}
	}
}

?>
<div class="jumbotron clearfix">
	<h1 class="text-center">Insert Recipe:</h1>
</div>
<?php
if ($msgSuccess) {
	echo $msgPreSuccess . $msgSuccess . $msgPost;
}
?>
<form id="myform" name="myform" method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" enctype="multipart/form-data">
	<div class="form-group row">
		<div class="col-md-6">
			<label for="title">Title:</label>
			<input type="text" name="title" class="form-control" value="<?php echo $title ?>">
			<?php
			if ($valTitleMsg) {
				echo $msgPreError . $valTitleMsg . $msgPost;
			}
			?>

			<label for="title">Author:</label>
			<input type="text" name="author" class="form-control" value="<?php echo $author ?>">
			<?php
			if ($valAuthorMsg) {
				echo $msgPreError . $valAuthorMsg . $msgPost;
			}
			?>

			<label for="description">Description:</label>
			<textarea name="description" rows="5" class="form-control"><?php echo $description ?></textarea>
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
		<div class="col-md-6">
			<label for="ingredients">Ingredients:</label>
			<textarea name="ingredients" rows="10" class="form-control"><?php echo $ingredients ?></textarea>
			<?php
			if ($valIngredientsMsg) {
				echo $msgPreError . $valIngredientsMsg . $msgPost;
			}
			?>
			<br>
		</div>
		<div class=" col-md-6">
			<label for="instructions">Instructions:</label>
			<textarea name="instructions" rows="10" class="form-control"><?php echo $instructions ?></textarea>
			<?php
			if ($valInstructionsMsg) {
				echo $msgPreError . $valInstructionsMsg . $msgPost;
			}
			?>
			<br>
		</div>
		<div class="col-md-6">
			<label for="source">Source:</label>
			<input type="source" name="source" class="form-control" value="<?php echo $webSource ?>">
			<?php
			if ($valSourceMsg) {
				echo $msgPreError . $valSourceMsg . $msgPost;
			}
			?>
		</div>
		<div class=" col-md-6">
			<label for="file">Upload an Image:</label>
			<input type="file" name="file" class="form-control" value="<?php echo $renameFile ?>">
			<?php
			if ($valMaxSizeMsg) {
				echo $msgPreError . $valMaxSizeMsg . $msgPost;
			}

			if ($valFileTypeMsg) {
				echo $msgPreError . $valFileTypeMsg . $msgPost;
			}

			if ($valNoFileMsg) {
				echo $msgPreError . $valNoFileMsg . $msgPost;
			}
			?>
		</div>
		<div class=" col text-center">
			<br>
			<label for="submit">&nbsp;</label>
			<input type="submit" name="submit" class="btn btn-info" value="Submit">
		</div>
	</div>
</form>

<?php
include("../includes/footer.php");
?>