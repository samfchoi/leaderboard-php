<?php
/* ============================================================================
 (c) Copyright 2014 Hewlett-Packard Development Company, L.P.
Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights to
use, copy, modify, merge,publish, distribute, sublicense, and/or sell copies of
the Software, and to permit persons to whom the Software is furnished to do so,
subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
============================================================================ */

// This simple application accepts entries for a leaderboard. Once the first
// entry has been submitted, the leaderboard's table will be printed out.

require('vendor/autoload.php');

// Use the VCAP_SERVICES environment variable. This variable contains
// credentials for all services bound to the application. In this case, MySQL
// is the only bound service.
$services = getenv('VCAP_SERVICES');

$json = json_decode($services, TRUE);

// Parse the json string that we got from VCAP_SERVICES
// The only top-level node will be mysql since it's the only service bound to
// this sample app.
// Note that some of the fields are optional but are included for reference
$dbname = $json['mysql'][0]['credentials']['name'];
$hostname = $json['mysql'][0]['credentials']['hostname'];
$user = $json['mysql'][0]['credentials']['user'];
$password = $json['mysql'][0]['credentials']['password'];
$port = $json['mysql'][0]['credentials']['port'];

// Create a connection to MySQL
//echo "\n <br> Connecting to MySQL...";
$connection = new mysqli($hostname, $user, $password, $dbname, $port);

// Check connection
if ($connection->connect_error) {
    echo "\n <br> Failed to connect to MySQL: " . mysqli_connect_error();
} else {
    //echo "\n <br>Connected to MySQL!";
}

// Free up the memory that was allocated to the result
//mysqli_free_result($result);

// Delete table if already created
//$sql = "DROP TABLE IF EXISTS Leaderboard";
//echo "\n <br> Table would be deleted now!";

// if ($connection->query($sql) === TRUE) {
//     echo "\n <br> Existing table Leaderboard deleted successfully";
// } else {
//     echo "\n <br> Error deleting table: " . $connection->error;
// }

// Create a table
$sql = "CREATE TABLE Leaderboard (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
name VARCHAR(40) NOT NULL,
region VARCHAR(40) NOT NULL,
score VARCHAR(40) NOT NULL,
reg_date TIMESTAMP
)";

// Use for debugging if the DB service is having issues.
if ($connection->query($sql) === TRUE) {
    //echo "\n <br> Table Leaderboard created successfully";
} else {
    //echo "\n <br> Error creating table: " . $connection->error;
}

// Insert test data if desired
// $sql = "INSERT INTO Leaderboard (name, region, score)
// VALUES ('John', 'Americas', '4000')";

// if ($connection->query($sql) === TRUE) {
//     echo "\n <br> New record created successfully";
// } else {
//     echo "\n <br> Error: " . $sql . "<br>" . $connection->error;
// }

// Print the table
// echo "\n <br> Printing the table";

// $sql = "SELECT id, name, region, score FROM Leaderboard";
// $result = $connection->query($sql);

// if ($result->num_rows > 0) {
//     // output data of each row
//     while($row = $result->fetch_assoc()) {
//         echo "\n <br> id: " . $row["id"]. " - Name: " . $row["name"]. " " . $row["region"]. " " . $row["score"] . "<br>";
//     }
// } else {
//     echo "\n <br> 0 results";
// }
?>

<html>
<head>
    <title>Title</title>
    <style type="text/css">
    table {
        margin: 8px;
    }
	h1 {
    	color: green;
    	text-align: center;
	}
    </style>
</head>
<body>

    <h1>Leaderboard PHP!</h1>

    <form method="post">
    Add a score: <br>
    Name: <input type="text" name="name"><br>
    Region: <input type="text" name="region"><br>
    Score: <input type="text" name="score"><br>
    <input type="submit" value="Submit Score">
    </form>
</body>
</html>

<?php
if(isset($_POST['score']))
{
    $name = $_POST['name'];
    $region = $_POST['region'];
    $score = $_POST['score'];
    //echo "Attempting to submit new score. Name: $name Region: $region Score: $score";

    // Insert the new score
    $sql = "INSERT INTO Leaderboard (name, region, score)
            VALUES ('$name', '$region', '$score')";

    if ($connection->query($sql) === TRUE) {
        //echo "\n <br> New score added successfully";
    } else {
        echo "\n <br> Error: " . $sql . "<br>" . $connection->error;
    }

    // Print the table
    //echo "\n <br> Printing the table";

    $sql = "SELECT name, region, score FROM Leaderboard ORDER BY score DESC";
    $result = $connection->query($sql);

    if ($result->num_rows > 0) {
        echo"
        <table style='width:100%' border='1'>
        <tr>
        <th>Name</th>
        <th>Region</th>
        <th>Points</th>
        </tr>";
        // output data of each row
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['name'] . "</td>" . "<td>" . $row['region'] . "</td>" . "<td>" . $row['score'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "\n <br> No results in the table!";
    }
}
?>
