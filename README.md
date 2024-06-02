# Universal Internet IR Remote

<br>

<b><i>Internetowy interfejs dla urządzeń sterowanych pilotem na poczerwień</i></b>

<br>

<i>The Internet Interface for IR Remote Controlled Devices</i>

<br><br><br>

Projekt ten składa się z 2 części:
- Część na ESP32
- Część Internetowa

<br>

Część na ESP32 znajduje się w katalogu <i>"Kod_ESP32"</i>

<br>

Część na Internetowa znajduje się w 2 katalogach:
- Aplikacja Internetowa znajduje się w katalogu <i>"Aplikacja_Internetowa"</i>
- Baza Danych znajduje się w katalogu <i>"Baza_Danych"</i>

<br>
<br>

W katalogu <i>"Aplikacja_Internetowa"</i> znajduje się katalog <i>"public_html"</i> oraz plik <i>"konfiguracja_hostingu.txt"</i>.
<br> Pliki z katalogu "public_html" są niemal gotowe do wgrania do hostingu. 
Jedyne co należy zmienić, to w plikach <i>"connect.php"</i> praz <i>"connectPDO.php"</i> należy wpisać użytkownika, hasło oraz nazwę Bazy Danych (w obu takie same).
<br> Natomiast w pliku <i>"konfiguracja_hostingu.txt"</i> opisana jest konfiguracja hostinhu oraz jak wgrać do niego pliki.

<br>

W katalogu <i>"Baza_Danych"</i> znajduje się plik <i>"Baza_Danych.sql"</i>. Plik ten należy wgrać na hosting do zakładki <i>Import</i> w <b>phpMyAdmin</b>
Plik ten ma zakomentowaną część. Można ją odkomentować oraz pozmieniać według potrzeb. 
Wtedy dane będą dostępne odrazu, bez konieczności tworzenia z osobna każdego pilota orz przycisku ręcznie.
Należy pamiętać, aby nie zmieniać tabeli <i>"action_table"</i>!

<br>

W katalogu "Kod_ESP32" znajduje się kod na ESP32. Plik ten jest prawie gotowy do wgrania do urządzenia. 
<br> Należy w nim zmienić 6 zmiennych na początku kodu (pod komentarzem <i>"// Linki"</i>):
- Są to zmienne <i>URL_getData</i> , <i>URL_sendData</i> , <i>URL_getIR_Codes</i> , <i>URL_getIR_Codes_RESP</i> - w których należy wpisać adres strony w miejsce napisu <i>"TU_WPISZ_ADRES_SWOJEJ_STRONY"</i> (we wszystkich będzie on taki sam, ponieważ po tym napisie jest odwołanie do różnych podstron).
- Kolejnymi zmiennymi, które należy zmienić są <i>ssid</i> oraz <i>password</i> - są to nazwa sieci WiFi oraz hasło do sieci WiFi.

Kod do ESP wykorzystuje 4 biblioteki :
- IRremote.hpp
- Arduino.h
- WiFi.h
- HTTPClient.h

Trzy z nich są wbudowane w IDE (czyli ArduinoIDE), należy doinstalować tylko jedną z nich - bliotekę <i> <b>"IRremote" </b> by shirriff, z3t0, ArminJo </i> 
<br>(ja korzystałem z wersji 4.2.0).
<br> Link do GitHub'a tej biblioteki: <a href=https://github.com/Arduino-IRremote/Arduino-IRremote> https://github.com/Arduino-IRremote/Arduino-IRremote </a>
