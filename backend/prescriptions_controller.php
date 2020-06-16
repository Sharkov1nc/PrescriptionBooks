<?php
include_once 'PrescriptionBooks.php';
$prescriptionBooks = new PrescriptionBooks();
if(isset($_POST['action'])){
    if($_POST['action'] == 'add'){
        $result = $prescriptionBooks->addRecipe($_POST);
        echo json_encode($result);
    } else if($_POST['action'] == 'delete'){
        $result = $prescriptionBooks->deleteRecipe($_POST['recipe_id']);
        echo json_encode($result);
    } else if($_POST['action'] == 'edit'){
        $result = $prescriptionBooks->editRecipe($_POST);
        echo json_encode($result);
    } else if($_POST['action'] == 'preview'){
        $result = $prescriptionBooks->previewRecipe($_POST);
        echo json_encode($result);
    }
} else if(isset($_GET['action'])) {
    if($_GET['action'] == 'search') {
        $result = [];
        if (isset($_GET['names']) && strlen($_GET['names']) > 2) {
            $names = explode(" ",$_GET['names']);
            $fname = $names[0];
            $lname = null;
            if(isset($names[1])){
                $lname = $names[1];
            }
            $result = $prescriptionBooks->searchRecipe(null, $fname, $lname);
            echo json_encode($result);
        } else if(isset($_GET['recipe_id'])){
            $result = $prescriptionBooks->searchRecipe($_GET['recipe_id'], null, null);
            echo json_encode($result);
        } else {
            echo json_encode($result);
        }
    }
}