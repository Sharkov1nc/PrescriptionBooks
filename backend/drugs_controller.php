<?php
include_once 'Drugs.php';
$drugs = new Drugs();

if(isset($_FILES['drugs_excel'])){
    $result = $drugs->importDrugs($_FILES['drugs_excel']);
    echo json_encode($result);
} else if(isset($_POST['action'])){

    if ($_POST['action'] == 'add'){
        $result = $drugs->addDrug($_POST);
        echo json_encode($result);
    }

    if ($_POST['action'] == 'delete'){
        $result = $drugs->removeDrug(intval($_POST['drug_id']));
        echo json_encode($result);
    }

    if($_POST['action'] == 'edit'){
        $result = $drugs->editDrug($_POST);
        echo json_encode($result);
    }

} else if(isset($_GET['action'])){

    if($_GET['action'] == 'search') {
        $result = [];
        if (isset($_GET['name']) && strlen($_GET['name']) > 2) {
            $result = $drugs->searchDrug(null, $_GET['name']);
            echo json_encode($result);
        } else if(isset($_GET['drug_id'])){
            $result = $drugs->searchDrug($_GET['drug_id'], null);
            echo json_encode($result);
        } else {
            echo json_encode($result);
        }
    }

}


