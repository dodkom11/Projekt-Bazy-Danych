<?php




/* ==========		SESJA I WARUNKI DOSTEPU		========== */
session_start();

//jezeli nie jestesmy zalogowani i nasze uprawnienia inne niz "admin" wroc do index.php
if (!isset($_SESSION['zalogowany']) OR (strcmp($_SESSION['S_UPRAWNIENIA'], "admin") AND strcmp($_SESSION['S_UPRAWNIENIA'], "pracownik"))) {
    header('Location: ../index.php');
    exit(); //opuszczamy plik nie wykonuje sie reszta
}

require_once "../logikaphp/connect.php";




/* ==========		POLACZENIE Z BAZA		========== */
$connection = oci_connect($username, $password, $database);
if (!$connection) {
    $m = oci_error();
    trigger_error('Nie udało się połaczyć z baza: ' . $m['message'], E_USER_ERROR);
}




/* ==========		ZMIENNE LOKALNE			========== */
//SELECT KLIENCI TABELA
$querySelectZamowienia = "begin 
              			 	:cursor := SELECTZAMOWIENIA;
          				end;";

$querySzczegoly        = "begin 
                            :cursor3 := SELECTSZCZEGOLYZAMOWIENIA(:rekord_id);
                        end;";

//SELECT LICZBA KLIENTOW
$queryLicz = "begin 
                :bv := COUNTRW(:tabl, :colm, :cond);    
               end;";

//SELECT KLIENT PO ID
$querySelectKlientID = "begin 
            				:cursor2 := SELECTKLIENCIKONTOID(:rekord_id);
            			end;";   

$tablename  = 'ZAMOWIENIE';
$columnname = 'ZAMOWIENIE_ID';
$condition  = "'TRUE'='TRUE'";

//WARUNEK CZY ISTENIEJE KLIENT 
$condition2  = "UPRAWNIENIA = 'klient' AND KONTO_ID = '";





/* ==========		SELECT KLIENCI TABELA		========== */
//PARSOWANIE  
$stid = oci_parse($connection, $querySelectZamowienia);
if (!$stid) {
    $m = oci_error($connection);
    trigger_error('Nie udało się przeanalizować polecenia pl/sql: ' . $m['message'], E_USER_ERROR);
}

//PHP VARIABLE --> ORACLE PLACEHOLDER
$cursorTabela = oci_new_cursor($connection);
oci_bind_by_name($stid, ":cursor", $cursorTabela, -1, OCI_B_CURSOR);

//EXECUTE POLECENIE
$result = oci_execute($stid);
if (!$result) {
    $m = oci_error($stid);
    trigger_error('Nie udało się wykonać polecenia: ' . $m['message'], E_USER_ERROR);
}

//EXECUTE KURSOR
$result = oci_execute($cursorTabela, OCI_DEFAULT);
if (!$result) {
    $m = oci_error($stid);
    trigger_error('Nie udało się wykonać polecenia: ' . $m['message'], E_USER_ERROR);
}

//ZWOLNIJ ZASOBY
oci_free_statement($stid);







/* ==========		SELECT LICZBA Zamowien			========== */
//PARSOWANIE  
$stid = oci_parse($connection, $queryLicz);

//PHP VARIABLE --> ORACLE PLACEHOLDER
oci_bind_by_name($stid, ":tabl", $tablename);
oci_bind_by_name($stid, ":colm", $columnname);
oci_bind_by_name($stid, ":cond", $condition);
oci_bind_by_name($stid, ":bv", $ile, 10);

//EXECUTE POLECENIE
$result = oci_execute($stid);
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
        <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <!-- Custom styles for this template -->
        <link href="../css/simple-sidebar.css" rel="stylesheet">
        <link href="../css/mycss.css" rel="stylesheet">
        <link href="../vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">
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
                        echo '<li class="nav-item"><a class="nav-link" href="../adminphp/zarzadzaj_pracownikiem.php"><i class="fas fa-gavel"></i>&nbsp;&nbsp;Admin Panel</a></li>';
                        }
                        ?>
                        <li class="nav-item active">
                            <a class="nav-link" href="zarzadzaj_produktem.php"><i class="fas fa-pencil-alt"></i>&nbsp;&nbsp;Pracownik Panel</a>
                            <span class="sr-only">(current)</span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../index.php"><i class="fas fa-home"></i>&nbsp;&nbsp;Strona Główna
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../sklep.php"><i class="fas fa-shopping-basket"></i></i>&nbsp;&nbsp;Sklep</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../koszyk.php"><i class="fas fa-shopping-cart"></i>&nbsp;&nbsp;Koszyk</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../zamowienie.php"><i class="fas fa-history"></i>&nbsp;&nbsp;Zamówienia</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../logikaphp/logout.php"><i class="fas fa-sign-in-alt"></i>&nbsp;&nbsp;Wyloguj</a>
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
                    <li class="aria-selected">
                        <a href="zarzadzaj_produktem.php">&nbsp;&nbsp;Zarządaj Produktem</a>
                    </li>
                    <li>
                        <a href="zarzadzaj_zamowieniem.php" class="nav-active">&nbsp;&nbsp;Zarządaj Zamówieniem</a>
                    </li>
                    <li>
                        <a href="zarzadzaj_kategoria.php">&nbsp;&nbsp;Zarządaj Kategorią</a>
                    </li>
                </ul>
            </div>
            <!-- /#sidebar-wrapper -->
            <!-- Page Content -->
            <div id="page-content-wrapper">
                <div class="container-fluid">
                    <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <a class="nav-item nav-link active" id="nav-zakladka1-tab" data-toggle="tab" href="#nav-zakladka1" role="tab" aria-controls="nav-zakladka1" aria-selected="true">Zarządaj</a>
                        </div>
                    </nav>
                    <br>
                    <div class="tab-content" id="nav-tabContent">
<!-- 
        +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++    
                            >>>>>>>>>>      ZAKLADKA 1      <<<<<<<<<<
        +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
-->
                        
<div class="tab-pane fade show active" id="nav-zakladka1" role="tabpanel" aria-labelledby="nav-zakladka1-tab">
<div class="row">
    <div class="card mb-3">
        <div class="card-header">
            <i class="fa fa-table"></i> Zamówienia [<?php
            echo $ile;
        ?>]</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>SCZEGOLY</th>
                            <th>ZAMOWIENIE_ID</th>
                            <th>KONTO_ID</th>
                            <th>IMIE</th>
                            <th>NAZWISKO</th>
                            <th>NAZWA_FIRMY</th>
                            <th>DATA_PRZYJECIA</th>
                            <th>DATA_REALIZACJI</th>
                            <th>ZAAKCEPTOWANE</th>
                            <th>ZAPLACONO</th>
                            <th>ZREALIZOWANO</th>
                            <th>KOSZT_ZAMOWIENIA</th>
                            <th>METODA_PLATNOSCI</th>
                            <th>DOKUMENT_SPRZEDAZY</th>
                            <th>MIEJSCOWOSC</th>
                            <th>WOJEWODZTWO</th>
                            <th>KOD_POCZTOWY</th>
                            <th>ULICA</th>
                            <th>NR_DOMU</th>
                            <th>NR_LOKALU</th>
                            <th>NR_TEL</th>
                            <th>EMAIL</th>
                        </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <th>SCZEGOLY</th>
                        <th>ZAMOWIENIE_ID</th>
                        <th>KONTO_ID</th>
                        <th>IMIE</th>
                        <th>NAZWISKO</th>
                        <th>NAZWA_FIRMY</th>
                        <th>DATA_PRZYJECIA</th>
                        <th>DATA_REALIZACJI</th>
                        <th>ZAAKCEPTOWANE</th>
                        <th>ZAPLACONO</th>
                        <th>ZREALIZOWANO</th>
                        <th>KOSZT_ZAMOWIENIA</th>
                        <th>METODA_PLATNOSCI</th>
                        <th>DOKUMENT_SPRZEDAZY</th>
                        <th>MIEJSCOWOSC</th>
                        <th>WOJEWODZTWO</th>
                        <th>KOD_POCZTOWY</th>
                        <th>ULICA</th>
                        <th>NR_DOMU</th>
                        <th>NR_LOKALU</th>
                        <th>NR_TEL</th>
                        <th>EMAIL</th>
                    </tr>
                    </tfoot>
                    <tbody>


                                                <?php
//WYPEŁNIJ TABELE KLIENTAMI Z BAZY
while (($row = oci_fetch_array($cursorTabela, OCI_ASSOC + OCI_RETURN_NULLS)) != false) {
$ZAMOWIENIE_ID               = $row['ZAMOWIENIE_ID'];
$KONTO_ID                    = $row['KONTO_ID'];
$IMIE                        = $row['IMIE'];
$NAZWISKO                    = $row['NAZWISKO'];
$NAZWA_FIRMY                 = $row['NAZWA_FIRMY'];
$DATA_PRZYJECIA_ZAMOWIENIA   = $row['DATA_PRZYJECIA_ZAMOWIENIA'];
$DATA_REALIZACJI_ZAMOWIENIA  = $row['DATA_REALIZACJI_ZAMOWIENIA'];
$ZAMOWIENIE_ZAAKCEPTOWANE    = $row['ZAMOWIENIE_ZAAKCEPTOWANE'];
$ZAPLACONO                   = $row['ZAPLACONO'];
$ZREALIZOWANO                = $row['ZREALIZOWANO'];
$KOSZT_ZAMOWIENIA            = $row['KOSZT_ZAMOWIENIA'];
$METODA_PLATNOSCI            = $row['METODA_PLATNOSCI'];
$DOKUMENT_SPRZEDAZY          = $row['DOKUMENT_SPRZEDAZY'];
$MIEJSCOWOSC                 = $row['MIEJSCOWOSC'];
$WOJEWODZTWO                 = $row['WOJEWODZTWO'];
$KOD_POCZTOWY                = $row['KOD_POCZTOWY'];
$ULICA                       = $row['ULICA'];
$NR_DOMU                     = $row['NR_DOMU'];
$NR_LOKALU                   = $row['NR_LOKALU'];
$NR_TEL                      = $row['NR_TEL'];
$EMAIL                       = $row['EMAIL'];
 
    echo "<tr><td><form action=\"" . $_SERVER['PHP_SELF'] . "\" method=\"post\"><button type=\"submit\" name=\"szczegoly\" class=\"btn btn-primary btn-sm\" value=\"" . htmlspecialchars($ZAMOWIENIE_ID). "\">szczegoly</button></form></td>";
    echo "<td>$ZAMOWIENIE_ID</td> <td>$KONTO_ID</td> <td>$IMIE</td> <td>$NAZWISKO</td> <td>$NAZWA_FIRMY</td> <td>$DATA_PRZYJECIA_ZAMOWIENIA</td> <td>$DATA_REALIZACJI_ZAMOWIENIA</td> <td>$ZAMOWIENIE_ZAAKCEPTOWANE</td> <td>$ZAPLACONO</td> <td>$ZREALIZOWANO</td> <td>$KOSZT_ZAMOWIENIA</td> <td>$METODA_PLATNOSCI</td> <td>$DOKUMENT_SPRZEDAZY</td><td>$MIEJSCOWOSC</td> <td>$WOJEWODZTWO</td> <td>$KOD_POCZTOWY</td><td>$ULICA</td><td>$NR_DOMU</td><td>$NR_LOKALU</td> <td>$NR_TEL</td> <td>$EMAIL</td></tr>";
}
?>
                                           </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>


<?php 

/* ==========       SELECT SZCZEGOLY ZAMOWIENIA      ========== */
if(isset($_REQUEST['szczegoly'])) {
//PARSOWANIE  
$stid = oci_parse($connection, $querySzczegoly);
if (!$stid) {
    $m = oci_error($connection);
    trigger_error('Nie udało się przeanalizować polecenia pl/sql: ' . $m['message'], E_USER_ERROR);
}

// ZBIERAMY DANE Z INPUT
htmlspecialchars($_REQUEST['szczegoly']);


$v=$_REQUEST['szczegoly'];
//PHP VARIABLE --> ORACLE PLACEHOLDER
$cursorSzczegoly = oci_new_cursor($connection);
oci_bind_by_name($stid, ":rekord_id", $v);////////////////
oci_bind_by_name($stid, ":cursor3", $cursorSzczegoly, -1, OCI_B_CURSOR);

//EXECUTE POLECENIE
$result = oci_execute($stid);
if (!$result) {
    $m = oci_error($stid);
    trigger_error('Nie udało się wykonać polecenia: ' . $m['message'], E_USER_ERROR);
}

//EXECUTE KURSOR
$result = oci_execute($cursorSzczegoly, OCI_DEFAULT);
if (!$result) {
    $m = oci_error($stid);
    trigger_error('Nie udało się wykonać polecenia: ' . $m['message'], E_USER_ERROR);
}

//ZWOLNIJ ZASOBY
oci_free_statement($stid);
?>

                    <div class="row">
                        <div class="card mb-3">
                            <div class="card-header">
                            <i class="fa fa-table"></i> Zamówienie <strong>ID: <?php echo $_REQUEST['szczegoly']; ?></strong></div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>PRODUKT_ID</th>
                                                <th>PRODUCENT</th>
                                                <th>MODEL</th>
                                                <th>NUMER_KATALOGOWY</th>
                                                <th>ILOSC_SZTUK</th>
                                                <th>KOSZT</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                                <?php

//WYPEŁNIJ TABELE KLIENTAMI Z BAZY
while (($row = oci_fetch_array($cursorSzczegoly, OCI_ASSOC + OCI_RETURN_NULLS)) != false) {
    $PRODUKT_ID                  = $row['PRODUKT_ID'];
    $PRODUCENT                   = $row['PRODUCENT'];
    $MODEL                       = $row['MODEL'];
    $NUMER_KATALOGOWY            = $row['NUMER_KATALOGOWY'];
    $ILOSC_SZTUK                 = $row['ILOSC_SZTUK'];
    $KOSZT                       = $row['KOSZT'];

        echo "<tr><td>$PRODUKT_ID</td> <td>$PRODUCENT</td> <td>$MODEL</td> <td>$NUMER_KATALOGOWY</td> <td>$ILOSC_SZTUK</td> <td>$KOSZT</td></tr>";
    }
?>
                                           </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>                        
                        </div>               
                <div class="row">
                    <div class="card">
                        <div class="card-header">
                            EDYTUJ ZAMÓWIENIE <strong>ID: <?php echo $_REQUEST['szczegoly']; ?></strong>
                        </div>
                        <div class="card-body">
                            <form action="funkcjePracownik.php" method="post">
                                <div class="form-row">
                                    <div class="form-group col-3">
                                        <label for="inputeralizacjadata">DATA_REALIZACJI</label>
                                        <input type="date" class="form-control" id="inputeralizacjadata" name="datarealizacji" >
                                    </div>
                                    <div class="form-group col-3">
                                        <label for="inputzaakceptowano">ZAAKCEPTOWANE</label>
                                        <input type="number" class="form-control" id="inputzaakceptowano" name="zaakceptowane" placeholder="1/0" min="0" max="1" >
                                    </div>
                                    <div class="form-group col-3">
                                        <label for="inputzaplacono">ZAPLACONO</label>
                                        <input type="number" class="form-control" id="inputzaplacono" name="zaplacono" placeholder="1/0" min="0" max="1">
                                    </div>
                                    <div class="form-group col-3">
                                        <label for="inputzrealizowano">ZREALIZOWANO</label>
                                        <input type="number" class="form-control" id="inputzrealizowano" name="zrealizowano" placeholder="1/0" min="0" max="1" >
                                    </div>
                                </div>
                                <?php 
                                    echo "<button type=\"submit\" name=\"edytujzamowieniebutton\" class=\"btn btn-primary btn-sm\" value=\"" . htmlspecialchars($_REQUEST['szczegoly']). "\">Potwierdź</button>";
                                ?>
                            </form>
                        </div>                        
                    </div>
                </div>
<?php } ?>  
            </div>
            <!-- ./container-fluid -->
        </div>
        <!-- /#page-content-wrapper -->
    </div>
    <!-- /#wrapper -->
    <!-- Bootstrap core JavaScript -->

    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../script/toogle.js"></script>
    <script src="../script/showAndHide.js"></script>
    <script src="../vendor/datatables/jquery.dataTables.js"></script>
    <script src="../vendor/datatables/dataTables.bootstrap4.js"></script>
    <script src="../vendor/datatables/callDataTables.js"></script>

</body>
</html>
<?php
	//CLOSE POŁĄCZENIE
	oci_close($connection);
?>
