<?php
   $conn = oci_connect('en1ceka', 'qwerty', 'localhost/XE');
   if (!$conn) {
      $e = oci_error();
      trigger_error(htmlentities($e('message'), ENT_QUOTES), E_USER_ERROR);
   }

   $tablename = 'KATEGORIA';
   $columnname = '*';

   $query = "begin 
               :cursor := selectFromTable(:tabl, :colm);
             end;";

   $stid = oci_parse($conn, $query);

   $p_cursor = oci_new_cursor($conn);

   oci_bind_by_name($stid, ":tabl", $tablename);
   oci_bind_by_name($stid, ":colm", $columnname);

   oci_bind_by_name($stid, ":cursor", $p_cursor, -1, OCI_B_CURSOR);

   oci_execute($stid);
   oci_execute($p_cursor, OCI_DEFAULT);

   while (($row = oci_fetch_array($p_cursor, OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
      echo $row['KATEGORIA_ID'] . " -> ". $row['KATEGORIA_NAZWA']. " -> ". $row['OPIS']. "<br />\n";
	 
   }

   
/*
	===== FUNKCJA PL/SQL =======
	
	-- Notice the cursor is not closed in the procedure. It is up to the calling code to manage the cursor once it has been opened.	
	
CREATE OR REPLACE FUNCTION SELECTFROMTABLE(TABLENAME VARCHAR2, COLUMNNAME VARCHAR2) 
  RETURN SYS_REFCURSOR 
AS
  MY_CURSOR SYS_REFCURSOR;
  MY_QUERY VARCHAR2(500);
BEGIN

  MY_QUERY := 'SELECT ' || COLUMNNAME || ' FROM ' || TABLENAME;

  OPEN MY_CURSOR FOR MY_QUERY;

  RETURN MY_CURSOR;
END SELECTFROMTABLE;

*/   
?>

