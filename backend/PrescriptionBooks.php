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
        $result = $this->conn->query("SELECT MAX(recipe.`date`) as max_date, users.id as user_id, users.fname as user_fname, users.lname as user_lname, prescription_books.id as id, recipe.id as recipe_id, recipe.`date` as recipe_date FROM prescription_books LEFT JOIN recipe ON prescription_books.id = recipe.prescription_book_id LEFT JOIN users ON prescription_books.patient_id = users.id WHERE  prescription_books.doctor_id = ".$this->doctor->id." GROUP BY users.id HAVING max_date < '".$date."' || max_date IS NULL ");
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
        $result = $this->conn->query("SELECT users.id, users.fname, users.lname, recipe.id as recipe_id, recipe.`date` as recipe_date FROM prescription_books LEFT JOIN recipe ON prescription_books.id = recipe.prescription_book_id LEFT JOIN users ON prescription_books.patient_id = users.id WHERE recipe.`date` > '".$date."' AND prescription_books.doctor_id = ".$this->doctor->id." GROUP BY users.id ORDER BY recipe.`date` DESC");
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                $data[] = $row;
            }
        }
        return $data;
    }

    public function addPrescription($data){
        $date = new DateTime();
        $dateField = $date->format('Y-m-d H:i:s');
        $hash = substr(hash('ripemd160', $this->doctor->fname.time()), 0, 32);
        $this->conn->query("INSERT INTO recipe(hash, prescription_book_id, `date`) VALUES('".$hash."', ".$data['prescription_id'].", '".$dateField."')");
        $recipeId = $this->conn->insert_id;
        if(isset($data['drugs']) && !empty($data['drugs'])){
            foreach ($data['drugs'] as $drug){
                $this->conn->query("INSERT INTO recipe_drugs(recipe_id, drug_id, quantity) VALUES(".$recipeId.", ".$drug['id'].", ".$drug['quantity'].")");
            }
        }
        if($this->conn->error){
            $result['status'] = 0;
            $result['message'] = 'Възникна грешка, моля опитайте отново по-късно';
        } else {
            $result['status'] = 1;
        }

        return $result;
    }
}