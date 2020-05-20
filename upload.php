<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" />
    <link rel="stylesheet" href="css/style.css" />
    <link rel="manifest" href="manifest.json" />
    <link rel="apple-touch-icon" sizes="152x152" href="/images/apple-touch-icon.png" />
    <script src="js/app.js"></script>
    <title>PWA Camera</title>
    <style type="text/css">
    body{
      font-size: 20px;
    }
</style>
  </head>
  <body>

<?php
include('simple_html_dom.php');


$filename   = uniqid() . "_" . time();
$target_dir = "src/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

$basename   = $filename . '.' . $imageFileType; 
// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if($check !== false) {
        echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }
}
// Check if file already exists


// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
    echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    $uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} else {

      $destionation  = $target_dir. $basename;
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $destionation)) {
        //echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";



        echo $full_url ="https://images.google.com/searchbyimage?q=sneakers&image_url=http://snkrden.us/search/src/".$basename;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $full_url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_REFERER, 'http://localhost');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.2 (KHTML, like Gecko) Chrome/22.0.1216.0 Safari/537.2");
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        $result = utf8_decode(curl_exec($curl));
        curl_close($curl);



          //echo "<div style='display:none'>".$result."<div>";


          $domResult = new simple_html_dom();
          $domResult->load($result);

          foreach($domResult->find('h3') as $link)
          echo $link->innertext . '<br>';


          echo "<br><br><hr><br><br>";

           preg_match_all("/var s=(.*);var/sU",$result,$EXT);
          //print_r($EXT);

          $array = array(';var','var s=',"'");
          foreach ($EXT[0] as $value ) {
           echo  "<img with='100px' src='".str_replace($array,"",$value)."' />\n"; 
           }



    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}  


//AIzaSyCEJTtEhmop-qnF7HPQNiLX_fS8YH19Oh0*/
?>

<hr><hr><br><br><br><br>

<a href="index.php">BACK TO INDEX</a>



<br><br><br><br><br><br>
  </body>
</html>