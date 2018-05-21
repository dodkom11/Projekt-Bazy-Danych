<?php
    



    /* ==========       SESJA I WARUNKI DOSTEPU     ========== */
    session_start();
    
    //jezeli nie jestesmy zalogowani wroc do index.php
    if (!isset($_SESSION['zalogowany']))
    {
        header('Location: index.php');
        exit(); //opuszczamy plik nie wykonuje sie reszta
    }

    require_once "connect.php"; 




    /* ==========       POLACZENIE Z BAZA       ========== */
    $connection = oci_connect($username, $password, $database);
    if (!$connection) {
        $m = oci_error();
        trigger_error('Nie udało się połaczyć z baza: '. $m['message'], E_USER_ERROR);
    }




    /* ==========       SELECT OSTATNIE 6 PRODUKTÓW       ========== */
    $query = "SELECT * FROM PRODUKT"; 
    //Parsowanie polecenia pl/sql   
    $stid = oci_parse($connection, $query);
    if (!$stid) {
        $m = oci_error($connection);
        trigger_error('Nie udało się przeanalizować polecenia pl/sql: '. $m['message'], E_USER_ERROR);
    }

    //Wykonaj polecenie SQL
    $result = oci_execute($stid);   
    if (!$result) {
        $m = oci_error($stid);
        trigger_error('Nie udało się wykonać polecenia: '. $m['message'], E_USER_ERROR);
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
    <body>
       
        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
            <a class="text-left text-info zwin" href="#menu-toggle" id="menu-toggle"><i class="fas fa-minus-square"></i> <span class="pokazukryj">Ukryj</span></a>
            <div class="container">
                <a class="navbar-brand" href="#"><i class="fas fa-hands-helping"></i>&nbsp;&nbsp;goFISHINGshop</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarResponsive">
                    <ul class="navbar-nav ml-auto">
                         <?php
                         if (!strcmp($_SESSION['S_UPRAWNIENIA'], "admin" )){
                            echo '<li class="nav-item"><a class="nav-link" href="adminphp/zarzadzaj_pracownikiem.php"><i class="fas fa-gavel"></i>&nbsp;&nbsp;Admin Panel</a></li>';
                         }
                         ?>  
                        <li class="nav-item">
                            <a class="nav-link" href="index.php"><i class="fas fa-home"></i>&nbsp;&nbsp;Strona Główna                                
                            </a>
                        </li>
                        <li class="nav-item active">
                            <a class="nav-link" href="sklep.php"><i class="fas fa-shopping-basket"></i></i>&nbsp;&nbsp;Sklep</a>
                            <span class="sr-only">(current)</span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#"><i class="fas fa-info"></i>&nbsp;&nbsp;O nas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#"><i class="fas fa-address-book"></i>&nbsp;&nbsp;Kontakt</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php"><i class="fas fa-sign-in-alt"></i>&nbsp;&nbsp;Wyloguj</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <div id="wrapper" class="toggled">
            <!-- Sidebar -->
            <div id="sidebar-wrapper">
                <ul class="sidebar-nav">
                    <li class="sidebar-brand">
                        <a href="#">
                            <strong>Kategorie</strong>
                        </a>
                    </li>
                    <li>
                        <a href="#"><i class="fas fa-check"></i>&nbsp;&nbsp;Wędki</a>
                    </li>
                    <li>
                        <a href="#"><i class="fas fa-check"></i>&nbsp;&nbsp;Przynęty</a>
                    </li>
                    <li>
                        <a href="#"><i class="fas fa-check"></i>&nbsp;&nbsp;Haki</a>
                    </li>
                    <li>
                        <a href="#"><i class="fas fa-check"></i>&nbsp;&nbsp;Kat. 4</a>
                    </li>
                    <li>
                        <a href="#"><i class="fas fa-check"></i>&nbsp;&nbsp;Kat. 5</a>
                    </li>
                    <li>
                        <a href="#"><i class="fas fa-check"></i>&nbsp;&nbsp;Kat. 6</a>
                    </li>
                    <li>
                        <a href="#"><i class="fas fa-check"></i>&nbsp;&nbsp;Kat. 7</a>
                    </li>
                </ul>
            </div>
            <!-- /#sidebar-wrapper -->
            <!-- Page Content -->
            <div id="page-content-wrapper">
                <div class="container-fluid">
                    <div class="row">
                        <div id="slider" class="carousel slide container-fluid" data-ride="carousel">
                            <!-- Indicators -->
                            <ul class="carousel-indicators">
                                <li data-target="#slider" data-slide-to="0" class="active"></li>
                                <li data-target="#slider" data-slide-to="1"></li>
                                <li data-target="#slider" data-slide-to="2"></li>
                            </ul>
                            
                            <!-- The slideshow -->
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <img src="img/carousel/carousel1.jpg" alt="Slider">
                                </div>
                                <div class="carousel-item">
                                    <img src="img/carousel/carousel2.jpg" alt="Slider">
                                </div>
                                <div class="carousel-item">
                                    <img src="img/carousel/carousel3.jpg" alt="Slider">
                                </div>
                            </div>
                            
                            <!-- Left and right controls -->
                            <a class="carousel-control-prev" href="#slider" data-slide="prev">
                                <span class="carousel-control-prev-icon"></span>
                            </a>
                            <a class="carousel-control-next" href="#slider" data-slide="next">
                                <span class="carousel-control-next-icon"></span>
                            </a>
                        </div>
                    </div>
                    <!-- /.row -->
                    <div class="row">
                    <?php                        
                        while (oci_fetch($stid)) {         
                            $producent = oci_result($stid, 'PRODUCENT');
                            $cena = oci_result($stid, 'CENA');
                            $numerKat = oci_result($stid, 'NUMER_KATALOGOWY');
                            $model =  oci_result($stid, 'MODEL');
                            $szukMagazyn = oci_result($stid, 'SZTUK_NA_MAGAZYNIE');
                            $opis = oci_result($stid, 'OPIS');
                            if( ($zdjecie = oci_result($stid, 'ZDJECIE')) != null){
                                $zdjecie = oci_result($stid, 'ZDJECIE')->load();
                            }                                                                                                  
echo <<<END
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card h-100">
                                <a href="#"> 
END;
                        if($zdjecie != null) {   
                            echo '<div><img class="card-img-top" alt="700x400" src="data:image/jpeg;base64,'.base64_encode($zdjecie).'" /></div>';
                        } else {
                            echo '<div><img class="card-img-top" alt="700x400" src="img/brakzdj.jpg" /></div>';
                        }

echo <<<END
                                <div class="card-body">
                                    <h4 class="card-title">
                                    <a href="#">$producent $model</a>
                                    </h4>
                                    <h5><i class="far fa-money-bill-alt"></i> $cena PLN</h5>
                                    <p class="card-text">$opis</p>
                                </div>

                                <table class="table table-hover">
                                    <tbody>
                                        <tr>
                                            <td><i class="fas fa-angle-right"></i> <em>Producent</em></td>
                                            <td><strong>$producent</strong></td>
                                        </tr>
                                        <tr>
                                            <td><i class="fas fa-angle-right"></i> <em>Numer katalogowy</em></td>
                                            <td><strong>$numerKat</strong></td>
                                        </tr>
                                        <tr>
                                            <td><i class="fas fa-angle-right"></i> <em>Model</em></td>
                                            <td><strong>$model</strong></td>
                                        </tr>
                                        <tr>
                                            <td><i class="fas fa-angle-right"></i> <em>Sztuk na magazynie</em></td>
                                            <td><strong>$szukMagazyn</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                                  <div class="card-footer">
                                    <div class="row">
                                        <div class="col-lg-6"><em class="align-middle">Dodano: 2018-04-29</em></div>
                                        <div class="col-lg-6 text-right"><button type="button" class="btn btn-success"><i class="fas fa-shopping-cart"></i> KUP</button></div>
                                    </div>
                                </div>
                            </div>
                        </div>
END;

                        }
                            oci_free_statement($stid); //wyczysc z pamieci RAM serwera zwrocone z bazy rezultaty zapytania     

                    ?>                       
                    </div>
                    <!-- /.row -->
                </div>
                <!-- ./container-fluid -->
            </div>
            <!-- /#page-content-wrapper -->
        </div>
        <!-- /#wrapper -->
        <!-- Bootstrap core JavaScript -->
        <script src="vendor/jquery/jquery.min.js"></script>
        <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="script/toogle.js"></script>
        <script src="script/showAndHide.js"></script>
    </body>
</html>