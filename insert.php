<?php
$servername = "masterdb.c5xlt0hbrsol.us-east-1.rds.amazonaws.com";
$username = "masterdb";
$password = "masterdb";
$dbname = "masterdb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "INSERT INTO mastertable (firstname, lastname)
VALUES ('$_POST[fname]', '$_POST[lname]')";

$sql1 = "select * from mastertable";
$result = $conn->query($sql1);

if ($conn->query($sql) === TRUE) {
    echo "New record created successfully";

    if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo " - Name: " . $row["firstname"]. " " . $row["lastname"]. "<br>";
          }
     } else {
    echo "0 results";
}

} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();

echo "<html><a href=test.php class='button'>GoBack</a></html>";

?>
