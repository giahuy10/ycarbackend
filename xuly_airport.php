<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "datxe";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$sql = "INSERT INTO gfwu2_jobs (start_point, end_point, distance_km)
VALUES ('".$_POST['start_point']."', '".$_POST['end_point']."', '".$_POST['distance_km']."')";

if ($conn->query($sql) === TRUE) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>