<?php




/* ==========       SESJA I WARUNKI DOSTEPU     ========== */
session_start();

//jezeli nie jestesmy zalogowani wroc do index.php
if (!isset($_SESSION['zalogowany']))
{
    header('Location: index.php');
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
//SELECT PRODUKTY w koszyku
$queryProduktyKoszyk =     "begin 
                                :cursor := POKAZKOSZYK(:kontoid);
                            end;";

//SELECT OSTATNIE KATEGORIA
$queryPokazKategorie =      "begin 
                                :cursor := SELECTKATEGORIA;
                            end;";


/* ==========       SELECT Produkty       ========== */
//PARSOWANIE  
$stid = oci_parse($connection, $queryProduktyKoszyk);
if (!$stid) {
    $m = oci_error($connection);
    trigger_error('Nie udało się przeanalizować polecenia pl/sql: ' . $m['message'], E_USER_ERROR);
}

//PHP VARIABLE --> ORACLE PLACEHOLDER
$cursorKoszyk = oci_new_cursor($connection);
oci_bind_by_name($stid, ":kontoid", $_SESSION['S_KONTO_ID']);
oci_bind_by_name($stid, ":cursor", $cursorKoszyk, -1, OCI_B_CURSOR);

//EXECUTE POLECENIE
$result = oci_execute($stid);
if (!$result) {
    $m = oci_error($stid);
    trigger_error('Nie udało się wykonać polecenia: ' . $m['message'], E_USER_ERROR);
}

//EXECUTE KURSOR
$result = oci_execute($cursorKoszyk, OCI_DEFAULT);
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
                        <li class="nav-item">
                            <a class="nav-link" href="sklep.php"><i class="fas fa-shopping-basket"></i></i>&nbsp;&nbsp;Sklep</a>                            
                        </li>
                        <li class="nav-item active">
                            <a class="nav-link" href="koszyk.php"><i class="fas fa-shopping-cart"></i>&nbsp;&nbsp;Koszyk</a>
                            <span class="sr-only">(current)</span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#"><i class="fas fa-info"></i>&nbsp;&nbsp;O nas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#"><i class="fas fa-address-book"></i>&nbsp;&nbsp;Kontakt</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logikaphp/logout.php"><i class="fas fa-sign-in-alt"></i>&nbsp;&nbsp;Wyloguj</a>
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
            <!-- /#sidebar-wrapper -->
            <!-- Page Content -->
            <div id="page-content-wrapper">
                <div class="container-fluid">
                    <div class="row">

                                                    <div class="card mb-3">
                                <div class="card-header">
                                <i class="fa fa-table"></i> KOSZYK</div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" width="100%" cellspacing="0">
                                              <thead>
                                                <tr>
                                                    <th>PRODUCENT</th>
                                                    <th>MODEL</th>
                                                    <th>NUMER_KATALOGOWY</th>
                                                    <th>CENA</th>
                                                    <th>ILOSC_SZTUK</th>
                                                    <th>ŁĄCZNIE</th>
                                                   
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
//WYPEŁNIJ TABELE JEŻELI PODANO ID KONTA
    $SUMA = 0;                                                
    while (($row = oci_fetch_array($cursorKoszyk, OCI_ASSOC + OCI_RETURN_NULLS)) != false) {
                    $PRODUKT_ID          = $row['PRODUKT_ID'];
                    $PRODUCENT           = $row['PRODUCENT'];
                    $MODEL               = $row['MODEL'];
                    $NUMER_KATALOGOWY    = $row['NUMER_KATALOGOWY'];
                    $CENA                = $row['CENA'];
                    $ILOSC_SZTUK         = $row['ILOSC_SZTUK'];
                    $ILOCZYN             = $row['ILOCZYN'];
                    $SUMA                += $row['ILOCZYN'];
                    echo "<tr><td>$PRODUCENT</td> <td>$MODEL</td><td>$NUMER_KATALOGOWY</td><td>$CENA</td><td>$ILOSC_SZTUK</td><td>$ILOCZYN</td>";

                    echo "<td><form action=\"logikaphp/logickoszyk.php\" method=\"post\"><button type=\"submit\" name=\"buttonproduktidkoszyk\" class=\"btn btn-primary btn-sm\" value=\"" . htmlspecialchars($PRODUKT_ID). "\">+</button></form></td>";
                    echo "<td><form action=\"logikaphp/logickoszyk.php\" method=\"post\"><button type=\"submit\" name=\"buttonproduktidkoszykdec\"class=\"btn btn-danger btn-sm\" value=\"" . htmlspecialchars($PRODUKT_ID) . "\">-</button></form></td></tr>";                   
        }       
?>
                                           </tbody>
                                        </table>
                                        <?php echo "Do zapłaty: <strong>" . $SUMA . " PLN </strong>"; ?>
                                    </div>
                                </div>
                            </div>

                                     
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