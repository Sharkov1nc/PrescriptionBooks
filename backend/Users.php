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

    public function getUsers($type = null){
        $data = array();
        $joinPosition = '';
        $position = '';
        if($type === 'doctors'){
            $position = "AND users.`user_position` = 2";
        } else if($type === 'patients') {
            $position = "AND users.`user_position` = 3";
        } else if($type === 'pharmacy') {
            $position = "AND users.`user_position` = 4";
        }
        if(!$type){
            $joinPosition = "INNER JOIN positions ON positions.id = users.user_position";
        }
        $result  = $this->conn->query("SELECT users.id, users.fname, users.lname, users.email, users.`date` ". ($joinPosition != '' ? ', `positions`.`position`' : '') ."  FROM users ". $joinPosition ." WHERE users.`user_position` != 1 ". $position ." ORDER BY users.id DESC");
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                $data[] = $row;
            }
        }
        return $data;
    }

    public function getPatients(){
        $data = array();
        $result  = $this->conn->query("SELECT users.id, users.fname, users.lname, users.email, users.`date` FROM users  WHERE user_position = 3 ORDER BY id DESC");
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                $data[] = $row;
            }
        }
        return $data;
    }

    public function searchUser($userId = null, $fname = null, $lname = null){
        $data = array();
        $query = '';
        if($userId){
            $query = "SELECT users.id, users.fname, users.lname, users.email, users.egn, users.`date`, users.user_position as position_id, positions.position as `position`, prescription_books.doctor_id as doctor FROM users INNER JOIN `positions` ON positions.id = users.user_position LEFT JOIN prescription_books ON users.id = prescription_books.patient_id WHERE users.id = ".$userId."  AND users.user_position != 1 ";
        } else {
            $lnameSearch = '';
            if($lname){
                $lnameSearch = " OR users.lname LIKE '%".$lname."%' ";
            }
            $query = "SELECT users.id, users.fname, users.lname, users.email, users.egn, users.`date`, users.user_position as position_id, positions.position as `position` FROM users INNER JOIN `positions` ON positions.id = users.user_position WHERE (users.fname LIKE '%".$fname."%' ". $lnameSearch .") AND users.user_position != 1 ";
        }
        $result  = $this->conn->query($query);
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                $data[] = $row;
            }
        }

        return $data;
    }

    public function editUser($data){
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

        $this->conn->query("UPDATE users SET fname = '".$data['fname']."', lname = '".$data['lname']."', email = '".$data['email']."', egn = '".$data['egn']."' WHERE id =" . $data['user_id']);
        if($this->conn->error){
            $result['status'] = 0;
            $result['message'] = 'Възникна грешка, моля опитайте отново по-късно';
            return $result;
        } else {
            $result['changes'] = [
                'fname' => $data['fname'],
                'lname' => $data['lname'],
                'email' => $data['email'],
                'egn' => $data['egn'],
                'user_id' => $data['user_id']
            ];
        }
        return $result;
    }


    public function removeUser($userId){
        $this->conn->query("DELETE FROM prescription_books WHERE patient_id = ".intval($userId));
        $this->conn->query("DELETE FROM users WHERE id=".intval($userId));
        $result = array(
            'status' => 1
        );
        if($this->conn->error){
            $result['status'] = 0;
            $result['message'] = 'Не можете да изтриете потребителя';
        }
        return $result;
    }

}