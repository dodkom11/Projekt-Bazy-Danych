<?php
$conn = oci_connect('en1ceka', 'qwerty', 'localhost/XE');
   if (!$conn) {
      $e = oci_error();
      trigger_error(htmlentities($e('message'), ENT_QUOTES), E_USER_ERROR);
   }

$id_konto = '1';
$koszt_zam = '132'; 
$metoda_plat = 'gotowka';
$data_wys = '11/12/12';
$dok_sprze = 'asd';

$sql = 'BEGIN STWORZ_ZAMOWIENIE(:id_konto, :koszt_zam, :metoda_plat, :data_wys, :dok_sprze); END;';

$stmt = oci_parse($conn,$sql);

oci_bind_by_name($stmt,':id_konto',$id_konto);
oci_bind_by_name($stmt,':koszt_zam',$koszt_zam);
oci_bind_by_name($stmt,':metoda_plat',$metoda_plat);
oci_bind_by_name($stmt,':data_wys',$data_wys);
oci_bind_by_name($stmt,':dok_sprze',$dok_sprze);

oci_execute($stmt);

/*

CREATE OR REPLACE PROCEDURE STWORZ_ZAMOWIENIE(KON_ID IN INT, KOSZT_ZAM IN FLOAT, METODA_PLAT IN VARCHAR2, DATA_WYS IN VARCHAR2, DOKUMENT_SPRZ IN VARCHAR2)
AS
ID_ZAM NUMBER;
BEGIN

  ID_ZAM:= ZAMOWIENIE_SEQ.NEXTVAL;
  INSERT INTO ZAMOWIENIE (ZAMOWIENIE_ID, KONTO_ID, KOSZT_ZAMOWIENIA, METODA_PLATNOSCI, DATA_WYSYLKI, DOKUMENT_SPRZEDAZY)
  VALUES (ID_ZAM, KON_ID, KOSZT_ZAM, METODA_PLAT, TO_DATE(DATA_WYS, 'DD/MM/YY'), DOKUMENT_SPRZ);


  INSERT INTO ZAMOWIONE_PRODUKTY (PRODUKT_ID, ZAMOWIENIE_ID, ILOSC_SZTUK)
  SELECT PRODUKT_ID, ID_ZAM, ILOSC_SZTUK FROM KOSZYK
  WHERE
  KONTO_ID = KON_ID;


  DELETE FROM KOSZYK 
  WHERE
  KONTO_ID = KON_ID;

END;
*/   
?>

