<?php
include_once 'PrescriptionBooks.php';
$prescriptionBooks = new PrescriptionBooks();
if(isset($_POST['action'])){
    if($_POST['action'] == 'add'){
        if(isset($_POST['drugs']) && !empty($_POST['drugs'])){
            $result = $prescriptionBooks->addRecipe($_POST);
        } else {
            $result = [
                'status' => 0,
                'message' => 'За да добавите рецепта е необходимо да изпишете лекарства за пациента!'
            ];
        }
        echo json_encode($result);
    } else if($_POST['action'] == 'delete'){
        $result = $prescriptionBooks->deleteRecipe($_POST['recipe_id']);
        echo json_encode($result);
    } else if($_POST['action'] == 'edit'){
        if(isset($_POST['drugs']) && !empty($_POST['drugs'])) {
            $result = $prescriptionBooks->editRecipe($_POST);
        } else {
            $result = [
                'status' => 0,
                'message' => 'За да редактирате рецепта е необходимо да изпишете лекарства за пациента!'
            ];
        }
        echo json_encode($result);
    } else if($_POST['action'] == 'preview'){
        if (isset($_POST['names']) && strlen($_POST['names']) > 2) {
            $names = explode(" ", $_POST['names']);
            $fname = $names[0];
            $lname = null;
            if (isset($names[1])) {
                $lname = $names[1];
            }
            $drugs = json_decode($_POST['drugs']);
            $drugsArr = [];
            foreach ($drugs as $drug){
                $drugsArr[] = (array) $drug;
            }
            $data = [
                'user_fname' => $fname,
                'user_lname' => $lname,
                'recipe_date' => gmdate('Y-m-d H:i:s', time()),
                'drugs' => $drugsArr,
                'additional_information' => $_POST['additional_info'],
                'doctor' => $prescriptionBooks->user->fname . " " . $prescriptionBooks->user->lname
            ];
            $result = $prescriptionBooks->printRecipe(null, $data);
            echo json_encode($result);
        }
    }  else if($_POST['action'] == "search_hash"){
        $recipe = $prescriptionBooks->searchHash($_POST['hash']);
        $result = $prescriptionBooks->printRecipe($recipe['recipe_id']);
    } else if($_POST['action'] == 'mark-as-taken'){
        $result = $prescriptionBooks->markRecipeAsTaken($_POST['recipe_id']);
        echo json_encode($result);
    }
} else if(isset($_GET['action'])) {
    if($_GET['action'] == 'search' || $_GET['action'] == 'search_written') {
        $result = [];
        if (isset($_GET['names']) && strlen($_GET['names']) > 2) {
            $names = explode(" ",$_GET['names']);
            $fname = $names[0];
            $lname = null;
            if(isset($names[1])){
                $lname = $names[1];
            }
            if($_GET['action'] == 'search_written'){
                $result = $prescriptionBooks->searchWrittenRecipe(null, $fname, $lname);

            } else {
                $result = $prescriptionBooks->searchRecipe(null, $fname, $lname);
            }
            echo json_encode($result);
        } else if(isset($_GET['recipe_id'])){
            if($_GET['action'] == 'search_written'){
                $result = $prescriptionBooks->searchWrittenRecipe($_GET['recipe_id'], null, null);
            } else {
                $result = $prescriptionBooks->searchRecipe($_GET['recipe_id'], null, null);
            }
            echo json_encode($result);
        } else {
            echo json_encode($result);
        }
    }
    else if($_GET['action'] == 'print'){
        $previous = false;
        if(isset($_GET['previous'])){
            $previous = true;
        }
        $result= $prescriptionBooks->printRecipe($_GET['recipe_id'], null, $previous);
    } else if($_GET['action'] == "search_hash"){
        $result = $prescriptionBooks->searchHash($_GET['hash']);
        echo json_encode($result);
    }
}