



-- #########################     TWORZENIE TABEL       #########################

CREATE TABLE KATEGORIA (
  KATEGORIA_ID INT,
  KATEGORIA_NAZWA VARCHAR2(45 char) NOT NULL,
  OPIS VARCHAR2(300 char),
  PRIMARY KEY (KATEGORIA_ID)
);
 
CREATE TABLE KURIER (
  KURIER_ID INT,
  NAZWA_FIRMY VARCHAR2(45 char) NOT NULL,
  PRIMARY KEY (KURIER_ID)
);
 
CREATE TABLE KARTA (
  KARTA_ID INT,
  NUMER_KARTY INT NOT NULL,
  DATA_WAZNOSCI VARCHAR2(5 char) NOT NULL,
  CVV INT NOT NULL,
  IMIE VARCHAR2(45 char) NOT NULL,
  NAZWISKO VARCHAR2(45 char) NOT NULL,
  PRIMARY KEY (KARTA_ID)
);
 
CREATE TABLE KONTO (
  KONTO_ID INT,
  KARTA_ID INT,
  ADRES_ID INT,
  KONTAKT_ID INT,
  LOGIN VARCHAR2(45 char) NOT NULL,
  HASLO VARCHAR2(45 char) NOT NULL,
  UPRAWNIENIA VARCHAR2(45 char) DEFAULT 'klient' NOT NULL,
  IMIE VARCHAR2(45 char) NOT NULL,
  NAZWISKO VARCHAR2(45 char) NOT NULL,
  KONTO_AKTYWNE NUMBER(1) DEFAULT 1 NOT NULL,
  PRIMARY KEY (KONTO_ID)
  
);
 
CREATE TABLE PRACOWNIK (
  PRACOWNIK_ID INT,
  KONTO_ID INT,
  DATA_ZATRUDNIENIA DATE,
  DATA_ZWOLNIENIA DATE,
  PENSJA FLOAT,
  PREMIA FLOAT,
  PRIMARY KEY (PRACOWNIK_ID)
);
  
CREATE TABLE ADRES (
  ADRES_ID INT,
  MIEJSCOWOSC VARCHAR2(45 char),
  WOJEWODZTWO VARCHAR2(45 char),
  KOD_POCZTOWY VARCHAR2(45 char),
  ULICA VARCHAR2(45 char),
  NR_DOMU INT,
  NR_LOKALU INT,
  PRIMARY KEY (ADRES_ID)
);
  
CREATE TABLE KONTAKT (
  KONTAKT_ID INT,
  NR_TEL VARCHAR2(45 char),
  FAX VARCHAR2(45 char),
  EMAIL VARCHAR2(45 char),
  WWW VARCHAR2(45 char),
  PRIMARY KEY (KONTAKT_ID)
);
 
CREATE TABLE DOSTAWCA (
  DOSTAWCA_ID INT,
  ADRES_ID INT,
  KONTAKT_ID INT,
  NAZWA_FIRMY VARCHAR2(45 char),
  PRIMARY KEY (DOSTAWCA_ID)
);
 
CREATE TABLE PRODUKT (
  PRODUKT_ID INT,
  DOSTAWCA_ID INT,
  KATEGORIA_ID INT,
  PRODUCENT VARCHAR2(45 char) NOT NULL,
  NUMER_KATALOGOWY VARCHAR2(45 char) NOT NULL,
  MODEL VARCHAR2(45 char),
  CENA FLOAT NOT NULL,
  SZTUK_NA_MAGAZYNIE INT NOT NULL,
  OPIS VARCHAR2(300 char),
  ZDJECIE BLOB,
  DATA_DODANIA TIMESTAMP NOT NULL,
  PRIMARY KEY (PRODUKT_ID)
);
 
CREATE TABLE KOSZYK (
  PRODUKT_ID INT,
  KONTO_ID INT,
  ILOSC_SZTUK INT NOT NULL
);

CREATE TABLE ZAMOWIONE_PRODUKTY (
  PRODUKT_ID INT,
  ZAMOWIENIE_ID INT,
  ILOSC_SZTUK INT NOT NULL
);
 
CREATE TABLE ZAMOWIENIE (
  ZAMOWIENIE_ID INT,
  KONTO_ID INT,
  KURIER_ID INT,
  DATA_PRZYJECIA_ZAMOWIENIA DATE,
  DATA_REALIZACJI_ZAMOWIENIA DATE,
  ZAMOWIENIE_ZAAKCEPTOWANE NUMBER(1) DEFAULT 0 NOT NULL,
  ZAPLACONO NUMBER(1) DEFAULT 0 NOT NULL,
  ZREALIZOWANO NUMBER(1) DEFAULT 0 NOT NULL,
  KOSZT_ZAMOWIENIA FLOAT NOT NULL,
  METODA_PLATNOSCI VARCHAR2(45 char) NOT NULL,
  DOKUMENT_SPRZEDAZY VARCHAR2(45 char) NOT NULL,
  PRIMARY KEY (ZAMOWIENIE_ID)
);




-- #########################         POWIĄZANIA TABEL       #########################

ALTER TABLE KONTO
ADD CONSTRAINT KONTO_KARTA_FK
  FOREIGN KEY (KARTA_ID)
  REFERENCES KARTA(KARTA_ID)
ADD CONSTRAINT KONTO_KONTAKT_FK
  FOREIGN KEY(KONTAKT_ID) 
  REFERENCES KONTAKT(KONTAKT_ID)
ADD CONSTRAINT KONTO_ADRES_FK
  FOREIGN KEY(ADRES_ID) 
  REFERENCES ADRES(ADRES_ID);

ALTER TABLE PRACOWNIK
ADD CONSTRAINT PRACOWNIK_KONTO_FK
  FOREIGN KEY (KONTO_ID)
  REFERENCES KONTO(KONTO_ID)
  ON DELETE CASCADE;

ALTER TABLE DOSTAWCA
ADD CONSTRAINT DOSTAWCA_ADRES_FK 
  FOREIGN KEY(ADRES_ID) 
  REFERENCES ADRES(ADRES_ID)
ADD CONSTRAINT DOSTAWCA_KONTAKT_FK 
  FOREIGN KEY(KONTAKT_ID) 
  REFERENCES KONTAKT(KONTAKT_ID);

ALTER TABLE PRODUKT
ADD CONSTRAINT PRODUKT_DOSTAWCA_FK 
  FOREIGN KEY(DOSTAWCA_ID) 
  REFERENCES DOSTAWCA(DOSTAWCA_ID)
ADD CONSTRAINT KATEGORIA_KONTAKT_FK 
  FOREIGN KEY(KATEGORIA_ID) 
  REFERENCES KATEGORIA(KATEGORIA_ID);

ALTER TABLE KOSZYK
ADD CONSTRAINT KOSZYK_PRODUKT_FK 
  FOREIGN KEY(PRODUKT_ID) 
  REFERENCES PRODUKT(PRODUKT_ID)
  ON DELETE CASCADE
ADD CONSTRAINT KOSZYK_KONTO_FK 
  FOREIGN KEY(KONTO_ID) 
  REFERENCES KONTO(KONTO_ID)
  ON DELETE CASCADE;

ALTER TABLE ZAMOWIONE_PRODUKTY
ADD CONSTRAINT ZAMOWIONE_PR_PRODUKT_FK 
  FOREIGN KEY(PRODUKT_ID) 
  REFERENCES PRODUKT(PRODUKT_ID)
ADD CONSTRAINT ZAMOWIONE_PR_ZAMOWIENIE_FK 
  FOREIGN KEY(ZAMOWIENIE_ID) 
  REFERENCES ZAMOWIENIE(ZAMOWIENIE_ID);

ALTER TABLE ZAMOWIENIE
ADD CONSTRAINT ZAMOWIENIE_KURIER_FK
  FOREIGN KEY(KURIER_ID) 
  REFERENCES KURIER(KURIER_ID)
ADD CONSTRAINT ZAMOWIENIE_KONTO_FK
  FOREIGN KEY(KONTO_ID)
  REFERENCES KONTO(KONTO_ID)
  ON DELETE CASCADE;

-- #########################        TRIGGERY       #########################

/* AUTO INCREMENT */
CREATE SEQUENCE KATEGORIA_SEQ;
CREATE OR REPLACE TRIGGER KATEGORIA_TRIG BEFORE INSERT ON KATEGORIA FOR EACH ROW
BEGIN
    :NEW.KATEGORIA_ID := KATEGORIA_SEQ.NEXTVAL;
END;
/

CREATE SEQUENCE KURIER_SEQ;
CREATE OR REPLACE TRIGGER KURIER_TRIG BEFORE INSERT ON KURIER FOR EACH ROW
BEGIN
    :NEW.KURIER_ID := KURIER_SEQ.NEXTVAL;
END;
/

CREATE SEQUENCE KARTA_SEQ;
CREATE OR REPLACE TRIGGER KARTA_TRIG BEFORE INSERT ON KARTA FOR EACH ROW
BEGIN
    :NEW.KARTA_ID := KARTA_SEQ.NEXTVAL;
END;
/

CREATE SEQUENCE KONTO_SEQ;
CREATE OR REPLACE TRIGGER KONTO_TRIG BEFORE INSERT ON KONTO FOR EACH ROW
BEGIN
    :NEW.KONTO_ID := KONTO_SEQ.NEXTVAL;
END;
/

CREATE SEQUENCE PRACOWNIK_SEQ;
CREATE OR REPLACE TRIGGER PRACOWNIK_TRIG BEFORE INSERT ON PRACOWNIK FOR EACH ROW
BEGIN
    :NEW.PRACOWNIK_ID := PRACOWNIK_SEQ.NEXTVAL;
END;
/

CREATE SEQUENCE ADRES_SEQ;
CREATE OR REPLACE TRIGGER ADRES_TRIG BEFORE INSERT ON ADRES FOR EACH ROW
BEGIN
    :NEW.ADRES_ID := ADRES_SEQ.NEXTVAL;
END;
/

CREATE SEQUENCE KONTAKT_SEQ;
CREATE OR REPLACE TRIGGER KONTAKT_TRIG BEFORE INSERT ON KONTAKT FOR EACH ROW
BEGIN
    :NEW.KONTAKT_ID := KONTAKT_SEQ.NEXTVAL;
END; 
/

CREATE SEQUENCE DOSTAWCA_SEQ;
CREATE OR REPLACE TRIGGER DOSTAWCA_TRIG BEFORE INSERT ON DOSTAWCA FOR EACH ROW
BEGIN
    :NEW.DOSTAWCA_ID := DOSTAWCA_SEQ.NEXTVAL;
END;
/

CREATE SEQUENCE PRODUKT_SEQ;
CREATE OR REPLACE TRIGGER  PRODUKT_TRIG BEFORE INSERT ON PRODUKT FOR EACH ROW
BEGIN
    :NEW.PRODUKT_ID := PRODUKT_SEQ.NEXTVAL;
END;
/

CREATE SEQUENCE ZAMOWIENIE_SEQ;
CREATE OR REPLACE TRIGGER ZAMOWIENIE_TRIG BEFORE INSERT ON ZAMOWIENIE FOR EACH ROW
BEGIN
    :NEW.ZAMOWIENIE_ID := ZAMOWIENIE_SEQ.NEXTVAL;
END; 
/

/* DATA DODANIA PRODUKTU */
CREATE OR REPLACE TRIGGER PRODUKT_TIMESTAMP_TRIG BEFORE INSERT ON PRODUKT FOR EACH ROW
BEGIN
	:NEW.DATA_DODANIA := SYSDATE;
END;
/

/* DODAJ PRACOWNIKA */
CREATE OR REPLACE TRIGGER DODAJPRACOWNIKA_TRIG AFTER INSERT ON PRACOWNIK FOR EACH ROW
BEGIN
  UPDATE KONTO SET KONTO.UPRAWNIENIA = 'pracownik' WHERE KONTO.KONTO_ID = :NEW.KONTO_ID;
END;
/

/* DATA ZATRUDNIENIA */
CREATE OR REPLACE TRIGGER PRACOWNIK_TIMESTAMP_TRIG BEFORE INSERT ON PRACOWNIK FOR EACH ROW
BEGIN
  :NEW.DATA_ZATRUDNIENIA := SYSDATE;
END;
/

/* DATA PRZYJECIA ZAMOWIENAIA = DATA ZLOZENIA ZAMOWIENIA */
CREATE OR REPLACE TRIGGER ZAMOWIENIE_TIMESTAMP_TRIG BEFORE INSERT ON ZAMOWIENIE FOR EACH ROW
BEGIN
    :NEW.DATA_PRZYJECIA_ZAMOWIENIA := SYSDATE;
END;
/

CREATE OR REPLACE TRIGGER ZAMOWIENIEAFTER_TRIG AFTER INSERT ON ZAMOWIONE_PRODUKTY FOR EACH ROW
BEGIN
  UPDATE PRODUKT
    SET SZTUK_NA_MAGAZYNIE = SZTUK_NA_MAGAZYNIE - :NEW.ILOSC_SZTUK
    WHERE PRODUKT_ID = :NEW.PRODUKT_ID;
END;
/




-- #########################       DODAWANIE ROKORDOW       #########################


/* KATEGORIA DATA*/
---------------------
INSERT INTO KATEGORIA (KATEGORIA_ID, KATEGORIA_NAZWA, OPIS)
VALUES ('1', 'AKCESORIA WĘDKARSKIE', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla elementum turpis risus, eu hendrerit odio lobortis aliquet.');  
/
INSERT INTO KATEGORIA (KATEGORIA_ID, KATEGORIA_NAZWA, OPIS)
VALUES ('2', 'WĘDKI', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla elementum turpis risus, eu hendrerit odio lobortis aliquet.'); 
/
INSERT INTO KATEGORIA (KATEGORIA_ID, KATEGORIA_NAZWA, OPIS)
VALUES ('3', 'KOŁOWROTKI', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla elementum turpis risus, eu hendrerit odio lobortis aliquet.'); 
/
INSERT INTO KATEGORIA (KATEGORIA_ID, KATEGORIA_NAZWA, OPIS)
VALUES ('4', 'PRZYNĘTY', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla elementum turpis risus, eu hendrerit odio lobortis aliquet.');  
/
INSERT INTO KATEGORIA (KATEGORIA_ID, KATEGORIA_NAZWA, OPIS)
VALUES ('5', 'HACZYKI', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla elementum turpis risus, eu hendrerit odio lobortis aliquet.');  
/
INSERT INTO KATEGORIA (KATEGORIA_ID, KATEGORIA_NAZWA, OPIS)
VALUES ('6', 'PODBIERAKI', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla elementum turpis risus, eu hendrerit odio lobortis aliquet.');
/

/* KURIER DATA */
---------------------
INSERT INTO KURIER (KURIER_ID, NAZWA_FIRMY)
VALUES ('1', 'UPS');  
/
INSERT INTO KURIER (KURIER_ID, NAZWA_FIRMY)
VALUES ('2', 'DHL');  
/
INSERT INTO KURIER (KURIER_ID, NAZWA_FIRMY)
VALUES ('3', 'DPD');  
/
INSERT INTO KURIER (KURIER_ID, NAZWA_FIRMY)
VALUES ('4', 'POCZTEX');  
/
INSERT INTO KURIER (KURIER_ID, NAZWA_FIRMY)
VALUES ('5', 'FEDEX');  
/


/* ADRES DATA */
----------------
INSERT INTO ADRES (ADRES_ID,MIEJSCOWOSC,WOJEWODZTWO,KOD_POCZTOWY,ULICA,NR_DOMU,NR_LOKALU) 
VALUES ('1','Krosno','Podkarpackie','38-400','Bajkowa','45','2');
/
INSERT INTO ADRES (ADRES_ID,MIEJSCOWOSC,WOJEWODZTWO,KOD_POCZTOWY,ULICA,NR_DOMU,NR_LOKALU) 
VALUES ('2','Rzeszów','Podkarpackie','38-330','Wergiliusza','32','4');
/
INSERT INTO ADRES (ADRES_ID,MIEJSCOWOSC,WOJEWODZTWO,KOD_POCZTOWY,ULICA,NR_DOMU,NR_LOKALU) 
VALUES ('3','Mielec','Wielkopolskie','45-520','Banalna','48','22');
/
INSERT INTO ADRES (ADRES_ID,MIEJSCOWOSC,WOJEWODZTWO,KOD_POCZTOWY,ULICA,NR_DOMU,NR_LOKALU) 
VALUES ('4','Warszawa','Mazowieckie','84-550','Ogromna','42',null);
/
INSERT INTO ADRES (ADRES_ID,MIEJSCOWOSC,WOJEWODZTWO,KOD_POCZTOWY,ULICA,NR_DOMU,NR_LOKALU) 
VALUES ('5','Kraków','Małopolskie','44-520','Zabytkowa','925',null);
/
INSERT INTO ADRES (ADRES_ID,MIEJSCOWOSC,WOJEWODZTWO,KOD_POCZTOWY,ULICA,NR_DOMU,NR_LOKALU) 
VALUES ('6','Sopot','Pomorskie','45-766','Magiczna','45','56');
/
INSERT INTO ADRES (ADRES_ID,MIEJSCOWOSC,WOJEWODZTWO,KOD_POCZTOWY,ULICA,NR_DOMU,NR_LOKALU) 
VALUES ('7','Piła','Wielkopolskie','45-655','Leśna','98','53');
/
INSERT INTO ADRES (ADRES_ID,MIEJSCOWOSC,WOJEWODZTWO,KOD_POCZTOWY,ULICA,NR_DOMU,NR_LOKALU) 
VALUES ('8','Kielce','Świętokrzyskie','54-54','Szybka','3','32');
/
INSERT INTO ADRES (ADRES_ID,MIEJSCOWOSC,WOJEWODZTWO,KOD_POCZTOWY,ULICA,NR_DOMU,NR_LOKALU) 
VALUES ('9','Katowice','Śląskie','34-254','Konkretna','655',null);
/
INSERT INTO ADRES (ADRES_ID,MIEJSCOWOSC,WOJEWODZTWO,KOD_POCZTOWY,ULICA,NR_DOMU,NR_LOKALU) 
VALUES ('10','Gdańsk','Pomorskie','54-345','Mądra','45','33');
/
INSERT INTO ADRES (ADRES_ID,MIEJSCOWOSC,WOJEWODZTWO,KOD_POCZTOWY,ULICA,NR_DOMU,NR_LOKALU) 
VALUES ('11','Białystok','Podlaskie','87-095','Logiczna','89','34');
/
INSERT INTO ADRES (ADRES_ID,MIEJSCOWOSC,WOJEWODZTWO,KOD_POCZTOWY,ULICA,NR_DOMU,NR_LOKALU) 
VALUES ('12','Łódź','Łódzkie','76-765','Barska','76','65');
/
INSERT INTO ADRES (ADRES_ID,MIEJSCOWOSC,WOJEWODZTWO,KOD_POCZTOWY,ULICA,NR_DOMU,NR_LOKALU) 
VALUES ('13','Opole','Opolskie','87-654','Kolorowa','98',null);
/
INSERT INTO ADRES (ADRES_ID,MIEJSCOWOSC,WOJEWODZTWO,KOD_POCZTOWY,ULICA,NR_DOMU,NR_LOKALU) 
VALUES ('14','Wrocław','Dolnośląskie','544-78','Marna','43','16');
/
INSERT INTO ADRES (ADRES_ID,MIEJSCOWOSC,WOJEWODZTWO,KOD_POCZTOWY,ULICA,NR_DOMU,NR_LOKALU) 
VALUES ('15','Zielona Góra','Lubuskie','523-65','Zagadkowa','54',null);
/
INSERT INTO ADRES (ADRES_ID,MIEJSCOWOSC,WOJEWODZTWO,KOD_POCZTOWY,ULICA,NR_DOMU,NR_LOKALU) 
VALUES ('16','Rzeszów','Podkarpackie','311-78','Komodowa','43','16');
/
INSERT INTO ADRES (ADRES_ID,MIEJSCOWOSC,WOJEWODZTWO,KOD_POCZTOWY,ULICA,NR_DOMU,NR_LOKALU) 
VALUES ('17','Leżajsk','Podkarpackie','423-65','Waranowa','54',null);
/

/* KONTAKT DATA */
------------------
INSERT INTO KONTAKT (KONTAKT_ID,NR_TEL,EMAIL) 
VALUES ('1','568357976', 'kowanacki@example.pl');
/
INSERT INTO KONTAKT (KONTAKT_ID,NR_TEL,EMAIL) 
VALUES ('2','875789578', 'bazar@example.pl');
/
INSERT INTO KONTAKT (KONTAKT_ID,NR_TEL,EMAIL) 
VALUES ('3','567098689', 'larry@example.pl');
/
INSERT INTO KONTAKT (KONTAKT_ID,NR_TEL,FAX,EMAIL,WWW) 
VALUES ('4','600500200','+1 (325) 456-789','wedex@wedex.pl','www.wedex.pl');
/
INSERT INTO KONTAKT (KONTAKT_ID,NR_TEL,FAX,EMAIL,WWW) 
VALUES ('5','200500555','+4 (456) 489-452','kowalex@kowalex.pl','www.kowalex.pl');
/
INSERT INTO KONTAKT (KONTAKT_ID,NR_TEL,EMAIL) 
VALUES ('6','341493621','risus@Proinvel.org');
/
INSERT INTO KONTAKT (KONTAKT_ID,NR_TEL,EMAIL) 
VALUES ('7','639423218','ultrices@Seddiam.edu');
/
INSERT INTO KONTAKT (KONTAKT_ID,NR_TEL,EMAIL) 
VALUES ('8','428732950','Suspendisse@consect.ca');
/
INSERT INTO KONTAKT (KONTAKT_ID,NR_TEL,EMAIL) 
VALUES ('9','197992819','sem@tempor.org');
/
INSERT INTO KONTAKT (KONTAKT_ID,NR_TEL,EMAIL) 
VALUES ('10','269112686','vel.est@justositamet.ca');
/
INSERT INTO KONTAKT (KONTAKT_ID,NR_TEL,EMAIL) 
VALUES ('11','917906240','dictum.mi.ac@adipiscing.com');
/
INSERT INTO KONTAKT (KONTAKT_ID,NR_TEL,EMAIL) 
VALUES ('12','412606057','eu.odio.tristique@libero.org');
/
INSERT INTO KONTAKT (KONTAKT_ID,NR_TEL,EMAIL) 
VALUES ('13','343852357','est.arcu.ac@lorem.org');
/
INSERT INTO KONTAKT (KONTAKT_ID,NR_TEL,EMAIL) 
VALUES ('14','160404986','feugiatem@estconguea.com');
/
INSERT INTO KONTAKT (KONTAKT_ID,NR_TEL,EMAIL) 
VALUES ('15','599685339','In.lorem@temporaugueac.com');
/
INSERT INTO KONTAKT (KONTAKT_ID,NR_TEL,EMAIL) 
VALUES ('16','156987569','dostawca@superdostawca.com');
/
INSERT INTO KONTAKT (KONTAKT_ID,NR_TEL,EMAIL) 
VALUES ('17','569125486','komodo@dragon.com');
/


/* KONTO DATA */
----------------
INSERT INTO KONTO (KONTO_ID,KARTA_ID,ADRES_ID,KONTAKT_ID,LOGIN,HASLO,UPRAWNIENIA,IMIE,NAZWISKO,KONTO_AKTYWNE) 
VALUES ('1',null,'1','1','admin','admin','admin','Mateusz','Kownacki','1');
/
INSERT INTO KONTO (KONTO_ID,KARTA_ID,ADRES_ID,KONTAKT_ID,LOGIN,HASLO,UPRAWNIENIA,IMIE,NAZWISKO,KONTO_AKTYWNE)
VALUES ('2',null,'2','2','pracownik','pracownik','pracownik','Adam','Base','1');
/
INSERT INTO KONTO (KONTO_ID,KARTA_ID,ADRES_ID,KONTAKT_ID,LOGIN,HASLO,UPRAWNIENIA,IMIE,NAZWISKO,KONTO_AKTYWNE) 
VALUES ('3',null,'3','3','klient','klient','klient','James','Flower','1');
/
INSERT INTO KONTO (KONTO_ID,KARTA_ID,ADRES_ID,KONTAKT_ID,LOGIN,HASLO,UPRAWNIENIA,IMIE,NAZWISKO,KONTO_AKTYWNE)
VALUES ('4',null,'4','4','pracownik2','pracownik2','pracownik','Castor ','Cooley','1');
/
INSERT INTO KONTO (KONTO_ID,KARTA_ID,ADRES_ID,KONTAKT_ID,LOGIN,HASLO,UPRAWNIENIA,IMIE,NAZWISKO,KONTO_AKTYWNE)
VALUES ('5',null,'5','5','pracownik3','pracownik3','pracownik','Rahim','Wall','1');
/
INSERT INTO KONTO (KONTO_ID,KARTA_ID,ADRES_ID,KONTAKT_ID,LOGIN,HASLO,UPRAWNIENIA,IMIE,NAZWISKO,KONTO_AKTYWNE)
VALUES ('6',null,'6','6','pracownik4','pracownik4','pracownik','Garth','Todd','1');
/
INSERT INTO KONTO (KONTO_ID,KARTA_ID,ADRES_ID,KONTAKT_ID,LOGIN,HASLO,UPRAWNIENIA,IMIE,NAZWISKO,KONTO_AKTYWNE)
VALUES ('7',null,'7','7','pracownik5','pracownik5','pracownik','Leonard','Summers','1');
/
INSERT INTO KONTO (KONTO_ID,KARTA_ID,ADRES_ID,KONTAKT_ID,LOGIN,HASLO,UPRAWNIENIA,IMIE,NAZWISKO,KONTO_AKTYWNE)
VALUES ('8',null,'8','8','pracownik6','pracownik6','pracownik','Benedict','Francis','1');
/
INSERT INTO KONTO (KONTO_ID,KARTA_ID,ADRES_ID,KONTAKT_ID,LOGIN,HASLO,UPRAWNIENIA,IMIE,NAZWISKO,KONTO_AKTYWNE)
VALUES ('9',null,'9','9','klient2','klient2','klient','Michael','Lindsay','1');
/
INSERT INTO KONTO (KONTO_ID,KARTA_ID,ADRES_ID,KONTAKT_ID,LOGIN,HASLO,UPRAWNIENIA,IMIE,NAZWISKO,KONTO_AKTYWNE)
VALUES ('10',null,'10','10','klient3','klient3','klient','Julian','Jackson ','1');
/
INSERT INTO KONTO (KONTO_ID,KARTA_ID,ADRES_ID,KONTAKT_ID,LOGIN,HASLO,UPRAWNIENIA,IMIE,NAZWISKO,KONTO_AKTYWNE)
VALUES ('11',null,'11','11','klient4','klient4','klient','Vladimir','Peters','1');
/
INSERT INTO KONTO (KONTO_ID,KARTA_ID,ADRES_ID,KONTAKT_ID,LOGIN,HASLO,UPRAWNIENIA,IMIE,NAZWISKO,KONTO_AKTYWNE)
VALUES ('12',null,'12','12','klient5','klient5','klient','Derek','Hammond','1');
/
INSERT INTO KONTO (KONTO_ID,KARTA_ID,ADRES_ID,KONTAKT_ID,LOGIN,HASLO,UPRAWNIENIA,IMIE,NAZWISKO,KONTO_AKTYWNE)
VALUES ('13',null,'13','13','klient6','klient6','klient','Mannix','Rocha','1');
/
INSERT INTO KONTO (KONTO_ID,KARTA_ID,ADRES_ID,KONTAKT_ID,LOGIN,HASLO,UPRAWNIENIA,IMIE,NAZWISKO,KONTO_AKTYWNE)
VALUES ('14',null,'14','14','klient7','klient7','klient','Vernon','Summers','1');
/
INSERT INTO KONTO (KONTO_ID,KARTA_ID,ADRES_ID,KONTAKT_ID,LOGIN,HASLO,UPRAWNIENIA,IMIE,NAZWISKO,KONTO_AKTYWNE)
VALUES ('15',null,'15','15','klient8','klient8','klient','Mohammad','Kirk','1');
/


/* PRACOWNIK DATA */
--------------------
INSERT INTO PRACOWNIK (PRACOWNIK_ID,KONTO_ID,DATA_ZATRUDNIENIA,DATA_ZWOLNIENIA,PENSJA,PREMIA) 
VALUES ('1','2',to_date('18/05/15','DD/MM/RR'),null,'3200','260');
/
INSERT INTO PRACOWNIK (PRACOWNIK_ID,KONTO_ID,DATA_ZATRUDNIENIA,DATA_ZWOLNIENIA,PENSJA,PREMIA) 
VALUES ('2','4',to_date('11/01/16','DD/MM/RR'),null,'2200','56');
/
INSERT INTO PRACOWNIK (PRACOWNIK_ID,KONTO_ID,DATA_ZATRUDNIENIA,DATA_ZWOLNIENIA,PENSJA,PREMIA) 
VALUES ('3','5',to_date('11/01/16','DD/MM/RR'),null,'4200','56');
/
INSERT INTO PRACOWNIK (PRACOWNIK_ID,KONTO_ID,DATA_ZATRUDNIENIA,DATA_ZWOLNIENIA,PENSJA,PREMIA) 
VALUES ('4','6',to_date('18/05/17','DD/MM/RR'),null,'1200','66');
/
INSERT INTO PRACOWNIK (PRACOWNIK_ID,KONTO_ID,DATA_ZATRUDNIENIA,DATA_ZWOLNIENIA,PENSJA,PREMIA) 
VALUES ('5','7',to_date('11/03/18','DD/MM/RR'),null,'5200','76');
/
INSERT INTO PRACOWNIK (PRACOWNIK_ID,KONTO_ID,DATA_ZATRUDNIENIA,DATA_ZWOLNIENIA,PENSJA,PREMIA) 
VALUES ('6','8',to_date('14/05/18','DD/MM/RR'),null,'2200','430');
/


/* DOSTAWCA DATA */
-------------------
INSERT INTO DOSTAWCA (ADRES_ID, KONTAKT_ID, NAZWA_FIRMY)
VALUES ('4', '4', 'WĘDEX'); 
/
INSERT INTO DOSTAWCA (ADRES_ID, KONTAKT_ID, NAZWA_FIRMY)
VALUES ('5', '5', 'KOWALEX'); 
/
INSERT INTO DOSTAWCA (ADRES_ID, KONTAKT_ID, NAZWA_FIRMY)
VALUES ('16', '16', 'BARTEX'); 
/
INSERT INTO DOSTAWCA (ADRES_ID, KONTAKT_ID, NAZWA_FIRMY)
VALUES ('17', '17', 'PEWEX'); 
/

/* PRODUKT DATA */
--------------------
INSERT INTO PRODUKT (DOSTAWCA_ID,KATEGORIA_ID,PRODUCENT,NUMER_KATALOGOWY,MODEL,CENA,SZTUK_NA_MAGAZYNIE,OPIS) 
VALUES ('1','2','NASH','FSSDTW-455','SUPER-LONG','250,25','78','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla elementum turpis risus, eu hendrerit odio lobortis aliquet.');
/
INSERT INTO PRODUKT (DOSTAWCA_ID,KATEGORIA_ID,PRODUCENT,NUMER_KATALOGOWY,MODEL,CENA,SZTUK_NA_MAGAZYNIE,OPIS) 
VALUES ('1','2','JAXON','GDFGDF-665','TERRA','199,54','22','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla elementum turpis risus, eu hendrerit odio lobortis aliquet.');
/
INSERT INTO PRODUKT (DOSTAWCA_ID,KATEGORIA_ID,PRODUCENT,NUMER_KATALOGOWY,MODEL,CENA,SZTUK_NA_MAGAZYNIE,OPIS) 
VALUES ('1','2','DAIWA','GASDGF-773','JUNGLE','450,84','2','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla elementum turpis risus, eu hendrerit odio lobortis aliquet.');
/
INSERT INTO PRODUKT (DOSTAWCA_ID,KATEGORIA_ID,PRODUCENT,NUMER_KATALOGOWY,MODEL,CENA,SZTUK_NA_MAGAZYNIE,OPIS) 
VALUES ('1','2','MIKADO','UNFEGG-633','TOP','220,45','42','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla elementum turpis risus, eu hendrerit odio lobortis aliquet.');
/
INSERT INTO PRODUKT (DOSTAWCA_ID,KATEGORIA_ID,PRODUCENT,NUMER_KATALOGOWY,MODEL,CENA,SZTUK_NA_MAGAZYNIE,OPIS) 
VALUES ('1','2','KORDA','FSDFSBF-745','PREMIUM','999,99','1','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla elementum turpis risus, eu hendrerit odio lobortis aliquet.');
/
INSERT INTO PRODUKT (DOSTAWCA_ID,KATEGORIA_ID,PRODUCENT,NUMER_KATALOGOWY,MODEL,CENA,SZTUK_NA_MAGAZYNIE,OPIS) 
VALUES ('1','2','NASH','GAFGSGS-885','MOETTO','680,58','58','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla elementum turpis risus, eu hendrerit odio lobortis aliquet.');
/

INSERT INTO PRODUKT (DOSTAWCA_ID,KATEGORIA_ID,PRODUCENT,NUMER_KATALOGOWY,MODEL,CENA,SZTUK_NA_MAGAZYNIE,OPIS) 
VALUES ('2','1','Prologic','GADFA-455','LAMPA','312,21','43','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla elementum turpis risus, eu hendrerit odio lobortis aliquet.');
/
INSERT INTO PRODUKT (DOSTAWCA_ID,KATEGORIA_ID,PRODUCENT,NUMER_KATALOGOWY,MODEL,CENA,SZTUK_NA_MAGAZYNIE,OPIS) 
VALUES ('2','1','Prologic','GDSA-665','KNIFE','312,54','2','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla elementum turpis risus, eu hendrerit odio lobortis aliquet.');
/
INSERT INTO PRODUKT (DOSTAWCA_ID,KATEGORIA_ID,PRODUCENT,NUMER_KATALOGOWY,MODEL,CENA,SZTUK_NA_MAGAZYNIE,OPIS) 
VALUES ('2','1','Prologic','GFSAF-543','Boilie','432,84','5','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla elementum turpis risus, eu hendrerit odio lobortis aliquet.');
/
INSERT INTO PRODUKT (DOSTAWCA_ID,KATEGORIA_ID,PRODUCENT,NUMER_KATALOGOWY,MODEL,CENA,SZTUK_NA_MAGAZYNIE,OPIS) 
VALUES ('2','1','Prologic','KHJG-765','PARASOL','432,45','43','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla elementum turpis risus, eu hendrerit odio lobortis aliquet.');
/
INSERT INTO PRODUKT (DOSTAWCA_ID,KATEGORIA_ID,PRODUCENT,NUMER_KATALOGOWY,MODEL,CENA,SZTUK_NA_MAGAZYNIE,OPIS) 
VALUES ('2','1','Prologic','FHGKG-234','KRZESZŁO WĘDKARSKIE','43,99','1','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla elementum turpis risus, eu hendrerit odio lobortis aliquet.');
/
INSERT INTO PRODUKT (DOSTAWCA_ID,KATEGORIA_ID,PRODUCENT,NUMER_KATALOGOWY,MODEL,CENA,SZTUK_NA_MAGAZYNIE,OPIS) 
VALUES ('2','1','Prologic','GKGHJG-523','PŁYWAK','43,58','43','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla elementum turpis risus, eu hendrerit odio lobortis aliquet.');
/

INSERT INTO PRODUKT (DOSTAWCA_ID,KATEGORIA_ID,PRODUCENT,NUMER_KATALOGOWY,MODEL,CENA,SZTUK_NA_MAGAZYNIE,OPIS) 
VALUES ('2','3','Tandem Baits','JFGF-455','PHANTOM','54,21','13','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla elementum turpis risus, eu hendrerit odio lobortis aliquet.');
/
INSERT INTO PRODUKT (DOSTAWCA_ID,KATEGORIA_ID,PRODUCENT,NUMER_KATALOGOWY,MODEL,CENA,SZTUK_NA_MAGAZYNIE,OPIS) 
VALUES ('2','3','Nash','JFGG-665','BAITRUNNER','542,54','15','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla elementum turpis risus, eu hendrerit odio lobortis aliquet.');
/
INSERT INTO PRODUKT (DOSTAWCA_ID,KATEGORIA_ID,PRODUCENT,NUMER_KATALOGOWY,MODEL,CENA,SZTUK_NA_MAGAZYNIE,OPIS) 
VALUES ('2','3','Daiwa','VMNBV-543','QUICKDRAG','54,84','16','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla elementum turpis risus, eu hendrerit odio lobortis aliquet.');
/
INSERT INTO PRODUKT (DOSTAWCA_ID,KATEGORIA_ID,PRODUCENT,NUMER_KATALOGOWY,MODEL,CENA,SZTUK_NA_MAGAZYNIE,OPIS) 
VALUES ('2','3','FOX','UYRT-765','EOS','543,45','43','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla elementum turpis risus, eu hendrerit odio lobortis aliquet.');
/
INSERT INTO PRODUKT (DOSTAWCA_ID,KATEGORIA_ID,PRODUCENT,NUMER_KATALOGOWY,MODEL,CENA,SZTUK_NA_MAGAZYNIE,OPIS) 
VALUES ('2','3','Mivardi','LHJK-234','WIDOW','76,99','32','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla elementum turpis risus, eu hendrerit odio lobortis aliquet.');
/
INSERT INTO PRODUKT (DOSTAWCA_ID,KATEGORIA_ID,PRODUCENT,NUMER_KATALOGOWY,MODEL,CENA,SZTUK_NA_MAGAZYNIE,OPIS) 
VALUES ('2','3','Okuma','FSAD-523','BLACK','876,58','4','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla elementum turpis risus, eu hendrerit odio lobortis aliquet.');
/

INSERT INTO PRODUKT (DOSTAWCA_ID,KATEGORIA_ID,PRODUCENT,NUMER_KATALOGOWY,MODEL,CENA,SZTUK_NA_MAGAZYNIE,OPIS) 
VALUES ('2','4','PROLOGIC','GDS-432','SUPERNATURAL','654,21','23','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla elementum turpis risus, eu hendrerit odio lobortis aliquet.');
/
INSERT INTO PRODUKT (DOSTAWCA_ID,KATEGORIA_ID,PRODUCENT,NUMER_KATALOGOWY,MODEL,CENA,SZTUK_NA_MAGAZYNIE,OPIS) 
VALUES ('2','4','ESP','GSFD-431','SWEETCORN','57,54','4','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla elementum turpis risus, eu hendrerit odio lobortis aliquet.');
/
INSERT INTO PRODUKT (DOSTAWCA_ID,KATEGORIA_ID,PRODUCENT,NUMER_KATALOGOWY,MODEL,CENA,SZTUK_NA_MAGAZYNIE,OPIS) 
VALUES ('2','4','ENTERPRISE','JHGJ-431','ZING','87,84','43','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla elementum turpis risus, eu hendrerit odio lobortis aliquet.');
/
INSERT INTO PRODUKT (DOSTAWCA_ID,KATEGORIA_ID,PRODUCENT,NUMER_KATALOGOWY,MODEL,CENA,SZTUK_NA_MAGAZYNIE,OPIS) 
VALUES ('2','4','KORDA','GDSS-437','CITRUS','98,45','54','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla elementum turpis risus, eu hendrerit odio lobortis aliquet.');
/
INSERT INTO PRODUKT (DOSTAWCA_ID,KATEGORIA_ID,PRODUCENT,NUMER_KATALOGOWY,MODEL,CENA,SZTUK_NA_MAGAZYNIE,OPIS) 
VALUES ('2','4','NASH','JHG-097','BIGJUICE','687,99','4','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla elementum turpis risus, eu hendrerit odio lobortis aliquet.');
/
INSERT INTO PRODUKT (DOSTAWCA_ID,KATEGORIA_ID,PRODUCENT,NUMER_KATALOGOWY,MODEL,CENA,SZTUK_NA_MAGAZYNIE,OPIS) 
VALUES ('2','4','Okuma','JHG-976','PELLET','98,58','58','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla elementum turpis risus, eu hendrerit odio lobortis aliquet.');
/

INSERT INTO PRODUKT (DOSTAWCA_ID,KATEGORIA_ID,PRODUCENT,NUMER_KATALOGOWY,MODEL,CENA,SZTUK_NA_MAGAZYNIE,OPIS) 
VALUES ('2','5','JAXON','GDDS-5342','PROCARP','543,21','4','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla elementum turpis risus, eu hendrerit odio lobortis aliquet.');
/
INSERT INTO PRODUKT (DOSTAWCA_ID,KATEGORIA_ID,PRODUCENT,NUMER_KATALOGOWY,MODEL,CENA,SZTUK_NA_MAGAZYNIE,OPIS) 
VALUES ('2','5','MYSTIC','HFDF-534','GAPE','54,54','54','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla elementum turpis risus, eu hendrerit odio lobortis aliquet.');
/
INSERT INTO PRODUKT (DOSTAWCA_ID,KATEGORIA_ID,PRODUCENT,NUMER_KATALOGOWY,MODEL,CENA,SZTUK_NA_MAGAZYNIE,OPIS) 
VALUES ('2','5','VMC','KJHG-654','SPEICMEN','76,84','42','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla elementum turpis risus, eu hendrerit odio lobortis aliquet.');
/
INSERT INTO PRODUKT (DOSTAWCA_ID,KATEGORIA_ID,PRODUCENT,NUMER_KATALOGOWY,MODEL,CENA,SZTUK_NA_MAGAZYNIE,OPIS) 
VALUES ('2','5','SONIK','FASD-643','TRUST','542,45','23','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla elementum turpis risus, eu hendrerit odio lobortis aliquet.');
/
INSERT INTO PRODUKT (DOSTAWCA_ID,KATEGORIA_ID,PRODUCENT,NUMER_KATALOGOWY,MODEL,CENA,SZTUK_NA_MAGAZYNIE,OPIS) 
VALUES ('2','5','CORMORAN','542-635','SHANK','123,99','4','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla elementum turpis risus, eu hendrerit odio lobortis aliquet.');
/
INSERT INTO PRODUKT (DOSTAWCA_ID,KATEGORIA_ID,PRODUCENT,NUMER_KATALOGOWY,MODEL,CENA,SZTUK_NA_MAGAZYNIE,OPIS) 
VALUES ('2','5','OWNER','542-436','CARPUP','98,58','54','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla elementum turpis risus, eu hendrerit odio lobortis aliquet.');
/

INSERT INTO PRODUKT (DOSTAWCA_ID,KATEGORIA_ID,PRODUCENT,NUMER_KATALOGOWY,MODEL,CENA,SZTUK_NA_MAGAZYNIE,OPIS) 
VALUES ('2','6','MIVARDI','JFGF-23','NET','324,21','5','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla elementum turpis risus, eu hendrerit odio lobortis aliquet.');
/
INSERT INTO PRODUKT (DOSTAWCA_ID,KATEGORIA_ID,PRODUCENT,NUMER_KATALOGOWY,MODEL,CENA,SZTUK_NA_MAGAZYNIE,OPIS) 
VALUES ('2','6','AVID','KHGJ-856','FLOAT','432,54','21','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla elementum turpis risus, eu hendrerit odio lobortis aliquet.');
/
INSERT INTO PRODUKT (DOSTAWCA_ID,KATEGORIA_ID,PRODUCENT,NUMER_KATALOGOWY,MODEL,CENA,SZTUK_NA_MAGAZYNIE,OPIS) 
VALUES ('2','6','FOX','LKJH-335','CAMOLITE','32,84','4','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla elementum turpis risus, eu hendrerit odio lobortis aliquet.');
/
INSERT INTO PRODUKT (DOSTAWCA_ID,KATEGORIA_ID,PRODUCENT,NUMER_KATALOGOWY,MODEL,CENA,SZTUK_NA_MAGAZYNIE,OPIS) 
VALUES ('2','6','NASH','JGF-532','CRUZADE','543,45','54','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla elementum turpis risus, eu hendrerit odio lobortis aliquet.');
/
INSERT INTO PRODUKT (DOSTAWCA_ID,KATEGORIA_ID,PRODUCENT,NUMER_KATALOGOWY,MODEL,CENA,SZTUK_NA_MAGAZYNIE,OPIS) 
VALUES ('2','6','TRAKKER','FAS-523','LANDING','342,99','6','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla elementum turpis risus, eu hendrerit odio lobortis aliquet.');
/
INSERT INTO PRODUKT (DOSTAWCA_ID,KATEGORIA_ID,PRODUCENT,NUMER_KATALOGOWY,MODEL,CENA,SZTUK_NA_MAGAZYNIE,OPIS) 
VALUES ('2','6','AQUA','FDS-562','MESH','321,58','76','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla elementum turpis risus, eu hendrerit odio lobortis aliquet.');

COMMIT;