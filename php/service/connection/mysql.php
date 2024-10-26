<?php

class mysql {

    private $hostname_mysqlDB ;
    private $database_mysqlDB = "allpacksfc_final";
    private $username_mysqlDB = "root";
    private $password_mysqlDB = "root";
     
 
    private $mysqlDB = NULL;

    private function connection() {
        
        $this->setSchemaConnection();
        
        return mysqli_connect(
                $this->hostname_mysqlDB, $this->username_mysqlDB, $this->password_mysqlDB
        );
    }

    protected function setAutocommit($mode) {
        if ($mode) {
            mysqli_commit($this->getConnection());
        }
        mysqli_autocommit($this->getConnection(), $mode);
    }

    private function setSchemaConnection() {

        
            $env = $GLOBALS["config"]["env"];
            
            $this->hostname_mysqlDB = $GLOBALS["config"]["database"][$env]["hostname"];
            $this->database_mysqlDB = $GLOBALS["config"]["database"][$env]["schema"];
            $this->username_mysqlDB = $GLOBALS["config"]["database"][$env]["username"];
            $this->password_mysqlDB = $GLOBALS["config"]["database"][$env]["password"];

    }


    /**
     * Funcion  
     * @return type 
     */
    protected function getConnection() {

        if ($this->mysqlDB == null) {
            $this->mysqlDB = $this->connection();
            mysqli_select_db($this->mysqlDB, $this->database_mysqlDB);
        }

        return $this->mysqlDB;
    }

    protected function closeConnection() {
        mysqli_close($this->getConnection());
        $this->mysqlDB = null;
    }

    /**
     * Funcion para ejecutar cualquier query y lo retorna en un arreglo de filas
     * @param type $query
     * @return array|null 
     */
    public function executeQuery($query) {

//        error_log("executeQuery: ".$query);
//        $query = str_replace("\\", "\\\\", $query);
        $arrayMenu = null; 
        $res = mysqli_query($this->getConnection(), $query);

        if ($res && mysqli_num_rows($res) > 0) {

            $arrayMenu = array();
            while ($rowRS = mysqli_fetch_array($res)) {
                array_push($arrayMenu, $rowRS);
            }
            mysqli_free_result($res);
            //return $arrayMenu;
        } else {
            if (mysqli_errno($this->getConnection()) != 0) {
                error_log("query:" . $query . "\n" . mysqli_error($this->getConnection()));
            }
        }

        mysqli_close($this->getConnection());
        $this->mysqlDB = null;
        return $arrayMenu;
    }

    /**
     * Primer paso
     * @param type $query
     * @return mysqli_stmt
     */
    public function createPreparedStatement($query) {

        $stmt = null;

        if (!($stmt = mysqli_prepare($this->getConnection(), $query))) {
            error_log("pquery:" . $query);
        }

        return $stmt;
    }

    /**
     * Segundo paso
     * @param mysqli_stmt $stmt
     * @return array
     */
    public function executePreparedStatement($stmt, $query) {
        $resultado = null;
        /* execute query */
        if (mysqli_stmt_execute($stmt)) {

            $result = mysqli_stmt_get_result($stmt);

            $resultado = array();
            while ($rowRS = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                array_push($resultado, $rowRS);
            }

            /* free result */
            mysqli_stmt_free_result($stmt);

            /* close statement */
            mysqli_stmt_close($stmt);
        } else {
            error_log("Error eps.query: " . $query . " -- " . mysqli_stmt_error($stmt));
        }

        mysqli_close($this->getConnection());
        $this->mysqlDB = null;
        return $resultado;
    }

    public function executeUpdateOrDeletePreparedStatement($stmt, $query) {

        $resultado = null;

        /* execute query */
        if (mysqli_stmt_execute($stmt)) {

            $resultado = mysqli_stmt_affected_rows($stmt);

            /* close statement */
            mysqli_stmt_close($stmt);
        } else {
            error_log("Error euodps.query: " . $query . " -- " . mysqli_stmt_error($stmt));
        }

        mysqli_close($this->getConnection());
        $this->mysqlDB = null;
        return $resultado;
    }

    public function executeInsertWithAutoIncrementPreparedStatement($stmt, $query) {

        $resultado = null;

        /* execute query */
        if (mysqli_stmt_execute($stmt)) {

            $resultado = mysqli_stmt_insert_id($stmt);

            /* close statement */
            mysqli_stmt_close($stmt);
        } else {
            error_log("Error eiwaps.query: " . $query . " -- " . mysqli_stmt_error($stmt));
        }

        mysqli_close($this->getConnection());
        $this->mysqlDB = null;
        return $resultado;
    }

    /**
     * Funcion para realizar actualizaciones de los registros sobre la base de datos
     * @param type $query
     * @return int|null  entero con el total de registros actualizados o eliminados
     */
    public function executeUpdateOrDelete($query) {
//error_log("executeUpdateOrDelete: ".$query);
        //error_log($query);

        $res = mysqli_query($this->getConnection(), $query);
        $affected_rows = null;
        if ($res) {

            $affected_rows = mysqli_affected_rows($this->getConnection());
            if (!is_bool($res)) {
                mysqli_free_result($res);
            }
        } else {
            if (mysqli_errno($this->getConnection()) != 0) {
                error_log("query:" . $query . "\n" . mysqli_error($this->getConnection()));
            }
        }

        mysqli_close($this->getConnection());
        $this->mysqlDB = null;
        return $affected_rows;
    }

    /**
     * Funcion que inserta en la tabla especificada y retorna el id del ultimo registro insertado
     * @param string $query
     * @return int id del registro insertado 
     */
    public function executeInsertWithAutoIncrement($query) {


//error_log("executeInsertWithAutoIncrement: ".$query);
        //   error_log($query);
        $query = str_replace("\\", "\\\\", $query);
        $res = mysqli_query($this->getConnection(), $query);
        $affected_rows = null;
        if ($res) {

            $affected_rows = mysqli_insert_id($this->getConnection());
            if (!is_bool($res)) {
                mysqli_free_result($res);
            }
        } else {
            if (mysqli_errno($this->getConnection()) != 0) {
                error_log("query:" . $query . "\n" . mysqli_error($this->getConnection()));
            }
        }

        mysqli_close($this->getConnection());
        $this->mysqlDB = null;
        return $affected_rows;
    }

}

?>
