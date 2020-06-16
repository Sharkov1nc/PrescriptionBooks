<?php

include_once 'Connection.php';
include_once 'Authentication.php';

class PrescriptionBooks extends Connection
{
    public  static $instance;
    public $user;

    public function __construct()
    {
        parent::__construct();
        $auth = new Authentication();
        $this->user = $auth->user;
    }

    public static function getInstance(){
        self::$instance = new PrescriptionBooks();
        return self::$instance;
    }

    public function getPrescriptionsForWritten()
    {
        $data = array();
        $date =  gmdate('Y-m-d H:i:s', strtotime('-1 month'));
        $result = $this->conn->query("SELECT MAX(recipe.`date`) as max_date, users.fname as user_fname, users.lname as user_lname, prescription_books.id as id, recipe.id as recipe_id, recipe.`date` as recipe_date FROM prescription_books LEFT JOIN recipe ON prescription_books.id = recipe.prescription_book_id LEFT JOIN users ON prescription_books.patient_id = users.id WHERE  prescription_books.doctor_id = ".$this->user->id." GROUP BY users.id HAVING max_date < '".$date."' || max_date IS NULL ");
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                $data[] = $row;
            }
        }
        return $data;
    }

    public function getWrittenPrescription()
    {
        $data = array();
        $date =  gmdate('Y-m-d H:i:s', strtotime('-1 month'));
        $result = $this->conn->query("SELECT users.id, users.fname, users.lname, recipe.id as recipe_id, recipe.`date` as recipe_date FROM prescription_books LEFT JOIN recipe ON prescription_books.id = recipe.prescription_book_id LEFT JOIN users ON prescription_books.patient_id = users.id WHERE recipe.`date` > '".$date."' AND prescription_books.doctor_id = ".$this->user->id." GROUP BY users.id ORDER BY recipe.`date` DESC");
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
        $hash = substr(hash('ripemd160', $this->user->fname.time()), 0, 32);
        $this->conn->query("INSERT INTO recipe(hash, prescription_book_id, `date`) VALUES('".$hash."', ".$data['prescription_id'].", '".$dateField."')");
        $recipeId = $this->conn->insert_id;
        if(isset($data['drugs']) && !empty($data['drugs'])){
            foreach ($data['drugs'] as $drug){
                $this->conn->query("INSERT INTO recipe_drugs(recipe_id, drug_id, quantity) VALUES(".$recipeId.", ".$drug['id'].", ".$drug['quantity'].")");
            }
        }
        $result = array(
            'status' => 1
        );
        if($this->conn->error){
            $result['status'] = 0;
            $result['message'] = 'Възникна грешка, моля опитайте отново по-късно';
        }

        return $result;
    }

    public function deletePrescription($recipeId){
        $this->conn->query("DELETE FROM recipe_drugs WHERE recipe_id = ".$recipeId);
        $this->conn->query("DELETE FROM recipe WHERE id =".$recipeId );
        $result = array(
            'status' => 1
        );
        if($this->conn->error){
            $result['status'] = 0;
            $result['message'] = 'Възникна грешка, моля опитайте отново по-късно';
        }
        return $result;
    }

    public function editPrescription($data){
        print_r($data);
        exit;
    }

    public function searchRecipe($recipeId = null, $userFname = null, $userLname = null){
        $query = '';
        $data = [];
        $date =  gmdate('Y-m-d H:i:s', strtotime('-1 month'));
        if($recipeId){
            $query = "SELECT MAX(recipe.`date`) as max_date, users.fname as user_fname, users.lname as user_lname, prescription_books.id as id, recipe.id as recipe_id, recipe.`date` as recipe_date FROM prescription_books LEFT JOIN recipe ON prescription_books.id = recipe.prescription_book_id LEFT JOIN users ON prescription_books.patient_id = users.id WHERE prescription_books.doctor_id = ".$this->user->id." AND recipe.id = ".$recipeId." GROUP BY users.id HAVING max_date < '".$date."' || max_date IS NULL ";
        } else {
            $lnameSearch = '';
            if($userLname){
                $lnameSearch = " OR users.lname LIKE '%".$userLname."%' ";
            }
            $query = "SELECT MAX(recipe.`date`) as max_date, users.fname as user_fname, users.lname as user_lname, prescription_books.id as id, recipe.id as recipe_id, recipe.`date` as recipe_date FROM prescription_books LEFT JOIN recipe ON prescription_books.id = recipe.prescription_book_id LEFT JOIN users ON prescription_books.patient_id = users.id WHERE (users.fname LIKE '%".$userFname."%' ". $lnameSearch .") AND prescription_books.doctor_id = ".$this->user->id." GROUP BY users.id HAVING max_date < '".$date."' || max_date IS NULL ";
        }
        $result  = $this->conn->query($query);
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                $data[] = $row;
            }
        }

        return $data;
    }

    public function printRecipe($recipeId){

    }

    public function previewRecipe($data){

    }

    private function printDocument($data){

    }

    public function markRecipeAsTaken($recipeId){

    }


    private function sendPrescriptionCode(){

    }
}