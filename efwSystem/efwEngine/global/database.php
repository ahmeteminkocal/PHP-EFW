<?php


namespace efwEngine;
define("DB_PREFIX", "efw_");
define("DB_CONFIGS", [
    "general" => ["db" => DB_PREFIX . "general"],
    "system" => ["db" => DB_PREFIX . "system"],
    "massdata" => ["db" => DB_PREFIX."massdata"]
]);

class database
{
    const DB_MASSDATA = [DB_CONFIGS["massdata"]];
    const DB_SYSTEM = [DB_CONFIGS["system"]];
    const DB_GENERAL = [DB_CONFIGS["general"]];
    private static $_buildQuery = "";
    private static $_pdoState = false;
    private static \PDO $connection;
    private static $_params = [];
    private static $_data = [];
    private static $_lastResult = [];
    private static $_lastError;
    private static $_currConfig = [];
    private static $_lastInsertID = 0;
    public static function setPdo($config = [])
    {
        $db = DB_PREFIX . "general";
        $host = config::getDB()::getServer(); #server
        $username = config::getDB()::getUser();
        $passwd = config::getDB()::getPass();
        if(!is_array($config)) $db = $config; else
        if (count($config) > 0) {
            if (isset($config["db"])) $db = $config["db"];
            if (isset($config["username"])) $username = $config["username"];
            if (isset($config["passwd"])) $passwd = $config["passwd"];
            if (isset($config["host"])) $host = $config["host"];
            #hata kontrolü
        }
        self::$_currConfig["db"] = $db;
        self::$_currConfig["username"] = $username;
        self::$connection = new \PDO("mysql:host=$host;dbname=$db", $username, $passwd);
        self::$connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        self::$connection->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
    }
    static function getConnection(){
        return self::$connection;
    }

    static function select($table = "", $rows = "*")
    {
        self::init();
        if ($rows === true) $rows = "*"; else if (is_array($rows))
            $columnsx = implode(", ", $rows);
        else $columnsx = $rows;
        static::$_buildQuery = "SELECT $columnsx FROM $table ";
        return new static;
    }
    static function insert($table, $columns, ...$data){
        self::init();
        $columnsx = implode(", ", $columns);
        $datas ="";

        foreach ($data as $no){
            $datas .= "?,";
        }
        $datas = substr($datas, 0, -1);
        foreach ($columns as $no => $val){
            self::$_params[] = (is_string($data[$no]) ? \PDO::PARAM_STR : \PDO::PARAM_INT);
            self::$_data[] = $data[$no];
        }
        ;
        static::$_buildQuery = "INSERT INTO $table($columnsx) VALUES ($datas) ";
        return new static;
    }
    static function delete($table): database
    {
        self::init();
        static::$_buildQuery = "DELETE FROM $table ";
        return new static;
    }
    public static function groupBy($column, ...$columns){
        self::$_buildQuery .= " GROUP BY $column ".implode(", ", $columns);
        return new static;
    }
    public static function limit($no, $start = 0){
        self::$_buildQuery .= " LIMIT $start, $no";
        return new static;
    }
    public static function setBind($data){
        foreach ($data as $no => $val) {
            self::$_params[] = (is_string($val) ? \PDO::PARAM_STR : \PDO::PARAM_INT);
            self::$_data[] = $val;
        }
        return new static;
    }
    public static function setQuery($query){
        self::$_buildQuery = $query;
        return new static;
    }
    public static function where($columns, ...$values): database
    {
        if(!isset($columns["textSql"])){

            $column = implode("=? AND ", $columns);
            self::$_buildQuery .= " WHERE {$column}=?";
            foreach ($columns as $no => $name){
                self::$_params[] = (is_string($values[$no]) ? \PDO::PARAM_STR : \PDO::PARAM_INT);
                self::$_data[] = $values[$no];
            }
        }else {
            self::$_buildQuery .= " WHERE " . $columns["textSql"];
            for($i = 0;$i < $columns["paramNo"];$i++) {
                self::$_params[] = (is_string($values[$i]) ? \PDO::PARAM_STR : \PDO::PARAM_INT);
                self::$_data[] = $values[$i];
            }
        }
        return new static;
    }
    static function init(){
        if (!isset(self::$connection)) self::setPdo();

    }
    static function update($table, $set_column, $value)
    {
        self::init();

        self::$_params[] = (is_string($value) ? \PDO::PARAM_STR : \PDO::PARAM_INT);
        self::$_data[] = $value;

        static::$_buildQuery = "UPDATE $table SET $set_column =?";
        return new static;
    }

    static function orderBy($column, $direction = "asc")
    {
        self::$_buildQuery .= " ORDER BY $column $direction ";
        return new static;

    }
    static function reset(){
        self::$_params = [];
        self::$_data = [];
        self::$_buildQuery = "";
        self::$_pdoState = false;
    }
    static function exec($returnType = 0)
    {
        try {
            $prepare = self::$connection->prepare(self::$_buildQuery);
            foreach (self::$_params as $no => $param){
                $prepare->bindParam($no+1, self::$_data[$no], $param);
            }
            $prepare->execute();
            foreach (self::$_params as $no => $param){
                $prepare->bindParam($no+1, self::$_data[$no], $param);
            }
            self::$_lastInsertID = self::$connection->lastInsertId();
            self::$_lastError = $prepare->errorInfo();
            self::$_lastResult = $prepare->fetchAll();
            self::reset();
            self::setPdo();
            $returnType = (int)$returnType;
            switch ($returnType){
                case 0:
                    return self::$_lastResult;

                    break;
                case 1:
                    return self::$_lastResult[0][0] ?? null;

                    break;
                case 2:
                    return self::$_lastResult[0] ?? null;
                    break;
            }
        }catch (\PDOException $e){
            echo $e->getMessage();

            //var_dump(self::$_buildQuery);
        }
    }
    static function getLastInsertID(){
        return self::$_lastInsertID;
    }
    static function getError(){
        return self::$_lastError;
    }
    static function query(): array
    {
        // TODO: Implement query() method.
    }

    static function getResult(): array
    {
        return self::$_lastResult;
        // TODO: Implement getResult() method.
    }
    static function massData(){
        self::setPdo(["db" => DB_PREFIX."massdata"]);
        return new self();
    }
    static function system(){
        self::setPdo(["db" => DB_PREFIX."system"]);
        return new self();
    }
}