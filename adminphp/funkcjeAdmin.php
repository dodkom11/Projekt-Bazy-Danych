<?php




/* ==========		SESJA I WARUNKI DOSTEPU		========== */
session_start();

//jezeli nie jestesmy zalogowani i nasze uprawnienia inne niz "admin" wroc do index.php
if (!isset($_SESSION['zalogowany']) OR strcmp($_SESSION['S_UPRAWNIENIA'], "admin")) {
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
$queryUsunKontoID = "begin 
              			 	DELETEKONTO(:rekord_id);
          				end;";

$queryDodajPracownikID = "begin 
              			 	INSERTPRACOWNIK(:rekord_id, :pensja, :premia);
          				end;";

$queryDodajKurier = "begin 
              			  	INSERTKURIER(:nazwa);
          			end;";

$queryUsunKurieraID= "begin 
              			 	DELETEKURIER(:rekord_id);
          			 end;";

$queryUsunDostawceID = "begin 
                            DELETEDOSTAWCA(:rekord_id);
                    end;";

$queryDodajDostawceID= "begin 
                            INSERTDOSTAWCA(:nazwadostawcy, :miejscowosc, :wojewodztwo, :kodpocztowy, :ulica, :nrdomu, :nrlokalu, :email, :nrtel, :fax, :www);
                     end;";




/* ==========		FUNKCJA DELETE KONTO			========== */

function funkcjaUsunKonto($connection, $queryUsunKontoID)
{
	//PARSOWANIE  
	$stid = oci_parse($connection, $queryUsunKontoID);

	//PHP VARIABLE --> ORACLE PLACEHOLDER
	oci_bind_by_name($stid, ":rekord_id", $_POST['usunkontoid']);

	//EXECUTE POLECENIE
	$result = oci_execute($stid);
	if (!$result) {
	    $m = oci_error($stid);
	    trigger_error('Nie udało się wykonać polecenia: ' . $m['message'], E_USER_ERROR);
	}

	//ZWOLNIJ ZASOBY
	oci_free_statement($stid);

	echo '<div class="alert alert-success" role="alert"><strong>INFORMACJA!</strong> Pomyślnie usunięto konto ID: <strong>' . $_POST['usunkontoid'] . "</strong></div>";
}


/* ==========		FUNKCJA DODAJ PRACOWNIKA			========== */

function funkcjaDodajPracownik($connection, $queryDodajPracownikID)
{
	//PARSOWANIE  
	$stid = oci_parse($connection, $queryDodajPracownikID);

	//PHP VARIABLE --> ORACLE PLACEHOLDER
	oci_bind_by_name($stid, ":rekord_id", $_POST['dodajpracownikid']);
	oci_bind_by_name($stid, ":pensja", $_POST['pensja']);
	oci_bind_by_name($stid, ":premia", $_POST['premia']);

	//EXECUTE POLECENIE
	$result = oci_execute($stid);
	if (!$result) {
	    $m = oci_error($stid);
	    trigger_error('Nie udało się wykonać polecenia: ' . $m['message'], E_USER_ERROR);
	}

	//ZWOLNIJ ZASOBY
	oci_free_statement($stid);

	echo '<div class="alert alert-success" role="alert"><strong>INFORMACJA!</strong> Pomyślnie dodano pracownika. Konto ID: <strong>' . $_POST['dodajpracownikid'] . "</strong></div>";
}





/* ==========		FUNKCJA DODAJ KURIER			========== */

function funkcjaDodajKurier($connection, $queryDodajKurier)
{
	//PARSOWANIE  
	$stid = oci_parse($connection, $queryDodajKurier);

	//PHP VARIABLE --> ORACLE PLACEHOLDER
	oci_bind_by_name($stid, ':nazwa', $_POST['nazwafirmy']);

	//EXECUTE POLECENIE
	$result = oci_execute($stid);
	if (!$result) {
	    $m = oci_error($stid);
	    trigger_error('Nie udało się wykonać polecenia: ' . $m['message'], E_USER_ERROR);
	}

	//ZWOLNIJ ZASOBY
	oci_free_statement($stid);

	echo '<div class="alert alert-success" role="alert"><strong>INFORMACJA!</strong> Pomyślnie dodano kuriera: <strong>' . $_POST['nazwafirmy'] . "</strong></div>";
}




/* ==========		FUNKCJA DELETE KONTO			========== */

function funkcjaUsunKuriera($connection, $queryUsunKurieraID)
{
	//PARSOWANIE  
	$stid = oci_parse($connection, $queryUsunKurieraID);

	//PHP VARIABLE --> ORACLE PLACEHOLDER
	oci_bind_by_name($stid, ":rekord_id", $_POST['usunkurieraid']);

	//EXECUTE POLECENIE
	$result = oci_execute($stid);
	if (!$result) {
	    $m = oci_error($stid);
	    trigger_error('Nie udało się wykonać polecenia: ' . $m['message'], E_USER_ERROR);
	}

	//ZWOLNIJ ZASOBY
	oci_free_statement($stid);

	echo '<div class="alert alert-success" role="alert"><strong>INFORMACJA!</strong> Pomyślnie usunięto kuriera ID: <strong>' . $_POST['usunkurieraid'] . "</strong></div>";
}


/* ==========       FUNKCJA DODAJ DOSTAWCE            ========== */

function funkcjaDodajDostawce($connection, $queryDodajDostawceID)
{
    //PARSOWANIE  
    $stid = oci_parse($connection, $queryDodajDostawceID);

    //PHP VARIABLE --> ORACLE PLACEHOLDER
    oci_bind_by_name($stid, ":nazwadostawcy", $_POST['nazwadostawcy']);
    oci_bind_by_name($stid, ":miejscowosc", $_POST['miejscowosc']);
    oci_bind_by_name($stid, ":wojewodztwo", $_POST['wojewodztwo']);
    oci_bind_by_name($stid, ":kodpocztowy", $_POST['kodpocztowy']);
    oci_bind_by_name($stid, ":ulica", $_POST['ulica']);
    oci_bind_by_name($stid, ":nrdomu", $_POST['nrdomu']);
    oci_bind_by_name($stid, ":nrlokalu", $_POST['nrlokalu']);
    oci_bind_by_name($stid, ":email", $_POST['email']);
    oci_bind_by_name($stid, ":nrtel", $_POST['nrtel']);
    oci_bind_by_name($stid, ":fax", $_POST['fax']);
    oci_bind_by_name($stid, ":www", $_POST['www']);

    //EXECUTE POLECENIE
    $result = oci_execute($stid);
    if (!$result) {
        $m = oci_error($stid);
        trigger_error('Nie udało się wykonać polecenia: ' . $m['message'], E_USER_ERROR);
    }

    //ZWOLNIJ ZASOBY
    oci_free_statement($stid);

        echo '<div class="alert alert-success" role="alert"><strong>INFORMACJA!</strong> Pomyślnie Dodano Dostawce: <strong>' . $_POST['nazwadostawcy'] . "</strong></div>";
}

/* ==========       FUNKCJA Usun Dostawce            ========== */

function funkcjaUsunDostawce($connection, $queryUsunDostawceID)
{
    //PARSOWANIE  
    $stid = oci_parse($connection, $queryUsunDostawceID);
    //PHP VARIABLE --> ORACLE PLACEHOLDER
    oci_bind_by_name($stid, ":rekord_id", $_POST['usundostawceid']);
    //EXECUTE POLECENIE
    $result = oci_execute($stid);
    if (!$result) {
        $m = oci_error($stid);
        trigger_error('Nie udało się wykonać polecenia: ' . $m['message'], E_USER_ERROR);
    }
    //ZWOLNIJ ZASOBY
    oci_free_statement($stid);
    echo '<div class="alert alert-success" role="alert"><strong>INFORMACJA!</strong> Pomyślnie usunięto Dostawce ID: <strong>' . $_POST['usundostawceid'] . "</strong></div>";
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
                        <a href="zarzadzaj_dostawca.php">&nbsp;&nbsp;Zarządaj Dostawcą</a>
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
<?php
	if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['usunkontobutton'])) {
    		funkcjaUsunKonto($connection, $queryUsunKontoID);
		}
	else if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['dodajpracownikbutton'])) {
			funkcjaDodajPracownik($connection, $queryDodajPracownikID);
	}
	else if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['dodajkurierbutton'])) {
			funkcjaDodajKurier($connection, $queryDodajKurier);
	}
	else if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['usunkurierabutton'])) {
			funkcjaUsunKuriera($connection, $queryUsunKurieraID);
	}
    else if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['usundostawcebutton'])) {
            funkcjaUsunDostawce($connection, $queryUsunDostawceID);
    }
    else if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['dodajdostawcebutton'])) {
            funkcjaDodajDostawce($connection, $queryDodajDostawceID);
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