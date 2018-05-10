<?php
   $conn = oci_connect('TEST', 'pass', 'localhost/XE');
   if (!$conn) {
      $e = oci_error();
      trigger_error(htmlentities($e('message'), ENT_QUOTES), E_USER_ERROR);
   }

   $tablename = 'KATEGORIA';
   $columnname = '*';

   $query = "begin 
               :cursor := LAST6PRODUCTS;
             end;";

   $stid = oci_parse($conn, $query);

   $p_cursor = oci_new_cursor($conn);

   oci_bind_by_name($stid, ":cursor", $p_cursor, -1, OCI_B_CURSOR);

   oci_execute($stid);
   oci_execute($p_cursor, OCI_DEFAULT);

   while (($row = oci_fetch_array($p_cursor, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
      echo $row['CTIME'] . " -> ". $row['PRODUCENT']. "<br />\n";	 
   }

   
/*
	===== FUNKCJA PL/SQL =======

CREATE OR REPLACE FUNCTION LAST6PRODUCTS
  RETURN SYS_REFCURSOR 
AS
  MY_CURSOR SYS_REFCURSOR;
  MY_QUERY VARCHAR2(500);
BEGIN

  MY_QUERY := q'[SELECT * FROM ( SELECT TO_CHAR(DATA_DODANIA, 'DD-MON-YYYY HH24:MI') AS CTIME, PRODUKT.* FROM PRODUKT ORDER BY DATA_DODANIA DESC ) WHERE ROWNUM <= 6]';

  OPEN MY_CURSOR FOR MY_QUERY;

  RETURN MY_CURSOR;
END LAST6PRODUCTS;

*/   
?>