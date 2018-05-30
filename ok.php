<?php
  require_once "logikaphp/connect.php"; 

 $image = file_get_contents($_FILES['lob_upload']['tmp_name']);
 echo "proszki";
     // $lob_upload contiene el nombre temporal del fichero subido

     // véase también la sección de características sobre la subida de ficheros,
     // si le gustaría usar subidas seguras

     $connection = oci_connect($username, $password, $database);

    $sql = "INSERT INTO PRODUKT (PRODUCENT, NUMER_KATALOGOWY, CENA, ZDJECIE, SZTUK_NA_MAGAZYNIE) VALUES(1, 'FDSFDSFF', 22, 44, empty_blob()) RETURNING ZDJECIE INTO :image";

    $result = oci_parse($connection, $sql);
    $blob = oci_new_descriptor($connection, OCI_D_LOB);
    oci_bind_by_name($result, ":image", $blob, -1, OCI_B_BLOB);
    oci_execute($result, OCI_DEFAULT) or die ("Unable to execute query");

    if(!$blob->save($image)) {
        oci_rollback($connection);
    }
    else {
        oci_commit($connection);
    }

    oci_free_statement($result);
    $blob->free();
  
?>