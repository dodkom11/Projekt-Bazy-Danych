<?php




/* ==========       SESJA I WARUNKI DOSTEPU     ========== */
session_start();

//jezeli nie jestesmy zalogowani i nasze uprawnienia inne niz "admin" wroc do index.php
if (!isset($_SESSION['zalogowany']) OR strcmp($_SESSION['S_UPRAWNIENIA'], "admin")) {
    header('Location: ../index.php');
    exit(); //opuszczamy plik nie wykonuje sie reszta
}

require_once "../logikaphp/connect.php";




/* ==========       POLACZENIE Z BAZA       ========== */
$connection = oci_connect($username, $password, $database);
if (!$connection) {
    $m = oci_error();
    trigger_error('Nie udało się połaczyć z baza: ' . $m['message'], E_USER_ERROR);
}




/* ==========       ZMIENNE LOKALNE         ========== */
//SELECT KLIENCI TABELA
$querySelectDostawcy = "begin 
                            :cursor := SELECTDOSTAWCA;
                        end;";

//SELECT LICZBA KLIENTOW
$queryLicz = "begin 
                :bv := COUNTRW(:tabl, :colm, :cond);    
               end;";

//SELECT KLIENT PO ID
$querySelectDostawcaID = "begin 
                            :cursor2 := SELECTDOSTAWCAID(:rekord_id);
                        end;";   

$tablename  = 'DOSTAWCA';
$columnname = 'DOSTAWCA_ID';
$condition  = "'true'='true'";


/* ==========       SELECT KLIENCI TABELA       ========== */
//PARSOWANIE  
$stid = oci_parse($connection, $querySelectDostawcy);
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

/* ==========       SELECT LICZBA KLIENTOW          ========== */
//PARSOWANIE  
$stid = oci_parse($connection, $queryLicz);

//PHP VARIABLE --> ORACLE PLACEHOLDER
oci_bind_by_name($stid, ":tabl", $tablename);
oci_bind_by_name($stid, ":colm", $columnname);
oci_bind_by_name($stid, ":cond", $condition);
oci_bind_by_name($stid, ":bv", $ileOsob, 10);

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
                        <li class="nav-item active">
                            <a class="nav-link" href="zarzadzaj_pracownikiem.php"><i class="fas fa-gavel"></i>&nbsp;&nbsp;Admin Panel</a>
                            <span class="sr-only">(current)</span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../pracownikphp/zarzadzaj_produktem.php"><i class="fas fa-pencil-alt"></i>&nbsp;&nbsp;Pracownik Panel
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../index.php"><i class="fas fa-home"></i>&nbsp;&nbsp;Strona Główna
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../sklep.php"><i class="fas fa-shopping-basket"></i></i>&nbsp;&nbsp;Sklep</a>
                        </li>
                        <li class="nav-item">
                            <?php echo '<a class="nav-link'; if($_SESSION['S_ILEKOSZYK'] > 0) echo ' koszykactive"'; else echo '"'; ?>href="../koszyk.php"><i class="fas fa-shopping-cart "></i>&nbsp;&nbsp;Koszyk (<?php echo $_SESSION['S_ILEKOSZYK']; ?>)</a>
 
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
                        <a href="zarzadzaj_pracownikiem.php">&nbsp;&nbsp;Zarządaj Pracownikiem</a>
                    </li>
                    <li>
                        <a href="zarzadzaj_klientem.php">&nbsp;&nbsp;Zarządaj Klientem</a>
                    </li>
                    <li>
                        <a href="zarzadzaj_dostawca.php" class="nav-active">&nbsp;&nbsp;Zarządaj Dostawcą</a>
                    </li>
                    <li>
                        <a href="zarzadzaj_kurierem.php">&nbsp;&nbsp;Zarządaj Kurierem</a>
                    </li>
                </ul>
            </div>
            <!-- /#sidebar-wrapper -->
            <!-- Page Content -->
            <div id="page-content-wrapper">
                <div class="container-fluid">
                    <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <a class="nav-item nav-link active" id="nav-zakladka1-tab" data-toggle="tab" href="#nav-zakladka1" role="tab" aria-controls="nav-zakladka1" aria-selected="true">Podaj ID Dostawcy</a>
                            <a class="nav-item nav-link" id="nav-zakladka2-tab" data-toggle="tab" href="#nav-zakladka2" role="tab" aria-controls="nav-zakladka2" aria-selected="false">Przeglądaj</a>
                            <a class="nav-item nav-link" id="nav-zakladka3-tab" data-toggle="tab" href="#nav-zakladka3" role="tab" aria-controls="nav-zakladka3" aria-selected="false">Usuń Dostawcę</a>
                            <a class="nav-item nav-link" id="nav-zakladka4-tab" data-toggle="tab" href="#nav-zakladka4" role="tab" aria-controls="nav-zakladka" aria-selected="false">Utwórz Dostawcę</a>
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
                            <form method="post" action="<?php
echo $_SERVER['PHP_SELF'];
?>">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group row">
                                            <label for="example-number-input" class="col-3 col-form-label">Podaj ID KONTA</label>
                                            <div class="col-9">
                                                <input class="form-control" type="number" name="number-input" min="1">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <button type="submit" class="btn btn-primary">AKCEPTUJ</button>
                                    </div>
                                    <div class="col-4">
   
                                        <?php
// POKAŻ WYBRANE ID JEŚLI PODANO ID
if (!empty($_REQUEST['number-input'])) {

//WARUNEK CZY ISTENIEJE KLIENT 
$condition2 = "DOSTAWCA_ID='" . $_REQUEST['number-input'] . "'";

/* ==========       SPRAWDZ CZY KONTO NALEZY DO KLIENT          ========== */
//PARSOWANIE  
$stid = oci_parse($connection, $queryLicz);

//PHP VARIABLE --> ORACLE PLACEHOLDER
oci_bind_by_name($stid, ":tabl", $tablename);
oci_bind_by_name($stid, ":colm", $columnname);
oci_bind_by_name($stid, ":cond", $condition2);
oci_bind_by_name($stid, ":bv", $istniejeKonto, 10);

//EXECUTE POLECENIE
$result = oci_execute($stid);
if (!$result) {
    $m = oci_error($stid);
    trigger_error('Nie udało się wykonać polecenia: ' . $m['message'], E_USER_ERROR);
}

//ZWOLNIJ ZASOBY
oci_free_statement($stid);
    if($istniejeKonto > 0) {
echo <<<END
<span style="font-size: 25px;">WYBRANE ID: </span> <span class="badge badge-dark" style="font-size: 26px;">
END;
    echo $_REQUEST['number-input'] . "</span>";
    }
    else {
        $_REQUEST['number-input'] = 0;
    }
}
?>
                               </div>
                            </div>
                        </form>
                        <?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // ZBIERAMY DANE Z INPUT
    htmlspecialchars($_REQUEST['number-input']);
    
    if (empty($_REQUEST['number-input'])) {     //JEŻELI INPUT PUSTY LUB NIEPOPRAWNE ID     
        $message = "PODAJ POPRAWNE KONTO_ID!";        
        echo "<script type='text/javascript'>alert('$message');</script>";
    } else {

        //PARSOWANIE 
        $stid = oci_parse($connection, $querySelectDostawcaID);
        if (!$stid) {
            $m = oci_error($connection);
            trigger_error('Nie udało się przeanalizować polecenia pl/sql: ' . $m['message'], E_USER_ERROR);
        }

        //PHP VARIABLE --> ORACLE PLACEHOLDER        
        $cursorPokazOsobe = oci_new_cursor($connection);
        oci_bind_by_name($stid, ":rekord_id", $_REQUEST['number-input']);
        oci_bind_by_name($stid, ":cursor2", $cursorPokazOsobe, -1, OCI_B_CURSOR);

        //EXECUTE POLECENIE
        $result = oci_execute($stid);
        if (!$result) {
            $m = oci_error($stid);
            trigger_error('Nie udało się wykonać polecenia: ' . $m['message'], E_USER_ERROR);
        }

        //EXECUTE KURSOR
        oci_execute($cursorPokazOsobe, OCI_DEFAULT);

        //ZWOLNIJ ZASOBY
        oci_free_statement($stid);
    }
}
?>
                       <div class="row">
                            <div class="card mb-3">
                                <div class="card-header">
                                <i class="fa fa-table"></i> Wybrany Klient</div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="dataTable2" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>

                                                <th>DOSTAWCA_ID</th>
                                                <th>NAZWA FIRMY</th>
                                                <th>MIEJSCOWOSC</th>
                                                <th>WOJEWODZTWO</th>
                                                <th>KOD_POCZTOWY</th>
                                                <th>ULICA</th>
                                                <th>NR_DOMU</th>
                                                <th>NR_LOKALU</th>
                                                <th>EMAIL</th>
                                                <th>NR_TEL</th>
                                                <th>TELEFAKS</th>
                                                <th>WWW</th>

                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
//WYPEŁNIJ TABELE JEŻELI PODANO ID KONTA

if (!empty($_REQUEST['number-input'])) {
    while (($row = oci_fetch_array($cursorPokazOsobe, OCI_ASSOC + OCI_RETURN_NULLS)) != false) {
        $DOSTAWCA_ID  = $row['DOSTAWCA_ID'];
        $NAZWA_FIRMY  = $row['NAZWA_FIRMY'];
        $MIEJSCOWOSC  = $row['MIEJSCOWOSC'];
        $WOJEWODZTWO  = $row['WOJEWODZTWO'];
        $KOD_POCZTOWY = $row['KOD_POCZTOWY'];
        $ULICA        = $row['ULICA'];
        $NR_DOMU      = $row['NR_DOMU'];
        $NR_LOKALU    = $row['NR_LOKALU'];
        $EMAIL        = $row['EMAIL'];
        $NR_TEL       = $row['NR_TEL'];
        $FAX          = $row['FAX'];
        $WWW          = $row['WWW'];

        echo "<tr> <td>$DOSTAWCA_ID</td> <td>$NAZWA_FIRMY</td> <td>$MIEJSCOWOSC</td> <td>$WOJEWODZTWO</td> <td>$KOD_POCZTOWY</td> <td>$ULICA</td> <td>$NR_DOMU</td> <td>$NR_LOKALU</td> <td>$EMAIL</td> <td>$NR_TEL</td> <td>$FAX</td> <td>$WWW</td> </tr>";
    }
}
?>
                                           </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>             
                    </div>
<!-- 
        +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++    
                            >>>>>>>>>>      ZAKLADKA 2      <<<<<<<<<<
        +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
-->
                    <div class="tab-pane fade" id="nav-zakladka2" role="tabpanel" aria-labelledby="nav-zakladka2-tab">
                        <?php
//POKAŻ WYBRANE ID JEŚLI PODANO ID
if (!empty($_REQUEST['number-input'])) {
    echo <<<END
                       <span style="font-size: 25px;">WYBRANE ID:&nbsp;</span> <span class="badge badge-dark" style="font-size: 26px;">
END;
    echo $_REQUEST['number-input'] . "</span><br/> <br/>";
}
?>
                        <div class="row">
                            <div class="card mb-3">
                                <div class="card-header">
                                <i class="fa fa-table"></i> Klienci [<?php
//WYŚWIETL LICZBE KLIENTÓW
echo $ileOsob;
?>]</div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                <th>DOSTAWCA_ID</th>
                                                <th>NAZWA FIRMY</th>
                                                <th>MIEJSCOWOSC</th>
                                                <th>WOJEWODZTWO</th>
                                                <th>KOD_POCZTOWY</th>
                                                <th>ULICA</th>
                                                <th>NR_DOMU</th>
                                                <th>NR_LOKALU</th>
                                                <th>EMAIL</th>
                                                <th>NR_TEL</th>
                                                <th>TELEFAKS</th>
                                                <th>WWW</th>
                                                </tr>
                                            </thead>
                                            <tfoot>
                                            <tr>
                                       
                                                <th>DOSTAWCA_ID</th>
                                                <th>NAZWA FIRMY</th>
                                                <th>MIEJSCOWOSC</th>
                                                <th>WOJEWODZTWO</th>
                                                <th>KOD_POCZTOWY</th>
                                                <th>ULICA</th>
                                                <th>NR_DOMU</th>
                                                <th>NR_LOKALU</th>
                                                <th>EMAIL</th>
                                                <th>NR_TEL</th>
                                                <th>TELEFAKS</th>
                                                <th>WWW</th>

                                            </tr>
                                            </tfoot>
                                            <tbody>
                                                <?php
//WYPEŁNIJ TABELE KLIENTAMI Z BAZY                                            
while (($row = oci_fetch_array($cursorTabela, OCI_ASSOC + OCI_RETURN_NULLS)) != false) {
        $DOSTAWCA_ID  = $row['DOSTAWCA_ID'];
        $NAZWA_FIRMY  = $row['NAZWA_FIRMY'];
        $MIEJSCOWOSC  = $row['MIEJSCOWOSC'];
        $WOJEWODZTWO  = $row['WOJEWODZTWO'];
        $KOD_POCZTOWY = $row['KOD_POCZTOWY'];
        $ULICA        = $row['ULICA'];
        $NR_DOMU      = $row['NR_DOMU'];
        $NR_LOKALU    = $row['NR_LOKALU'];
        $EMAIL        = $row['EMAIL'];
        $NR_TEL       = $row['NR_TEL'];
        $FAX          = $row['FAX'];
        $WWW          = $row['WWW'];

        echo "<tr> <td>$DOSTAWCA_ID</td> <td>$NAZWA_FIRMY</td> <td>$MIEJSCOWOSC</td> <td>$WOJEWODZTWO</td> <td>$KOD_POCZTOWY</td> <td>$ULICA</td> <td>$NR_DOMU</td> <td>$NR_LOKALU</td> <td>$EMAIL</td> <td>$NR_TEL</td> <td>$FAX</td> <td>$WWW</td> </tr>";
    }
?>
                                           </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
<!-- 
        +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++    
                            >>>>>>>>>>      ZAKLADKA 3      <<<<<<<<<<
        +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
-->
                    <div class="tab-pane fade" id="nav-zakladka3" role="tabpanel" aria-labelledby="nav-zakladka3-tab">
                        <?php
//POKAŻ WYBRANE ID JEŚLI PODANO ID
if (!empty($_REQUEST['number-input'])) {
    echo <<<END
                       <span style="font-size: 25px;">WYBRANE ID:&nbsp;</span> <span class="badge badge-dark" style="font-size: 26px;">
END;
    echo $_REQUEST['number-input'] . "</span><br/> <br/>";
}
?>
                    <div class="row">
                        <div class="card mb-3">
                            <div class="card-header">
                            <i class="fa fa-table"></i> Wybrany Klient</div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="dataTable3" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>                                       
                                                <th>DOSTAWCA_ID</th>
                                                <th>NAZWA FIRMY</th>
                                                <th>MIEJSCOWOSC</th>
                                                <th>WOJEWODZTWO</th>
                                                <th>KOD_POCZTOWY</th>
                                                <th>ULICA</th>
                                                <th>NR_DOMU</th>
                                                <th>NR_LOKALU</th>
                                                <th>EMAIL</th>
                                                <th>NR_TEL</th>
                                                <th>TELEFAKS</th>
                                                <th>WWW</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
if (!empty($_REQUEST['number-input'])) { 
    //PARSOWANIE
    $stid = oci_parse($connection, $querySelectDostawcaID);
    if (!$stid) {
        $m = oci_error($connection);
        trigger_error('Nie udało się przeanalizować polecenia pl/sql: ' . $m['message'], E_USER_ERROR);
    }

    //PHP VARIABLE --> ORACLE PLACEHOLDER
    $cursorUsunOsobe = oci_new_cursor($connection);
    oci_bind_by_name($stid, ":rekord_id", $_REQUEST['number-input']);
    oci_bind_by_name($stid, ":cursor2", $cursorUsunOsobe, -1, OCI_B_CURSOR);


    //EXECUTE POLECENIE
    $result = oci_execute($stid);
    if (!$result) {
        $m = oci_error($stid);
        trigger_error('Nie udało się wykonać polecenia: ' . $m['message'], E_USER_ERROR);
    }

    //EXECUTE KURSOR
    oci_execute($cursorUsunOsobe, OCI_DEFAULT);

    //ZWOLNIJ ZASOBY
    oci_free_statement($stid); 
}
if (!empty($_REQUEST['number-input'])) {
    while (($row = oci_fetch_array($cursorUsunOsobe, OCI_ASSOC + OCI_RETURN_NULLS)) != false) {
        $DOSTAWCA_ID  = $row['DOSTAWCA_ID'];
        $NAZWA_FIRMY  = $row['NAZWA_FIRMY'];
        $MIEJSCOWOSC  = $row['MIEJSCOWOSC'];
        $WOJEWODZTWO  = $row['WOJEWODZTWO'];
        $KOD_POCZTOWY = $row['KOD_POCZTOWY'];
        $ULICA        = $row['ULICA'];
        $NR_DOMU      = $row['NR_DOMU'];
        $NR_LOKALU    = $row['NR_LOKALU'];
        $EMAIL        = $row['EMAIL'];
        $NR_TEL       = $row['NR_TEL'];
        $FAX          = $row['FAX'];
        $WWW          = $row['WWW'];

        echo "<tr> <td>$DOSTAWCA_ID</td> <td>$NAZWA_FIRMY</td> <td>$MIEJSCOWOSC</td> <td>$WOJEWODZTWO</td> <td>$KOD_POCZTOWY</td> <td>$ULICA</td> <td>$NR_DOMU</td> <td>$NR_LOKALU</td> <td>$EMAIL</td> <td>$NR_TEL</td> <td>$FAX</td> <td>$WWW</td> </tr>";
    }
}
?>
                                       </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>    
<?php
if (!empty($_REQUEST['number-input'])) {
echo <<<END
<form action="funkcjeAdmin.php" method="post">
<input type="hidden" name="usundostawceid" min="1" value="
END;
?>
<?php echo htmlspecialchars($_REQUEST['number-input']);
echo <<<END
"><br>
<input type="submit" name="usundostawcebutton" class="btn btn-primary" value="POTWIERDZ USUNIECIE" />
</form>
END;
}
?>
             
                </div>
<!-- 
        +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++    
                            >>>>>>>>>>      ZAKLADKA 4     <<<<<<<<<<
        +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
-->
            <div class="tab-pane fade" id="nav-zakladka4" role="tabpanel" aria-labelledby="nav-zakladka4-tab">
                <div class="row">  
                      
                    <form action="funkcjeAdmin.php" method="post">
                        <div class="form-row">
                            <div class="form-group col-3">
                                <label for="inputnazwaid">Nazwa Dostawcy</label>
                                <input type="text" class="form-control" id="inputnazwaid" name="nazwadostawcy" placeholder="Nazwa Dostawcy" required>
                            </div>
                            <div class="form-group col-3">
                                <label for="inputmiejscowoscid">Miejscowość</label>
                                <input type="text" class="form-control" id="inputmiejscowoscid" name="miejscowosc" placeholder="Miejscowość" required>
                            </div>
                            <div class="form-group col-3">
                                <label for="inputwojewodztwoid">Województwo</label>
                                <input type="text" class="form-control" id="inputwojewodztwoid" name="wojewodztwo" placeholder="Województwo" required>
                            </div>
                            <div class="form-group col-3">
                                <label for="inputkodpocztowyid">Kod Pocztowy</label>
                                <input type="text" class="form-control" id="inputkodpocztowyid" name="kodpocztowy" placeholder="00-000" required>
                            </div>
                            <div class="form-group col-3">
                                <label for="inputulicaid">Ulica</label>
                                <input type="text" class="form-control" id="inputulicaid" name="ulica" placeholder="Ulica" required>
                            </div>
                            <div class="form-group col-3">
                                <label for="inputnrdomuid">Nr budynku</label>
                                <input type="number" class="form-control" id="inputnrdomuid" name="nrdomu" placeholder="Nr budynku" required>
                            </div>
                            <div class="form-group col-3">
                                <label for="inputnrlokaluid">Nr Lokalu</label>
                                <input type="number" class="form-control" id="inputnrlokaluid" name="nrlokalu" placeholder="Nr Lokalu" >
                            </div>
                            <div class="form-group col-3">
                                <label for="inputemailid">Email</label>
                                <input type="text" class="form-control" id="inputemailid" name="email" placeholder="Email" required>
                            </div>
                            <div class="form-group col-3">
                                <label for="inputnrtelid">Nr Tel</label>
                                <input type="text" class="form-control" id="inputnrtelid" name="nrtel" placeholder="Nr Tel" required>
                            </div>
                            <div class="form-group col-3">
                                <label for="inputfaxid">FAX</label>
                                <input type="text" class="form-control" id="inputfaxid" name="fax" placeholder="FAX" required>
                            </div>
                            <div class="form-group col-3">
                                <label for="inputwwwid">Strona WWW</label>
                                <input type="text" class="form-control" id="inputwwwid" name="www" placeholder="Strona WWW">
                            </div>
                        </div>
                        <input type="submit" name="dodajdostawcebutton" class="btn btn-primary" value="POTWIERDZ DODANIE" />
                    </form>  

                </div>                
                <!-- /.row -->
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