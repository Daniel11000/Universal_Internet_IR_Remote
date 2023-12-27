#include <IRremote.hpp>
#include <Arduino.h>

#include <WiFi.h>
#include <HTTPClient.h>

#define RXD2 16
#define TXD2 17

  // Odczytane kody IR (do przesłania na stronę)
  String ir_protocol = "";
  String ir_address = "";
  String ir_command = "";
  String ir_rep_nob = "";

  // Odebrane (ze strony) kody IR do nadania
  String ir_protocol_toSEND = "";
  String ir_address_toSEND = "";
  String ir_command_toSEND = "";
  String ir_rep_nob_toSEND = "";


const int RECV_PIN = 27;
const int SEND_PIN = 5;
IRrecv irrecv(RECV_PIN);
decode_results results;

                                          // LINKI
const char* URL_getData = "TU_WPISZ_ADRES_SWOJEJ_STRONY/ESP_conn_scan_ir.php";
const char* URL_sendData = "TU_WPISZ_ADRES_SWOJEJ_STRONY/ESP_conn_post_ir_data.php";

const char* URL_getIR_Codes = "TU_WPISZ_ADRES_SWOJEJ_STRONY/ESP_conn_send_ir_codes.php";
const char* URL_getIR_Codes_RESP = "TU_WPISZ_ADRES_SWOJEJ_STRONY/ESP_conn_resp_from_snd_ir_data.php";

  // nazwa sieci WIFI i haslo
const char* ssid = "TU_WPISZ_NAZWĘ_SWOJEJ_SIECI_WIFI";     // Network SSID
const char* password = "TU_WPISZ_HASŁO_DO_SWOJEJ_SIECI_WIFI";        // Network Password


// Zmienne przechowujące otrzymane dane
String table = "";
String buttonName = "";
String buttonColumn = "";
String buttonRow = "";

String ESP_action = "";


void setup() {
  // put your setup code here, to run once:

  Serial.begin(9600);
  pinMode(27, INPUT);
  pinMode(5, OUTPUT);
  IrReceiver.begin(RECV_PIN, ENABLE_LED_FEEDBACK);
Serial.println("Code: \n");
pinMode(5, OUTPUT);   // initialize digital pin 5 as an output.
IrSender.begin(SEND_PIN, ENABLE_LED_FEEDBACK, USE_DEFAULT_FEEDBACK_LED_PIN);

connectWiFi();
connectToWiFi();

Serial2.begin(9600, SERIAL_8N1, RXD2, TXD2);


}

void loop() {
  // put your main code here, to run repeatedly:

  if(WiFi.status() != WL_CONNECTED) {
    connectWiFi();
  }


// ---------------------------------------------  <  MODUŁ DO WYSYŁANIA KODU IR (do urządzenia) >  ------------------------------------------------------

recv_IR_data();
if(ir_protocol_toSEND != "" && ir_address_toSEND != "" && ir_command_toSEND != "" && ir_rep_nob_toSEND != "" && ir_protocol_toSEND != "UNKNOWN")
{
  send_received_IR_code(ir_protocol_toSEND, ir_address_toSEND, ir_command_toSEND, ir_rep_nob_toSEND);
  IR_resp();
}


// ---------------------------------------------  </  MODUŁ DO WYSYŁANIA KODU IR  >  ------------------------------------------------------





// =============================================  <  MODUŁ DO ODCZYTYWANIA KODU IR (z pilota)  >  =============================================================================
recv_data();

      Serial.println("VVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVV");
      Serial.print("table: ");
      Serial.println(table);
      Serial.print("buttonName: ");
      Serial.println(buttonName);
      Serial.print("buttonColumn: ");
      Serial.println(buttonColumn);
      Serial.print("buttonRow: ");
      Serial.println(buttonRow);
      Serial.println("================================================================");

Serial.println();

      Serial.println("??????????????????????????????????????????????????????? ");
      Serial.println("IR Data:");

      Serial.print("Protocol: ");
      Serial.println(ir_protocol);
      Serial.print("Address: ");
      Serial.println(ir_address);
      Serial.print("Command: ");
      Serial.println(ir_command);
      Serial.print("rep_nob: ");
      Serial.println(ir_rep_nob);

      Serial.println("!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!");
      Serial.println();



if(ESP_action == "ESP_recv_IR"){
  send_data();
    // recv_resp();
}
  recv_resp();


// =============================================  </  MODUŁ DO ODCZYTYWANIA KODU IR >  =============================================================================



              // Wysyłanie z poziomu konsoli

  //recv_command = Serial.readStringUntil('\n');
  String recv_command = Serial.readStringUntil('_'); // Odczyt do pierwszego znaku '_'
  if(recv_command == "xSENDx"){

    sendIRcode();
    
  }


}

 // Funkcje:

void receiveIRcode()
{

  if (IrReceiver.decode()) {

    Serial.println(IrReceiver.decodedIRData.decodedRawData, HEX); // Print "old" raw data
    IrReceiver.printIRResultShort(&Serial); // Print complete received data in one line
    IrReceiver.printIRSendUsage(&Serial); // Print the statement required to send this data
    IrReceiver.resume(); // Enable receiving of the next value

  }

}


void sendIRcode()
{

  
    String protocol = Serial.readStringUntil('_'); // Odczyt do kolejnego znaku '_'

      String address_str = Serial.readStringUntil('_');  // Odczyt pierwszej liczby jako tekst
      String command_str = Serial.readStringUntil('_');  // Odczyt drugiej liczby jako tekst
      String repeats_str = Serial.readStringUntil('_');  // Odczyt trzeciej liczby jako tekst

      // Konwersja tekstowej reprezentacji na liczby
      int address, command, repeats;
      
// W protokole SONY "repeats" służy jako: numberOfBits

      if (address_str.startsWith("0x")) {
        address = strtol(address_str.c_str() + 2, NULL, 16);  // Odczyt szesnastkowy
      } else {
        address = strtol(address_str.c_str(), NULL, 10);  // Odczyt dziesiętny
      }

      if (command_str.startsWith("0x")) {
        command = strtol(command_str.c_str() + 2, NULL, 16);  // Odczyt szesnastkowy
      } else {
        command = strtol(command_str.c_str(), NULL, 10);  // Odczyt dziesiętny
      }

      if (repeats_str.startsWith("0x")) {
        repeats = strtol(repeats_str.c_str() + 2, NULL, 16);  // Odczyt szesnastkowy
      } else {
        repeats = strtol(repeats_str.c_str(), NULL, 10);  // Odczyt dziesiętny
      }

    ///*
    // Wyświetlenie otrzymanych danych w monitorze szeregowym
      Serial.print("Protocol: ");
      Serial.println(protocol);
      Serial.print("Address: 0X");
      Serial.println(address, HEX);
      Serial.print("Command: 0X");
      Serial.println(command, HEX);
      Serial.print("Repeats: 0X");
      Serial.println(repeats, HEX);
      //*/

    // Wysyłanie przez rozne protokoly:

    if(protocol == "NEC") {
      IrSender.sendNEC(address, command, repeats);
    }
    else if(protocol == "Samsung") {
      IrSender.sendSamsung(address, command, repeats);
    }
    else if(protocol == "SAMSUNG48") {
      IrSender.sendSamsung48(address, command, repeats);
    }
    else if(protocol == "SAMSUNG_LG") {
      IrSender.sendSamsungLG(address, command, repeats);
    }
    else if(protocol == "Sony") {
      // IrSender.sendSony(address, command, repeats); /////////////////////////////////
      // IrSender.sendSony(address, command, 1, repeats);
      IrSender.sendSony(address, command, 2, repeats);
    }
    else if(protocol == "Panasonic") {
      IrSender.sendPanasonic(address, command, repeats);
    }
    else if(protocol == "Denon") {
      IrSender.sendDenon(address, command, repeats);
    }
    else if(protocol == "Sharp") {
      IrSender.sendSharp(address, command, repeats);
    }
    else if(protocol == "LG") {
      IrSender.sendLG(address, command, repeats);
    }
    else if(protocol == "JVC") {
      IrSender.sendJVC((uint8_t) address, (uint8_t) command, repeats); ///////////////////////////////
    }
    else if(protocol == "RC5") {
      IrSender.sendRC5(address, command, repeats);  // No toggle for repeats
    }
    else if(protocol == "RC6") {
      IrSender.sendRC6(address, command, repeats);  // No toggle for repeats
    }

    else if(protocol == "KASEIKYO_JVC") {
      IrSender.sendKaseikyo_JVC(address, command, repeats);
    }
    else if(protocol == "KASEIKYO_DENON") {
      IrSender.sendKaseikyo_Denon(address, command, repeats);
    }
    else if(protocol == "KASEIKYO_SHARP") {
      IrSender.sendKaseikyo_Sharp(address, command, repeats);
    }
    else if(protocol == "KASEIKYO_MITSUBISHI") {
      IrSender.sendKaseikyo_Mitsubishi(address, command, repeats);
    }
    else if(protocol == "NEC2") {
      IrSender.sendNEC2(address, command, repeats);
    }
    else if(protocol == "Onkyo") {
      IrSender.sendOnkyo(address, command, repeats);
    }
    else if(protocol == "Apple") {
      IrSender.sendApple(address, command, repeats);
    }

// / EXOTIC PROTOCOLS
    
    else if(protocol == "FAST") {
      IrSender.sendFAST(command, repeats);  //////////////////////////////////////   // We have only 8 bit command
    }
    else if(protocol == "Lego Power Functions") { // "LEGO_PF"
      IrSender.sendLegoPowerFunctions(address, command, command >> 4, repeats);   //////////////////////   // send 5 autorepeats
    }
    
    else {
      //return 0;
      // Do nothing
    }

    

}


                              // ULEPSZONE Funkcje IR

String getValue(String data, String key, String delimiter) {
  int keyIndex = data.indexOf(key);
  if (keyIndex != -1) {
    int start = keyIndex + key.length();
    int end = data.indexOf(delimiter, start);
    if (end != -1) {
      return data.substring(start, end);
    }
  }
  return "";
}

// void receiveIRcode_ChnangeValues(String ir_protocol, String ir_address, String ir_command, String ir_rep_nob)
void receiveIRcode_ChnangeValues(String& ir_protocol, String& ir_address, String& ir_command, String& ir_rep_nob)
{
  if (ir_protocol == "" && ir_address == "" && ir_command == "" && ir_rep_nob == "") {

  if (IrReceiver.decode()) {

    // String IRData = "";
    Serial.println(IrReceiver.decodedIRData.decodedRawData, HEX); // Print "old" raw data
    IrReceiver.printIRResultShort(&Serial); // Print complete received data in one line

      IrReceiver.printIRResultShort(&Serial2);
      String IRData = Serial2.readString();

    IrReceiver.printIRSendUsage(&Serial); // Print the statement required to send this data
    IrReceiver.resume(); // Enable receiving of the next value

    ir_protocol = getValue(IRData, "Protocol=", " ");

    if(ir_protocol == "UNKNOWN"){
      ir_protocol = "";
    }
    else {
              //ir_protocol = getValue(IRData, "Protocol=", " ");
    ir_address = getValue(IRData, "Address=", " ");
    ir_command = getValue(IRData, "Command=", " ");
    String irRawData = getValue(IRData, "Raw-Data=", "bits");
    ir_rep_nob = getValue(irRawData, " ", " ");


      Serial.println("- - - - - - - - - - - - - - - - - ");
      
      Serial.print("IRData: ");
      Serial.println(IRData);

      Serial.print("Protocol: ");
      Serial.println(ir_protocol);
      Serial.print("Address: ");
      Serial.println(ir_address);
      Serial.print("Command: ");
      Serial.println(ir_command);
      Serial.print("rep_nob: ");
      Serial.println(ir_rep_nob);

      Serial.println("===================================");
      Serial.println();
    }   //  if(ir_protocol == "UNKNOWN"){

  }
  }   //  if (ir_protocol == "" && ir_address == "" && ir_command == "" && ir_rep_nob == "") {

}



void send_received_IR_code(String RECV_protocol, String RECV_address, String RECV_command, String RECV_rep_nob)
{

      // Konwersja tekstowej reprezentacji na liczby
      int address, command, repeats;
      String protocol = RECV_protocol;
      
// W protokole SONY "repeats" służy jako: numberOfBits

      if (RECV_address.startsWith("0x")) {
        address = strtol(RECV_address.c_str() + 2, NULL, 16);  // Odczyt szesnastkowy
      } else {
        address = strtol(RECV_address.c_str(), NULL, 10);  // Odczyt dziesiętny
      }

      if (RECV_command.startsWith("0x")) {
        command = strtol(RECV_command.c_str() + 2, NULL, 16);  // Odczyt szesnastkowy
      } else {
        command = strtol(RECV_command.c_str(), NULL, 10);  // Odczyt dziesiętny
      }

      if (RECV_rep_nob.startsWith("0x")) {
        repeats = strtol(RECV_rep_nob.c_str() + 2, NULL, 16);  // Odczyt szesnastkowy
      } else {
        repeats = strtol(RECV_rep_nob.c_str(), NULL, 10);  // Odczyt dziesiętny
      }

    ///*
    // Wyświetlenie otrzymanych danych w monitorze szeregowym
      Serial.println();
      Serial.println(">>>>>>>>>>>>>>>>>>>>>>> Sending from Website <<<<<<<<<<<<<<<<<<<<<<<<");
      Serial.print("Protocol: ");
      Serial.println(protocol);
      Serial.print("Address: 0X");
      Serial.println(address, HEX);
      Serial.print("Command: 0X");
      Serial.println(command, HEX);
      Serial.print("Repeats: 0X");
      Serial.println(repeats, HEX);
      Serial.println(">>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> ||||| <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<");
      Serial.println();
      //*/

    // Wysyłanie przez rozne protokoly:

    if(protocol == "NEC") {
      IrSender.sendNEC(address, command, repeats);
    }
    else if(protocol == "Samsung") {
      IrSender.sendSamsung(address, command, repeats);
    }
    else if(protocol == "SAMSUNG48") {
      IrSender.sendSamsung48(address, command, repeats);
    }
    else if(protocol == "SAMSUNG_LG") {
      IrSender.sendSamsungLG(address, command, repeats);
    }
    else if(protocol == "Sony") {
      // IrSender.sendSony(address, command, 1, repeats);
      IrSender.sendSony(address, command, 2, repeats);
    }
    else if(protocol == "Panasonic") {
      IrSender.sendPanasonic(address, command, repeats);
    }
    else if(protocol == "Denon") {
      IrSender.sendDenon(address, command, repeats);
    }
    else if(protocol == "Sharp") {
      IrSender.sendSharp(address, command, repeats);
    }
    else if(protocol == "LG") {
      IrSender.sendLG(address, command, repeats);
    }
    else if(protocol == "JVC") {
      IrSender.sendJVC((uint8_t) address, (uint8_t) command, repeats); ///////////////////////////////
    }
    else if(protocol == "RC5") {
      IrSender.sendRC5(address, command, repeats);  // No toggle for repeats
    }
    else if(protocol == "RC6") {
      IrSender.sendRC6(address, command, repeats);  // No toggle for repeats
    }

    else if(protocol == "KASEIKYO_JVC") {
      IrSender.sendKaseikyo_JVC(address, command, repeats);
    }
    else if(protocol == "KASEIKYO_DENON") {
      IrSender.sendKaseikyo_Denon(address, command, repeats);
    }
    else if(protocol == "KASEIKYO_SHARP") {
      IrSender.sendKaseikyo_Sharp(address, command, repeats);
    }
    else if(protocol == "KASEIKYO_MITSUBISHI") {
      IrSender.sendKaseikyo_Mitsubishi(address, command, repeats);
    }
    else if(protocol == "NEC2") {
      IrSender.sendNEC2(address, command, repeats);
    }
    else if(protocol == "Onkyo") {
      IrSender.sendOnkyo(address, command, repeats);
    }
    else if(protocol == "Apple") {
      IrSender.sendApple(address, command, repeats);
    }

// / EXOTIC PROTOCOLS
    
    else if(protocol == "FAST") {
      IrSender.sendFAST(command, repeats);  //////////////////////////////////////   // We have only 8 bit command
    }
    else if(protocol == "Lego Power Functions") { // "LEGO_PF"
      IrSender.sendLegoPowerFunctions(address, command, command >> 4, repeats);   //////////////////////   // send 5 autorepeats
    }
    
    else {
      //return 0;
      // Do nothing
    }

  //  IR_resp(); 

}



                    // Connection Functions

void connectWiFi() {
  WiFi.mode(WIFI_OFF);
  delay(1000);
  //This line hides the viewing of ESP as wifi hotspot
  WiFi.mode(WIFI_STA);
  
  WiFi.begin(ssid, password);
  Serial.println("Connecting to WiFi");
  
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
    
  Serial.print("connected to : "); Serial.println(ssid);
  Serial.print("IP address: "); Serial.println(WiFi.localIP());
}









// //////////////////////////////////////////////////////////////////////////////////////////////////////

void connectToWiFi() {
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.println("Connecting to WiFi...");
  }
  Serial.println("Connected to WiFi");
}



void recv_data() { // odbierz_dane()
    HTTPClient http;

    http.begin(URL_getData);


    int httpResponseCode = http.GET();

    if (httpResponseCode > 0) {
      String response = http.getString();
      Serial.print("Received data from PHP: ");
      Serial.println(response);

  // //////////////////////////////////////////////////////////////////////////////////////

  String resp = response;
  removeSpacesAndNewlines(resp);
  int pipeIndex = resp.indexOf('=');
  int endIndex = resp.indexOf('\n');

  resp = resp.substring(pipeIndex+1, endIndex);
  pipeIndex = resp.indexOf('|');
  // String ESP_action = resp.substring(0, pipeIndex);
  ESP_action = resp.substring(0, pipeIndex);
  pipeIndex = resp.indexOf('=');
  resp = resp.substring(pipeIndex+1, endIndex);
  
  pipeIndex = resp.indexOf('|');
  String current_table = resp.substring(0, pipeIndex);
  pipeIndex = resp.indexOf('=');
  resp = resp.substring(pipeIndex+1, endIndex);

  pipeIndex = resp.indexOf('|');
  String button_name = resp.substring(0, pipeIndex);
  pipeIndex = resp.indexOf('=');
  resp = resp.substring(pipeIndex+1, endIndex);

  pipeIndex = resp.indexOf('|');
  String button_protocol = resp.substring(0, pipeIndex);
  pipeIndex = resp.indexOf('=');
  resp = resp.substring(pipeIndex+1, endIndex);

  pipeIndex = resp.indexOf('|');
  String button_address = resp.substring(0, pipeIndex);
  pipeIndex = resp.indexOf('=');
  resp = resp.substring(pipeIndex+1, endIndex);

  pipeIndex = resp.indexOf('|');
  String button_command = resp.substring(0, pipeIndex);
  pipeIndex = resp.indexOf('=');
  resp = resp.substring(pipeIndex+1, endIndex);

  pipeIndex = resp.indexOf('|');
  String button_rep_nob = resp.substring(0, pipeIndex);
  pipeIndex = resp.indexOf('=');
  resp = resp.substring(pipeIndex+1, endIndex);


  pipeIndex = resp.indexOf('|');
  String button_column = resp.substring(0, pipeIndex);
  pipeIndex = resp.indexOf('=');
  resp = resp.substring(pipeIndex+1, endIndex);


  pipeIndex = resp.indexOf('|');
  String button_row = resp.substring(0, pipeIndex);
  pipeIndex = resp.indexOf('=');
  resp = resp.substring(pipeIndex+1, endIndex);



      Serial.println();
      Serial.println("XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX");
      Serial.print("ESP_action: ");
      Serial.println(ESP_action);
      Serial.print("current_table: ");
      Serial.println(current_table);
      Serial.print("button_name: ");
      Serial.println(button_name);
      Serial.print("button_protocol: ");
      Serial.println(button_protocol);
      Serial.print("button_address: ");
      Serial.println(button_address);
      Serial.print("button_command: ");
      Serial.println(button_command);
      Serial.print("button_rep_nob: ");
      Serial.println(button_rep_nob);
      Serial.print("button_column: ");
      Serial.println(button_column);
      Serial.print("button_row: ");
      Serial.println(button_row);
      Serial.println("YYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYY");
      Serial.println();



  if(ESP_action == "ESP_recv_IR"){
    table = current_table;
    buttonName = button_name;
    buttonColumn = button_column;
    buttonRow = button_row;
  }

  // //////////////////////////////////////////////////////////////////////////////////////

    } else {
      Serial.print("Error in receiving data from PHP. HTTP Response code: ");
      Serial.println(httpResponseCode);
    }

    http.end();

  delay(5000); // Poczekaj 5 sekund przed ponownym próbowaniem
  // delay(2000);
}

void removeSpacesAndNewlines(String &inputString) {
  // Usuń spacje
  inputString.replace(" ", "");

  // Usuń znaki nowej linii
  inputString.replace("\n", "");
  inputString.replace("\r", "");
}





void send_data(){

  HTTPClient http;

  http.begin(URL_sendData);
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");

  receiveIRcode_ChnangeValues(ir_protocol, ir_address, ir_command, ir_rep_nob); // ODBIERANIE DANYCH IR !
  // delay(2000);
  delay(250);
  String data = "protocol=" + String(ir_protocol) + "&address=" + String(ir_address) + "&command=" + String(ir_command) + "&rep_nob=" + String(ir_rep_nob);

      Serial.println();
      Serial.println("PPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPP");
      Serial.println(data);
      Serial.println("TTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTT");
      Serial.println();

  int httpResponseCode = http.POST(data);

  if (httpResponseCode > 0) {
    Serial.print("HTTP Response code: ");
    Serial.println(httpResponseCode);
  } else {
    Serial.print("Error in sending data. HTTP Response code: ");
    Serial.println(httpResponseCode);
  }


  http.end();

  // ///////////
    table = "";
    buttonName = "";
    buttonColumn = "";
    buttonRow = "";

    ESP_action = "";

    // IR Data:
        ir_protocol = "";
        ir_address = "";
        ir_command = "";
        ir_rep_nob = "";
  // ///////////

  // recv_resp();

}



void recv_resp() {
    HTTPClient http;

    http.begin(URL_sendData);

    int httpResponseCode = http.GET();

    if (httpResponseCode > 0) {
      String response = http.getString();
      Serial.print("Received data from PHP: ");
      Serial.println(response);

  // //////////////////////////////////////////////////////////////////////////////////////

  String resp = response;
  removeSpacesAndNewlines(resp);


  if(resp == "DataReceivedSuccessfully" || resp == "datareceivedsuccessfully"){
    table = "";
    buttonName = "";
    buttonColumn = "";
    buttonRow = "";

    ESP_action = "";

    // IR Data:
        ir_protocol = "";
        ir_address = "";
        ir_command = "";
        ir_rep_nob = "";

  }

  // //////////////////////////////////////////////////////////////////////////////////////

    } else {
      Serial.print("Error in receiving data from PHP. HTTP Response code: ");
      Serial.println(httpResponseCode);
    }

    http.end();

  delay(1500); // Poczekaj 5 sekund przed ponownym próbowaniem
  // delay(2000);
}





void recv_IR_data() { // odbiera dane o kodach IR ze strony
    HTTPClient http;

    http.begin(URL_getIR_Codes);


    int httpResponseCode = http.GET();

    if (httpResponseCode > 0) {
      String response = http.getString();
      Serial.print("Received data from PHP: ");
      Serial.println(response);

  // //////////////////////////////////////////////////////////////////////////////////////

  String resp = response;
  removeSpacesAndNewlines(resp);
  int pipeIndex = resp.indexOf('=');
  int endIndex = resp.indexOf('\n');

  resp = resp.substring(pipeIndex+1, endIndex);
  pipeIndex = resp.indexOf('|');
  String RECV_prot = resp.substring(0, pipeIndex);
  pipeIndex = resp.indexOf('=');
  resp = resp.substring(pipeIndex+1, endIndex);
  
  pipeIndex = resp.indexOf('|');
  String RECV_addr = resp.substring(0, pipeIndex);
  pipeIndex = resp.indexOf('=');
  resp = resp.substring(pipeIndex+1, endIndex);

  pipeIndex = resp.indexOf('|');
  String RECV_cmnd = resp.substring(0, pipeIndex);
  pipeIndex = resp.indexOf('=');
  resp = resp.substring(pipeIndex+1, endIndex);

  pipeIndex = resp.indexOf('|');
  String RECV_r_n = resp.substring(0, pipeIndex);
  pipeIndex = resp.indexOf('=');
  resp = resp.substring(pipeIndex+1, endIndex);



      Serial.println();
      Serial.println("XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX");
      Serial.print("Protocol (RECV_prot): ");
      Serial.println(RECV_prot);
      Serial.print("Address (RECV_addr): ");
      Serial.println(RECV_addr);
      Serial.print("Command (RECV_cmnd): ");
      Serial.println(RECV_cmnd);
      Serial.print("RECV_r_n: ");
      Serial.println(RECV_r_n);
      Serial.println("YYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYY");
      Serial.println();



  if(RECV_prot != "UNKNOWN" && RECV_prot != "" && RECV_addr != "" && RECV_cmnd != "" && RECV_r_n != ""){

    ir_protocol_toSEND = RECV_prot;
    ir_address_toSEND = RECV_addr;
    ir_command_toSEND = RECV_cmnd;
    ir_rep_nob_toSEND = RECV_r_n;

  }

  // //////////////////////////////////////////////////////////////////////////////////////

    } else {
      Serial.print("Error in receiving data from PHP. HTTP Response code: ");
      Serial.println(httpResponseCode);
    }

    http.end();

  delay(100);
}



void IR_resp(){ // Odpowiedź na odebrane kody IR (które trzeba nadać)

  HTTPClient http;

  http.begin(URL_getIR_Codes_RESP);
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");

  String data = "snd_resp=Success";

      Serial.println();
      Serial.println("<< | >>");
      Serial.println(data);
      Serial.println("<<>>");
      Serial.println();

  int httpResponseCode = http.POST(data);

  if (httpResponseCode > 0) {
    Serial.print("HTTP Response code: ");
    Serial.println(httpResponseCode);
  } else {
    Serial.print("Error in sending data. HTTP Response code: ");
    Serial.println(httpResponseCode);
  }

    delay(100);
  // ///// Zerowanie odebranych danych
  ir_protocol_toSEND = "";
  ir_address_toSEND = "";
  ir_command_toSEND = "";
  ir_rep_nob_toSEND = "";


  http.end();

  // recv_resp();

}






