<?php
// ============================================================
// DB_CONECT.PHP — Clase de conexion a base de datos
//
// Extiende Illuminate\Database\Capsule\Manager (Eloquent ORM).
// Proporciona metodos simples: select_all, select_row, select_one,
// insert, update, query, etc.
//
// Seguridad:
//   - escape_string() usa addslashes (basico, mejorable a prepared statements)
//   - Los formularios deben usar parametros ? siempre que sea posible
// ============================================================

use \Illuminate\Database\Capsule\Manager as DB;

class db_conect extends DB
{
    private $last_error = "";
    private $driver = "mysql";

    function pconnect($host, $username, $passwd, $dbname, $port, $driver, $charset = 'utf8', $collation = 'utf8_unicode_ci')
    {
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
                'strict' => true,
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
            log_system($e->getMessage(), $e->getLine(), $e->getFile());
            return false;
        }
    }

    function escape_string($txt)
    {
        try {
            return addslashes($txt);
        } catch (Exception $e) {
            return $txt;
        }
    }

    function connect_error()
    {
        return $this->last_error;
    }

    function error()
    {
        return $this->last_error;
    }

    function object_to_array($object = array())
    {
        try {
            return json_decode(json_encode($object), true);
        } catch (Exception $e) {
            log_system($e->getMessage(), $e->getLine(), $e->getFile());
            $this->last_error = $e->getMessage();
            return array();
        }
    }

    function select_limit($sql, $numrows, $offset = 0)
    {
        try {
            $sql = $this->set_limit($sql, $numrows, $offset);
            $result = $this::select($sql);
            return $this->object_to_array($result);
        } catch (Exception $e) {
            log_system($e->getMessage(), $e->getLine(), $e->getFile());
            $this->last_error = $e->getMessage();
            return array();
        }
    }

    function select_all($sql)
    {
        try {
            $result = $this::select($sql);
            return $this->object_to_array($result);
        } catch (Exception $e) {
            log_system($e->getMessage(), $e->getLine(), $e->getFile());
            $this->last_error = $e->getMessage();
            return 0;
        }
    }

    function count_rows($sql)
    {
        try {
            $result = $this::select($sql);
            return count($result);
        } catch (Exception $e) {
            log_system($e->getMessage(), $e->getLine(), $e->getFile());
            $this->last_error = $e->getMessage();
            return array();
        }
    }

    function select_row($sql)
    {
        try {
            $sql = $this->set_limit($sql, 1, 0);
            $result = collect($this::select($sql))->first();
            return $this->object_to_array($result);
        } catch (Exception $e) {
            log_system($e->getMessage(), $e->getLine(), $e->getFile());
            $this->last_error = $e->getMessage();
            return array();
        }
    }

    function select_one($sql)
    {
        try {
            $sql = $this->set_limit($sql, 1, 0);
            $result = collect($this::select($sql))->first();
            if ($result) {
                $result = $this->object_to_array($result);
                $data = array_values($result);
                return $data[0];
            } else {
                return array();
            }
        } catch (Exception $e) {
            log_system($e->getMessage(), $e->getLine(), $e->getFile());
            $this->last_error = $e->getMessage();
            return array();
        }
    }

    function set_limit($sql, $numrows, $offset = 0)
    {
        $driver = $this->driver;
        switch ($driver) {
            case 'mysql':
                $sql .= " LIMIT $numrows OFFSET $offset";
                break;
            case 'sqlsrv':
                $sql_b = mb_strtolower($sql, 'UTF-8');
                $pos = strpos($sql_b, 'order');
                if ($pos) {
                    $limit = " ";
                } else {
                    $limit = " ORDER BY 1 ";
                }
                $limit .= " OFFSET $offset ROWS FETCH NEXT $numrows ROWS ONLY ";
                $sql .= $limit;
                break;
            default:
                $sql .= " LIMIT $numrows OFFSET $offset";
                break;
        }
        return $sql;
    }

    function select_json($sql)
    {
        $data = $this->select_all($sql);
        return json_encode($data);
    }

    function query($sql)
    {
        try {
            return $this::statement($sql);
        } catch (Exception $e) {
            log_system($e->getMessage(), $e->getLine(), $e->getFile());
            $this->last_error = $e->getMessage();
            return false;
        }
    }

    function insert($table, $array, $empty_is_null = true)
    {
        try {
            $id = $this::table($table)->insertGetId($array);
            return $id;
        } catch (Exception $e) {
            log_system($e->getMessage(), $e->getLine(), $e->getFile());
            $this->last_error = $e->getMessage();
            return 0;
        }
    }

    function update($tabla, $datos, $id)
    {
        $valores = "";
        $ids = "";

        foreach ($datos as $key => $val) {
            $val = $this->escape_string($val);
            $valores .= $key . "=" . "'" . $val . "'" . ",";
        }
        foreach ($id as $key => $val) {
            $val = $this->escape_string($val);
            $ids = $key . "=" . $val;
        }

        $valores = trim($valores, ", ");
        $sql = 'UPDATE ' . $tabla . ' SET ' . $valores . ' WHERE ' . $ids;
        $this->query($sql);
    }

    function last_insert_id()
    {
        try {
            return $this::getPdo()->lastInsertId();
        } catch (Exception $e) {
            $this->last_error = $e->getMessage();
            log_system($e->getMessage(), $e->getLine(), $e->getFile());
            return 0;
        }
    }

    function SubirArchivo($elemento, $tamano_p, $tipo_archivo = false, $carpeta)
    {
        try {
            $fileTmpPath = $_FILES[$elemento]['tmp_name'];
            $fileName = $_FILES[$elemento]['name'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $uploadFileDir = "storage/" . $carpeta;
            $dest_path = $uploadFileDir . $newFileName;

            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0777, true);
            }

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                return array('error' => false, 'mensaje' => $dest_path);
            } else {
                return array('error' => true, 'mensaje' => "El archivo no se pudo subir");
            }
        } catch (Exception $e) {
            return array('error' => true, 'mensaje' => $e->getMessage());
        }
    }
}
