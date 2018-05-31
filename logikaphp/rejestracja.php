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

	$email = $_POST['email'];
	$woj = $_POST['woj'];
	$miejsc = $_POST['miejsc'];
	$poczt = $_POST['poczt'];
	$ulica = $_POST['ulica'];
	$nr_domu = $_POST['nr_domu'];
	$nr_tel = $_POST['nr_tel'];


	$_SESSION['login'] = $login;
	$_SESSION['imie'] = $imie;
	$_SESSION['nazwisko'] = $nazwisko;

	$_SESSION['email'] = $email;
	$_SESSION['woj'] = $woj;
	$_SESSION['miejsc'] = $miejsc;
	$_SESSION['poczt'] = $poczt;
	$_SESSION['ulica'] = $ulica;
	$_SESSION['nr_domu'] = $nr_domu;
	$_SESSION['nr_tel'] = $nr_tel;


	//Polaczenie z baza
	$conn = oci_connect($username, $password, $database);
	if (!$conn) {
		$m = oci_error();
		trigger_error('Nie udało się połaczyć z baza: '. $m['message'], E_USER_ERROR);
	}

	




$sql = 'BEGIN REJESTRACJA(:login, :haslo, :haslo2, :imie, :nazwisko, :email, :woj, :miejsc, :poczt, :ulica, :nr_domu, :nr_tel); END;';

$stid = oci_parse($conn,$sql);

oci_bind_by_name($stid,':login',$login);
oci_bind_by_name($stid,':haslo',$haslo);
oci_bind_by_name($stid,':haslo2',$haslo2);
oci_bind_by_name($stid,':imie',$imie);
oci_bind_by_name($stid,':nazwisko',$nazwisko);

oci_bind_by_name($stid,':email',$email);
oci_bind_by_name($stid,':woj',$woj);
oci_bind_by_name($stid,':miejsc',$miejsc);
oci_bind_by_name($stid,':poczt',$poczt);
oci_bind_by_name($stid,':ulica',$ulica);
oci_bind_by_name($stid,':nr_domu',$nr_domu);
oci_bind_by_name($stid,':nr_tel',$nr_tel);


$r = oci_execute($stid);


if (!$r) {
	$e = oci_error($stid); 
    $_SESSION['error_code'] = $e['code'];
    $_SESSION['msg'] = $e['message'];
}else{
	header('Location: ../zarejestrowano.php');

}

oci_close($conn); 
	
?>