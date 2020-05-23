<?php
include_once 'Connection.php';

class Users extends connection {

    public $company;
    public  static $instance;

    public function __construct()
    {
        parent::__construct();
    }

    public static function getInstance(){
        self::$instance = new Users();
        return self::$instance;
    }

    public function getUsers(){
        $data = array();
        $result  = $this->conn->query("SELECT users.id, users.fname, users.lname, users.email, `positions`.`position` FROM users INNER JOIN positions ON positions.id = users.user_position WHERE users.`user_position` != 1 ORDER BY users.id DESC");
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                $data[] = $row;
            }
        }
        return $data;
    }

    public function searchUser($username){
        $data = array();

        $result  = $this->conn->query("SELECT users.id, users.username, users.email, users.password, users.position as position_id, positions.position FROM users INNER JOIN `positions` ON positions.id = users.position WHERE (users.username LIKE '%".$username."%' OR users.id = ".intval($username).") AND `position` != 1 ");
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                $data[] = $row;
            }
        }

        return $data;
    }

    public function editUser($username, $email, $password, $position = null, $egn = null, $userId){
        $result = array(
            'status' => 1
        );
        if(strlen($username) < 6){
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
        if ($egn && strlen($egn) < 6) {
            $result['status'] = 0;
            $result['message'] = 'Невалиден единен граждански номер';
            return $result;
        }

        $updateQ = "UPDATE users SET username = '".$username."', email = '".$email."', password = '".$password."'";
        if($position){
            $updateQ .= ' ,position = '.$position;
        }
        if($egn){
            $updateQ .= ' ,egn = '.$egn;
            $_SESSION['user']->egn = $egn;
        }
        $updateQ.= ' WHERE id = '.$userId;

        $this->conn->query($updateQ);
        $_SESSION['user']->password = $password;
        $_SESSION['user']->username = $username;
        $_SESSION['user']->email = $email;

        return $result;
    }


    public function removeUser($userId){

        $this->conn->query("DELETE FROM company_details WHERE user_id = ".intval($userId)."");
        $this->conn->query("DELETE FROM users WHERE id=".intval($userId)."");
        $result = array(
            'status' => 1
        );
        if($this->conn->error){
            $result['status'] = 0;
            $result['message'] = 'Не можете да изтриете потребител докато участва в приходи, разходи или договори';
        }
        return $result;
    }

}