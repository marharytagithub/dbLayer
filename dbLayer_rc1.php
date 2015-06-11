<?php

/**
 * Class DbLayer
 */
class DbLayer
{
    private $host;
    private $login;
    private $password;
    private $dbName;
    private $dbConnection;
    public $errorSet = array();

    /**
     * @param $host
     * @param $login
     * @param $password
     * @param $dbName
     */
    public function __construct($host, $login, $password, $dbName)
    {
        $this->host = $host;
        $this->login = $login;
        $this->password = $password;
        $this->dbName = $dbName;
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        $res = implode("<br />", $this->errorSet);

        return $res;
    }

    /**
     * @param $tableName
     * @param array $paramSet
     * @param int $limit
     * @return array
     */
    public function fetchAll($tableName, array $paramSet = array(), $limit = 100)
    {
        // $sql = "select * from $tableName {$this->buildWhere($paramSet)} limit $limit";
        $fetchAll = array();

        $selectAll = "select * from $tableName";
        $where = "";
        $lim = "limit $limit";

        if (!empty($paramSet)) // empty() returns FALSE if var exists and has a non-empty, non-zero value. Otherwise returns TRUE.
        {
            $where = $this->buildWhere($paramSet);
        }

        $sql = "$selectAll $where $lim";
        //echo $sql; die;

        $res = mysqli_query($this->dbConnection, $sql);
        if ($res) { // mysqli_query return false or object
            while ($row = mysqli_fetch_assoc($res)) // array or false
            {
                $fetchAll[] = $row;
            }
        }

        return $fetchAll;
    }

    /**
     * @param array $paramSet
     * @return string
     */
    private function buildWhere(array $paramSet)
    {
        $paramSetWhere = array();
        // select * from tea where id = '5' and price = '12.60'

        foreach ($paramSet as $key => $value) {
            $paramSetWhere[] = "$key = '$value'";
        }
        $paramSetLine = implode(' and ', $paramSetWhere);
        $sqlWhere = "where $paramSetLine";
        //echo '<pre>'; var_dump($sqlWhere); die;

        return $sqlWhere;
    }

    /**
     * @param $tableName
     * @param array $paramSet
     * @return array|null
     */
    public function fetchRow($tableName, array $paramSet)
    {
        $where = $this->buildWhere($paramSet);
        $sql = "select * from $tableName $where";
        // echo $sql; die;

        $res = mysqli_query($this->dbConnection, $sql);
        $row = mysqli_fetch_array($res, MYSQLI_ASSOC);

        return $row;
    }

    /**
     * @param $tableName
     * @param array $rowSet
     * @return bool|mysqli_result
     */
    public function insert($tableName, array $rowSet = array())
    {
        $key = array_keys($rowSet);
        $columnSet = implode("', '", $key);

        $value = array_values($rowSet);
        $valueSet = implode("', '", $value);

        $sql = "insert into $tableName ('$columnSet') values ('$valueSet')";
        //echo $sql; die;

        $res = mysqli_query($this->dbConnection, $sql); // mysqli_query return false or object

        return $res;
    }

    /**
     * @param $sql
     * @return bool|mysqli_result
     */
    public function query($sql)
    {
        $res = mysqli_query($this->dbConnection, $sql);

        return $res;
    }

    /**
     * @return bool|string
     */
    public function connect()
    {
        $this->errorSet = array();
        $this->dbConnection = mysqli_connect($this->host, $this->login, $this->password, $this->dbName);

        if (!$this->dbConnection) {
            $this->errorSet[] = mysqli_connect_error();

            return false;
        }

        return true;
    }
}
