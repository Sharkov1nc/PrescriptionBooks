<?php
include_once 'PrescriptionBooks.php';
$prescriptionBooks = new PrescriptionBooks();
if(isset($_POST['action'])){
    if($_POST['action'] == 'add_prescription'){
        $result = $prescriptionBooks->addPrescription($_POST);
        echo json_encode($result);
    }
} else if(isset($_POST['action'])) {

}