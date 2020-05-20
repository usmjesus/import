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


}
      $obj = new extract();
//echo $obj->root(162,'');


      $mysqli = new mysqli($obj->servername, $obj->username, $obj->password, $obj->dbname);
      $query = "SELECT * FROM `model_cat` ";
      $result = $mysqli->query($query);
      
      while ($row = $result->fetch_assoc()) {

        $sql = "UPDATE `model_cat` SET `slug_full` = '".$obj->root($row['id'],'')."' WHERE `model_cat`.`id` = ".$row['id'].";";
        mysqli_query($mysqli, $sql);

      }

?>