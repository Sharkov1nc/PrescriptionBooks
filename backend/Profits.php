<?php
include_once 'Connection.php'; // вмъкваме файла съдържащ класа connection
include_once 'Authentication.php'; // вмъкваме файла съдържащ класа Auth

// декларираме клас Profits, който наследява клас connection (осъществяващ вързката с базата данни)
class Profits extends Connection {

    // вътрешна (частна) променлива за класа, достъпна само в самия клас
    private $user;

    // конструктур функция на класа Profits (изивква автоматично се при създаване на нов обект от класа)
    public function __construct() {
        parent::__construct(); // извикваме конструктур функцията на родителския клас (този, който наследяваме)
        $auth = new Authentication(); // Създаваме нов обект от клас Auth
        $this->user = $auth->initUser(); // присвояваме резулата от изпълнението на метода initUser на вътрешната променлива $user
    }

    // метод getProfits, който извлича данните за приходите от таблицата с приходи
    public function getProfits(){
        $data = array();
        $key = 0;

        if ($this->user->position == 3){ // проверяваме дали текущия потребител е клиент
            // select заявка извличаща всички приходи свързани с текущия потребител
            $profits = $this->conn->query("SELECT profits.id, profits.name, profits.date, users.username as created_for FROM profits INNER JOIN users ON users.id = profits.created_for WHERE profits.created_for = ".intval($this->user->id)." ORDER BY profits.id ASC");
        } else {
            // select заявка извличаща всички приходи
            $profits = $this->conn->query("SELECT profits.id, profits.name, profits.date, users.username as created_for FROM profits INNER JOIN users ON users.id = profits.created_for ORDER BY profits.id ASC");
        }
        if ($profits->num_rows > 0){ // проверяваме броя върнати редове от заявката дали е по-голям от 0 , ако е значи имаме резултати
            while ($row = $profits->fetch_assoc()){ // циклим за всеки един от резулатите и го представяме като масив
                $data[$key]['profit'] = $row; // записваме масива в масив $data
                $data[$key]['profit']['position_id'] = $this->user->position; // добавяме елемент на масива съдържащ позицията на текущия потребител
                // select заявка извличаща всички предмети на фактуриране свързани с приходи
                $profitItems = $this->conn->query("SELECT items.* FROM items INNER JOIN profits_items ON profits_items.item = items.id WHERE profits_items.profit = ".intval($row['id'])."");
                if ($profitItems->num_rows > 0){ // проверяваме броя върнати редове от заявката дали е по-голям от 0 , ако е значи имаме резултати
                    while($item = $profitItems->fetch_assoc()){ // циклим за всеки един от резулатите и го представяме като масив
                        $data[$key]['profit_items'][] = $item;  // записваме масива в масив $data
                    }
                }
                $key++;
            }
        }
        return $data; // връщаме масива $data, съдържащ извлечените резултати
    }

    // метод createProfit, който създава нов приход
    public function createProfit($name, $createdFor, $items){
        $date = new DateTime(); // вземаме текущата дата
        $dateField = $date->format('Y-m-d H:i:s'); // форматираме дата в подходящ формат
        // insert заявка, която добавя нов запис в таблица profits (таблицата за приходите)
        $this->conn->query("INSERT INTO profits(`name`, `created_for`, `date`) 
        VALUES('".$name."', ".$createdFor.", '".$dateField."')") or die();
        $profitId = $this->conn->insert_id; 
        // присвояваме ид-то на записа, който сме добавили в таблицата с приходи на променливата $profitId
        foreach ($items as $item) {  // циклим за всеки един от предметите на фактуриране
            // insert заявка, добавяща нов запис в таблица items
            $this->conn->query("INSERT INTO items(`name`, `price`) VALUES ('". $item['item'] . "', " . $item['price'] . ")") or die();
            $itemId = $this->conn->insert_id;
            // присвояваме ид-то на записа, който сме добавили в таблицата items на променливата $itemId
            // insert заявка, добавящя в ид-то на предмета на фактуриране и ид-то на прихода в таблица profits_items,
            // така осъществяваме връзка между всеки от предметите на фактуриране и прихода
            $this->conn->query("INSERT INTO profits_items(`profit`, `item`) VALUES (".$profitId.", ".$itemId.")") or die();
        }
    }

    // метод editProfit, който редактира вече съществуващ запис в таблицата с приходи 
    // (в нашия случай изтриваме вече съсществуващия запис и добавяме нов с модифицираните данни)
    public function editProfit($profit_id, $name, $createdFor, $items){
        $this->removeProfit($profit_id); // извикваме метода за изтриване на приход
        $this->createProfit($name, $createdFor, $items); // извикваме метода за добавяне на нов приход
    }

    // метод removeProfit, който изтрива приход от базата
    public function removeProfit($profitId){
        $itemsIds = array();
        // select заявка, която извлича всички свързани към прихода предмети на фактуриране
        $items = $this->conn->query("SELECT item FROM profits_items WHERE profit = ".$profitId."");
        if($items->num_rows > 0){ // проверяваме броя върнати редове от заявката дали е по-голям от 0 , ако е значи имаме резултати
            while($row = $items->fetch_assoc()){ // циклим за всеки един от резулатите и го представяме като масив
                $itemsIds[] = $row['item']; // добавяме ид-то на предмета на фактуриране в масива $itemIds
            }
        }
        // delete заявка, която изтрива връзките между прихода и предметите на фактуриране
        $this->conn->query("DELETE FROM profits_items WHERE profit = ".$profitId."");
        // delete заявка, която изтрива самия приход
        $this->conn->query("DELETE FROM profits WHERE id=".$profitId."");
        // delete завка, която изтрива преметите на фактуриране, който се намират в масива $itemIds
        $this->conn->query("DELETE FROM items WHERE id IN (".implode(',', $itemsIds).")");
    }

    // метод printProfit, който извлича приход и предметите свързани с него и връща
    // като резултат генерирания от метода createHtml html код за принтиране на прихода
    public function printProfit($profitId){
        $data = array();
        // select заявка, извличаща прихода отговарящ на посоченото ид ($profitId - парамерърам който метода приема)
        $profit = $this->conn->query("SELECT * FROM profits WHERE id=".intval($profitId)."");
        if ($profit->num_rows > 0){ // проверяваме броя върнати редове от заявката дали е по-голям от 0 , ако е значи имаме резултати
            $profit = $profit->fetch_assoc(); // представяме прихода като масив и го присвояваме на променливата $profit
            $data['document'] = $profit; // добавяме приход в масива $data
            // select заявка извличаща всички предмети на фактуриране свързани с прихода
            $profitItems = $this->conn->query("SELECT items.* FROM items INNER JOIN profits_items ON profits_items.item = items.id WHERE profits_items.profit = ".intval($profit['id'])."");
            if ($profitItems->num_rows > 0){ // проверяваме броя върнати редове от заявката дали е по-голям от 0 , ако е значи имаме резултати
                $sum = 0;
                while($item = $profitItems->fetch_assoc()){ // циклим за всеки един от резулатите и го представяме като масив
                    $data['items'][] = $item; // добавяме предметите свързани с прихода в масива $data
                    $sum += $item['price']; // сумираме и записваме стойноста от всички предмети на фактуриране в променливата $sum
                }
                $data['total_amount'] = $sum; // добавяме общата сума в масива $data
            }
        }
        $data['type'] = 'ФАКТУРА ЗА ПРИХОДИ'; // добавявме типа на документа в масива $data
        return $this->createHtml($data); // връщаме функция, която генерира html код от подадените параметри
    }

    // метод searchProfit, който търси за приход по име на приход или по ид на приход
    public function searchProfit($profitName){
        $data = array();
        $key = 0;
        if ($this->user->position == 3){ // проверяваме дали потребителя е клиент
            // select заявка извличаща всички приходи свързани с текущия потребител, отговарящи на търсенето
            $profits = $this->conn->query("SELECT profits.id, profits.name, profits.date, users.username as created_for, users.id as u_id FROM profits INNER JOIN users ON users.id = profits.created_for WHERE (profits.name LIKE '%".$profitName."%' OR profits.id = ".intval($profitName).") AND created_for = ".intval($this->user->id)."");
        } else {
            // select заявка извличаща всички приходи, отговарящи на търсенето
            $profits = $this->conn->query("SELECT profits.id, profits.name, profits.date, users.username as created_for, users.id as u_id FROM profits INNER JOIN users ON users.id = profits.created_for WHERE (profits.name LIKE '%".$profitName."%' OR profits.id = ".intval($profitName).")");
        }
        if($profits->num_rows > 0){ // проверяваме броя върнати редове от заявката дали е по-голям от 0 , ако е значи имаме резултати
            while ($row = $profits->fetch_assoc()){ // циклим за всеки един от резулатите и го представяме като масив
                $data[$key]['profit'] = $row; // записваме върнатите от резултати в масива $data
                $data[$key]['profit']['position_id'] = $this->user->position; // добавяме елемент на масива съдържащ позицията на текущия потребител
                // select завка извличаща предметите на фактуриране свързани с прихода
                $profitItems = $this->conn->query("SELECT items.* FROM items INNER JOIN profits_items ON profits_items.item = items.id WHERE profits_items.profit = ".intval($row['id'])."");
                if ($profitItems->num_rows > 0){ // проверяваме броя върнати редове от заявката дали е по-голям от 0 , ако е значи имаме резултати
                    while($item = $profitItems->fetch_assoc()){ // добавяме елемент на масива съдържащ позицията на текущия потребител
                        $data[$key]['profit_items'][] = $item; // записваме предметите на фактуриране в масива $data
                    }
                }
                $key++;
            }
        }
        return $data; // връщаме масива $data съдържащ резултатите
    }

    // метод createHtml, генериращ html кода за принтиране на прихода
    private function createHtml($data){
        $itemsHtml = '';
        foreach ($data['items'] as $item){ // циклим през всеки единт от предметите на фактуриране
            // показваме името на предмета и форматираме сумата в подходящ формат
            $itemsHtml .=
                '<tr>
                    <td>'.$item["name"].'</td>
                    <td style="text-align: right">'.number_format($item["price"], 2, ',', ' ').' лв</td>
                </tr>;';
        }
        $date = new DateTime(); // вземаме текущата дата
        $dateAndTime = $date->format('d/m/Y'); // форматираме датата в подходящ формат

        $html = '
            <!doctype html>
            <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                    <meta name="viewport"
                          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
                    <meta http-equiv="X-UA-Compatible" content="ie=edge">
                    <style>
                        body{
                            font-family: DejaVu Sans;
                            font-size: 12px;;
                        }
                        .company-details {
                            float: left;
                            width: 50%;
                        }
                        .client-content{
                            margin-left: 138px;
                        }
                        .font-12{
                            font-size: 12px;
                        }
                        .title {
                            text-align: center;
                            padding-top: 120px;
                            font-size: 34px;
                        }
                        .table-content{
                            margin-top: 20px;
                        }
                        table {
                            border-collapse: collapse;
                            width: 100%;
                        }
                
                        td, th {
                            border: 1px solid #a4a4a4;
                            text-align: left;
                            padding: 8px;
                        }
                
                        tr:nth-child(even) {
                            background-color: rgb(220,220,220) ;
                        }
                        .text-center{
                            text-align: center;
                        }
                        .signature-content {
                            margin-top: 40px;
                        }
                        .signature-content > p {
                            display: inline-block;
                        }
                        .signature {
                            margin-left: 250px;
                        }
                        .signature-text {
                            display: block;
                            text-align: right;
                            margin-right: 40px;
                        }
                        @page { size: 595px 842px }
                    </style>
                
                </head>
                <body>
                <div>
                    <div class="company-details">
                        <i>Beta Finans</i><br>
                        <i>Освобождение 23/ Пловдив/ България</i><br>
                        <i>Телефон: 0883238991</i><br>
                        <i>Имейл: betafinans@gmail.com </i>
                    </div>
                </div>
                <div class="title">
                    '.$data['type'].'
                </div>
                <div class="table-content">
                    <table>
                        <tr>
                            <th class="text-center" colspan="2">'.$data['document']['name'].'</th>
                        </tr>
                        <tr>
                            <th width="75%">Предмет на фактуриране</th>
                            <th>Стойност</th>
                        </tr>
                        '.$itemsHtml.'
                        <tr>
                            <th>Обща стойност</th>
                            <th style="text-align: right">'.number_format($data['total_amount'], 2, ',', ' ').' лв</th>
                        </tr>
                    </table>
                </div>
                <div class="signature-content">
                    <p>Дата : '.$dateAndTime.'</p>
                    <p class="signature"> _______________________</p>
                    <div class="signature-text">(Подпис)</div>
                </div>       
                </body>
            </html>
        ';

        return $html; // връщаме генерирания html код
    }
}