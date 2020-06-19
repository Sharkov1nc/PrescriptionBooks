<?php
include '../backend/Authentication.php';
?>
<!doctype html>
<html lang="en">
<head>
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
</head>
<body class="body-auth">
<div class="container">
    <div class="card auth-form">
        <div class="card-header">
           Влезте в профила си
        </div>
        <div class="card-body">
            <form method="post" action="">
                <div class="row">
                    <div class="form-group col-12">
                        <label for="inputEmail4">Имейл адрес</label>
                        <input type="text" name="username" class="form-control form-control-lg" placeholder="Имейл адрес">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-12">
                        <label for="inputEmail4">Парола</label>
                        <input type="password" name="password" class="form-control form-control-lg" placeholder="Парола">
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <button class="btn-login btn-block btn-danger">Влез</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-footer">
            <div class="row link-content">
                <div class="col-6 offset-6 text-right">
                    <a href="password_recovery.php">Забравена парола ?</a>
                </div>
            </div>
        </div>
    </div>
    <?php
    if(isset($_POST['username']) && isset($_POST['password'])){
        $auth = new Authentication();
        $result = $auth->validateLogin($_POST['username'],$_POST['password']);
        if(!$result['status']){  ?>
            <p class="text-center text-white m-3"><?= $result['message'] ?></p>
    <?php
        }
    }
    ?>
</div>
</body>
</html>