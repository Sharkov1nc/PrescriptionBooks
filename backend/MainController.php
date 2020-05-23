<?php
include_once 'Connection.php';
class MainController extends Connection {

    public  static $instance;

    public static function getInstance(){
        self::$instance = new MainController();
        return self::$instance;
    }

    public function getPositions(){
        $data = [];
        $result  = $this->conn->query("SELECT * FROM positions");
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                $data[] = $row;
            }
        }
        return $data;
    }

}