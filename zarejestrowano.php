<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>goFISHINGshop</title>
        <!-- STYLE CSS -->
        <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link href="css/simple-sidebar.css" rel="stylesheet">
        <link href="css/mycss.css" rel="stylesheet">        
        <!-- IKONY -->
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
    </head>
    <body class="bg-dark">

         <!--  ==========    PASEK NAWIGACJI   ==========  -->

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
                            <a class="nav-link" href="zalogujsie.php"><i class="fas fa-sign-in-alt"></i>&nbsp;&nbsp;Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="reg.php"><i class="fa fa-user"></i>&nbsp;&nbsp;Rejestracja</a>
                            <span class="sr-only">(current)</span>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>   

        <div class="container" id="wrapper">
            <div class="card card-login mx-auto mt-5">
                <div class="card-body">                	
                	<div class="text-center">
                        <a class="d-block small mt-3" >&nbsp;&nbsp;<h2>Pomyślnie Zarejestrowano!</h2>&nbsp;&nbsp;</a>
                    </div>                	
                    <div class="text-center">
                        <a class="d-block small mt-3" href="zalogujsie.php"><h4>Zaloguj Sie!<h4></a>
                    </div>
                </div>                
            </div>
        </div>
        <!-- JavaScripts -->
        <script src="vendor/jquery/jquery.min.js"></script>
        <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="script/toogle.js"></script>
        <script src="script/showAndHide.js"></script>
    </body>
</html>