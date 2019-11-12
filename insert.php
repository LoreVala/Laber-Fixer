<?php  
 session_start();
 if(isset($_SESSION['table'])) $table=$_SESSION['table'];
 if(isset($_SESSION['columns'])) $columns= $_SESSION['columns'];
 if(isset($_SESSION['image_column'])) $image_column= $_SESSION['image_column'];
 if(isset($_SESSION['path_to_csv_file'])) $file_path=$_SESSION['path_to_csv_file'];
 if(isset($_SESSION['sql_path'])) $sql_path= $_SESSION['sql_path'];
 if(isset($_SESSION['file'])) $file= $_SESSION['file'];
 if(isset($_SESSION['editables_columns'])) $editables_columns=$_SESSION['editables_columns'];

 $connect = mysqli_connect("localhost", "root", "", "test");  
 if(!empty($_POST))  
 {  
      $post_array = array();
      $edit_array = array();
      foreach($editables_columns as $label => $values){
        $edit_array[] = $label;
        $post_array[] = mysqli_real_escape_string($connect, $_POST[$label]);
      }  
      $post_array = array_combine($edit_array, $post_array);  //join arrays (key->value)

      $img_id = mysqli_real_escape_string($connect, $_POST["i_m_g_i_d"]);  


      $query = "UPDATE $table SET ";

      foreach($post_array as $key => $value){
        if($value != ""){
          if(is_numeric($value)){
            $query .= "$key = $value, ";
          } else {
            $query .= "$key = '".$value."', ";
          }
        }
      }
      $query = substr($query, 0, -2);
      $query .= " WHERE $image_column = '".$img_id."' ";
      
    
    $result = mysqli_query($connect, $query);

    // save updated csv to temp file
    
    $temp_file = $table . "_temp.csv";
    $query = "SELECT ";
    foreach($columns as $col){
      $query .= "'".$col."', ";
    }
    $query = substr($query, 0, -2);
    $query .= " UNION ALL SELECT * FROM $table INTO OUTFILE '".$temp_file."' ";
    $query .= "FIELDS TERMINATED BY '".","."' LINES TERMINATED BY '"."\n"."';";
    $result = mysqli_query($connect, $query);

    // delete previous csv file and rename temp 
    $comm = "rm -f $sql_path"."$file";
    exec($comm, $o, $return);
    if($return){
      $sql_2 = str_replace("/","\\","$sql_path");
      exec("del /f $sql_2"."$file");
  }
  
    $comm = "mv -f $sql_path"."$temp_file $sql_path"."$file";
    exec($comm, $o, $return);
    if($return){
      $sql_2 = str_replace("/","\\","$sql_path");
      $comm = "copy /y $sql_2"."$temp_file $sql_2"."$file";
      exec($comm);
    }
    $comm = "cp -f $sql_path"."$file $file_path";
    exec($comm, $o, $return);
    if($return){
      $f2 = str_replace("/","\\","$file_path");
      $sql_2 = str_replace("/","\\","$sql_path");
      exec("copy /y $sql_2"."$file $f2");
    }
 }  
 ?>
 