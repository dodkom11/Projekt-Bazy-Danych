<?php

    session_start();
    
    //jezeli nie jestesmy zalogowani wroc do index.php
    if (!isset($_SESSION['zalogowany']) OR strcmp($_SESSION['S_UPRAWNIENIA'], "admin" ))
    {
        header('Location: ../index.php');
        exit(); //opuszczamy plik nie wykonuje sie reszta
    }

    require_once "../connect.php"; 

    $query = "begin 
               :cursor := SELECTKLIENCI;
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
                        <li class="nav-item">
                            <a class="nav-link" href="zarzadzaj_pracownikiem.php"><i class="fas fa-gavel"></i>&nbsp;&nbsp;Admin Panel</a>
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
                    <li>
                        <a href="zarzadzaj_pracownikiem.php">&nbsp;&nbsp;Zarządaj Pracownikiem</a>
                    </li>
                    <li>
                        <a href="zarzadzaj_klientem.php">&nbsp;&nbsp;Zarządaj Klientem</a>
                    </li>
                    <li>
                        <a href="#">&nbsp;&nbsp;Haki</a>
                    </li>
                    <li>
                        <a href="#">&nbsp;&nbsp;Kat. 4</a>
                    </li>
                    <li>
                        <a href="#">&nbsp;&nbsp;Kat. 5</a>
                    </li>
                    <li>
                        <a href="#">&nbsp;&nbsp;Kat. 6</a>
                    </li>
                    <li>
                        <a href="#">&nbsp;&nbsp;Kat. 7</a>
                    </li>
                </ul>
            </div>
            <!-- /#sidebar-wrapper -->
            <!-- Page Content -->
            <div id="page-content-wrapper">
                <div class="container-fluid">
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