<?php
   $conn = oci_connect('TEST', 'pass', 'localhost/XE');
   if (!$conn) {
      $e = oci_error();
      trigger_error(htmlentities($e('message'), ENT_QUOTES), E_USER_ERROR);
   }
   	
   $query2 = "begin 
				:bv := COUNTRW(:tabl, :colm);    
			   end;";

   $tablename = 'KATEGORIA';
   $columnname = 'KATEGORIA_ID';
   
   $s = oci_parse($conn, $query2);
   
   oci_bind_by_name($s, ":tabl", $tablename);
   oci_bind_by_name($s, ":colm", $columnname);
   oci_bind_by_name($s, ":bv", $v, 10);
   oci_execute($s);
   echo $v, "<br>\n";  
   
   


   
/*
	===== FUNKCJA PL/SQL =======

create or replace function COUNTRW(TABLENAME VARCHAR2, COLUMNNAME VARCHAR2) 
   return number
AS
   row_count number;
   MY_QUERY VARCHAR2(500);
BEGIN
    SELECT COUNT(KATEGORIA_ID) into row_count FROM KATEGORIA;
    
    MY_QUERY := 'SELECT COUNT(' || COLUMNNAME || ') FROM ' || TABLENAME; 
    EXECUTE IMMEDIATE MY_QUERY INTO row_count;    
     
    return row_count;
END COUNTRW;
*/   
?>