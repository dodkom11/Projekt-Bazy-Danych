<?php

    session_start();
    
    //jezeli nie jestesmy zalogowani wroc do index.php
    if (!isset($_SESSION['zalogowany']) OR strcmp($_SESSION['S_UPRAWNIENIA'], "admin" ))
    {
        header('Location: ../index.php');
        exit(); //opuszczamy plik nie wykonuje sie reszta
    }

    require_once "../connect.php"; 

    $konto_id = null;

    $query = "begin 
               :cursor := SELECTKLIENCI;
             end;";

    $query2 = "begin 
               :cursor2 := SELECTKLIENCIKONTOID(:konto_id);
             end;";

    //Polaczenie z baza
    $connection = oci_connect($username, $password, $database);
    if (!$connection) {
        $m = oci_error();
        trigger_error('Nie udało się połaczyć z baza: '. $m['message'], E_USER_ERROR);
    }

    //Parsowanie polecenia pl/sql   
    $stid = oci_parse($connection, $query);
    if (!$stid) {
        $m = oci_error($connection);
        trigger_error('Nie udało się przeanalizować polecenia pl/sql: '. $m['message'], E_USER_ERROR);
    }

    $p_cursor = oci_new_cursor($connection);
    oci_bind_by_name($stid, ":cursor", $p_cursor, -1, OCI_B_CURSOR);

    //Wykonaj polecenie SQL
    $result = oci_execute($stid);   
    if (!$result) {
        $m = oci_error($stid);
        trigger_error('Nie udało się wykonać polecenia: '. $m['message'], E_USER_ERROR);
    }

    oci_execute($p_cursor, OCI_DEFAULT);

    
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
                            <a class="nav-link" href="../index.php"><i class="fas fa-home"></i>&nbsp;&nbsp;Strona Główna
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../sklep.php"><i class="fas fa-shopping-basket"></i></i>&nbsp;&nbsp;Sklep</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#"><i class="fas fa-info"></i>&nbsp;&nbsp;O nas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#"><i class="fas fa-address-book"></i>&nbsp;&nbsp;Kontakt</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../logout.php"><i class="fas fa-sign-in-alt"></i>&nbsp;&nbsp;Wyloguj</a>
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
                        <a href="#">&nbsp;&nbsp;Zarządaj Producentem</a>
                    </li>
                    <li>
                        <a href="#">&nbsp;&nbsp;Zarządaj Kurierem</a>
                    </li>
                </ul>
            </div>
            <!-- /#sidebar-wrapper -->
            <!-- Page Content -->
            <div id="page-content-wrapper">
                <div class="container-fluid">
                    <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <a class="nav-item nav-link active" id="nav-zakladka1-tab" data-toggle="tab" href="#nav-zakladka1" role="tab" aria-controls="nav-zakladka1" aria-selected="true">Podaj ID Konta</a>
                            <a class="nav-item nav-link" id="nav-zakladka2-tab" data-toggle="tab" href="#nav-zakladka2" role="tab" aria-controls="nav-zakladka2" aria-selected="false">Przeglądaj</a>
                            <a class="nav-item nav-link" id="nav-zakladka3-tab" data-toggle="tab" href="#nav-zakladka3" role="tab" aria-controls="nav-zakladka3" aria-selected="false">Usuń Konto</a>
                        </div>
                    </nav>
                    <br>
                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="nav-zakladka1" role="tabpanel" aria-labelledby="nav-zakladka1-tab">
                            <form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
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
                                        if(!empty($_REQUEST['number-input'])){
echo <<<END
<span style="font-size: 25px;">WYBRANE ID: </span> <span class="badge badge-dark" style="font-size: 26px;">
END;
                                    echo $_REQUEST['number-input'] . "</span>";}
                                    ?>
                                </div>
                            </div>
                        </form>
                        <?php
                        if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        // collect value of input field
                        $konto_id = htmlspecialchars($_REQUEST['number-input']);
                        if (empty($konto_id)) {
                        $message = "NIE PODANO ID";
                        echo "<script type='text/javascript'>alert('$message');</script>";
                        } else if(!empty($_REQUEST['number-input'])) {
                        
                        //Parsowanie polecenia pl/sql
                        $stid = oci_parse($connection, $query2);
                        if (!$stid) {
                        $m = oci_error($connection);
                        trigger_error('Nie udało się przeanalizować polecenia pl/sql: '. $m['message'], E_USER_ERROR);
                        }
                        $p2_cursor = oci_new_cursor($connection);
                        oci_bind_by_name($stid, ":konto_id", $konto_id);
                        oci_bind_by_name($stid, ":cursor2", $p2_cursor, -1, OCI_B_CURSOR);
                        //Wykonaj polecenie SQL
                        $result = oci_execute($stid);
                        if (!$result) {
                        $m = oci_error($stid);
                        trigger_error('Nie udało się wykonać polecenia: '. $m['message'], E_USER_ERROR);
                        }
                        oci_execute($p2_cursor, OCI_DEFAULT);
                        }
                        }
                        ?>
                        <div class="row">
                            <div class="card mb-3">
                                <div class="card-header">
                                <i class="fa fa-table"></i> Klienci</div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th>KONTO_ID</th>
                                                    <th>IMIE</th>
                                                    <th>NAZWISKO</th>
                                                    <th>UPRAWNIENIA</th>
                                                    <th>MIEJSCOWOSC</th>
                                                    <th>WOJEWODZTWO</th>
                                                    <th>KOD_POCZTOWY</th>
                                                    <th>ULICA</th>
                                                    <th>NR_DOMU</th>
                                                    <th>NR_LOKALU</th>
                                                    <th>EMAIL</th>
                                                    <th>NR_TEL</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                if(!empty($_REQUEST['number-input'])) {
                                                while (($row = oci_fetch_array($p2_cursor, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
                                                $KONTO_ID = $row['KONTO_ID'];
                                                $IMIE = $row['IMIE'];
                                                $NAZWISKO = $row['NAZWISKO'];
                                                $UPRAWNIENIA = $row['UPRAWNIENIA'];
                                                $MIEJSCOWOSC = $row['MIEJSCOWOSC'];
                                                $WOJEWODZTWO = $row['WOJEWODZTWO'];
                                                $KOD_POCZTOWY = $row['KOD_POCZTOWY'];
                                                $ULICA = $row['ULICA'];
                                                $NR_DOMU = $row['NR_DOMU'];
                                                $NR_LOKALU = $row['NR_LOKALU'];
                                                $EMAIL = $row['EMAIL'];
                                                $NR_TEL = $row['NR_TEL'];
                                                echo "<tr><td>$KONTO_ID</td> <td>$IMIE</td> <td>$NAZWISKO</td> <td>$UPRAWNIENIA</td> <td>$MIEJSCOWOSC</td> <td>$WOJEWODZTWO</td> <td>$KOD_POCZTOWY</td> <td>$ULICA</td> <td>$NR_DOMU</td> <td>$NR_LOKALU</td> <td>$EMAIL</td> <td>$NR_TEL</td></tr>";
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
                    <div class="tab-pane fade" id="nav-zakladka2" role="tabpanel" aria-labelledby="nav-zakladka2-tab">
                        <div class="row">
                            <div class="card mb-3">
                                <div class="card-header">
                                <i class="fa fa-table"></i> Klienci</div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th>KONTO_ID</th>
                                                    <th>IMIE</th>
                                                    <th>NAZWISKO</th>
                                                    <th>UPRAWNIENIA</th>
                                                    <th>MIEJSCOWOSC</th>
                                                    <th>WOJEWODZTWO</th>
                                                    <th>KOD_POCZTOWY</th>
                                                    <th>ULICA</th>
                                                    <th>NR_DOMU</th>
                                                    <th>NR_LOKALU</th>
                                                    <th>EMAIL</th>
                                                    <th>NR_TEL</th>
                                                </tr>
                                            </thead>
                                            <tfoot>
                                            <tr>
                                                <th>KONTO_ID</th>
                                                <th>IMIE</th>
                                                <th>NAZWISKO</th>
                                                <th>UPRAWNIENIA</th>
                                                <th>MIEJSCOWOSC</th>
                                                <th>WOJEWODZTWO</th>
                                                <th>KOD_POCZTOWY</th>
                                                <th>ULICA</th>
                                                <th>NR_DOMU</th>
                                                <th>NR_LOKALU</th>
                                                <th>EMAIL</th>
                                                <th>NR_TEL</th>
                                            </tr>
                                            </tfoot>
                                            <tbody>
                                                <?php
                                                while (($row = oci_fetch_array($p_cursor, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
                                                $KONTO_ID = $row['KONTO_ID'];
                                                $IMIE = $row['IMIE'];
                                                $NAZWISKO = $row['NAZWISKO'];
                                                $UPRAWNIENIA = $row['UPRAWNIENIA'];
                                                $MIEJSCOWOSC = $row['MIEJSCOWOSC'];
                                                $WOJEWODZTWO = $row['WOJEWODZTWO'];
                                                $KOD_POCZTOWY = $row['KOD_POCZTOWY'];
                                                $ULICA = $row['ULICA'];
                                                $NR_DOMU = $row['NR_DOMU'];
                                                $NR_LOKALU = $row['NR_LOKALU'];
                                                $EMAIL = $row['EMAIL'];
                                                $NR_TEL = $row['NR_TEL'];
                                                echo "<tr><td>$KONTO_ID</td> <td>$IMIE</td> <td>$NAZWISKO</td> <td>$UPRAWNIENIA</td> <td>$MIEJSCOWOSC</td> <td>$WOJEWODZTWO</td> <td>$KOD_POCZTOWY</td> <td>$ULICA</td> <td>$NR_DOMU</td> <td>$NR_LOKALU</td> <td>$EMAIL</td> <td>$NR_TEL</td></tr>";
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="nav-zakladka3" role="tabpanel" aria-labelledby="nav-zakladka3-tab">
                        <?php
                        if(!empty($_REQUEST['number-input'])){
echo <<<END
                        <span style="font-size: 25px;">WYBRANE ID:&nbsp;</span> <span class="badge badge-dark" style="font-size: 26px;">
END;
                    echo $_REQUEST['number-input'] . "</span>";}
                    ?>
                    <br/> <br/>
                    <div class="row">
                        <div class="card mb-3">
                            <div class="card-header">
                            <i class="fa fa-table"></i> Klienci</div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>KONTO_ID</th>
                                                <th>IMIE</th>
                                                <th>NAZWISKO</th>
                                                <th>UPRAWNIENIA</th>
                                                <th>MIEJSCOWOSC</th>
                                                <th>WOJEWODZTWO</th>
                                                <th>KOD_POCZTOWY</th>
                                                <th>ULICA</th>
                                                <th>NR_DOMU</th>
                                                <th>NR_LOKALU</th>
                                                <th>EMAIL</th>
                                                <th>NR_TEL</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if(!empty($_REQUEST['number-input'])) {
                                            
                                            //Parsowanie polecenia pl/sql
                                            $stid3 = oci_parse($connection, $query2);
                                            if (!$stid3) {
                                            $m = oci_error($connection);
                                            trigger_error('Nie udało się przeanalizować polecenia pl/sql: '. $m['message'], E_USER_ERROR);
                                            }
                                            $p3_cursor = oci_new_cursor($connection);
                                            oci_bind_by_name($stid3, ":konto_id", $konto_id);
                                            oci_bind_by_name($stid3, ":cursor2", $p3_cursor, -1, OCI_B_CURSOR);
                                            //Wykonaj polecenie SQL
                                            $result3 = oci_execute($stid3);
                                            if (!$result3) {
                                            $m = oci_error($stid3);
                                            trigger_error('Nie udało się wykonać polecenia: '. $m['message'], E_USER_ERROR);
                                            }
                                            oci_execute($p3_cursor, OCI_DEFAULT);
                                            }
                                            if(!empty($_REQUEST['number-input'])) {
                                            while (($row = oci_fetch_array($p3_cursor, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
                                            $KONTO_ID = $row['KONTO_ID'];
                                            $IMIE = $row['IMIE'];
                                            $NAZWISKO = $row['NAZWISKO'];
                                            $UPRAWNIENIA = $row['UPRAWNIENIA'];
                                            $MIEJSCOWOSC = $row['MIEJSCOWOSC'];
                                            $WOJEWODZTWO = $row['WOJEWODZTWO'];
                                            $KOD_POCZTOWY = $row['KOD_POCZTOWY'];
                                            $ULICA = $row['ULICA'];
                                            $NR_DOMU = $row['NR_DOMU'];
                                            $NR_LOKALU = $row['NR_LOKALU'];
                                            $EMAIL = $row['EMAIL'];
                                            $NR_TEL = $row['NR_TEL'];
                                            echo "<tr><td>$KONTO_ID</td> <td>$IMIE</td> <td>$NAZWISKO</td> <td>$UPRAWNIENIA</td> <td>$MIEJSCOWOSC</td> <td>$WOJEWODZTWO</td> <td>$KOD_POCZTOWY</td> <td>$ULICA</td> <td>$NR_DOMU</td> <td>$NR_LOKALU</td> <td>$EMAIL</td> <td>$NR_TEL</td></tr>";
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
                    if(!empty($_REQUEST['number-input'])){                        
                        echo '<button type="submit" class="btn btn-primary">POTWIERDZ USUNIECIE</button>';
                    }   
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
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../script/toogle.js"></script>
    <script src="../script/showAndHide.js"></script>
    <script src="../vendor/datatables/jquery.dataTables.js"></script>
    <script src="../vendor/datatables/dataTables.bootstrap4.js"></script>
    <script src="../vendor/datatables/callDataTables.js"></script>
</body>
</html>