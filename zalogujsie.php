<?php
    session_start();
    
    //jezeli zalogowani przekieruj
    if ((isset($_SESSION['zalogowany'])) && ($_SESSION['zalogowany']==true))
    {
        header('Location: sklep.php');
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>goFISHINGshop</title>
        <!-- Bootstrap core CSS -->
        <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <!-- Custom styles for this template -->
        <link href="css/simple-sidebar.css" rel="stylesheet">
        <link href="css/mycss.css" rel="stylesheet">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
    </head>
    <body class="bg-dark">
         <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
            <div class="container">
                <a class="navbar-brand" href="index.php"><i class="fas fa-hands-helping"></i>&nbsp;&nbsp;goFISHINGshop</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarResponsive">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php"><i class="fas fa-home"></i>&nbsp;&nbsp;Strona Główna                                
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="zalogujsie.php"><i class="fas fa-sign-in-alt"></i>&nbsp;&nbsp;Login</a>
                            <span class="sr-only">(current)</span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="reg.php"><i class="fa fa-user"></i>&nbsp;&nbsp;Rejestracja</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container" id="wrapper">
            <div class="card card-login mx-auto mt-5">
                <div class="card-header">Login</div>
                <div class="card-body">
                    <form action="logikaphp/login.php" method="post">
                        <div class="form-group">
                            <label for="log">Login</label>
                            <input class="form-control" id="log" type="text" name="login" aria-describedby="emailHelp" placeholder="Podaj login">
                        </div>
                        <div class="form-group">
                            <label for="password">Hasło</label>
                            <input class="form-control" id="password"  name="haslo" type="password" placeholder="Podaj Hasło">
                        </div>
                       
                        <input type="submit" class="btn btn-primary btn-block" value="Zaloguj się"/>
                        <!-- <a class="btn btn-primary btn-block" href="index.html">Login</a> -->
                    </form>
                    <div class="text-center">
                        <a class="d-block small mt-3" href="reg.php">Register an Account</a>
                    </div>
                    <?php
                        if(isset($_SESSION['blad_log'])){     
                            echo $_SESSION['blad_log'];
                            unset($_SESSION['blad_log']); 
                        }
                    ?>
                </div>
            </div>
        </div>
        
        <!-- /#wrapper -->
        <!-- Bootstrap core JavaScript -->
        <script src="vendor/jquery/jquery.min.js"></script>
        <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="script/toogle.js"></script>
        <script src="script/showAndHide.js"></script>
    </body>
</html>