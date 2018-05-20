-- >>>>> DODAWANIE FUNKCJI <<<<<


CREATE OR REPLACE FUNCTION SELECTPRACOWNICY
  RETURN SYS_REFCURSOR 
AS
  MY_CURSOR SYS_REFCURSOR;
  MY_QUERY VARCHAR2(500);
BEGIN

  MY_QUERY := 'SELECT K.KONTO_ID ,K.IMIE, K. NAZWISKO, K.UPRAWNIENIA, P.DATA_ZATRUDNIENIA, P.DATA_ZWOLNIENIA, P.PENSJA, P.PREMIA, A.MIEJSCOWOSC, A.WOJEWODZTWO ,A.KOD_POCZTOWY, A.ULICA, A.NR_DOMU, A.NR_LOKALU, KT.EMAIL, KT.NR_TEL FROM KONTO K, PRACOWNIK P, ADRES A, KONTAKT KT WHERE K.KONTO_ID = P.KONTO_ID AND A.ADRES_ID = K.ADRES_ID AND KT.KONTAKT_ID = K.KONTAKT_ID';

  OPEN MY_CURSOR FOR MY_QUERY;

  RETURN MY_CURSOR;
END SELECTPRACOWNICY;
/

CREATE OR REPLACE FUNCTION SELECTKLIENCI
  RETURN SYS_REFCURSOR 
AS
  MY_CURSOR SYS_REFCURSOR;
  MY_QUERY VARCHAR2(500);
BEGIN

  MY_QUERY := q'[SELECT DISTINCT K.KONTO_ID ,K.IMIE, K. NAZWISKO, K.UPRAWNIENIA, A.MIEJSCOWOSC, A.WOJEWODZTWO ,A.KOD_POCZTOWY, A.ULICA, A.NR_DOMU, A.NR_LOKALU, KT.EMAIL, KT.NR_TEL FROM KONTO K, PRACOWNIK P, ADRES A, KONTAKT KT WHERE A.ADRES_ID = K.ADRES_ID AND K.UPRAWNIENIA = 'klient' AND KT.KONTAKT_ID = K.KONTAKT_ID]';

  OPEN MY_CURSOR FOR MY_QUERY;

  RETURN MY_CURSOR;
END SELECTKLIENCI;
/
CREATE OR REPLACE FUNCTION SELECTKLIENCIKONTOID(IDKONTO INT)
  RETURN SYS_REFCURSOR 
AS
  MY_CURSOR SYS_REFCURSOR;
  MY_QUERY VARCHAR2(500);
BEGIN

  MY_QUERY := q'[SELECT DISTINCT K.KONTO_ID ,K.IMIE, K. NAZWISKO, K.UPRAWNIENIA, A.MIEJSCOWOSC, A.WOJEWODZTWO ,A.KOD_POCZTOWY, A.ULICA, A.NR_DOMU, A.NR_LOKALU, KT.EMAIL, KT.NR_TEL FROM KONTO K, PRACOWNIK P, ADRES A, KONTAKT KT WHERE A.ADRES_ID = K.ADRES_ID AND K.UPRAWNIENIA = 'klient' AND KT.KONTAKT_ID = K.KONTAKT_ID AND K.KONTO_ID=]' || IDKONTO;

  OPEN MY_CURSOR FOR MY_QUERY;

  RETURN MY_CURSOR;
END SELECTKLIENCIKONTOID;
/

create or replace FUNCTION SELECTPRACOWNIKKONTOID(IDKONTO INT)
  RETURN SYS_REFCURSOR 
AS
  MY_CURSOR SYS_REFCURSOR;
  MY_QUERY VARCHAR2(500);
BEGIN

  MY_QUERY := q'[SELECT DISTINCT K.KONTO_ID ,K.IMIE, K. NAZWISKO, K.UPRAWNIENIA, A.MIEJSCOWOSC, A.WOJEWODZTWO ,A.KOD_POCZTOWY, A.ULICA, A.NR_DOMU, A.NR_LOKALU, KT.EMAIL, KT.NR_TEL FROM KONTO K, PRACOWNIK P, ADRES A, KONTAKT KT WHERE A.ADRES_ID = K.ADRES_ID AND K.UPRAWNIENIA = 'pracownik' AND KT.KONTAKT_ID = K.KONTAKT_ID AND K.KONTO_ID=]' || IDKONTO;

  OPEN MY_CURSOR FOR MY_QUERY;

  RETURN MY_CURSOR;
END SELECTPRACOWNIKKONTOID;
/

create or replace function COUNTRW(TABLENAME VARCHAR2, COLUMNNAME VARCHAR2, CONDITION VARCHAR2) 
   return number
AS
   row_count number;
   MY_QUERY VARCHAR2(500);
BEGIN
    SELECT COUNT(KATEGORIA_ID) into row_count FROM KATEGORIA;

    MY_QUERY := 'SELECT COUNT(' || COLUMNNAME || ') FROM ' || TABLENAME || ' WHERE ' || CONDITION; 
    EXECUTE IMMEDIATE MY_QUERY INTO row_count;    

    return row_count;
END COUNTRW;
/

-- >>>>> DODAWANIE PROCEDUR <<<<<



/* ----------------------------- */
/* ---------- INSERTY ---------- */
/* ----------------------------- */

/* --- ZAMOWIENIE --- */
/* tworzy nowe zamowienie >> dodaje produkty z KOSZYK do ZAMOWIONE_PRODUKTY >> usuwa KOSZYK 

BEGIN
STWORZ_ZAMOWIENIE(1, 1, '1', '11/11/11', '1');
END;

*/
CREATE OR REPLACE PROCEDURE STWORZ_ZAMOWIENIE(KON_ID IN INT, KOSZT_ZAM IN FLOAT, METODA_PLAT IN VARCHAR2, DATA_WYS IN VARCHAR2, DOKUMENT_SPRZ IN VARCHAR2)
AS
ID_ZAM NUMBER;
BEGIN

  
  INSERT INTO ZAMOWIENIE (ZAMOWIENIE_ID, KONTO_ID, KOSZT_ZAMOWIENIA, METODA_PLATNOSCI, DATA_WYSYLKI, DOKUMENT_SPRZEDAZY)
  VALUES (ID_ZAM, KON_ID, KOSZT_ZAM, METODA_PLAT, TO_DATE(DATA_WYS, 'DD/MM/YY'), DOKUMENT_SPRZ);

  ID_ZAM:= ZAMOWIENIE_SEQ.CURRVAL;

  INSERT INTO ZAMOWIONE_PRODUKTY (PRODUKT_ID, ZAMOWIENIE_ID, ILOSC_SZTUK)
  SELECT PRODUKT_ID, ID_ZAM, ILOSC_SZTUK FROM KOSZYK
  WHERE
  KONTO_ID = KON_ID;


  DELETE FROM KOSZYK 
  WHERE
  KONTO_ID = KON_ID;

END;
/


/* --- PRACOWNIK --- */
/* Trigger zmienia automatycznie uprawnienia */
CREATE OR REPLACE PROCEDURE INSERTPRACOWNIK
       ( 
        KON_ID IN INT,
        PEN IN FLOAT, 
        PREM IN FLOAT                 
       )
AS 
BEGIN 
    INSERT INTO PRACOWNIK(KONTO_ID, PENSJA, PREMIA) VALUES (KON_ID, PEN, PREM);

END INSERTPRACOWNIK;
/
/* --- ADRES --- */
/* tworzy nowy adres i dodaje go do konta(KON_ID)*/
CREATE OR REPLACE PROCEDURE INSERTADRES
       ( 
       	KON_ID IN INT,
        MIEJSC IN VARCHAR2,
 		WOJ IN VARCHAR2,
 		KOD_POCZT IN VARCHAR2,
 	    UL IN VARCHAR2,
 		NR_DOM IN INT,
 		NR_LOK IN INT                
       )
AS 
	ADR_ID NUMBER;
BEGIN 
	
    INSERT INTO ADRES(ADRES_ID, MIEJSCOWOSC, WOJEWODZTWO, KOD_POCZTOWY, ULICA, NR_DOMU, NR_LOKALU) VALUES (ADR_ID, MIEJSC, WOJ, KOD_POCZT, UL, NR_DOM, NR_LOK);
      
    ADR_ID:= ADRES_SEQ.CURRVAL;

    UPDATE KONTO
	SET ADRES_ID = ADR_ID
	WHERE KONTO_ID = KON_ID;

END INSERTADRES;
/
/* --- KONTAKT --- */
/* tworzy nowy kontakt i dodaje go do konta(KON_ID) */
CREATE OR REPLACE PROCEDURE INSERTKONTAKT
       (
        KON_ID IN INT, 
  		TEL IN VARCHAR2,
  		FAX_1 IN VARCHAR2,
 		MAIL IN VARCHAR2,
 		WWW_1 IN VARCHAR2            
       )
AS 
	KONT_ID NUMBER;
BEGIN 
	
    INSERT INTO KONTAKT(KONTAKT_ID, NR_TEL, FAX, EMAIL, WWW) VALUES (KONT_ID, TEL, FAX_1, MAIL, WWW_1);

    KONT_ID:= KONTAKT_SEQ.CURRVAL;  

    UPDATE KONTO
	SET KONTAKT_ID = KONT_ID
	WHERE KONTO_ID = KON_ID;

END INSERTKONTAKT;
/
/* --- KURIER --- */
/* dodaje kuriera do zamowienia 
mozliwie do zmiany, poniewaz nie sprawdza ZAMOWIENIE_ID, 
a więc w przypadku gdy konto ma więcej zamówień wszystkie zamowienia zostaną z update'owane o to KURIER_ID
ale poki co zostawiam tak jak jest*/
CREATE OR REPLACE PROCEDURE INSERTKURIER
       (
        KON_ID IN INT, 
  		NAZ_FIRMY IN VARCHAR2            
       )
AS 
	KUR_ID NUMBER;
BEGIN 
	
    INSERT INTO KURIER(KURIER_ID, NAZWA_FIRMY) VALUES (KUR_ID, NAZ_FIRMY);
    
    KUR_ID:= KURIER_SEQ.CURRVAL;  
    UPDATE ZAMOWIENIE
	SET KURIER_ID = KUR_ID
	WHERE KONTO_ID = KON_ID;

END INSERTKURIER;
/

/* ---------------------------- */
/* ---------- DELETE ---------- */
/* ---------------------------- */
/* nie wiem czy to usuwanie oplaca sie w procedurze
ale przetestowane że działa, wystarczyło do constraintow dac ON DELETE CASCADE */

CREATE OR REPLACE PROCEDURE DELETEKONTO
(KON_ID IN INT)
AS 
BEGIN 
	DELETE FROM KONTO
	WHERE KONTO_ID = KON_ID;
END DELETEKONTO;

/

/* fajna procedura na dodawanie do bazy wielu rekordow, liczby ustawia randomowo z zakresu jaki wybierzemy
mysle że mozemy to wykorzystac  */

/* wywołujemy ją, w taki sposób w oraclu:

BEGIN
DODAWANIE_PRODUKTOW;
END;

*/
CREATE OR REPLACE PROCEDURE DODAWANIE_PRODUKTOW AS 

BEGIN

  FOR i IN 1..1000 LOOP

    INSERT INTO PRODUKT (DOSTAWCA_ID,KATEGORIA_ID,PRODUCENT,NUMER_KATALOGOWY,MODEL,CENA,SZTUK_NA_MAGAZYNIE,OPIS) 
    VALUES (dbms_random.value(1,4),dbms_random.value(1,3),'JAXON','GDFGDF-665','TERRA',dbms_random.value(1,1044),dbms_random.value(1,130),'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla elementum turpis risus, eu hendrerit odio lobortis aliquet.');
    
  END LOOP;

END DODAWANIE_PRODUKTOW;
