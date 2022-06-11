<?php
include('includes/header.php');

?>
<div class="jumbotron">
  <h2 style="text-align: center;">Recipes Under 60 Minutes.</h2>
  <p>This project display 25 recipes that take less than or equal to 60 minutes to make. I used three search filters to sort my data. The first was a randomly generated recipe, the second was by cook time using a between query and the third way was by difficulty. I could not get any of my extra features to work so I am unable to demonstrate it in this lab assignment. </p>
</div>

<br>
<?php
?>
<div class="row">
  <div class="col-sm-6" style='display: flex; flex-wrap: wrap;'>
    <?php
    $result = mysqli_query($con, "SELECT * FROM recipeapp");

    $displayby = $_GET['displayby'];
    $displayvalue = $_GET['displayvalue'];
    if (isset($displayby) && isset($displayvalue)) {
      $result = mysqli_query($con, "SELECT * FROM recipeapp WHERE $displayby LIKE '$displayvalue' ") or die(mysqli_error($con));
    }
    $min = $_GET['min'];
    $max = $_GET['max'];

    if ($displayby == "total_time") {
      $result = mysqli_query($con, "SELECT * FROM recipeapp WHERE total_time BETWEEN '$min' AND '$max'");
    }

    ?>

    <?php while ($row = mysqli_fetch_array($result)) : ?>

      <?php $id = ($row['id']); ?>
      <?php $title = ($row['title']); ?>
      <?php $author = ($row['author']); ?>
      <?php $difficulty_level = ($row['difficulty_level']); ?>
      <?php $prep_time = ($row['prep_time']); ?>
      <?php $cook_time = ($row['cook_time']); ?>
      <?php $total_time = ($row['total_time']); ?>
      <?php $servings = $row['servings']; ?>
      <?php $directions = ($row['directions']); ?>
      <?php $description = ($row['resp_description']); ?>
      <?php $source = ($row['source']); ?>
      <?php $picture = ($row['picture']); ?>

      <?php
      $truncated = substr($title, 0, 15);
      echo "<div class='col-md-6' style='width: 15rem;'>";
      echo "<div class='card' style='display: flex; flex-wrap: wrap; margin-bottom: 1rem;'>";
      echo "<div class='card-body'>";
      echo "<h4 style='text-align: center;'><a style='text-decoration:none; color: #FFF;' href='display.php?id=$id\'>$truncated</a></h4>";
      echo "<p style='text-align: center;'>$difficulty_level</p>";
      echo "<a href='display.php?id=$id'><img src='admin/uploads/thumbs/$picture' style='margin:auto; display:block;'/></a>";
      echo "</div>";
      echo "</div>";
      echo "</div>";
      ?>

    <?php endwhile; ?>
  </div>
  <div class="col-sm-6">
    <?php
    echo "<h4>Random Recipe:</h4>";
    $randomRecipe = mysqli_query($con, "SELECT * FROM recipeapp ORDER BY RAND() LIMIT 1");
    while ($row = mysqli_fetch_array($randomRecipe)) {
      $title = $row["title"];
      $id = $row["id"];
      $picture = $row["picture"];
      $truncated = substr($title, 0, 15);
      echo "<div class='card' style='max-width: 500px;'>";
      echo "<div class='row no-gutters'>";
      echo "<div class='col-sm-5'>";
      echo "<img src='admin/uploads/thumbs/$picture' class='card-img-top h-100'/>";
      echo "</div>";
      echo "<div class='col-sm-7'>";
      echo "<div class='card-body'>";
      echo "<h4 class='card-title'>$truncated</h4>";
      echo "<p class='card-text'>$description</p>";
      echo "<a href='display.php?id=$id' class='btn btn-primary stretched-link'>View Recipe</a>";
      echo "</div>";
      echo "</div>";
      echo "</div>";
      echo "</div>";
    }
    ?>
    &nbsp;
    <div class="sort-by-time">
      <h4>Sort By Total Cook Time:</h4>
      <br>
      <a href="index.php?displayby=total_time&min=1&max=30" class="btn btn-outline-primary">1 minute to 29 minutes</a>
      <a href="index.php?displayby=total_time&min=31&max=60" class="btn btn-outline-primary">30 minutes to 60 minutes</a>
      <br>
      <br>
      <a href='index.php' class='btn btn-primary stretched-link'>Reset Filter</a>
    </div>
    <br>
    <div class="sort-by-difficulty">
      <h4>Sort By Difficulty:</h4>
      <br>
      <a href="index.php?displayby=difficulty_level&displayvalue=Easy" class="btn btn-outline-primary">Easy</a>
      <a href="index.php?displayby=difficulty_level&displayvalue=Intermediate" class="btn btn-outline-primary">Intermediate</a>
      <a href="index.php?displayby=difficulty_level&displayvalue=Hard" class="btn btn-outline-primary">Hard</a>
      <br>
      <br>
      <a href='index.php' class='btn btn-primary stretched-link'>Reset Filter</a>
    </div>
  </div>
</div>

<?php
include("includes/footer.php");
?>