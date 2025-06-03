<?php
$host = "localhost";
$dbname = "surveyapp";
$user = "root";
$pass = "";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$countResult = $conn->query("SELECT COUNT(*) AS total FROM surveyresponses");
if (!$countResult) {
    die("Count query failed: " . $conn->error);
}
$total = $countResult->fetch_assoc()['total'];

if ($total == 0) {
    echo "<h2 style='text-align:center;'>No Surveys Available</h2>";
    exit;
}

$ageSQL = "
    SELECT 
        AVG(TIMESTAMPDIFF(YEAR, dob, CURDATE())) AS avg_age,
        MIN(TIMESTAMPDIFF(YEAR, dob, CURDATE())) AS min_age,
        MAX(TIMESTAMPDIFF(YEAR, dob, CURDATE())) AS max_age
    FROM surveyresponses
";
$ageQuery = $conn->query($ageSQL);
if (!$ageQuery) {
    die("Age query failed: " . $conn->error);
}
$age = $ageQuery->fetch_assoc();
$avgAge   = isset($age['avg_age']) ? round($age['avg_age'], 1) : 'N/A';
$oldest   = isset($age['max_age']) ? $age['max_age'] : 'N/A';
$youngest = isset($age['min_age']) ? $age['min_age'] : 'N/A';

function getFoodPercentage($conn, $food, $total) {
    $res = $conn->query("SELECT COUNT(*) AS total FROM surveyresponses WHERE favoriteFoods LIKE '%$food%'");
    if (!$res) return 0;
    return round(($res->fetch_assoc()['total'] / $total) * 100, 1);
}
$pizza = getFoodPercentage($conn, "Pizza", $total);
$pasta = getFoodPercentage($conn, "Pasta", $total);
$pap   = getFoodPercentage($conn, "Pap and Wors", $total);

$ratingSQL = "
    SELECT 
        AVG(moviesRating) AS movies, 
        AVG(radioRating) AS radio, 
        AVG(eatoutRating) AS eatout, 
        AVG(tvRating) AS tv
    FROM surveyresponses
";
$ratingsQuery = $conn->query($ratingSQL);
if (!$ratingsQuery) {
    die("Ratings query failed: " . $conn->error);
}
$ratings = $ratingsQuery->fetch_assoc();
$movieRating  = isset($ratings['movies']) ? round($ratings['movies'], 1) : 'N/A';
$radioRating  = isset($ratings['radio']) ? round($ratings['radio'], 1) : 'N/A';
$eatoutRating = isset($ratings['eatout']) ? round($ratings['eatout'], 1) : 'N/A';
$tvRating     = isset($ratings['tv']) ? round($ratings['tv'], 1) : 'N/A';

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Survey Results</title>
  <link rel="stylesheet" href="resultsStyle.css">
</head>
<body>
  <nav>
  <div class="nav-left"></div>
  <div class="nav-right">
    <a href="survey.html">FILL OUT SURVEY</a>
    <a href="results.php">VIEW SURVEY RESULTS</a>
  </div>
</nav>

  <h2>Survey Results</h2>

  <div class="results-grid">
    <div>Total number of surveys:</div>               <div><?= $total ?></div>
    <div>Average Age:</div>                           <div><?= $avgAge ?></div>
    <div>Oldest person who participated in survey:</div>  <div><?= $oldest ?></div>
    <div>Youngest person who participated in survey:</div><div><?= $youngest ?></div>
    <div>Percentage of people who like Pizza:</div>   <div><?= $pizza ?>%</div>
    <div>Percentage of people who like Pasta:</div>   <div><?= $pasta ?>%</div>
    <div>Percentage of people who like Pap and Wors:</div><div><?= $pap ?>%</div>
    <div>People who like to watch movies:</div>       <div><?= $movieRating ?></div>
    <div>People who like to listen to radio:</div>    <div><?= $radioRating ?></div>
    <div>People who like to eat out:</div>            <div><?= $eatoutRating ?></div>
    <div>People who like to watch TV:</div>           <div><?= $tvRating ?></div>
  </div>
</body>
</html>
