<?php
include '../backend/Authentication.php'; // вмъкваме клас Auth
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Accounting Software</title>
    <!-- вмъкваме библиотеки bootstrap,jquery и css файлове -->
    <link rel="stylesheet" href="../bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="../bootstrap/stroke-7/style.css">
    <link rel="stylesheet" href="../css/styles.css">
    <script src="../bootstrap/jquery.min.js"></script>
    <script src="../bootstrap/bootstrap.min.js"></script>
</head>
<body class="body-auth">
    <div class="container">
        <div class="card password-recovery-form">
            <div class="card-header">
                Въстановяване на парола
            </div>
            <div class="card-body">
                <form method="post" action="">
                    <div class="row">
                        <div class="form-group col-12">
                            <label>Имейл адрес</label>
                            <input type="email" name="email" class="form-control form-control-lg" placeholder="Имейл адрес">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button class="btn-login btn-block">Изпрати парола</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-footer">
                <div class="row link-content">
                    <div class="col-6 text-center">
                        <a href="login.php">Влезте в профила си</a>
                    </div>
                    <div class="col-6 text-center">
                        <a href="register.php">Регистрация</a>
                    </div>
                </div>
            </div>
        </div>
        <?php
        if(isset($_POST['email'])){ // проверяваме за изпратени елементи в масива $_POST
            $auth = new Auth(); // създаваме обект от клас Auth
            // извикваме метод, който изпраща забравена парола на имейл адреса на потребителя
            $result = $auth->passwordRecovery($_POST['email']);  ?>
            <!-- Съобщение дали е изпратена паролата на посочената поща или е възникнала грешка  -->
            <p class="text-center text-white" style="margin-top: 15%;"><?= $result['message'] ?></p> 
            <?php
        }
        ?>
    </div>
</body>
</html>