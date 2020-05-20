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


function getString($content, $start, $end) {
    $r = explode($start, $content);
    if (isset($r[1])){
         $r = explode($end, $r[1]);
        return $r[0];
    }
    return '';
}



$servername = 'localhost';
$username = 'root';
$password = "root";
$dbname = 'snkrsdenbd';
$mysqli = new mysqli($servername, $username, $password, $dbname);



$inix = $_GET["inix"];

$query = "SELECT id,slug,name  FROM product WHERE  brand_name IS NULL  ORDER BY id desc limit 0, 70";
$result = $mysqli->query($query);
if ($result->num_rows) {
while ($row = $result->fetch_assoc()) {

    echo $row['id']."-".$row['slug']."<br>";




$urlS = "https://stockx.com/".$row['slug'];
$resultU = xtrac($urlS);
$domResult = new simple_html_dom();
$content = $domResult->load($resultU);

if ($domResult->find('div.product-details', 0)) {

    $model ='';
    foreach($domResult->find('ul[data-testid="product-breadcrumbs"]', 0)->find('a') as $a) {
        $model .= strtolower($a->innertext."/");
    }

    $clway  = rtrim(ltrim(@$domResult->find('span[data-testid="product-detail-colorway"]', 0)->innertext));
    $st     = rtrim(ltrim(@$domResult->find('span[data-testid="product-detail-style"]', 0)->innertext));
    $retail = rtrim(ltrim(str_replace(array("$",","),"",@$domResult->find('span[data-testid="product-detail-retail price"]', 0)->innertext)));
    if(!$retail) $retail = 0;
    $release = @rtrim(ltrim($domResult->find('span[data-testid="product-detail-release date"]', 0)->innertext));
    if($release) {
                    if($release=="N/A")  {

                        $release = "";

                    }else{
                        $release= explode("/",$release);
                        $release = intval($release[2])."-".intval($release[0])."-".intval($release[1]);
                    }
                }

    $info           = explode("/",$model);
    $db_brand       = $info[2];
    $remove         = array($info[0]."/".$info[1]."/".$db_brand."/", "/".$info[count($info)-2]."/");
    $urlCat         = str_replace($remove,"",$model);
    

    $updateSQL = "UPDATE product SET 
                  brand_name = '".$db_brand."',
                  model_cat_name = '".$urlCat."',
                  colorway = '".$clway."',
                  style = '".$st."',
                  retail_price = '".number_format($retail, 2, '.', '')."' ";
                  
    if($release)
    $updateSQL .= ", release_date = '".$release."'"; 
    
    $updateSQL .= " where id='".$row['id']."'";

    $updateSQL = str_replace("<br>","",$updateSQL);
    mysqli_query($mysqli, $updateSQL);


    $cleanSQL = "DELETE FROM product_detail WHERE product_id='".$row['id']."'";
    mysqli_query($mysqli, $cleanSQL);


    //lastSale
    $lasts = str_replace(array('"',"’","[","]"),'',getString($content, 'window.preLoaded = {"countryCode"', '</script>'));
    $lastsx = explode("},",$lasts);

    foreach($lastsx as $v){
        if (preg_match("/\blastSale\b/i",$v)){

            
            $dt_sizeSale    = getString($v, "lastSale:", ",lastSaleSize")."<br>";
            $dt_size        = getString($v, "lastSaleSize:", ",salesLast72Hours")."<br>";
            $dt_sizeDate    =  str_replace("T"," ",str_replace("+00:00","",getString($v, "lastSaleDate:", ",createdAt")))."<br>";
            if($dt_sizeSale>0){
                // $sql = str_replace("<br>","","INSERT INTO product_detail (id, slug, colorway, style, retail_price, release_date, model, size, last_sale, last_sale_date) 
                            // VALUES (NULL, '".$row['slug']."', '".$clway."', '".$st."', '".$retail."', '".$release."', '".strtolower($model)."', '".$dt_size."', '".$dt_sizeSale."', '".$dt_sizeDate."');");

                $sql = str_replace("<br>","","INSERT INTO product_detail (id, product_id, sizes_shoes_val, lowest_ask, highest_offer, last_sale, last_sale_date, sales) VALUES (NULL, '".$row['id']."', '".$dt_size."', NULL, NULL, '".$dt_sizeSale."', '".$dt_sizeDate."', '0')");

                mysqli_query($mysqli, $sql);
 

            }
        }

    }


} else {

    echo "<br><br><br>Data no found!"; exit;
}


}

$inix = $inix+0;
echo "<br><br><img src='search-computer.svg' width='50px'>";
echo '<meta http-equiv="refresh" content="5; url=detail.php?inix=' . $inix . '">';
echo "<br><br><hr>Total(" . $result->num_rows . "): Done 5 seconds to continue...";


$query = "SELECT id,slug,name  FROM product WHERE  brand_name IS NULL";
$result = $mysqli->query($query);
echo "<br> faltante: ".$result->num_rows;

}else{

    echo "end"; exit;

}
$mysqli->close();





