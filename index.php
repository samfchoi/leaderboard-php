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

require('vendor/autoload.php');

echo "Leaderboard PHP \n <br>";

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
echo "\n <br> Connecting to MySQL...";
$connection = new mysqli($hostname, $user, $password, $dbname, $port);

// Check connection
if ($connection->connect_error) {
    echo "\n <br> Failed to connect to MySQL: " . mysqli_connect_error();
} else {
    echo "\n <br>Connected to MySQL!";
}

// Execute a simple query to grab a string
$queryString = "SELECT \"Hello World!\" AS result";
$result = mysqli_query($connection, $queryString);
echo "\n <br> Executed $queryString";

// Get the result
$row = mysqli_fetch_assoc($result);

if ($row) {
    echo "\n <br> Result: " . $row['result'];
} else {
    echo "\n <br> Error: Result of query is NULL!";
}

// Free up the memory that was allocated to the result
mysqli_free_result($result);

// Create a table
$sql = "CREATE TABLE MyGuests (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
firstname VARCHAR(30) NOT NULL,
lastname VARCHAR(30) NOT NULL,
email VARCHAR(50),
reg_date TIMESTAMP
)";

if ($connection->query($sql) === TRUE) {
    echo "\n <br> Table MyGuests created successfully";
} else {
    echo "\n <br> Error creating table: " . $connection->error;
}

// Insert some rows
$sql = "INSERT INTO MyGuests (firstname, lastname, email)
VALUES ('John', 'Doe', 'john@example.com')";

if ($connection->query($sql) === TRUE) {
    echo "\n <br> New record created successfully";
} else {
    echo "\n <br> Error: " . $sql . "<br>" . $connection->error;
}

// Print the table
echo "\n <br> Printing the table";

$sql = "SELECT id, firstname, lastname FROM MyGuests";
$result = $connection->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo "\n <br> id: " . $row["id"]. " - Name: " . $row["firstname"]. " " . $row["lastname"]. "<br>";
    }
} else {
    echo "\n <br> 0 results";
}

echo "\n <br> Finished!";

// Finally, close the MySQL connection.
$connection->close();
