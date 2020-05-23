<?php
include_once 'Profits.php'; // вмъкваме файла съдържащ класа Profits
include_once '../bootstrap/dompdf/autoload.inc.php'; // вмъкваме файла съдържащ класа Dompdf

use Dompdf\Dompdf; // използваме namespace Dompdf

$profit = new Profits(); // създаваме нов обект от клас Profits

if(isset($_POST['action'])){ // проверяваме дали имаме елемент action в масива $_POST

    if ($_POST['action'] == 'add'){ // проверяваме дали имаме елемент add в масива $_POST

        $result = array(
            'status' => 1
        );
        // Проверяваме дали е въведени име на прихода, което съдържа повече от 3 символа
        if(strlen($_POST['name']) < 3){
            $result['status'] = 0;
            $result['message'] = 'Не сте въвели име на прихода';
            echo json_encode($result); // принтираме масива $result в кодиран json формат за да може ajax да вземе данните
            exit; // прекратяваме изпълнението на кода
        }

        $items = array();
        // Циклим през всеки един от продуктите на фактуриране и валидираме въведените данни
        for($i=1;$i<=$_POST['profits-items-count'];$i++){
            if(isset($_POST['item-'.$i]) && strlen($_POST['item-'.$i]) > 3 &&
             isset($_POST['item-price-'.$i]) && strlen($_POST['item-price-'.$i]) > 0){
                $items[] = array(
                    'item' => $_POST['item-'.$i],
                    'price' => $_POST['item-price-'.$i]
                );
            } else {
                if(!isset($_POST['item-'.$i]) || strlen($_POST['item-'.$i]) <= 3){
                    $result['status'] = 0;
                    $result['message'] = 'Не сте въвели име на предмет на фактуритане';
                    echo json_encode($result); // принтираме масива $result в кодиран json формат за да може ajax да вземе данните
                    exit; // прекратяваме изпълнението на кода
                }
                if(!isset($_POST['item-price-'.$i]) || strlen($_POST['item-price-'.$i]) < 1){
                    $result['status'] = 0;
                    $result['message'] = 'Не сте въвели цена на предмет на фактуритане';
                    echo json_encode($result); // принтираме масива $result в кодиран json формат за да може ajax да вземе данните
                    exit; // прекратяваме изпълнението на кода
                }
            }
        }
        $profit->createProfit($_POST['name'], $_POST['created_for'], $items); // изивикваме метод createProfit, който добавя нов приход
        echo json_encode($result); // принтираме резултата от изпълнението на метода в кодиран json формат за да може ajax да го вземе
        exit; // прекратяваме изпълнението на кода
    }

    if($_POST['action'] == 'delete'){ // проверяваме дали имаме елемент delete в масива $_POST
        $profit->removeProfit($_POST['id']); // изивикваме метод removeProfit, който изтрива приход
    }

    if($_POST['action'] == 'edit'){ // проверяваме дали имаме елемент edit в масива $_POST
        $result = array(
            'status' => 1
        );

        // Проверяваме дали е въведено име на приход, което съдържа повече от 3 символа
        if(strlen($_POST['name']) < 3){
            $result['status'] = 0;
            $result['message'] = 'Не сте въвели име на прихода';
            echo json_encode($result); // принтираме масива $result в кодиран json формат за да може ajax да вземе данните
            exit; // прекратяваме изпълнението на кода
        }
        $items = array();
        // Циклим през всеки един от продуктите на фактуриране и валидираме въведените данни
        for($i=1;$i<=$_POST['profits-items-count'];$i++){
            if(isset($_POST['item-'.$i]) && strlen($_POST['item-'.$i]) > 3 && isset($_POST['item-price-'.$i]) && strlen($_POST['item-price-'.$i]) > 0){
                $items[] = array(
                    'item' => $_POST['item-'.$i],
                    'price' => $_POST['item-price-'.$i]
                );
            } else {
                if(!isset($_POST['item-'.$i]) || strlen($_POST['item-'.$i]) <= 3){
                    $result['status'] = 0;
                    $result['message'] = 'Не сте въвели име на продукт на фактуритане';
                    echo json_encode($result); // принтираме масива $result в кодиран json формат за да може ajax да вземе данните
                    exit; // прекратяваме изпълнението на кода
                }
                if(!isset($_POST['item-price-'.$i]) || strlen($_POST['item-price-'.$i]) < 1){
                    $result['status'] = 0;
                    $result['message'] = 'Не сте въвели цена на продукт на фактуритане';
                    echo json_encode($result); // принтираме масива $result в кодиран json формат за да може ajax да вземе данните
                    exit; // прекратяваме изпълнението на кода
                }
            }
        }
        $profit->editProfit($_POST['profit_id'] ,$_POST['name'], $_POST['created_for'], $items); // изивикваме метод editProfit, който редактира данните за вече съществуващ приход
        echo json_encode($result); // принтираме резултата от изпълнението на метода в кодиран json формат за да може ajax да го вземе
        exit; // прекратяваме изпълнението на кода
    }

} else if (isset($_GET['action'])){ // проверяваме дали имаме елемент action в масива $_GET

    if($_GET['action'] == 'view') { // проверяваме дали имаме елемент view в масива $_GET

        $html = $profit->printProfit($_GET['id']); // извикваме метод printProfit, който генерира html кода за прихода

        $dompdf = new Dompdf(); // създаваме нов обект от клас Dompdf

        $dompdf->loadHtml($html); // извикваме метод loadHtml, който зарежда html кода на прихода

        $dompdf->setPaper('A4'); // извикваме метод setPaper, който задава размер на листа

        $dompdf->render(); // извикваме метод, който рендерира html кода

        $dompdf->stream(time() . '.pdf', ['Attachment' => 0]); // извикваме метод, който показва прихода в pdf format
    }

    if($_GET['action'] == 'search'){ // проверяваме дали имаме елемент search в масива $_GET
        if(isset($_GET['profit-name'])){ // проверяваме дали имаме елемент profit-name в масива $_GET
            $key = $_GET['profit-name']; // присвояваме на променливата $key фразата (име или част от име на приход), по която търсим
        } else {
            $key = $_GET['profitId']; // присвояваме на променливата $key id на приход, който търсим
        }
        $result = $profit->searchProfit($key);  // изивикваме метод searchProfit, който търси приход по id или име на приход
        echo json_encode($result); // принтираме резултата от изпълнението на метода в кодиран json формат за да може ajax да го вземе
    }

    if($_GET['action'] == 'load'){ // проверяваме дали имаме елемент load в масива $_GET
        $result = $profit->getProfits(); // изивикваме метод getProfits, който извлича всички приходи от базата данни
        echo json_encode($result); // принтираме резултата от изпълнението на метода в кодиран json формат за да може ajax да го вземе
    }
}