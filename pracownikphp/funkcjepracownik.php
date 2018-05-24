<?php




/* ==========       SESJA I WARUNKI DOSTEPU     ========== */
session_start();

//jezeli nie jestesmy zalogowani i nasze uprawnienia inne niz "admin" wroc do index.php
if (!isset($_SESSION['zalogowany']) OR (strcmp($_SESSION['S_UPRAWNIENIA'], "admin") AND strcmp($_SESSION['S_UPRAWNIENIA'], "pracownik"))) {
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
//DELETE PRODUKT
$queryUsunProduktID= "begin 
                            DELETEPRODUKT(:rekord_id);
                     end;";

//DODAJ PRODUKT
$queryDodajProdukt = "begin 
                            INSERTPRODUKT(:dostawcaid, :kategoriaid, :producent, :nrkatalogowy, :model, :cena, :sztuk, :opis);
                     end;";


//DELTE KATEGORIA
$queryDeleteKategoria = "begin 
                            DELETEKATEGORIA(:rekord_id);
                        end;"; 

//INSERT KATEGORIA
$queryInsertKategoria = "begin 
                            INSERTKATEGORIA(:nazwa, :opis);
                        end;"; 


/* ==========       FUNKCJA DELETE PRODUKT            ========== */

function funkcjaUsunProdukt($connection, $queryUsunProduktID)
{
    //PARSOWANIE  
    $stid = oci_parse($connection, $queryUsunProduktID);

    //PHP VARIABLE --> ORACLE PLACEHOLDER
    oci_bind_by_name($stid, ":rekord_id", $_POST['usunproduktid']);

    //EXECUTE POLECENIE
    $result = oci_execute($stid);
    if (!$result) {
        $m = oci_error($stid);
        trigger_error('Nie udało się wykonać polecenia: ' . $m['message'], E_USER_ERROR);
    }

    //ZWOLNIJ ZASOBY
    oci_free_statement($stid);

    echo '<div class="alert alert-success" role="alert"><strong>INFORMACJA!</strong> Pomyślnie usunięto konto ID: <strong>' . $_POST['usunproduktid'] . "</strong></div>";
}


/* ==========       FUNKCJA DODAJ PRACOWNIKA           ========== */

function funkcjaDodajProdukt($connection, $queryDodajProdukt)
{
    //PARSOWANIE  
    $stid = oci_parse($connection, $queryDodajProdukt);

    //PHP VARIABLE --> ORACLE PLACEHOLDER
    oci_bind_by_name($stid, ":dostawcaid", $_POST['dostawcaid']);
    oci_bind_by_name($stid, ":kategoriaid", $_POST['kategoriaid']);
    oci_bind_by_name($stid, ":producent", $_POST['producent']);
    oci_bind_by_name($stid, ":nrkatalogowy", $_POST['nrkatalogowy']);
    oci_bind_by_name($stid, ":model", $_POST['model']);
    oci_bind_by_name($stid, ":cena", $_POST['cena']);
    oci_bind_by_name($stid, ":sztuk", $_POST['sztuk']);
    oci_bind_by_name($stid, ":opis", $_POST['opis']);

    //EXECUTE POLECENIE
    $result = oci_execute($stid);
    if (!$result) {
        $m = oci_error($stid);
        trigger_error('Nie udało się wykonać polecenia: ' . $m['message'], E_USER_ERROR);
    }

    //ZWOLNIJ ZASOBY
    oci_free_statement($stid);

        echo '<div class="alert alert-success" role="alert"><strong>INFORMACJA!</strong> Pomyślnie Dodano Produkt: <strong>' . $_POST['producent'] . "</strong></div>";
}

/* ==========       FUNKCJA DELETE KATEGORIA            ========== */

function funkcjaUsunKategorie($connection, $queryDeleteKategoria)
{
    //PARSOWANIE  
    $stid = oci_parse($connection, $queryDeleteKategoria);

    //PHP VARIABLE --> ORACLE PLACEHOLDER
    oci_bind_by_name($stid, ":rekord_id", $_POST['usunkategorieid']);

    //EXECUTE POLECENIE
    $result = oci_execute($stid);
    if (!$result) {
        $m = oci_error($stid);
        trigger_error('Nie udało się wykonać polecenia: ' . $m['message'], E_USER_ERROR);
    }

    //ZWOLNIJ ZASOBY
    oci_free_statement($stid);

    echo '<div class="alert alert-success" role="alert"><strong>INFORMACJA!</strong> Pomyślnie usunięto kategorie ID: <strong>' . $_POST['usunkategorieid'] . "</strong></div>";
}


/* ==========       FUNKCJA DODAJ KATEGORIA           ========== */

function funkcjaDodajKategorie($connection, $queryInsertKategoria)
{
    //PARSOWANIE  
    $stid = oci_parse($connection, $queryInsertKategoria);

    //PHP VARIABLE --> ORACLE PLACEHOLDER
    oci_bind_by_name($stid, ":nazwa", $_POST['nazwakategori']);
    oci_bind_by_name($stid, ":opis", $_POST['opis']);

    //EXECUTE POLECENIE
    $result = oci_execute($stid);
    if (!$result) {
        $m = oci_error($stid);
        trigger_error('Nie udało się wykonać polecenia: ' . $m['message'], E_USER_ERROR);
    }

    //ZWOLNIJ ZASOBY
    oci_free_statement($stid);

        echo '<div class="alert alert-success" role="alert"><strong>INFORMACJA!</strong> Pomyślnie Dodano Kategorie: <strong>' . $_POST['nazwakategori'] . "</strong></div>";
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
                            <a class="nav-link" href="#"><i class="fas fa-info"></i>&nbsp;&nbsp;O nas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#"><i class="fas fa-address-book"></i>&nbsp;&nbsp;Kontakt</a>
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
                        <a href="zarzadzaj_zamowieniem.php">&nbsp;&nbsp;Zarządaj Zamówieniem</a>
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
<?php
    if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['usunproduktbutton'])) {
            funkcjaUsunProdukt($connection, $queryUsunProduktID);
        }
    else if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['dodajproduktbutton'])) {
            funkcjaDodajProdukt($connection, $queryDodajProdukt);
    }
    else if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['dodajkategoriebutton'])) {
            funkcjaDodajKategorie($connection, $queryInsertKategoria);
    }
    else if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['usunkategoriebutton'])) {
            funkcjaUsunKategorie($connection, $queryDeleteKategoria);
    }
?>
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