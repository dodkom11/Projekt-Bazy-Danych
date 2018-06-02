<?php
ini_set('display_errors', 'Off');



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
$queryDodajProdukt = "INSERT INTO PRODUKT(DOSTAWCA_ID, KATEGORIA_ID, PRODUCENT, NUMER_KATALOGOWY, MODEL, CENA, SZTUK_NA_MAGAZYNIE, OPIS, ZDJECIE) VALUES (:dostawcaid, :kategoriaid, :producent, :nrkatalogowy, :model, :cena, :sztuk, :opis, empty_blob()) RETURNING ZDJECIE INTO :image";

//DELTE KATEGORIA
$queryDeleteKategoria = "begin 
                            DELETEKATEGORIA(:rekord_id);
                        end;"; 

//INSERT KATEGORIA
$queryInsertKategoria = "begin 
                            INSERTKATEGORIA(:nazwa, :opis);
                        end;"; 

//EDYTUJ ZAMOWIENIE
$queryEdytujZamowienie = "begin 
                            UPDATEZAMOWIENIE(:rekord_id, :data, :zaakaceptowano, :zaplacnono, :zrealizowano);
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
        if($m['code'] == "02292") {
            echo '<div class="alert alert-danger" role="alert"><strong>BŁĄD!</strong> Nie można usunąć, ponieważ istnieją powiązania między tabelami.';
        }
    } else {
        echo '<div class="alert alert-success" role="alert"><strong>INFORMACJA!</strong> Pomyślnie usunięto Produkt ID: <strong>' . $_POST['usunproduktid'] . "</strong></div>";
    }

    //ZWOLNIJ ZASOBY
    oci_free_statement($stid);    
}


/* ==========       FUNKCJA DODAJ PRODUKT           ========== */

function funkcjaDodajProdukt($connection, $queryDodajProdukt)
{
    $image = file_get_contents($_FILES['fileToUpload']['tmp_name']);
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
    $blob = oci_new_descriptor($connection, OCI_D_LOB);
    oci_bind_by_name($stid, ":image", $blob, -1, OCI_B_BLOB);

    oci_execute($stid, OCI_DEFAULT) or die ("Unable to execute query");

    if(!$blob->save($image)) {
        oci_rollback($connection);
    }
    else {
        oci_commit($connection);
    }

    oci_free_statement($stid);
    $blob->free();

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
        if($m['code'] == "02292") {
            echo '<div class="alert alert-danger" role="alert"><strong>BŁĄD!</strong> Nie można usunąć, ponieważ istnieją powiązania między tabelami.';
        }
    } else {
       echo '<div class="alert alert-success" role="alert"><strong>INFORMACJA!</strong> Pomyślnie usunięto kategorie ID: <strong>' . $_POST['usunkategorieid'] . "</strong></div>";
    }

    //ZWOLNIJ ZASOBY
    oci_free_statement($stid);

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


/* ==========       FUNKCJA EDYTUJ ZAMOWIENIE          ========== */

function funkcjaEdytujZamowienie($connection, $queryEdytujZamowienie)
{
    //PARSOWANIE  
    $stid = oci_parse($connection, $queryEdytujZamowienie);

    //PHP VARIABLE --> ORACLE PLACEHOLDER
    oci_bind_by_name($stid, ":rekord_id", $_POST['edytujzamowieniebutton']);
    oci_bind_by_name($stid, ":data", $_POST['datarealizacji']);
    oci_bind_by_name($stid, ":zaakaceptowano", $_POST['zaakceptowane']);
    oci_bind_by_name($stid, ":zaplacnono", $_POST['zaplacono']);
    oci_bind_by_name($stid, ":zrealizowano", $_POST['zrealizowano']);

    //EXECUTE POLECENIE
    $result = oci_execute($stid);
    if (!$result) {
        $m = oci_error($stid);
        trigger_error('Nie udało się wykonać polecenia: ' . $m['message'], E_USER_ERROR);
    }



    //ZWOLNIJ ZASOBY
    oci_free_statement($stid);

    echo '<div class="alert alert-success" role="alert"><strong>INFORMACJA!</strong> Pomyślna edycja zamówienia ID: <strong>' . $_POST['edytujzamowieniebutton'] . "</strong></div>";
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
        <!-- STYLE CSS -->
        <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link href="../css/simple-sidebar.css" rel="stylesheet">
        <link href="../css/mycss.css" rel="stylesheet">
        <link href="../vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">
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
            
       <!--  ==========    PASEK BOCZNY   ==========  -->

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

<!--  ==========  WYWOŁANIE FUNKCJI PRACOWNIK ==========  -->
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
    else if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['edytujzamowieniebutton'])) {
        funkcjaEdytujZamowienie($connection, $queryEdytujZamowienie);
    }
?>
        </div>
    </div>
    <!-- JavaScripts -->
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