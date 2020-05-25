<?php
    include_once '../backend/Authentication.php';
    include_once '../backend/MainController.php';
    $authentication = new Authentication();
    $authentication->isLoggedIn();
?>
<meta charset="UTF-8">
<meta name="viewport"
      content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<title>Accounting Software</title>
<link rel="stylesheet" href="../bootstrap/bootstrap.min.css">
<link rel="stylesheet" href="../bootstrap/stroke-7/style.css">
<link rel="stylesheet" href="../css/styles.css">
<script src="../bootstrap/jquery.min.js"></script>
<script src="../bootstrap/bootstrap.min.js"></script>
<script src="../js/main.js"></script>