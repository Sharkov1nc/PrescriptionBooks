<?php
session_start();
include_once 'Connection.php';

class Authentication extends Connection {

    public $user;

    protected function register($username, $password, $email, $position){

        $date = new DateTime();
        $dateField = $date->format('Y-m-d H:i:s');
        $this->conn->query("INSERT INTO users (username, password, email, `position`, `date`)  
        VALUES('".$username."', '".$password."', '".$email."', ".md5($position).", '".$dateField."')");
        if($position == 3 && !isset($_SESSION['user'])){
            $this->login($email, $password);
        }

        return array(
            'status' => 1
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
        if(isset($_SESSION['user']) && !$this->user){
            $this->user = $_SESSION['user'];
            return;
        }
        header("Location: login.php");
    }

    public function initUser(){
        if(isset($_SESSION['user'])){
            return $_SESSION['user'];
        }
    }

    public function validateRegister($username, $password, $passwordConfirm, $email, $position = 3){
        $result = array(
            'status' => 1
        );
        if(strlen($username) < 6) {
            $result['status'] = 0;
            $result['message'] = 'Потебителското име трябва да е над 5 символа';
            return $result;
        }
        if(strpos($email, '@') === false || strlen($email) < 6){
            $result['status'] = 0;
            $result['message'] = 'Невалиден имейл';
            return $result;
        }
        if(strlen($password) < 6){
            $result['status'] = 0;
            $result['message'] = 'Паролата трябва да е над 5 символа';
            return $result;
        }
        if($password != $passwordConfirm){
            $result['status'] = 0;
            $result['message'] = 'Паролите не съвпадат';
            return $result;
        }

        if(!$result['status']){
            return $result;
        }

        return $this->register($username, $password, $email, $position);
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

    public function passwordRecovery($email){
        include_once "../bootstrap/PHPMailer/PHPMailerAutoload.php";

        $password = $this->getUserPassword($email);

        if(!$password){
            $result = array(
                'status' => 0,
                'message' => 'Не е намерен потребител с този имейл адрес'
            );
            return $result;
        }
        $mail = new PHPMailer();
        $mail->Mailer = "mail";
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = "tls";
        $mail->Port = 587;
        $mail->Username = "betafinanspld@gmail.com";
        $mail->Password = 'betafinans3';
        $mail->CharSet = "UTF-8";
        $mail->setFrom("betafinanspld@gmail.com" , "Beta Finans");
        $mail->AddAddress($email);
        $mail->isHTML(true);
        $mail->Subject = "Забравена парола";
        $mail->Body = " Вашата парола е :". $password ." <br>
					    Можете да влезете в акаунта си от : <a href='http://localhost/accountingSoftware/' >Тук</a>";
        if(!$mail->Send())
        {
            $result = array(
                'status' => 0,
                'message' => 'Възникна грешка, моля опитайте отново по-късно'
           );
        } else {
            $result = array(
                'status' => 1,
                'message' => 'Паролата ви е изпратена успешно'
            );
        }
        return $result;
    }

}