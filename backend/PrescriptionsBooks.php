<?php
include_once 'Connection.php';
include_once '../bootstrap/phpExcel/Classes/PHPExcel.php';

class PrescriptionsBooks extends Connection
{
    public  static $instance;

    public function __construct()
    {
        parent::__construct();
    }

    public static function getInstance(){
        self::$instance = new PrescriptionsBooks();
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

            for ($row = 1; $row <= $lastRow; $row++) {
                $this->conn->query("INSERT INTO drugs(`name`) VALUES('".$worksheet->getCell('A'.$row)->getValue()."') ON DUPLICATE KEY UPDATE");
            }
        } else {
            $result['status'] = 0;
            $result['message'] = "Моля използвайте Excel формат файлове";
        }

        return $result;
    }

    public function getDrugs(){
        $data = [];
        $result = $this->conn->query("SELECT * FROM drugs");
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                $data[] = $row;
            }
        }
        return $data;
    }
}