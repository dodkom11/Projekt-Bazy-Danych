<?php

function abc($kontoid){

   require_once "../connect.php"; 
 //Polaczenie z baza
    $connection = oci_connect($username, $password, $database);
    if (!$connection) {
        $m = oci_error();
        trigger_error('Nie udało się połaczyć z baza: '. $m['message'], E_USER_ERROR);
    }
    $s = oci_parse($connection, "DELETE FROM KONTO WHERE KONTO_ID=:rekordid");
    oci_bind_by_name($s, ":rekordid", $kontoid);
    oci_execute($s);
}
 ?>