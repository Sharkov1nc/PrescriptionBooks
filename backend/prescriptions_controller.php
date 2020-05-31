<?php
include_once 'PrescriptionsBooks.php';
$prescriptionsBooks = new PrescriptionsBooks();

if(isset($_FILES['drugs_excel'])){
    $result = $prescriptionsBooks->importDrugs();
    echo json_encode($result);
}
