<?php
include_once 'users.php'; // вмъкваме файла съдържащ класа Users
include_once 'Authentication.php'; // вмъкваме файла съдържащ класа Auth

$auth = new Auth(); // създаваме нов обект от клас Auth
$users = new Users(); // създаваме нов обект от клас Users

if(isset($_POST['action'])){ // проверяваме дали имаме елемент action в масива $_POST

    if ($_POST['action'] == 'add'){ // проверяваме дали имаме елемент add в масива $_POST
        // изивикваме метод validateRegister, който валидира въведените даннии и ако са валидни преминава към регистрация на потребител
        $result = $auth->validateRegister($_POST['username'], $_POST['password'], $_POST['password'], $_POST['email'], $_POST['position']);
        echo json_encode($result); // принтираме резултата от изпълнението на метода в кодиран json формат за да може ajax да го вземе
    }

    if ($_POST['action'] == 'delete'){ // проверяваме дали имаме елемент delete в масива $_POST
        $result = $users->removeUser($_POST['id']); // изивикваме метод removeUser, който изтрива потребител
        echo json_encode($result); // принтираме резултата от изпълнението на метода в кодиран json формат за да може ajax да го вземе
    }

    if($_POST['action'] == 'edit'){ // проверяваме дали имаме елемент edit в масива $_POST
        // проверяваме да ли имаме елемент egn в масива $_POST, ако имаме го присвояваме на променливата $egn, ако нямаме присвояваме null
        $egn = isset($_POST['egn']) ? $_POST['egn'] : null; 
        $result = $users->editUser($_POST['username'], $_POST['email'], $_POST['password'], $_POST['position'], $egn, $_POST['user-id']); 
        echo json_encode($result); // принтираме резултата от изпълнението на метода в кодиран json формат за да може ajax да го вземе
    }

    if ($_POST['action'] == 'profile_edit'){ // проверяваме дали имаме елемент edit в масива $_POST
        // изивикваме метод addCompanyDetails, който добавя или редактира информацията за компанията, която потребителя представлява
        $companyResult = $users->addCompanyDetails($_POST['name'], $_POST['city'], $_POST['street'], $_POST['bulstat'], $_POST['company-id'], $_POST['user-id']);
        // изивикваме метод editUser, който редактира информацията за регистриран вече потребител
        $userResult = $users->editUser($_POST['username'], $_POST['email'], $_POST['password'], null, $_POST['egn'], $_POST['user-id']);

        if($companyResult['status'] == 0){ // проверяваме статуса върнат от резултата на изълнението на метода addCompanyDetails, статус 0 означава ,че имаме върната грешка
            $result = $companyResult; // присвояваме резултата от изпълнението на метода addCompanyDetails na променливата $result
        } else if($userResult['status'] == 0) { // проверяваме статуса върнат от резултата на изълнението на метода editUser, статус 0 означава ,че имаме върната грешка
            $result = $userResult; // присвояваме резултата от изпълнението на метода editUser na променливата $result
        } else { // ако нямаме статус за грешки в изпълнението на методите addCompanyDetails и editUser създаваме променлива $result съдържаща статус 1 (за успешно изпълнение)
            $result = array(
                'status' => 1
            );
        }

        echo json_encode($result); // принтираме резултата променливата $result в кодиран json формат за да може ajax да вземе данните
    }

} else if (isset($_GET['action'])) { // проверяваме дали имаме елемент edit в масива $_GET

    if($_GET['action'] == 'search') {
        if (isset($_GET['username'])) { // проверяваме дали имаме елемент edit в масива $_GET
            $key = $_GET['username']; // присвояваме на променливата $key фразата (име или част от име на потребител), по която търсим
        } else {
            $key = $_GET['id']; // присвояваме на променливата $key id на потребителя, който търсим
        }
        $result = $users->searchUser($key); // изивикваме метод searchUser, който търси потребител по id или име на потребител
        echo json_encode($result); // принтираме резултата от изпълнението на метода в кодиран json формат за да може ajax да го вземе
    }

    if($_GET['action'] == 'load'){ // проверяваме дали имаме елемент edit в масива $_GET
        $result = $users->getUsers(); // изивикваме метод getUsers, който извлича всички потребители от базата данни
        echo json_encode($result); // принтираме резултата от изпълнението на метода в кодиран json формат за да може ajax да го вземе
    }
}