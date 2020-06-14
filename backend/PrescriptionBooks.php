<?php

include_once 'Connection.php';
include_once 'Authentication.php';

class PrescriptionBooks extends Connection
{
    public  static $instance;
    public $doctor;

    public function __construct()
    {
        parent::__construct();
        $auth = new Authentication();
        $this->doctor = $auth->user;
    }

    public static function getInstance(){
        self::$instance = new PrescriptionBooks();
        return self::$instance;
    }

    public function getPrescriptionsForWritten()
    {
        $data = [];
        $date =  gmdate('Y-m-d H:i:s', strtotime('-1 month'));
        $result = $this->conn->query("SELECT users.id, users.fname, users.lname, recipe.id as recipe_id, recipe.`date` as recipe_date FROM prescription_books LEFT JOIN recipe ON prescription_books.id = recipe.prescription_book_id LEFT JOIN users ON prescription_books.patient_id = users.id WHERE (recipe.`date` < '".$date."' || recipe.id IS NULL) AND prescription_books.doctor_id = ".$this->doctor->id." ");
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                $data[] = $row;
            }
        }
        return $data;
    }

    public function getWrittenPrescription()
    {
        $data = [];
        $date =  gmdate('Y-m-d H:i:s', strtotime('-1 month'));
        $result = $this->conn->query("SELECT users.id, users.fname, users.lname, recipe.id as recipe_id, recipe.`date` as recipe_date FROM prescription_books LEFT JOIN recipe ON prescription_books.id = recipe.prescription_book_id LEFT JOIN users ON prescription_books.patient_id = users.id WHERE recipe.`date` > '".$date."' AND prescription_books.doctor_id = ".$this->doctor->id." ");
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                $data[] = $row;
            }
        }
        return $data;
    }

    public function addPrescription(){
        //
    }
}