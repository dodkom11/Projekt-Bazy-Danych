<?php




/* ==========       SESJA I WARUNKI DOSTEPU     ========== */
session_start();

//jezeli nie jestesmy zalogowani i nasze uprawnienia inne niz "admin" wroc do index.php
if (!isset($_SESSION['zalogowany'])) {
    header('Location: index.php');
    exit(); //opuszczamy plik nie wykonuje sie reszta
}

require_once "logikaphp/connect.php";




/* ==========       POLACZENIE Z BAZA       ========== */
$connection = oci_connect($username, $password, $database);
if (!$connection) {
    $m = oci_error();
    trigger_error('Nie udało się połaczyć z baza: ' . $m['message'], E_USER_ERROR);
}




/* ==========       ZMIENNE LOKALNE         ========== */
$querySelectZamowienia = "begin 
                            :cursor := SELECTZAMOWIENIAID(:konto_id);
                        end;";

$querySzczegoly        = "begin 
                            :cursor3 := SELECTSZCZEGOLYZAMOWIENIA(:rekord_id);
                        end;";

$queryPokazKategorie =      "begin 
                                :cursor := SELECTKATEGORIA;
                            end;";

// ---------------------------------------------------
$queryLicz = "begin 
                :bv := COUNTRW(:tabl, :colm, :cond);    
               end;"; 

$tablename  = 'ZAMOWIENIE';
$columnname = 'ZAMOWIENIE_ID';
$condition = "KONTO_ID='" . $_SESSION['S_KONTO_ID'] . "'";
// ---------------------------------------------------




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




/* ==========       SELECT KLIENCI TABELA       ========== */
//PARSOWANIE  
$stid = oci_parse($connection, $querySelectZamowienia);
if (!$stid) {
    $m = oci_error($connection);
    trigger_error('Nie udało się przeanalizować polecenia pl/sql: ' . $m['message'], E_USER_ERROR);
}

//PHP VARIABLE --> ORACLE PLACEHOLDER
$cursorTabela = oci_new_cursor($connection);
oci_bind_by_name($stid, ":konto_id", $_SESSION['S_KONTO_ID']);
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




/* ==========       SELECT LICZBA Zamowien          ========== */
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
                        <li class="nav-item">
                            <a class="nav-link" href="sklep.php"><i class="fas fa-shopping-basket"></i></i>&nbsp;&nbsp;Sklep</a>
                        </li>
                        <li class="nav-item">
                            <?php echo '<a class="nav-link'; if($_SESSION['S_ILEKOSZYK'] > 0) echo ' koszykactive"'; else echo '"'; ?>href="koszyk.php"><i class="fas fa-shopping-cart "></i>&nbsp;&nbsp;Koszyk (<?php echo $_SESSION['S_ILEKOSZYK']; ?>)</a>
                        </li>
                        <li class="nav-item active">
                            <a class="nav-link" href="zamowienie.php"><i class="fas fa-history"></i>&nbsp;&nbsp;Zamówienia</a>
                            <span class="sr-only">(current)</span>
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
END;
                    }
                ?></div>
            </ul>
        </div>
        <div id="page-content-wrapper">
            <div class="container-fluid">
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
                    <tbody>


                                                <?php
//WYPEŁNIJ TABELE KLIENTAMI Z BAZY
while (($row = oci_fetch_array($cursorTabela, OCI_ASSOC + OCI_RETURN_NULLS)) != false) {
$ZAMOWIENIE_ID               = $row['ZAMOWIENIE_ID'];
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

if($ZAMOWIENIE_ZAAKCEPTOWANE == 0) $ZAMOWIENIE_ZAAKCEPTOWANE = "Nie";
    else $ZAMOWIENIE_ZAAKCEPTOWANE = "Tak";

if($ZAPLACONO == 0) $ZAPLACONO = "Nie";
    else $ZAPLACONO = "Tak";

if($ZREALIZOWANO == 0) $ZREALIZOWANO = "Nie";
    else $ZREALIZOWANO = "Tak";

 
    echo "<tr><td><form action=\"" . $_SERVER['PHP_SELF'] . "\" method=\"post\"><button type=\"submit\" name=\"szczegoly\" class=\"btn btn-primary btn-sm\" value=\"" . htmlspecialchars($ZAMOWIENIE_ID). "\">szczegoly</button></form></td>";
    echo "<td>$ZAMOWIENIE_ID</td><td>$NAZWA_FIRMY</td> <td>$DATA_PRZYJECIA_ZAMOWIENIA</td> <td>$DATA_REALIZACJI_ZAMOWIENIA</td> <td>$ZAMOWIENIE_ZAAKCEPTOWANE</td> <td>$ZAPLACONO</td> <td>$ZREALIZOWANO</td> <td>$KOSZT_ZAMOWIENIA PLN</td> <td>$METODA_PLATNOSCI</td> <td>$DOKUMENT_SPRZEDAZY</td><td>$MIEJSCOWOSC</td> <td>$WOJEWODZTWO</td> <td>$KOD_POCZTOWY</td><td>$ULICA</td><td>$NR_DOMU</td><td>$NR_LOKALU</td> <td>$NR_TEL</td> <td>$EMAIL</td></tr>";
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
$SUMA = 0;                                                 
while (($row = oci_fetch_array($cursorSzczegoly, OCI_ASSOC + OCI_RETURN_NULLS)) != false) {
    $PRODUKT_ID                  = $row['PRODUKT_ID'];
    $PRODUCENT                   = $row['PRODUCENT'];
    $MODEL                       = $row['MODEL'];
    $NUMER_KATALOGOWY            = $row['NUMER_KATALOGOWY'];
    $ILOSC_SZTUK                 = $row['ILOSC_SZTUK'];
    $KOSZT                       = $row['KOSZT'];
    $SUMA                        += $row['KOSZT'];

        echo "<tr><td>$PRODUKT_ID</td> <td>$PRODUCENT</td> <td>$MODEL</td> <td>$NUMER_KATALOGOWY</td> <td>$ILOSC_SZTUK</td> <td>$KOSZT PLN</td></tr>";

    }
?>
                                           </tbody>
                                        </table>
 <?php echo "KOSZT ZAMOWIENIA:&nbsp;&nbsp;<strong>" . $SUMA . " PLN </strong>"; ?>                                        
                                    </div>
                                </div>
                            </div>                        
                        </div>            
<?php } ?>                 
                    
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