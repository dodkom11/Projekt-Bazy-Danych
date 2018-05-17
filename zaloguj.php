<?php
	/*	>>> INFORMACJE - zaloguj.php
			> Odpowiada za proces logowania
			> Dodać IF-a || user || worker || admin		
	*/
	session_start(); //tworzenie sesji PHP
	
	// Wróć do login.php, jeżeli nie nastąpiła próba logowania - zmienne nie ustawione
	if ((!isset($_POST['login'])) || (!isset($_POST['haslo'])))
	{
		header('Location: login.php');
		exit();
	}

	//Wymagane dołącznie pliku connect.php, który zawiera dane logowania do bazy Oracle
	require_once "connect.php"; 
	
	//Nasze zmienne
	$login = $_POST['login'];
	$haslo = $_POST['haslo'];	
	$query = "SELECT * FROM KONTO WHERE LOGIN=:login AND HASLO=:haslo";	

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
	oci_bind_by_name($stid, ':login', $login); //Powiązuje zmienną PHP do Oracle placeholder
	oci_bind_by_name($stid, ':haslo', $haslo);	

	//Wykonaj polecenie SQL
	$result = oci_execute($stid);	
	if (!$result) {
		$m = oci_error($stid);
		trigger_error('Nie udało się wykonać polecenia: '. $m['message'], E_USER_ERROR);
	}
	else {	
	    //Jezeli istnieje uzytkownik o zadanym loginie i password oraz wynik nie jest pustym wierszem					
		if ($list=oci_fetch($stid) && empty($list) ) {			
			$_SESSION['zalogowany'] = true;	

			$_SESSION['S_KONTO_ID'] =  oci_result($stid, 'KONTO_ID');			
			$_SESSION['S_KARTA_ID'] =  oci_result($stid, 'KARTA_ID');
			$_SESSION['S_ADRES_ID'] =  oci_result($stid, 'ADRES_ID');
			$_SESSION['S_KONTAKT_ID'] =  oci_result($stid, 'KONTAKT_ID');
			$_SESSION['S_LOGIN'] =  oci_result($stid, 'S_LOGIN');
			$_SESSION['S_UPRAWNIENIA'] =  oci_result($stid, 'UPRAWNIENIA');
			$_SESSION['S_IMIE'] =  oci_result($stid, 'IMIE');
			$_SESSION['S_NAZWISKO'] =  oci_result($stid, 'NAZWISKO');

    		unset($_SESSION['blad_log']); //usuń z sesji zmienna blad skoro udalo nam sie zalogowac
    		oci_free_statement($stid); //wyczysc z pamieci RAM serwera zwrocone z bazy rezultaty zapytania			
    		header('Location: sklep.php');
		}
		else {
			$_SESSION['blad_log'] = '<span>Nieprawidlowy login lub haslo!</span>';
			header('Location: login.php');
		}
	}	
	oci_close($connection); 

	/* OBRAZEK PHP Z BAZY
			$query2 = "SELECT ZDJECIE FROM PRODUKT WHERE PRODUKT_ID=1";
			$stidx = oci_parse($connection, $query2);
			$res = oci_execute($stidx);
			$row = oci_fetch_array($stidx, OCI_ASSOC+OCI_RETURN_NULLS);
			if (!$row) {
			    header('Status: 404 Not Found');
			} else {
		 	   $img = $row['ZDJECIE']->load();			 	   
		 	   echo '<div><img src="data:image/jpeg;base64,'.base64_encode($img).'" /></div>';
			}
			oci_free_statement($stidx);
			oci_close($connection); 
	*/
?>