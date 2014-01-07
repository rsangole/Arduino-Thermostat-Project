/* ArduStat, a web-controlled wifi thermostat project
Author - Rahul Sangole
Date - Jan 2014

Thanks for Adafruit and all the active members on the Arduino forums for their inputs!
 */
#include <Adafruit_CC3000.h>
#include <ccspi.h>
#include <SPI.h>
#include <string.h>
#include "utility/debug.h"
#include <Servo.h>
#include "dht11.h"

// ----------------------------
// ----------------------------
// Global Variables & Initiation
// ----------------------------
// Wireless Setup
// These are the interrupt and control pins
#define ADAFRUIT_CC3000_IRQ   3  // MUST be an interrupt pin!
// These can be any two pins
#define ADAFRUIT_CC3000_VBAT  5
#define ADAFRUIT_CC3000_CS    10
// On an UNO, SCK = 13, MISO = 12, and MOSI = 11
Adafruit_CC3000 cc3000 = Adafruit_CC3000(ADAFRUIT_CC3000_CS, ADAFRUIT_CC3000_IRQ, ADAFRUIT_CC3000_VBAT,SPI_CLOCK_DIV2); // you can change this clock speed but DI
#define WLAN_SSID "YOURSSID"        // cannot be longer than 32 characters!
#define WLAN_PASS "YOURPASS"
// Security can be WLAN_SEC_UNSEC, WLAN_SEC_WEP, WLAN_SEC_WPA or WLAN_SEC_WPA2
#define WLAN_SECURITY WLAN_SEC_WPA2
#define IDLE_TIMEOUT_MS 3000      // Amount of time to wait (in milliseconds) with no data 

// ----------------------------
// Website Setup
#define WEBSITE      "YOUR SITE FOR TEMP COMMAND DATA"
#define WEBPAGE      "/tdata.html"

// ----------------------------
// Xively Setup
#define XWEBSITE  "api.xively.com"
#define API_key  "YOUR API KEY"
#define feedID  "YOUR FEED ID"

// ----------------------------
// Misc
#define DELAY 900000
int TEXIST=68;

// ----------------------------
// DHT Setup
#define DHTPIN A0     // what pin we're connected to
#define DHTTYPE DHT11
dht11 DHT11;

// ----------------------------
Servo myservo;
int SPOS=60;
#define SPIN 4


// ----------------------------
// ----------------------------

void setup(void){
  Serial.begin(9600);
  myservo.attach(SPIN);
  myservo.write(SPOS);
  delay(1000);
  myservo.detach();
}

// ----------------------------
// ----------------------------

void loop(void){
  Serial.print(F("Existing Temperature: "));
  Serial.print(TEXIST);
  Serial.println(F(" deg F"));
  initWifi();
  deleteOldConn();
  connectWifi();
  requestDCHP();
  // Get Commanded Temperature
  int treq=getTemp();
  // Check Against Existing Temperature, Command Servo
  if(treq!=TEXIST){
    myservo.attach(SPIN);
    changeTemp(treq);
    delay(1000);
    myservo.detach();
  }
  // Update Xively
  updateXively();
  // Close Wifi Connection
  closeWifi();
  // Delay
  delay(DELAY);
}

void initWifi(void){
  Serial.println(F("\nInitialising the CC3000 ..."));
  if (!cc3000.begin())
  {
    Serial.println(F("Unable to initialise the CC3000! Check your wiring?"));
    while(1);
  }
}

void deleteOldConn(void){
  /* Delete any old connection data on the module */
  Serial.println(F("\nDeleting old connection profiles"));
  if (!cc3000.deleteProfiles()) {
    Serial.println(F("Failed!"));
    while(1);
  }
}

void connectWifi(void){
  /* Attempt to connect to an access point */
  char *ssid = WLAN_SSID;             /* Max 32 chars */
  Serial.print(F("\nAttempting to connect to ")); 
  Serial.println(ssid);
  if (!cc3000.connectToAP(WLAN_SSID, WLAN_PASS, WLAN_SECURITY)) {
    Serial.println(F("Failed!"));
    while(1);
  }

  Serial.println(F("Connected!"));
  Serial.println(F("\n"));
}

void requestDCHP(){
  /* Wait for DHCP to complete */
  Serial.println(F("Requesting DHCP..."));
  while (!cc3000.checkDHCP())
  {
    delay(100); // ToDo: Insert a DHCP timeout!
  }  
  Serial.println(F("DCHP Done."));
  Serial.println(F("\n\n"));

}

int getTemp(){
  uint32_t ip = 0;
  String readString = "";
  Serial.println(F("Connecting to website..."));
  while  (ip  ==  0)  {
    if  (!  cc3000.getHostByName(WEBSITE, &ip))  {
      Serial.println(F("Couldn't resolve!"));
    }
    delay(500);
  }  
  cc3000.printIPdotsRev(ip);  

  Adafruit_CC3000_Client www = cc3000.connectTCP(ip, 80);

  Serial.println(F("Text Return:"));
  if (www.connected()) {
    www.fastrprint(F("GET "));
    www.fastrprint(WEBPAGE);
    www.fastrprint(F(" HTTP/1.1\r\n"));
    www.fastrprint(F("Host: ")); 
    www.fastrprint(WEBSITE); 
    www.fastrprint(F("\r\n"));
    www.fastrprint(F("\r\n"));
    www.println();
  }
  Serial.println(F("-------------------------------------"));

  /* Read data until either the connection is closed, or the idle timeout is reached. */
  unsigned long lastRead = millis();
  while (www.connected() && (millis() - lastRead < IDLE_TIMEOUT_MS)) {
    while (www.available()) {
      char c = www.read();
      Serial.print(c);      //read char by char HTTP request
      readString+=c; 
      lastRead = millis();
    }
  }
  www.close();
  Serial.println(F("-------------------------------------"));
  Serial.println(F("\n"));
  //Find temperautre in the data based on <<
  int index = readString.indexOf('<<');
  String tempstr = readString.substring(index+2, index+4);
  int tempcomm = tempstr.toInt();
  Serial.print("Commanded temp in int: ");
  Serial.println(tempcomm);
  Serial.println(F("-------------------------------------"));
  return tempcomm;
}

void changeTemp(int treq){
  if (treq!=TEXIST){
    //Command Servo
    delay(1000);
    Serial.print(F("Command Servo to "));
    Serial.println(treq);
    myservo.write(110);
    delay(1000);
    switch(treq){
    case 58:
      myservo.write(110);
      break;
    case 60:
      myservo.write(100);
      break;
    case 62:
      myservo.write(90);
      break;
    case 64:
      myservo.write(80);
      break;
    case 66:
      myservo.write(70);
      break;
    case 68:
      myservo.write(60);
      break;
    case 70:
      myservo.write(50);
      break;
    case 0:
      myservo.write(100);
      break;
    }
    TEXIST=treq;
  }
  else
    Serial.println(F("No change in temp, no servo command"));
  Serial.println(F("\n"));

}

void updateXively(){

  uint32_t ip = 0;
  
  Serial.print(F("Humidity is: ")); Serial.println((float)h,2);
  Serial.print(F("Temp is: ")); Serial.println((float)t,2);
  
  int length = 0;
  String data = "";
  data = data + "\n" + "{\"version\":\"1.0.0\",\"datastreams\" : [ {\"id\" : \"TCommand\",\"current_value\" : \"" + String((int)((TEXIST-32)*5/9)) + "\"}," 
    + "{\"id\" : \"Temperature\",\"current_value\" : \"" + String(t) + "\"}," 
    + "{\"id\" : \"Humidity\",\"current_value\" : \"" + String(h) + "\"}]}";

  Serial.println(F("-------------------------------------"));
  Serial.println(F("Connecting to Xively website..."));
  while  (ip  ==  0)  {
    if  (!  cc3000.getHostByName(XWEBSITE, &ip))  {
      Serial.println(F("Couldn't resolve!"));
    }
    delay(500);
  }  
  cc3000.printIPdotsRev(ip);  
  length = data.length();
  Adafruit_CC3000_Client client = cc3000.connectTCP(ip, 80);
  if (client.connected()) {
    Serial.println("Connected!");
    client.println("PUT /v2/feeds/" + String(feedID) + ".json HTTP/1.0");
    client.println("Host: api.xively.com");
    client.println("X-ApiKey: " + String(API_key));
    client.println("Content-Length: " + String(length));
    client.print("Connection: close");
    client.println();
    client.print(data);
    client.println();
  }

  while (client.connected()) {
    while (client.available()) {
      char c = client.read();
      Serial.print(c);
    }
  }
  Serial.println(F("Xively update complete"));
  client.close();
}

void closeWifi(void){
  Serial.println(F("\nCC3000 disconnected"));
  cc3000.disconnect();
}



