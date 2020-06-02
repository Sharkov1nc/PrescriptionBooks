<?php
include_once 'Connection.php';
include_once '../bootstrap/phpExcel/Classes/PHPExcel.php';

class Drugs extends Connection
{
    public  static $instance;

    public function __construct()
    {
        parent::__construct();
    }

    public static function getInstance(){
        self::$instance = new Drugs();
        return self::$instance;
    }

    public function importDrugs(){
        $allowedFileType = [
            'application/vnd.ms-excel',
            'text/xls',
            'text/xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ];

        $result = array(
            'status' => 1
        );

        if (in_array($_FILES['drugs_excel']['type'], $allowedFileType)) {
            $targetPath = '../uploads/' . $_FILES['drugs_excel']['name'];
            move_uploaded_file($_FILES['drugs_excel']['tmp_name'], $targetPath);

            $excelReader = PHPExcel_IOFactory::createReaderForFile($targetPath);
            $excelObj = $excelReader->load($targetPath);
            $worksheet = $excelObj->getSheet(0);
            $lastRow = $worksheet->getHighestRow();

            $date = new DateTime();
            $dateField = $date->format('Y-m-d H:i:s');

            for ($row = 1; $row <= $lastRow; $row++) {
                $this->conn->query("INSERT INTO drugs(`name`, `date`) VALUES('".$worksheet->getCell('A'.$row)->getValue()."', '".$dateField."') ON DUPLICATE KEY UPDATE `date`=VALUES(`date`)");
            }
        } else {
            $result['status'] = 0;
            $result['message'] = "Моля използвайте Excel формат файлове";
        }

        return $result;
    }

    public function getDrugs(){
        $data = [];
        $result = $this->conn->query("SELECT * FROM drugs ORDER BY id DESC LIMIT 20");
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                $data[] = $row;
            }
        }
        return $data;
    }

    public function addDrug($data){
        $result = array(
            'status' => 1
        );
        if(strlen($data['name']) < 3 ){
            $result['status'] = 0;
            $result['message'] = "Моля въведете име на лекаство";
            return $result;
        }

        $date = new DateTime();
        $dateField = $date->format('Y-m-d H:i:s');
        $this->conn->query("INSERT INTO drugs(`name`, `date`) VALUES('".$data['name']."', '".$dateField."') ON DUPLICATE KEY UPDATE `date`=VALUES(`date`)");
        $result['drug'] = [
            'id' => $this->conn->insert_id,
            'name' => $data['name'],
            'date' => $dateField
        ];
        return $result;
    }

    public function searchDrug($drugId = null, $name = null){
        $data = array();
        $where = '';
        if($drugId){
            $where = " id = ".$drugId;
        } else {
            $where = " `name` LIKE '%".$name."%' ";
        }
        $result  = $this->conn->query($query = "SELECT * FROM drugs WHERE ". $where);
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                $data[] = $row;
            }
        }
        return $data;
    }

    public function removeDrug($userId){
        $this->conn->query("DELETE FROM drugs WHERE id=".intval($userId));
        $result = array(
            'status' => 1
        );
        if($this->conn->error){
            $result['status'] = 0;
            $result['message'] = 'Не можете да изтриете лекарството';
        }
        return $result;
    }

    public function editDrug($data){
        $result = array(
            'status' => 1
        );

        if(strlen($data['name']) < 3 ){
            $result['status'] = 0;
            $result['message'] = "Моля въведете име на лекарство";
            return $result;
        }

        $this->conn->query("UPDATE drugs SET name = '".$data['name']."' WHERE id =" . $data['drug_id']);
        if($this->conn->error){
            $result['status'] = 0;
            $result['message'] = 'Възникна грешка, моля опитайте отново по-късно';
            return $result;
        } else {
            $result['changes'] = [
                'id' => $data['drug_id'],
                'name' => $data['name']
            ];
        }
        return $result;
    }
}