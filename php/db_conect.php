<?php
use \Illuminate\Database\Capsule\Manager as DB;
class db_conect extends DB {
  
  private $last_error ="";
  private $driver ="mysql";
 
  function pconnect ($host,$username,$passwd,$dbname,$port,$driver,$charset='utf8',$collation='utf8_unicode_ci') {
      try {  
        
        $this->addConnection([
            'driver' => $driver,
            'host' => $host,
            'database' => $dbname,
            'username' => $username,
            'password' => $passwd,
            'port' => $port,
            'charset' => $charset,
            'prefix' => '',
            'strict'    => true,
            'options' => [
                \PDO::ATTR_PERSISTENT => false
            ]

        ]);

        $this->setAsGlobal();
        $this->bootEloquent();
        $this->getConnection();
        $this->driver = $driver;

     } catch (Exception $e) {       
       $this->last_error = $e->getMessage();
       log_system($e->getMessage(),$e->getLine(),$e->getFile());
       return false;
     }  

  }
  
  function escape_string($txt){
    try {
        return addslashes($txt);
    }catch (Exception $e) {
       return $txt;
    }  
  }
  function connect_error(){
    return $this->last_error;
  }

  function error(){
    return $this->last_error;
  }

  function object_to_array($object=array()){
    try { 
        return json_decode( json_encode($object) , true );
     }catch (Exception $e) {
       log_system($e->getMessage(),$e->getLine(),$e->getFile());
       $this->last_error = $e->getMessage();
       return array();
     }  
  }

   function select_limit($sql,$numrows,$offset=0) {

     try {
        $sql=$this->set_limit($sql,$numrows,$offset);
        $result=$this::select($sql);
        return $this->object_to_array($result);            
     } catch (Exception $e) {
       log_system($e->getMessage(),$e->getLine(),$e->getFile());
       $this->last_error =   $e->getMessage();
       return array();
     }        

   }

  function select_all($sql) {

     try {
        $result=$this::select($sql);
        return $this->object_to_array($result);         
      } catch (Exception $e) {
         log_system($e->getMessage(),$e->getLine(),$e->getFile());
         $this->last_error = $e->getMessage();
         return 0;
      }

   }
   
   function count_rows($sql){
     try {
        $result=$this::select($sql);
        return count($result);         
     } catch (Exception $e) {
        log_system($e->getMessage(),$e->getLine(),$e->getFile());
        $this->last_error = $e->getMessage();
        return array(); 
     }  
   }

   function select_row($sql) {

     try {
        $sql=$this->set_limit($sql,1,0);
        $result = collect($this::select($sql))->first();
        return $this->object_to_array($result);         
     } catch (Exception $e) {
        log_system($e->getMessage(),$e->getLine(),$e->getFile());
        $this->last_error = $e->getMessage(); 
        return array();  
     }        

   }


   function select_one($sql) {

     try {
        $sql=$this->set_limit($sql,1,0);
        $result = collect($this::select($sql))->first();
        if ($result) {
            $result= $this->object_to_array($result); 
            $data = array_values($result);      
            return $data[0];  
        }else{
            return array();
        }
        
     } catch (Exception $e) {
        log_system($e->getMessage(),$e->getLine(),$e->getFile());
        $this->last_error = $e->getMessage();  
        return array(); 
     }        

   }


   function set_limit($sql,$numrows,$offset=0){
      $driver = $this->driver;
      switch ($driver) {
          case 'mysql':
              $sql.=" LIMIT $numrows OFFSET $offset";
              break;

         case 'sqlsrv':
              $sql_b = mb_strtolower($sql,'UTF-8');
              $pos = strpos($sql_b, 'order');
              if($pos){
                  $limit= " ";
               }else{
                  $limit = " ORDER BY 1 ";
               }
               $limit.= " OFFSET $offset ROWS FETCH NEXT $numrows ROWS ONLY ";
               $sql.=$limit;
              break;
          
         
          default:
              $sql.=" LIMIT $numrows OFFSET $offset";
              break;
      }
        
        return $sql;
    }

   function query_json_table($sql){
        $data=$this->select_all($sql);
        return  json_encode($data) ;
    }
    
    function select_json($sql){
        return $this->query_json_table($sql);
    }

    function query($sql){
        try {
            return  $this::statement($sql);
        } catch (Exception $e) {
            log_system($e->getMessage(),$e->getLine(),$e->getFile());
            $this->last_error = $e->getMessage();
            return false; 
        }   
    }

    function set_update($sql,$dats){
        try {
            return $this::update($sql,$dats);
        } catch (Exception $e) {
            log_system($e->getMessage(),$e->getLine(),$e->getFile());
            $this->last_error = $e->getMessage();
            return false; 
        }   
    }

    function set_delete($sql){
        try {
            return $this::delete($sql);
        } catch (Exception $e) {
            log_system($e->getMessage(),$e->getLine(),$e->getFile());
            $this->last_error = $e->getMessage();
            return false; 
        }   
    }

    function set_insert($sql,$dats){
        try {
            return $this::insert($sql,$dats);
        } catch (Exception $e) {
            log_system($e->getMessage(),$e->getLine(),$e->getFile());
            $this->last_error = $e->getMessage();
            return false; 
        }   
    }




    function make_insert($table,$array,$empty_is_null=true){
        $campos="";
        $valores="";
        foreach ($array as $campo => $valor)
        {
            $valor = $this->escape_string($valor);
            $campos .= "$campo,";
            if( ($valor=="NULL" || $valor==NULL) || ($empty_is_null==true && trim($valor)=="") )
            {
                $valores .= "NULL,";
            }
            else
            {
                $valores .= "'$valor',";
            }
        }
        $campos=trim($campos, ", ");
        $valores=trim($valores, ", ");
        $sql="insert into $table ($campos) values($valores)";
        return $sql;
    }

    function insert($table,$array,$empty_is_null=true){
        try {
            $id=$this::table($table)->insertGetId($array);
            return $id;
        } catch (Exception $e) {    
            log_system($e->getMessage(),$e->getLine(),$e->getFile());      
            $this->last_error = $e->getMessage();
            return 0; 
        }   
    }

    function get_insert($table,$array,$empty_is_null=true){
        $sql=$this->make_insert($table,$array,$empty_is_null);
        return ($sql);
    }
    


    function make_update($table,$array,$empty_is_null=true){
        $sql="update " . $table . " set  ";
        foreach ($array as $campo => $valor)
        {
            $valor = $this->escape_string($valor);
            if( ($valor=="NULL" || $valor==NULL) || ($empty_is_null==true && trim($valor)=="") )
            {
                $sql.= $campo. " = NULL, ";
            }
            else
            {
                $sql.= $campo. " = '" . $valor . "', ";
            }
        }
        $sql=trim($sql,", "); //Permire borrar la ultima coma (,) y los espacios  que quedan al final   
        return $sql;
    }    

    function update($tabla,$datos,$id){
        $valores="";
        $ids="";

        foreach ($datos as $key => $val) {
            $val = $this->escape_string($val);
            $valores.=$key."="."'".$val."'".",";
        }
        foreach ($id as $key => $val) {
            $val = $this->escape_string($val);
            $ids=$key."=".$val."";
        }

        $valores=trim($valores,", "); //ultima coma
        $sql= 'UPDATE '.$tabla.' SET '.$valores.' WHERE '.$ids.'';
        $this->query($sql);
    }

    function get_update($tabla,$datos,$id){
        $valores="";
        $ids="";

        foreach ($datos as $key => $val) {
            $val = $this->escape_string($val);
            $valores.=$key."="."'".$val."'".",";
        }
        foreach ($id as $key => $val) {
            $val = $this->escape_string($val);
            $ids=$key."=".$val."";
        }

        $valores=trim($valores,", "); //ultima coma
        $sql= 'UPDATE '.$tabla.' SET '.$valores.' WHERE '.$ids.'';
        return $sql;
    }


    
    function last_insert_id(){
        try {
            return $this::getPdo()->lastInsertId();
        } catch (Exception $e) {            
            $this->last_error = $e->getMessage();
            log_system($e->getMessage(),$e->getLine(),$e->getFile());
            return 0; 
        }  
    }

    function set_charset(){
        return true;
    }

    function comprimirImagen($recurso, $destino, $calidad) { 

        $imgInfo = getimagesize($recurso); 
        $mime = $imgInfo['mime']; 

          //Creamos una imagen temporal
        switch($mime){ 
          case 'image/jpeg': 
              $imagen = imagecreatefromjpeg($recurso); 
              break; 
          case 'image/png': 
              $imagen = imagecreatefrompng($recurso); 
              break; 
          case 'image/gif': 
              $imagen = imagecreatefromgif($recurso); 
              break; 
          default: 
              $imagen = imagecreatefromjpeg($recurso); 
        } 

        // Guardamos la imagen
        imagejpeg($imagen, $destino, $calidad);  
        
        // Devolvemos la imagen comprimida
        return $destino; 

    }

    function subirbase64($elemento,$carpeta){
            $base64 = $_POST[$elemento.'_base64'];
            list($type, $data) = explode(';', $base64);
            list(, $data) = explode(',', $data);
            $data = base64_decode($data); // Decodificar la cadena Base64

            // Obtener la extensión del archivo
            $extension = '';
            if (strpos($type, 'image/jpeg') !== false) {
                $extension = 'jpg';
            } elseif (strpos($type, 'image/png') !== false) {
                $extension = 'png';
            } elseif (strpos($type, 'image/gif') !== false) {
                $extension = 'gif';
            } else {
                return false;
            }

            
            $fileName = uniqid() . '.' . $extension;
            $filePath = "storage/".$carpeta.''.$fileName;
            // Guardar el archivo en el servidor
            if (file_put_contents($filePath, $data)) {
                return $filePath;
            } else {
                return false;
            }
    }


    function SubirArchivo($elemento,$tamano_p,$tipo_archivo=false,$carpeta){
        try {
            // Obtener detalles del archivo
            $fileTmpPath = $_FILES[$elemento]['tmp_name'];
            $fileName = $_FILES[$elemento]['name'];
            #$fileSize = $_FILES[$elemento]['size'];
            #$fileType = $_FILES[$elemento]['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            

            // Generar un nombre único para el archivo
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $name = uniqid();

            // Ruta donde se guardará la imagen
            $uploadFileDir = "storage/".$carpeta;
            $dest_path = $uploadFileDir . $newFileName;

            error_log($dest_path, 0);
            error_log($fileTmpPath, 0);

            $subirbase64 = $this->subirbase64($elemento,$carpeta);
            if($subirbase64){ 
                return  array('error' =>false ,'mensaje'=>$subirbase64);
            }else{
                $compressedImage = false; //$this->comprimirImagen($fileTmpPath,"$uploadFileDir/$name.jpg",90);
                if($compressedImage){ 
                    return  array('error' =>false ,'mensaje'=>$compressedImage);
                }else if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    return  array('error' =>false ,'mensaje'=>$dest_path);
                }else{
                    return  array('error' =>true ,'mensaje'=> "El archivo: ".$elemento." no se pudo subir : ".$_FILES[$elemento]["error"]);
                } 
            }
        } catch (Exception $e) {
           return  array('error' =>true ,'mensaje'=> $e->getMessage()); 
        }
        
        
    }

    function SubirArchivov1($elemento,$tamano_p,$tipo_archivo=false,$carpeta){
            $file = $_FILES[$elemento]['name'];
            $file2 = $_FILES[$elemento]['tmp_name'];
            $tamano = $_FILES[$elemento]['size'];
            $tipo = $_FILES[$elemento]['type'];
            
            $tamano=$tamano/1048576;            
            $archivo = $file; 
            
            if (empty($archivo) or $archivo=='null') {
            return array('error' =>true ,'mensaje'=> "Ingrese archivo");  //valido si viene archivo
            exit(0);}
            
            if ($tamano>$tamano_p) {return array('error' =>true ,'mensaje'=> "Tamaño del archivo: ".$file." no esta permitido");  // si el tamaño se excede
            exit(0);}


                $trozos = explode(".", $archivo); 
                $extension = end($trozos);
                
                $nombre =$elemento.date('Y-m-d').time();
                $carpeta = "storage/".$carpeta;
                if(!is_dir($carpeta)) 
                mkdir($carpeta, 0777,true);
                
                $file = $nombre.".".$extension;
                //comprobamos si el archivo ha subido
                if ($file && move_uploaded_file($_FILES[$elemento]['tmp_name'],$carpeta.$file))
                {
                //sleep(3);//retrasamos la petición 3 segundos
                   $direccion=$carpeta.$file;
                   return  array('error' =>false ,'mensaje'=>$direccion);

                }else
                {
                    return  array('error' =>true ,'mensaje'=> "El archivo: ".$elemento." no se pudo subir");
                }
    

    }

}
?>