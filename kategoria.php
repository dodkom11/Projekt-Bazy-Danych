<?php




/* ==========       SESJA I WARUNKI DOSTEPU     ========== */
session_start();

//jezeli nie jestesmy zalogowani wroc do index.php
if (!isset($_SESSION['zalogowany']))
{
    header('Location: index.php');
    exit(); //opuszczamy plik nie wykonuje sie reszta
} else if (!isset($_POST['katid'])) {
    header('Location: sklep.php');
    exit(); //opuszczamy plik nie wykonuje sie reszta
}

require_once "logikaphp/connect.php"; 




/* ==========       POLACZENIE Z BAZA       ========== */
$connection = oci_connect($username, $password, $database);
if (!$connection) {
    $m = oci_error();
    trigger_error('Nie udało się połaczyć z baza: '. $m['message'], E_USER_ERROR);
}




/* ==========       ZMIENNE LOKALNE         ========== */
//SELECT PRODUKTY Z KATEGORI
$queryProduktyKategoria = "begin 
                                :cursor := PPRODUKT.SELECTPRODUKTYKATEGORIAID(:katid);
                            end;";

//SELECT KATEGORIA
$queryPokazKategorie =      "begin 
                                :cursor := PKATEGORIA.SELECTKATEGORIA;
                            end;";




/* ==========       SELECT PRODUKTY Z KATEGORI    ========== */
//PARSOWANIE  
$stid = oci_parse($connection, $queryProduktyKategoria);
if (!$stid) {
    $m = oci_error($connection);
    trigger_error('Nie udało się przeanalizować polecenia pl/sql: ' . $m['message'], E_USER_ERROR);
}

//PHP VARIABLE --> ORACLE PLACEHOLDER
$cursorProdukty = oci_new_cursor($connection);
oci_bind_by_name($stid, ":cursor", $cursorProdukty, -1, OCI_B_CURSOR);
oci_bind_by_name($stid, ":katid", $_POST['katid']);

//EXECUTE POLECENIE
$result = oci_execute($stid);
if (!$result) {
    $m = oci_error($stid);
    trigger_error('Nie udało się wykonać polecenia: ' . $m['message'], E_USER_ERROR);
}

//EXECUTE KURSOR
$result = oci_execute($cursorProdukty, OCI_DEFAULT);
if (!$result) {
    $m = oci_error($stid);
    trigger_error('Nie udało się wykonać polecenia: ' . $m['message'], E_USER_ERROR);
}

//ZWOLNIJ ZASOBY
oci_free_statement($stid);      




/* ==========       SELECT KATEGORIA      ========== */
//PARSOWANIE  
$stid = oci_parse($connection, $queryPokazKategorie);
if (!$stid) {
    $m = oci_error($connection);
    trigger_error('Nie udało się przeanalizować polecenia pl/sql: ' . $m['message'], E_USER_ERROR);
}

//PHP VARIABLE --> ORACLE PLACEHOLDER
$cursorKategoria = oci_new_cursor($connection);
oci_bind_by_name($stid, ":cursor", $cursorKategoria, -1, OCI_B_CURSOR);

//EXECUTE POLECENIE
$result = oci_execute($stid);
if (!$result) {
    $m = oci_error($stid);
    trigger_error('Nie udało się wykonać polecenia: ' . $m['message'], E_USER_ERROR);
}

//EXECUTE KURSOR
$result = oci_execute($cursorKategoria, OCI_DEFAULT);
if (!$result) {
    $m = oci_error($stid);
    trigger_error('Nie udało się wykonać polecenia: ' . $m['message'], E_USER_ERROR);
}

//ZWOLNIJ ZASOBY
oci_free_statement($stid);        
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
    <body>
       
        <!--  ==========    PASEK NAWIGACJI   ==========  -->

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
                         if ((isset($_SESSION['S_UPRAWNIENIA'])) && (!strcmp($_SESSION['S_UPRAWNIENIA'], "admin" ))){
                            echo '<li class="nav-item"><a class="nav-link" href="adminphp/zarzadzaj_pracownikiem.php"><i class="fas fa-gavel"></i>&nbsp;&nbsp;Admin Panel</a></li>';
                         }
                         ?>
                         <?php
                         if ((isset($_SESSION['S_UPRAWNIENIA'])) && (!strcmp($_SESSION['S_UPRAWNIENIA'], "admin" ) OR !strcmp($_SESSION['S_UPRAWNIENIA'], "pracownik" ))){
                            echo '<li class="nav-item"><a class="nav-link" href="pracownikphp/zarzadzaj_produktem.php"><i class="fas fa-pencil-alt"></i>&nbsp;&nbsp;Pracownik Panel</a></li>';
                         }
                         ?>   
                        <li class="nav-item">
                            <a class="nav-link" href="index.php"><i class="fas fa-home"></i>&nbsp;&nbsp;Strona Główna</a>
                        </li>
                        <li class="nav-item active">
                            <a class="nav-link" href="sklep.php"><i class="fas fa-shopping-basket"></i></i>&nbsp;&nbsp;Sklep</a>
                            <span class="sr-only">(current)</span>
                        </li>
                        <li class="nav-item">
                            <?php echo '<a class="nav-link'; if($_SESSION['S_ILEKOSZYK'] > 0) echo ' koszykactive"'; else echo '"'; ?>href="koszyk.php"><i class="fas fa-shopping-cart "></i>&nbsp;&nbsp;Koszyk (<?php echo $_SESSION['S_ILEKOSZYK']; ?>)</a>
 
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="zamowienie.php"><i class="fas fa-history"></i>&nbsp;&nbsp;Zamówienia</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="logikaphp/logout.php"><i class="fas fa-sign-in-alt"></i>&nbsp;&nbsp;Wyloguj</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <div id="wrapper" class="toggled">
            
       <!--  ==========    PASEK BOCZNY   ==========  -->

            <div id="sidebar-wrapper">
                <ul class="sidebar-nav">
                    <li class="sidebar-brand">
                        <a href="#">
                            <strong>Kategorie</strong>
                        </a>
  
                    </li><div class="btn-group-vertical">
<?php                 
while (($row = oci_fetch_array($cursorKategoria, OCI_ASSOC + OCI_RETURN_NULLS)) != false) {
    $KATEGORIA_ID       = $row['KATEGORIA_ID'];
    $KATEGORIA_NAZWA    = $row['KATEGORIA_NAZWA'];
echo<<<END
<li>
    <form action="kategoria.php" method="post">
        <button type="submit" name="katid" class="btn btn-outline-secondary" style="min-width: 205px;" value="
END;
?>
<?php echo htmlspecialchars($KATEGORIA_ID);
echo <<<END
        ">$KATEGORIA_NAZWA</button>        
     </form>
</li>
END;
}
?></div>
                </ul>
            </div>

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
    
                    <div class="row">
                    <?php                        
                        while (($row = oci_fetch_array($cursorProdukty, OCI_ASSOC + OCI_RETURN_NULLS)) != false) {     
                            $produktid = $row['PRODUKT_ID'];
                            $producent = $row['PRODUCENT'];
                            $cena = $row['CENA'];
                            $numerKat = $row['NUMER_KATALOGOWY'];
                            $model =  $row['MODEL'];
                            $szukMagazyn = $row['SZTUK_NA_MAGAZYNIE'];
                            $opis = $row['OPIS'];
                            if( ($zdjecie = $row['ZDJECIE']) != null){
                                $zdjecie = $row['ZDJECIE']->load();
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
                                        
                                            <div class="col-lg-6 text-right">
                                                <form action="logikaphp/logickoszyk.php" method="post">
                                                    <button type="submit" name="buttonproduktid" class="btn btn-success" value="
END;
?>
<?php echo htmlspecialchars($produktid);
echo <<<END
">
                                                    <i class="fas fa-shopping-cart"></i> KUP</button>
                                                  </form>
                                            </div>

                                    </div>
                                </div>
                            </div>
                        </div>
END;

                        }
                    ?>                       
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
</html>s