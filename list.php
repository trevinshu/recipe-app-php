<?php
include('includes/header.php');
?>

<?php

$getcount = mysqli_query($con, "SELECT COUNT(*) FROM recipeapp");
$postnum = mysqli_result($getcount, 0);

function mysqli_result($res, $row, $field = 0)
{
    $res->data_seek($row);
    $datarow = $res->fetch_array();
    return $datarow[$field];
}
$limit = 4;
if ($postnum > $limit) {
    $tagend = round($postnum % $limit, 0);
    $splits = round(($postnum - $tagend) / $limit, 0);

    if ($tagend == 0) {
        $num_pages = $splits;
    } else {
        $num_pages = $splits + 1;
    }

    if (isset($_GET['pg'])) {
        $pg = $_GET['pg'];
    } else {
        $pg = 1;
    }
    $startpos = ($pg * $limit) - $limit;
    $limstring = "LIMIT $startpos,$limit";
} else {
    $limstring = "LIMIT 0,$limit";
}


$result = mysqli_query($con, "SELECT * FROM recipeapp ORDER BY title $limstring") or die(mysqli_error($con));
$totalResult = mysqli_query($con, "SELECT * FROM recipeapp") or die(mysqli_error($con));
echo "<div class='col-12'>";
echo "<h2 style='text-align: center; margin-bottom: 1rem;'>All Recipes</h2>";
echo "</div>";
while ($row = mysqli_fetch_array($result)) :
    $id = ($row['id']);
    $title = ($row['title']);
    $picture = ($row['picture']);
    $truncated = substr($title, 0, 15);

    echo "<div class='col-md-6'>";
    echo "<div class='card' style='display: flex; flex-wrap: wrap; margin-bottom: 1rem;'>";
    echo "<div class='card-body'>";
    echo "<h4><a href='display.php?id=$id\' style='text-decoration:none; color: #fff; text-align: center;'>$truncated</a></h4>";
    echo "<a href=\"display.php?id=$id\"><img src='admin/uploads/thumbs/$picture' style='margin:auto; display:block;'/></a>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
endwhile;

echo "<nav class='col-md-12'>";
echo "<ul class='pagination'>";

if ($postnum > $limit) :
    $n = $pg + 1;
    $p = $pg - 1;
    $thisroot = $_SERVER['PHP_SELF'];

    if ($pg > 1) : ?>
        <li class="page-item"><a class="page-link" href="<?php echo "$thisroot?pg=$p" ?>">&laquo;</a>
        <li>
        <?php
    else : ?>
        <li class="page-item disabled"><a class="page-link" href="#">&laquo;</a>
        <li>
            <?php
        endif;

        for ($i = 1; $i <= $num_pages; $i++) :
            if ($i != $pg) : ?>
        <li class="page-item"><a class="page-link" href="<?php echo "$thisroot?pg=$i" ?>"><?php echo $i ?></a></li>
    <?php
            else : ?>
        <li class="page-item active"><a class="page-link" href="#"><?php echo $i ?></a></li>
    <?php
            endif;
        endfor;

        if ($pg < $num_pages) : ?>
    <li class="page-item"><a class="page-link" href="<?php echo "$thisroot?pg=$n" ?>">&raquo;</a>
    <li>
    <?php
        else : ?>
    <li class="page-item disabled"><a class="page-link" href="#">&raquo;</a>
    <li>
<?php
        endif;
    endif;

    echo "</ul>";
    echo "</nav>";
?>
<?php
include("includes/footer.php");
?>