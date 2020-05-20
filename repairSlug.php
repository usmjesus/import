<?php 


class extract{

public $servername = 'localhost';
public $username = 'root';
public $password = "root";
public $dbname = 'snkrsdenbd';


  function arrayData($id){

      $mysqli = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
      $query = "SELECT * FROM `model_cat` WHERE id='".$id."'";
      $result = $mysqli->query($query);
      return $row = $result->fetch_assoc();

  }

  function removeSl($fend){

      if(substr($fend, -1)=="/")
      $fend = substr($fend, 0, -1);

      return $fend;
  }

  function root($id,$concat){

      $row = $this->arrayData($id);

      $slug_full = $row['slug']."/".$concat;

      if($row['model_cat_id']>0){
        return $slug_full = $this->root($row['model_cat_id'],$slug_full);

      }else{
        return $this->removeSl($slug_full);
      }

  }

  function slug($name){

    $name =  str_replace(" ","-",strtolower($name));
    $name =  $this->removeSl($name);
    return $name;

  }


}
      $obj = new extract();
//echo $obj->root(162,'');

      //3568
      $mysqli = new mysqli($obj->servername, $obj->username, $obj->password, $obj->dbname);
      $query = "SELECT * FROM `product` WHERE `model_cat_name` IS NOT NULL  ";
      $result = $mysqli->query($query);
      
      while ($row = $result->fetch_assoc()) {
        
        $name = $row['name'];
        
        $pos = strpos($name, "/");
        if(!$pos === false ){
          $name = explode ("/",$row['name']);
          $name =  $name[0];
        }

        
        $model_cat_name = str_replace($obj->slug($name),"",$obj->slug($row['model_cat_name']));

        if($model_cat_name)
        $sql = "UPDATE `product` SET `model_cat_name` = '".$obj->removeSl($model_cat_name)."' WHERE id = '".$row['id']."';";
        else
        $sql = "UPDATE `product` SET `model_cat_name` = NULL WHERE id = '".$row['id']."';";

        mysqli_query($mysqli, $sql);

      }

?>