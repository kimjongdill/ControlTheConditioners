#include <ESP8266HTTPClient.h>
#include <ESP8266WiFi.h>
#include <IRsend.h>
#include <ArduinoJson.h>

IRsend irsend(4);

// Temperature Reading Variables
int tempPin = A0;
float tempF = 0.0;

// IR Control Variables
#define POWER 0x0010AF8877
#define FAN_SLOWER 0x0010AF20DF
#define FAN_FASTER 0x0010AF807F
#define FAN_AUTO 0x0010AFF00F
#define TEMP_UP 0x0010AF708F
#define TEMP_DOWN 0x0010AFB04F

// Wifi Information

#define networkID "Taco Tuesday"
#define networkPass "LiveMas4"
#define networkSec "AES"

// API Information
#define API_KEY "" //API Key here
#define DELAY_NORMAL 300000
#define DELAY_ERROR 1200000

// AC State Information
int currTemp = 60;
int lastTemp = 60;
int currFan = 3;
int currState = 1;

// Desired State Information

String jsonSettings;
int desTemp;
int desState;
int desFan;

// Sending Temperature Information
// API Information
char* URL = ""; // URL for REST Interface
// For Main room Thermostat
String room = "main";

// Connect to website and get desired state
// ToDo: Add error handling
void getDesiredState(){
  StaticJsonBuffer<1000> jsonBuffer;
  // Diagnostic
  //Serial.println("Connecting to georgedill.net");
  // Signal that its connecting
  digitalWrite(0, HIGH);
  // Open a connection object
  HTTPClient http;
  http.begin(""); //URL for REST Interface
  // Post a get request to the website
  http.GET();

  // Read the payload
  jsonSettings = http.getString();
  //Serial.println(jsonSettings);

  // Close the connection
  http.end();
  digitalWrite(0, LOW);
  
  JsonArray &root = jsonBuffer.parseArray(jsonSettings);

  if(!root.success()){
    //Serial.println("JSON Parsing Failed");
    return;
  }
  
  desTemp = root[0][String("desTemp")];
  desState = root[0][String("desState")];
  desFan = root[0][String("desFan")];



}

void readTemp(){

  int readVal;
  float voltage;
  float tempC;
  readVal = analogRead(tempPin);
  voltage = (float) readVal * 1000/1023;
  tempC = (voltage - 424) / 6.25;
  tempF = tempC * 1.8 + 32;
  currTemp = tempF;
  //Serial.print("The room temp is: ");
  //Serial.println(currTemp);

  String postText = String("currentTemp=");
  postText = postText + tempF;
  postText = postText + "&room=";
  postText = postText + room;
  
  HTTPClient http;
  http.begin(URL);
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");
  http.POST(postText);
  http.writeToStream(&Serial);
  String payload = http.getString();
  
  //Serial.println(httpCode);
  //Serial.println(payload);
  http.end();

  
  return;
}

void initializeUnit(){

  // Turn unit on
  //Serial.println("power");
  //Serial.println(POWER, HEX);
  irsend.sendNEC(POWER, 32);
  delay(500);

  
  // Turn thermostat to min
  for(int i=0; i<30; i++){
    //Serial.println("Thermo Down");
    irsend.sendNEC(TEMP_DOWN, 32);
    delay(500);
  }

  // Turn fan to max
  for(int i=0; i<3; i++){
    //Serial.println("Fan Up");
    irsend.sendNEC(FAN_FASTER, 32);
    delay(500);
  }
  
}

void setFanSpeed(){

  if(currFan == desFan){
    return;
  }

  if(desFan == 2){
    irsend.sendNEC(FAN_AUTO, 32);
    currFan = 2;
    return;
  }

  if(desFan == 1){
    irsend.sendNEC(FAN_FASTER, 32);
    irsend.sendNEC(FAN_FASTER, 32);
    currFan = 1;
    return;
  }

  if(desFan == 0){
    irsend.sendNEC(FAN_SLOWER, 32);
    irsend.sendNEC(FAN_SLOWER, 32);
    currFan = 0;
    return;
  }
  
}

// Do thermostat functions
void thermoControl(){
  // Situations to turn the conditioner on
    // If desState is on, curr state is off, currTemp > desTemp
  
  if(desState == 1 && currState == 0){
    //Serial.println("Turning on");
    irsend.sendNEC(POWER, 32);
    currState = 1;
  }

  // Situations to turn the conditioner off
    // If desState is off and curr state is on
    // If desState is on and currTemp < desTemp

  if(desState == 0 && currState == 1){
    //Serial.println("Turning off - user shutdown");
    irsend.sendNEC(POWER, 32);
    currState = 0;
  }

  // Set temperature of unit

  while(lastTemp != desTemp){
    delay(500);
    if(desTemp > lastTemp){
      lastTemp++;
      irsend.sendNEC(TEMP_UP, 32);
      continue;
    }

    if(desTemp < lastTemp){
      lastTemp--;
      irsend.sendNEC(TEMP_DOWN, 32);
      continue;
    }
  }
  
}

void setup()
{
  /* Comment out displays to serial port
  Serial.begin(9600);
  delay(100);

  // Connect to Wifi
  Serial.println();
  Serial.println();
  Serial.print("Connecting to ");
  Serial.println(networkID);
  */

  pinMode(0, OUTPUT);
  digitalWrite(0, HIGH);
  
  WiFi.begin(networkID, networkPass);

  while(WiFi.status() != WL_CONNECTED){
    delay(500);
    //Serial.print(".");
  }
  /*
  Serial.println("");
  Serial.println("WiFi connected");
  Serial.println("IP address: ");
  Serial.println(WiFi.localIP());  
  */

  digitalWrite(0, LOW);
  
  // Initialize IR output pin
  pinMode(tempPin, INPUT);
  irsend.begin();
  initializeUnit();
}


void loop(){
  
  readTemp();
  getDesiredState();
  thermoControl();
  setFanSpeed();
  
  delay(60000);
}

