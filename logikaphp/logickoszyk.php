<?php




/* ==========       SESJA I WARUNKI DOSTEPU     ========== */
session_start();

//jezeli nie jestesmy zalogowani wroc do index.php
if (!isset($_SESSION['zalogowany']))
{
    header('Location: ../index.php');
    exit(); //opuszczamy plik nie wykonuje sie reszta
}

require_once "connect.php"; 




/* ==========		POLACZENIE Z BAZA		========== */
$connection = oci_connect($username, $password, $database);
if (!$connection) {
    $m = oci_error();
    trigger_error('Nie udało się połaczyć z baza: ' . $m['message'], E_USER_ERROR);
}




/* ==========		ZMIENNE LOKALNE			========== */
//CZY PRODUKT W KOSZYKU
$queryLicz = "begin 
                :bv := COUNTRW(:tabl, :colm, :cond);    
               end;";

$tablename  = 'KOSZYK';
$columnname = 'PRODUKT_ID';


$querySztuki = "begin 
                :bv := COUNTSZTUKI(:tabl, :colm, :cond);    
               end;";

$columnname1 = 'ILOSC_SZTUK';


if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['buttonproduktid'])) {
		$condition  = "PRODUKT_ID = '" . $_POST['buttonproduktid'] . "' AND KONTO_ID = '" . $_SESSION['S_KONTO_ID'] . "'";
}

if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['buttonproduktidkoszykdec'])) {
		$condition  = "PRODUKT_ID = '" . $_POST['buttonproduktidkoszykdec'] . "' AND KONTO_ID = '" . $_SESSION['S_KONTO_ID'] . "'";
}

//DODAJ PRODUKT DO KOSZYKA
$queryDodajDoKoszyka = "begin 
              				INSERTKOSZYK(:produkt_id, :konto_id, :sztuk);
          				end;";

//ZWIEKSZ ILOSC PRODUKTOW O 1
$queryProduktInkrementuj =  "begin 
              			 		UPDATEKOSZYKINC(:produkt_id, :konto_id);
          				    end;";

//ZMNIEJSZ ILOSC PRODUKTOW O 1
$queryProduktDekrementuj=  "begin 
              			 		UPDATEKOSZYKDEC(:produkt_id, :konto_id);
          				    end;";

//STWORZ ZAMOWIENIE
$queryStworzZamowienie =   "begin 
              			 		STWORZ_ZAMOWIENIE(:konto_id, :kurier_id, :koszt_zamowienia, :metoda_platnosci, :dokument_sprzedazy);
          				    end;";





/* ==========		DOADAJ ZE SKLEPU			========== */
if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['buttonproduktid'])) {	
	/* ==========		SELECT LICZBA produktow			========== */
	//PARSOWANIE  
	$stid = oci_parse($connection, $queryLicz);

	//PHP VARIABLE --> ORACLE PLACEHOLDER
	oci_bind_by_name($stid, ":tabl", $tablename);
	oci_bind_by_name($stid, ":colm", $columnname);
	oci_bind_by_name($stid, ":cond", $condition);
	oci_bind_by_name($stid, ":bv", $liczProdukt, 10);

	//EXECUTE POLECENIE
	$result = oci_execute($stid);
	if (!$result) {
	    $m = oci_error($stid);
	    trigger_error('Nie udało się wykonać polecenia: ' . $m['message'], E_USER_ERROR);
	}

	//ZWOLNIJ ZASOBY
	oci_free_statement($stid);

	if($liczProdukt == 0) {
		$_SESSION['S_ILEKOSZYK'] += 1;
		$sztuk = 1;
		//PARSOWANIE  
		$stid = oci_parse($connection, $queryDodajDoKoszyka);

		//PHP VARIABLE --> ORACLE PLACEHOLDER
		oci_bind_by_name($stid, ":produkt_id", $_POST['buttonproduktid']);
		oci_bind_by_name($stid, ":konto_id", $_SESSION['S_KONTO_ID']);
		oci_bind_by_name($stid, ":sztuk", $sztuk);

		//EXECUTE POLECENIE
		$result = oci_execute($stid);
		if (!$result) {
		    $m = oci_error($stid);
		    trigger_error('Nie udało się wykonać polecenia: ' . $m['message'], E_USER_ERROR);
		}

		//ZWOLNIJ ZASOBY
		oci_free_statement($stid);

		
	} else {
		//PARSOWANIE  
		$stid = oci_parse($connection, $queryProduktInkrementuj);

		//PHP VARIABLE --> ORACLE PLACEHOLDER
		oci_bind_by_name($stid, ":produkt_id", $_POST['buttonproduktid']);
		oci_bind_by_name($stid, ":konto_id", $_SESSION['S_KONTO_ID']);

		//EXECUTE POLECENIE
		$result = oci_execute($stid);
		if (!$result) {
		    $m = oci_error($stid);
		    trigger_error('Nie udało się wykonać polecenia: ' . $m['message'], E_USER_ERROR);
		}
		//ZWOLNIJ ZASOBY
		oci_free_statement($stid);		
	}
}




/* ==========		DODAJ Z KOSZYKA			========== */
if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['buttonproduktidkoszyk'])) {	

		//PARSOWANIE  
		$stid = oci_parse($connection, $queryProduktInkrementuj);

		//PHP VARIABLE --> ORACLE PLACEHOLDER
		oci_bind_by_name($stid, ":produkt_id", $_POST['buttonproduktidkoszyk']);
		oci_bind_by_name($stid, ":konto_id", $_SESSION['S_KONTO_ID']);

		//EXECUTE POLECENIE
		$result = oci_execute($stid);
		if (!$result) {
		    $m = oci_error($stid);
		    trigger_error('Nie udało się wykonać polecenia: ' . $m['message'], E_USER_ERROR);
		}

		//ZWOLNIJ ZASOBY
		oci_free_statement($stid);
		oci_close($connection); 
		header('Location: ../koszyk.php');
		exit();
	}	

/* ==========		USUN Z KOSZYKA			========== */
if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['buttonproduktidkoszykdec'])) {	

/* ==========		SELECT LICZBA produktow			========== */
	//PARSOWANIE  
	$stid = oci_parse($connection, $querySztuki);

	//PHP VARIABLE --> ORACLE PLACEHOLDER
	oci_bind_by_name($stid, ":tabl", $tablename);
	oci_bind_by_name($stid, ":colm", $columnname1);
	oci_bind_by_name($stid, ":cond", $condition);
	oci_bind_by_name($stid, ":bv", $liczSztuki, 10);

	//EXECUTE POLECENIE
	$result = oci_execute($stid);
	if (!$result) {
	    $m = oci_error($stid);
	    trigger_error('Nie udało się wykonać polecenia: ' . $m['message'], E_USER_ERROR);
	}

	//ZWOLNIJ ZASOBY
	oci_free_statement($stid);

	if($liczSztuki == 1) {
		$_SESSION['S_ILEKOSZYK'] -= 1;
	}

		//PARSOWANIE  
		$stid = oci_parse($connection, $queryProduktDekrementuj);

		//PHP VARIABLE --> ORACLE PLACEHOLDER
		oci_bind_by_name($stid, ":produkt_id", $_POST['buttonproduktidkoszykdec']);
		oci_bind_by_name($stid, ":konto_id", $_SESSION['S_KONTO_ID']);

		//EXECUTE POLECENIE
		$result = oci_execute($stid);
		if (!$result) {
		    $m = oci_error($stid);
		    trigger_error('Nie udało się wykonać polecenia: ' . $m['message'], E_USER_ERROR);
		}

		//ZWOLNIJ ZASOBY
		oci_free_statement($stid);
		oci_close($connection); 
		header('Location: ../koszyk.php');
		exit();
	}


/* ==========		DODAJ ZAMOWIENIE			========== */
if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['zamow'])) {		   
		//PARSOWANIE  
		$stid = oci_parse($connection, $queryStworzZamowienie);

		//PHP VARIABLE --> ORACLE PLACEHOLDER
		oci_bind_by_name($stid, ":konto_id", $_SESSION['S_KONTO_ID']);
		oci_bind_by_name($stid, ":kurier_id", $_POST['skurier']);
		oci_bind_by_name($stid, ":koszt_zamowienia", $_SESSION['S_SUMA']);
		oci_bind_by_name($stid, ":metoda_platnosci", $_POST['splatnosc']);
		oci_bind_by_name($stid, ":dokument_sprzedazy", $_POST['sdokument']);



		//EXECUTE POLECENIE
		$result = oci_execute($stid);
		if (!$result) {
		    $m = oci_error($stid);
		  
		    $_SESSION['error_code'] = $m['code'];
		    header('Location: ../koszyk.php');
		    exit();
		} else {  
	 		$_SESSION['S_ILEKOSZYK'] = 0;
			//ZWOLNIJ ZASOBY
			oci_free_statement($stid);
			oci_close($connection); 
			header('Location: ../zamowienie.php'); //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
			exit();
		}
	}

	oci_close($connection); 
	header('Location: ../sklep.php');
?>