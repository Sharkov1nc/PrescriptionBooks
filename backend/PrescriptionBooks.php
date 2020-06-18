<?php

include_once 'Connection.php';
include_once 'Authentication.php';
include_once '../bootstrap/dompdf/autoload.inc.php';
include_once '../bootstrap/PHPMailer/PHPMailerAutoload.php';

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

    public function getRecipeForWritten()
    {
        $data = [];
        $date =  gmdate('Y-m-d H:i:s', strtotime('-1 month'));
        $result = $this->conn->query("SELECT MAX(recipe.`date`) as max_date, users.fname as user_fname, users.lname as user_lname, prescription_books.id as id, recipe.id as recipe_id, recipe.`date` as recipe_date FROM prescription_books LEFT JOIN recipe ON prescription_books.id = recipe.prescription_book_id LEFT JOIN users ON prescription_books.patient_id = users.id WHERE  prescription_books.doctor_id = ".$this->user->id." GROUP BY users.id HAVING max_date < '".$date."' || max_date IS NULL ");
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                $data[] = $row;
            }
        }
        return $data;
    }

    public function getWrittenRecipe()
    {
        $data = [];
        $date =  gmdate('Y-m-d H:i:s', strtotime('-1 month'));
        $result = $this->conn->query("SELECT users.id, users.fname, users.lname, recipe.id as recipe_id, recipe.`date` as recipe_date FROM prescription_books LEFT JOIN recipe ON prescription_books.id = recipe.prescription_book_id LEFT JOIN users ON prescription_books.patient_id = users.id WHERE recipe.`date` > '".$date."' AND prescription_books.doctor_id = ".$this->user->id." GROUP BY users.id ORDER BY recipe.`date` DESC");
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                $data[] = $row;
            }
        }
        return $data;
    }

    public function addRecipe($data){
        $date = new DateTime();
        $dateField = $date->format('Y-m-d H:i:s');
        $hash = substr(hash('ripemd160', $this->user->fname.time()), 0, 32);
        $additionalInfo = addslashes($data['additional_info']);
        $this->conn->query("INSERT INTO recipe(hash, prescription_book_id, `date`, additional_information) VALUES('".$hash."', ".$data['prescription_id'].", '".$dateField."', '".$additionalInfo."')");
        $recipeId = $this->conn->insert_id;
        if(isset($data['drugs']) && !empty($data['drugs'])){
            foreach ($data['drugs'] as $drug){
                $this->conn->query("INSERT INTO recipe_drugs(recipe_id, drug_id, quantity) VALUES(".$recipeId.", ".$drug['id'].", ".$drug['quantity'].")");
            }
        }
        $result = [
            'status' => 1
        ];
        if($this->conn->error){
            $result['status'] = 0;
            $result['message'] = 'Възникна грешка, моля опитайте отново по-късно';
        } else {
            $res = $this->conn->query("SELECT patient_id FROM prescription_books WHERE id = ".$data['prescription_id']);
            if($res->num_rows > 0){
                $patient = $res->fetch_assoc();
                $this->sendPrescriptionCode($hash, $patient['patient_id']);
            }
        }

        return $result;
    }

    public function deleteRecipe($recipeId){
        $this->conn->query("DELETE FROM recipe_drugs WHERE recipe_id = ".$recipeId);
        $this->conn->query("DELETE FROM recipe WHERE id =".$recipeId );
        $result = [
            'status' => 1
        ];
        if($this->conn->error){
            $result['status'] = 0;
            $result['message'] = 'Възникна грешка, моля опитайте отново по-късно';
        }
        return $result;
    }

    public function editRecipe($data){
        $this->conn->query("UPDATE recipe SET additional_information = ".addslashes($data['additional_info']));
        $this->conn->query("DELETE FROM recipe_drugs WHERE recipe_id = ".$data['recipe_id']);
        if(isset($data['drugs']) && !empty($data['drugs'])){
            foreach ($data['drugs'] as $drug){
                $this->conn->query("INSERT INTO recipe_drugs(recipe_id, drug_id, quantity) VALUES(".$data['recipe_id'].", ".$drug['id'].", ".$drug['quantity'].")");
            }
        }
        $result = [
            'status' => 1
        ];
        if($this->conn->error){
            $result['status'] = 0;
            $result['message'] = 'Възникна грешка, моля опитайте отново по-късно';
        }
        return $result;
    }

    public function searchRecipe($recipeId = null, $userFname = null, $userLname = null){
        $query = '';
        $data = [];
        $date =  gmdate('Y-m-d H:i:s', strtotime('-1 month'));
        if($recipeId){
            $query = "SELECT MAX(recipe.`date`) as max_date, users.fname as user_fname, users.lname as user_lname, prescription_books.id as id, recipe.id as recipe_id, recipe.`date` as recipe_date, recipe.additional_information, doctor.fname as doctor_fname, doctor.lname as doctor_lname FROM prescription_books LEFT JOIN recipe ON prescription_books.id = recipe.prescription_book_id LEFT JOIN users ON prescription_books.patient_id = users.id INNER JOIN users as doctor ON doctor.id = prescription_books.doctor_id WHERE prescription_books.doctor_id = ".$this->user->id." AND recipe.id = ".$recipeId." GROUP BY users.id HAVING max_date < '".$date."' || max_date IS NULL ";
        } else {
            $lnameSearch = '';
            if($userLname){
                $lnameSearch = " OR users.lname LIKE '%".$userLname."%' ";
            }
            $query = "SELECT MAX(recipe.`date`) as max_date, users.fname as user_fname, users.lname as user_lname, prescription_books.id as id, recipe.id as recipe_id, recipe.`date` as recipe_date FROM prescription_books LEFT JOIN recipe ON prescription_books.id = recipe.prescription_book_id LEFT JOIN users ON prescription_books.patient_id = users.id WHERE (users.fname LIKE '%".$userFname."%' ". $lnameSearch .") AND prescription_books.doctor_id = ".$this->user->id." GROUP BY users.id HAVING max_date < '".$date."' || max_date IS NULL ";
        }
        $result  = $this->conn->query($query);
        if($result->num_rows > 0){
            $key = 0;
            while($row = $result->fetch_assoc()){
                $data[$key] = $row;
                if($recipeId) {
                    $qResults = $this->conn->query("SELECT drugs.id as id, drugs.name as `name`, quantity FROM recipe_drugs as rd INNER JOIN drugs ON drugs.id = rd.drug_id WHERE rd.recipe_id =" . $row['recipe_id']);
                    $drugs = [];
                    if ($qResults->num_rows > 0) {
                        while ($drug = $qResults->fetch_assoc()) {
                            $drugs[] = [
                                'id' => intval($drug['id']),
                                'name' => $drug['name'],
                                'quantity' => intval($drug['quantity'])
                            ];
                        }
                    }
                    if (!empty($drugs)) {
                        $data[$key]['drugs'] = $drugs;
                    }
                }
            }
        }

        return $data;
    }

    public function searchWrittenRecipe($recipeId = null,  $userFname = null, $userLname = null){
        $query = '';
        $data = [];
        $date =  gmdate('Y-m-d H:i:s', strtotime('-1 month'));
        if($recipeId){
            $query = "SELECT users.id as user_id, users.fname as user_fname, users.lname as user_lname, recipe.id as recipe_id, recipe.`date` as recipe_date, recipe.additional_information, doctor.fname as doctor_fname, doctor.lname as doctor_lname  FROM prescription_books INNER JOIN recipe ON prescription_books.id = recipe.prescription_book_id INNER JOIN users ON prescription_books.patient_id = users.id INNER JOIN users as doctor ON doctor.id = prescription_books.doctor_id WHERE recipe.`date` > '".$date."' AND prescription_books.doctor_id = ".$this->user->id." AND recipe.id = ".$recipeId." GROUP BY users.id ORDER BY recipe.`date` DESC";
        } else {
            $lnameSearch = '';
            if($userLname){
                $lnameSearch = " OR users.lname LIKE '%".$userLname."%' ";
            }
            $query = "SELECT users.id as user_id, users.fname as user_fname, users.lname as user_lname, recipe.id as recipe_id, recipe.`date` as recipe_date FROM prescription_books LEFT JOIN recipe ON prescription_books.id = recipe.prescription_book_id LEFT JOIN users ON prescription_books.patient_id = users.id WHERE recipe.`date` > '".$date."' AND prescription_books.doctor_id = ".$this->user->id." AND (users.fname LIKE '%".$userFname."%' ". $lnameSearch .") GROUP BY users.id ORDER BY recipe.`date` DESC";
        }
        $result  = $this->conn->query($query);
        if($result->num_rows > 0){
            $key = 0;
            while($row = $result->fetch_assoc()){
                $data[$key] = $row;
                if($recipeId) {
                    $qResults = $this->conn->query("SELECT drugs.id as id, drugs.name as `name`, quantity FROM recipe_drugs as rd INNER JOIN drugs ON drugs.id = rd.drug_id WHERE rd.recipe_id =" . $row['recipe_id']);
                    $drugs = [];
                    if ($qResults->num_rows > 0) {
                        while ($drug = $qResults->fetch_assoc()) {
                            $drugs[] = [
                                'id' => intval($drug['id']),
                                'name' => $drug['name'],
                                'quantity' => intval($drug['quantity'])
                            ];
                        }
                    }
                    if (!empty($drugs)) {
                        $data[$key]['drugs'] = $drugs;
                    }
                }
            }
        }

        return $data;
    }

    public function printRecipe($recipeId = null, $data = null, $previous = false){
        if($recipeId) {
            if($previous){
                $data = $this->searchRecipe($recipeId);
            } else {
                $data = $this->searchWrittenRecipe($recipeId);
            }
            $data = $data[0];
            $data['doctor'] = $data['doctor_fname'] . ' ' . $data['doctor_lname'];
        }
        if(!$data || ($data && empty($data))){
            return null;
        }

        $document = $this->createDocument($data);
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($document);
        $dompdf->setPaper('A4');
        $dompdf->render();
        $dompdf->stream(time() . '.pdf', ['Attachment' => 0]);
    }

    public function markRecipeAsTaken($recipeId){
        // recipe received date and pharmacy id update
    }

    private function getUserEmail($userId){
        $result = $this->conn->query("SELECT email FROM users WHERE id = ".$userId);
        if($result->num_rows > 0){
            $data = $result->fetch_assoc();
            $email = $data['email'];
            return $email;
        }
        return false;
    }

    private function sendPrescriptionCode($hash, $patientId){
        $email = $this->getUserEmail($patientId);
        if(!$email){
            return;
        }
        $mail = new PHPMailer();
        $mail->Mailer = "mail";
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = "tls";
        $mail->Port = 587;
        $mail->Username = "i.sharkovv@gmail.com";
        $mail->Password = 'powerm3sharkov';
        $mail->CharSet = "UTF-8";
        $mail->setFrom("i.sharkovv@gmail.com" , "Дигитални рецептурни книжки");
        $mail->AddAddress($email);
        $mail->isHTML(true);
        $mail->Subject = "Изписана рецепта";
        $mail->Body = "Вашият личен лекар изписа нова рецепта към рецептурната ви книжка. <br> Можете да вземате изписаните лекарства от удобна за вас аптека <br> Продиктувайте следния код <b>". $hash ."</b> на фармацевта за да получите лекарствата си." ;
        $mail->Send();
    }

    private function createDocument($data){
        $drugsHtml = '';
        foreach ($data['drugs'] as $key => $drug){
            $drugsHtml .=
                '<tr>
                    <td>'. ($key + 1) .'</td>      
                    <td>'.$drug["name"].'</td>
                    <td class="quantity">'.$drug["quantity"].'</td>
                </tr>;';
        }

        $html = '
            <!doctype html>
            <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                    <meta name="viewport"
                          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
                    <meta http-equiv="X-UA-Compatible" content="ie=edge">
                    <style>
                        body {
                            font-family: DejaVu Sans;
                            font-size: 12px;;
                        }
                        .patient-info {
                            width: 50%;
                            display: inline-block;
                        }
                        .doctor-info {
                            width: 50%;
                            padding-left: 150px;
                            margin-top: -13px;
                            display: inline-block;
                        }
                        .font-12{
                            font-size: 12px;
                        }
                        .title {
                            text-align: center;
                            padding-top: 30px;
                            font-size: 34px;
                        }
                        .table-content{
                            margin-top: 20px;
                        }
                        table {
                            border-collapse: collapse;
                            width: 100%;
                        }
                
                        td, th {
                            border: 1px solid #a4a4a4;
                            text-align: left;
                            padding: 8px;
                        }
                
                        tr:nth-child(even) {
                            background-color: rgb(220,220,220) ;
                        }
                        .text-center{
                            text-align: center;
                        }
                        .quantity {
                            text-align: right;
                            padding-right: 13px;
                        }
                        .additional-info {
                            margin-top: 45px;
                        }
                        .additional-info .info-title{
                            font-weight: 600;
                            font-size: 13px;
                        }
                        .additional-info .info-content {
                            padding: 5px 0 0 20px;
                        }
                        @page { size: 595px 842px }
                    </style>
                
                </head>
                <body>
                <div class="info">
                    <div class="patient-info">
                        <i>Пациент:</i><br>
                        <i>'.$data["user_fname"]. " " . $data["user_lname"] .'</i><br>
                        <i>Дата:</i><br>
                        <i>'.$data["recipe_date"].'</i>
                    </div>
                    <div class="doctor-info">
                        <i>Лекар:</i><br>
                        <i>'. $data['doctor'] .'</i><br>
                    </div>
                </div>
                <div class="title">
                    Рецепта
                </div>
                <div class="table-content">
                    <table>
                        <tr>
                            <th>#</th>
                            <th width="75%">Име на лекарство</th>
                            <th>Количество</th>
                        </tr>
                        '.$drugsHtml.'
                    </table>
                </div>
                <div class="additional-info">
                    <div class="info-title"> Допълнителна информация </div>
                    <div class="info-content">'. $data['additional_information'].'</div>
                </div>
                </body>
            </html>
        ';

        return $html;
    }
}