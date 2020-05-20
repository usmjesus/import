<?php
include('simple_html_dom.php');

function xtrac($url)
{

  $header = array();
  $header[] = 'Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5';
  $header[] = 'Cache-Control: max-age=0';
  $header[] = 'mysqliection: keep-alive';
  $header[] = 'Keep-Alive: 300';
  $header[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7';
  $header[] = 'Accept-Language: en-us,en;q=0.5';
  $header[] = 'Pragma: ';
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.0.11) Gecko/2009060215 Firefox/3.0.11 (.NET CLR 3.5.30729)');
  curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
  curl_setopt($ch, CURLOPT_AUTOREFERER, true);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_ENCODING, '');
  curl_setopt($ch, CURLOPT_TIMEOUT, 20);
  $result = curl_exec($ch);
  return $result;
  curl_close($ch);
}

function strEx($img)
{
  $img = explode('?', $img);
  return $img[0];
}

function slugEx($slug)
{
  $slug = str_replace('https://stockx.imgix.net/', '', $slug);
  $slug = strtolower($slug);
  $slug = str_replace('-Product', '', $slug);
  $slug = str_replace('.', '-snkrsden.', $slug);
  return $slug;
}


if (@$_GET["pag"])
  $pag = $_GET["pag"];
else $pag = 1;


$urlS = "https://stockx.com/luxury-brands/release-date";
$product = array();
$image = array();
$slug = array();
$url = array();
$result = xtrac($urlS . "?page=" . $pag);
$domResult = new simple_html_dom();
$domResult->load($result);



if ($domResult->find('div.lmWSbq',0)) {

  $ic = 0;
  foreach ($domResult->find('div.lmWSbq') as $div) {
    $url[$ic] = $div->parent->href;
    $product[$ic] = $div->find('img', 0)->alt;
    $image[$ic] = strEx($div->find('img', 0)->src);
    $ic++;
  }



  $servername = 'localhost';
  $username = 'root';
  $password = "root";
  $dbname = 'snkrsdenbd';
  $mysqli = new mysqli($servername, $username, $password, $dbname);



  for ($i = 0; $i < $ic; $i++) {

    $query = "SELECT slug  FROM product WHERE slug='" . $url[$i] . "'";
    $result = $mysqli->query($query);

    if (!$result->num_rows) {
      //echo "row = " . $i  . "<br>\n";
      //echo $url[$i]  . "<br>\n";
      echo $product[$i]  . "<br>\n";
      //echo $image[$i]  . "<br><br>\n\n";


      //New-Product-Placeholder-Default.jpg
      $nImg = slugEx($image[$i]);
      $img = @file_get_contents($image[$i]);
      if(@file_put_contents('products/' . $nImg, $img)) $urlP = $nImg; else $urlP = "";

      $sql = "INSERT INTO  product (id,name,image,user_id,created_at,updated_at,slug) VALUES ( NULL,  '" . $product[$i] . "', '" . $urlP . "','1','".date('Y-m-d H:i:s')."','".date('Y-m-d H:i:s')."', '" . str_replace("/","",$url[$i]) . "');";
      mysqli_query($mysqli, $sql);




    }
  }

  $pag = $pag + 1;  if($pag>=22) { echo "Data end..."; exit;}


  echo '<meta http-equiv="refresh" content="1; url=index.php?pag=' . $pag . '">';
  echo "<img src='search-computer.svg'>";
  echo "<br><br><hr>Total(" . $i . "): Done 5 seconds to continue...";
  $mysqli->close();
  

} else {

  echo "<br><br><br>Data no found!";
}
