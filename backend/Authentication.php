<?php
session_start();
include_once 'Connection.php';
include_once 'MainController.php';

class Authentication extends Connection {

    public $user;

    public function __construct()
    {
        parent::__construct();
        if(isset($_SESSION['user']) && !$this->user){
            $this->user = $_SESSION['user'];
        }
    }

    protected function register($data){
        $date = new DateTime();
        $dateField = $date->format('Y-m-d H:i:s');
        $position = isset($data['position']) ? $data['position'] : 3;
        $this->conn->query("INSERT INTO users (fname, lname, egn, email, password, `user_position`, `date`)  
        VALUES('".$data['fname']."', '".$data['lname']."', '".$data['egn']."', '".$data['email']."', '".md5($data['password'])."', '".$position."', '".$dateField."')");
        $userId = $this->conn->insert_id;
        if($position == 3){
            $this->conn->query("INSERT INTO prescription_books(patient_id, doctor_id, `date`) VALUES(".$userId.", ".$data['doctor'].", '".$dateField."')");
        }

        return array(
            'status' => 1,
            'user' => [
                'id' => $userId,
                'fname' => $data['fname'],
                'lname' => $data['lname'],
                'email' => $data['email'],
                'date' => $dateField,
                'position' => MainController::getInstance()->getPositionById($position)
            ]
        );
    }


    protected function login($email, $password){

        $result = $this->conn->query("SELECT * FROM users WHERE email ='" . $email . "' AND password = '" . md5($password) . "' LIMIT 1");
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_object()) {
                $_SESSION['user'] = $row;
            }
            header('Location: index.php');
        } else {
            return array(
                'status' => 0,
                'message' => 'Не е намерен потребител с въведените име и парола'
            );
        }
    }

    public function logout(){
        session_destroy();
        header("Location: login.php");
    }

    public function isLoggedIn(){
        if(isset($_SESSION['user'])){
            return;
        }
        header("Location: login.php");
    }

    public function initUser(){
        if(isset($_SESSION['user'])){
            return $_SESSION['user'];
        }
    }

    public function validateRegister($data){
        $result = array(
            'status' => 1
        );
        if(strlen($data['fname']) < 1) {
            $result['status'] = 0;
            $result['message'] = 'Моля въведете име';
            return $result;
        }
        if(strlen($data['lname']) < 1) {
            $result['status'] = 0;
            $result['message'] = 'Моля въведете фамилия';
            return $result;
        }
        if(strlen($data['egn']) < 1){
            $result['status'] = 0;
            $result['message'] = 'Моля въведете ЕГН';
            return $result;
        }
        if(strpos($data['email'], '@') === false || strlen($data['email']) < 6){
            $result['status'] = 0;
            $result['message'] = 'Невалиден имейл';
            return $result;
        }
        if(strlen($data['password']) < 6){
            $result['status'] = 0;
            $result['message'] = 'Паролата трябва да е над 5 символа';
            return $result;
        }
        if(!$result['status']){
            return $result;
        }

        return $this->register($data);
    }

    public function validateLogin($email, $password){
        $result = array(
            'status' => 1
        );
        if(strlen($email) < 6) {
            $result['status'] = 0;
            $result['message'] = 'Невалиден имейл';
            return $result;
        }
        if(strlen($password) < 6){
            $result['status'] = 0;
            $result['message'] = 'Невалидена парола';
            return $result;
        }
        return $this->login($email, $password);
    }

    public function getUserPassword($email){
        $password = null;
        $result = $this->conn->query("SELECT password FROM users WHERE email = '".$email."' LIMIT 1");
        if ($result->num_rows > 0){
            $arr = $result->fetch_assoc();
            $password = $arr['password'];
        }
        return $password; // връщаме паролата
    }

}