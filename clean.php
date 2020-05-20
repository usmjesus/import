<?php

$servername = 'localhost';
$username = 'root';
$password = "root";
$dbname = 'snkrsdenbd';
$mysqli = new mysqli($servername, $username, $password, $dbname);


$query = "SELECT id,name,image  FROM product ORDER BY id asc ";
$result = $mysqli->query($query);

while ($row = $result->fetch_assoc()) {

    

    if (!file_exists("products/".$row['image'])) {

    	echo $row['name']." - ".$row['image']."<br>";
        //$sql = "DELETE FROM product WHERE id='".$row['id']."'";
    	//mysqli_query($mysqli, $sql);

    }



}
$mysqli->close();


?>

