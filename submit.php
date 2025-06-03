<?php
$host = "localhost";
$dbname = "surveyapp";
$user = "root";
$pass = "";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$full_name = htmlspecialchars($_POST['fullname']);
$email = htmlspecialchars($_POST['email']);
$dob = $_POST['dob'];
$contact = htmlspecialchars($_POST['contact']);

// ✅ Age validation
$birthDate = date_create($dob);
$today = date_create('today');
$age = date_diff($birthDate, $today)->y;

if ($age < 5 || $age > 120) {
    die("<h3>Age must be between 5 and 120 years. Please go back and enter a valid date of birth.</h3>");
}

// Continue processing
$foods = isset($_POST['foods']) ? implode(", ", $_POST['foods']) : ""; // Join food array
$movies = $_POST['movies'];
$radio = $_POST['radio'];
$eatout = $_POST['eatout'];
$tv = $_POST['tv'];

// Insert into database
$sql = "INSERT INTO surveyresponses 
        (fullName, email, dob, contactNumber, favoriteFoods, moviesRating, radioRating, eatoutRating, tvRating)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssiii", $full_name, $email, $dob, $contact, $foods, $movies, $radio, $eatout, $tv);

if ($stmt->execute()) {
    echo "<h3>Survey submitted successfully!</h3>";
    echo "<p><a href='index.html'>Submit another</a> | <a href='results.php'>View Results</a></p>";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
