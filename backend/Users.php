<?php
include_once 'Connection.php';
include_once 'Authentication.php';

class Users extends connection {

    public  static $instance;
    public $user;

    public function __construct()
    {
        parent::__construct();
        $auth = new Authentication();
        $this->user = $auth->user;
    }

    public static function getInstance(){
        self::$instance = new Users();
        return self::$instance;
    }

    public function getUsers($type = null){
        $data = array();
        $joins = '';
        $selects = '';
        $where = '';
        if($type === 'doctors'){
            $where = "AND users.`user_position` = 2";
        } else if($type === 'patients') {
            $where = "AND users.`user_position` = 3 AND prescription_books.doctor_id = ". $this->user->id;
            $joins = "INNER JOIN prescription_books ON prescription_books.patient_id = users.id";
            $selects = ', prescription_books.doctor_id';
        } else if($type === 'pharmacy') {
            $where = "AND users.`user_position` = 4";
        }
        if(!$type){
            $joins = "INNER JOIN positions ON positions.id = users.user_position";
            $selects = ', `positions`.`position`';
        }
        $result  = $this->conn->query("SELECT users.id, users.fname, users.lname, users.email, users.`date` ". $selects ."  FROM users ". $joins ." WHERE users.`user_position` != 1 ". $where ." ORDER BY users.id DESC");
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
        $where = '';
        if($this->user->user_position == 2){
            $where = 'AND prescription_books.doctor_id = ' . $this->user->id;
        }
        if($userId){
            $query = "SELECT users.id, users.fname, users.lname, users.email, users.egn, users.`date`, users.user_position as position_id, positions.position as `position`, prescription_books.doctor_id as doctor FROM users INNER JOIN `positions` ON positions.id = users.user_position LEFT JOIN prescription_books ON users.id = prescription_books.patient_id WHERE users.id = ".$userId."  AND users.user_position != 1 ".$where." ";
        } else {
            $lnameSearch = '';
            if($lname){
                $lnameSearch = " OR users.lname LIKE '%".$lname."%' ";
            }
            $query = "SELECT users.id, users.fname, users.lname, users.email, users.egn, users.`date`, users.user_position as position_id, positions.position as `position` FROM users INNER JOIN `positions` ON positions.id = users.user_position LEFT JOIN prescription_books ON users.id = prescription_books.patient_id WHERE (users.fname LIKE '%".$fname."%' ". $lnameSearch .") AND users.user_position != 1 ".$where." ";
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
        $result = $this->conn->query("SELECT recipe.id as recipe_id FROM prescription_books LEFT JOIN recipe ON prescription_books.id = recipe.prescription_book_id LEFT JOIN users ON prescription_books.patient_id = users.id WHERE users.id = ".$userId);
        if($result->num_rows > 0){
            $row = $result->fetch_assoc();
            if(!empty($row['recipe_id'])){
                $this->conn->query("DELETE FROM recipe_drugs WHERE recipe_id = ".$row['recipe_id']);
                $this->conn->query("DELETE FROM recipe WHERE id =".$row['recipe_id'] );
            }
        }
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