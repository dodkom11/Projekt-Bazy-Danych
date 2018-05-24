<?php

session_start();
header('Location: ../reg.php');
    require_once "connect.php"; 
	ini_set('display_errors', 'Off');
	
	
	//Nasze zmienne
	$login = $_POST['login'];
	$haslo = $_POST['password'];
	$haslo2 = $_POST['password2'];	
	$imie = $_POST['imie'];
	$nazwisko = $_POST['nazwisko'];


	$_SESSION['login'] = $login;
	$_SESSION['imie'] = $imie;
	$_SESSION['nazwisko'] = $nazwisko;

	//Polaczenie z baza
	$conn = oci_connect($username, $password, $database);
	if (!$conn) {
		$m = oci_error();
		trigger_error('Nie udało się połaczyć z baza: '. $m['message'], E_USER_ERROR);
	}

	




$sql = 'BEGIN REJESTRACJA(:login, :haslo, :haslo2, :imie, :nazwisko); END;';

$stid = oci_parse($conn,$sql);

oci_bind_by_name($stid,':login',$login);
oci_bind_by_name($stid,':haslo',$haslo);
oci_bind_by_name($stid,':haslo2',$haslo2);
oci_bind_by_name($stid,':imie',$imie);
oci_bind_by_name($stid,':nazwisko',$nazwisko);


$r = oci_execute($stid);


if (!$r) {
	$e = oci_error($stid);  
    $_SESSION['error_code'] = $e['code'];	
}else{
	$_SESSION['asd'] = $login;
	header('Location: ../zarejestrowano.php');

}

oci_close($conn); 
	
?>