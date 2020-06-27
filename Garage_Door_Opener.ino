// library inclusion
#include <SPI.h>      // we need this but I don't know why
#include <WiFiNINA.h> // Wifi interaction

// testing variables
bool test_wifi_reconnect = false; // true; // if true, every time the sonar reads "open" it will disconnect from the wifi

// network settings
char ssid[] = "lan down under"; // network SSID (name)
char pass[] = "notaustralia";   // network password

// WiFi variables
int status = WL_IDLE_STATUS;    // the Wifi radio's status
WiFiClient client;              // define our wifi client
bool was_disconnected_from_wifi = false;

// server
char server[] = "10.0.0.3";
bool was_disconnected_from_server = false;

// module settings
int opener_id = 15; // ID for opener module
int id_sonar  = 17; // ID for sonar module

// indicator LEDs pins
int power_led  = 0; // when the board is on
int wifi_led   = 1; // when the board is on WiFi
int server_led = 2; // when the board is connected to the server
int active_led = 3; // when the board is doing something, or once every few seconds

// relay pin
int relay      = 4; // relay for garage door switch

// sonar pins
int sonar_trig = 5; // trigger pin for sonar
int sonar_echo = 6; // echo (response) pin for sonar

long duration;      // duration between pulse and echo
int distance;       // distance as a result of math and the duration

// calibration variables
int d_short;        // the short distance for calibration      
int d_long;         // the long distance for calibration
int d_trig;         // the over-under distance that is used to deduce the state of the garage door

// misc

int count = 0;      // count used to make the active LED flicker occasionally

bool activated = false; // for activating the relay

void setup() {      // before the main loop...
  // set pin modes
  pinMode(relay,      OUTPUT);

  pinMode(sonar_trig, OUTPUT);
  pinMode(sonar_echo, INPUT);

  pinMode(power_led,  OUTPUT);
  pinMode(wifi_led,   OUTPUT);
  pinMode(server_led, OUTPUT);
  pinMode(active_led, OUTPUT);

  digitalWrite(power_led, HIGH);  // turn on power LED

  Serial.begin(9600); // initialize serial connection for monitoring

  // a little lightshow while we wait for the serial to spin up
  delay(700);
  digitalWrite(wifi_led, HIGH);
  delay(700);
  digitalWrite(server_led, HIGH);
  delay(700);
  digitalWrite(active_led, HIGH);
  delay(700);

  digitalWrite(active_led, LOW);
  delay(100);
  digitalWrite(server_led, LOW);
  delay(100);
  digitalWrite(wifi_led,   LOW);

  Serial.println("Hello!");

  delay(1000);

  Serial.println("Calibrating distance sensor...");

  d_short = getDistance();  // get the short calibration distance
  Serial.print("Short: ");
  Serial.print(d_short);  // display it

  // flicker active LED once
  flicker(active_led);

  delay(3000);  // wait before taking next calibration distance
  
  d_long = getDistance(); // get the long calibration distance
  Serial.print(", Long: ");
  Serial.print(d_long); // display it

  // flicker the active LED twice
  flicker(active_led);
  flicker(active_led);

  d_trig = (d_short + d_long)/2;  // find the trigger distance
  Serial.print(", Trig: ");
  Serial.println(d_trig);         // display it

  Serial.println("Distance sensor calibrated.");

  // check for the WiFi module
  if (WiFi.status() == WL_NO_MODULE) {
    Serial.println("Communication with WiFi module failed!");

    while (true); // do nothing forever
  }

  // attempt to connect to Wifi network
  while (status != WL_CONNECTED) {
    Serial.print("Attempting to connect to WPA SSID: ");
    Serial.println(ssid);

    status = WiFi.begin(ssid, pass);  // make the connection
    wifiDelay();
  }

  Serial.println("Welcome the LAN down under!");  // say we're connected
  digitalWrite(wifi_led, HIGH);                         // turn on the WiFi LED

  printIP();  // print out the IP address
}

void loop() { // for our main loop...
  count++;            // increment the loop count (used to make the active led blink occasionally)

  digitalWrite(server_led, LOW);  // set the server connection LED to off so we know when it goes on that a connection has been made
  digitalWrite(wifi_led, LOW);    // same for WiFi

  while (status != WL_CONNECTED) {  // double check we're connected to WiFi
    digitalWrite(wifi_led, LOW);                                // turn off WiFi LED
    was_disconnected_from_wifi = true;                                    // set bool for printing reconnect message
    
    Serial.println("Disconnected from WiFi. Reconnecting...");  // throw error

    status = WiFi.begin(ssid, pass);                            // try to reconnect
    wifiDelay();
  }

  if (was_disconnected_from_wifi) {
    Serial.println("Welcome (back) to the LAN down under!");

    was_disconnected_from_wifi = false;
  }

  digitalWrite(wifi_led, HIGH); // turn the WiFi LED back on because it's after a "while not connected" for loop

  String s; // string for storing server data

  while (client.available()) {  // while there is data available...
    char c = client.read();       // read the next character
    s = s + String(c);            // add it to our string
  } 

  int i = s.indexOf("<html>");  // find the index of the <html> tag right before our data digit
  if (i > 0) {                  // if found...
    String q = String(s[i+7]);    // the digit is 7 digits (somehow) after the "<" which is indexed. q is 1 if the module has been activated, 0 if not
    
    if (q=="1") {
      activated = true;
    } else {
      activated = false;
    }
  }

  distance = getDistance(); // get the distance from the sonar

  String data;  // string for storing data
  
  if (distance <= d_trig) { // if the distance is under the trigger distance...
    data = "closed";          // the door is closed
  } else {                  // else...
    data = "open";            // it's open
  }

  // connect to server for the sonar
  if (client.connect(server, 80)) { // if we can connect to the server...
    digitalWrite(server_led, HIGH);       // turn on the server LED

    if (was_disconnected_from_server == true) {
      Serial.println("Reconnected to server.");

      was_disconnected_from_server = false;
    }

    // write the data to the server
    client.print("GET /modules/write_data.php?id="); // beginning of our HTTP statement
    client.print(id_sonar);                     // the sonar module's ID
    client.print("&data=\"");                   // some more HTTP to allow for the next variable
    client.print(data);                         // the data to write
    client.println("\" HTTP/1.1");              // saying we're speaking HTML, I assume
    client.println("Host: server");           // give the host IP for some reason
    client.println("Connection: close");        // close our connection
    client.println();

    client.connect(server, 80);
    if (activated) {                                    // if activated...
      client.print("GET /modules/deactivate.php?id=");    // deactivate the opener module on the server, so it no longer thinks it has work to do
    } else {                                            // else
      client.print("GET /modules/read_status.php?id=");          // check if we've been activated
    }
    client.print(opener_id);
    client.println(" HTTP/1.1");
    client.println("Host: server");
    client.println("Connection: close");
    client.println();
  } else {
    Serial.println("Error connecting to server. Retrying..."); // couldn't connect error message
    was_disconnected_from_server = true;
  }

  // using the relay
  if (activated) {
    Serial.println("Activated");    // say so

    digitalWrite(relay, HIGH);      // start signal
    digitalWrite(active_led, HIGH); // turn on the active LED
    delay(3000);                    // hold for 3 seconds
    digitalWrite(relay, LOW);       // end signal
    digitalWrite(active_led, LOW);  // turn off the active LED

    activated = false;  // deactivate the module      
  }

  // serial output for loop
  long rssi = WiFi.RSSI();      // get WiFi signal strength

  if (!was_disconnected_from_server && !was_disconnected_from_wifi) {
    Serial.print("Distance: ");
    Serial.print(distance);       // current distance
    Serial.print(", State: ");
    Serial.print(data);           // data (state of the door)
    Serial.print(", Signal: ");
    Serial.println(rssi);         // signal strength
  }
  
  if (count == 20) {            // if it's been 20 loops (10s at 0.5s loop)
    flickerFast(active_led);
    
    count = 0;  // reset the count
  }

  if (data == "open" && test_wifi_reconnect) {
    WiFi.end();  
    status = WiFi.status();
    Serial.println("Disconnecting from WiFi for testing");
  }
  
  delay(500); // half second delay for the loop
}

void printIP() {
  IPAddress ip = WiFi.localIP();  // find IP
  Serial.print("IP Address: ");
  Serial.println(ip);             // print it
}

void flicker(int led) {
  digitalWrite(led, HIGH);
  delay(250);
  digitalWrite(led, LOW);
  delay(250);
}

void flickerFast(int led) {
  digitalWrite(led, HIGH);
  delay(125);
  digitalWrite(led, LOW);
  delay(125);
}

void wifiDelay() {
  for (int i = 0; i < 15; i++) {
      flicker(wifi_led);
    }
    for (int i = 0; i < 10; i++) {
      flickerFast(wifi_led);
    }
}

int getDistance() {               // read the sonar
  digitalWrite(sonar_trig, LOW);    // clear the trigger pin
  delayMicroseconds(2);
  
  digitalWrite(sonar_trig, HIGH);   //   activate the trigger pin (starts pulse)   
  delayMicroseconds(10);
  digitalWrite(sonar_trig, LOW);    // deactivate the trigger pin ( ends  pulse)
  
  duration = pulseIn(sonar_echo, HIGH); // reads the echo time

  distance = duration*0.034/2;  // converts from time to distance
  
  return distance;
}
