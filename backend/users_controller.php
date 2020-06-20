<?php
include_once 'Users.php';
include_once 'Authentication.php';

$auth = new Authentication();
$users = new Users();

if(isset($_FILES['users_excel'])){
    $result = $users->importPatients($_FILES['users_excel']);
    echo json_encode($result);
} else if(isset($_POST['action'])){
    if ($_POST['action'] == 'add'){
        if($_POST['position'] == 3 && !isset($_POST['doctor'])){
           $_POST['doctor'] = $auth->user->id;
        }
        $result = $auth->validateRegister($_POST);
        echo json_encode($result);
    }

    if ($_POST['action'] == 'delete'){
        $result = $users->removeUser(intval($_POST['user_id']));
        echo json_encode($result);
    }

    if($_POST['action'] == 'edit'){
        $result = $users->editUser($_POST);
        echo json_encode($result);
    }

} else if (isset($_GET['action'])) {

    if($_GET['action'] == 'search') {
        $result = [];
        if (isset($_GET['names']) && strlen($_GET['names']) > 2) {
            $names = explode(" ",$_GET['names']);
            $fname = $names[0];
            $lname = null;
            if(isset($names[1])){
                $lname = $names[1];
            }
            $result = $users->searchUser(null, $fname, $lname);
            echo json_encode($result);
        } else if(isset($_GET['user_id'])){
            $result = $users->searchUser($_GET['user_id'], null, null);
            echo json_encode($result);
        } else {
            echo json_encode($result);
        }
    }
}