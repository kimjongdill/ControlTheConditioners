# ControlTheConditioners
A Web Enabled Remote Control for a Window Air Conditioner

The web enabled remote control works via a REST interface with my personal website to allow 
for control of the AC window unit via infrared. The project required three modules:

<UL>
<LI>Server Setup - LAMP Stack</LI>
<LI>PHP Scripting for REST API</LI>
<LI>Programming the ESP8266 with Arduino / Wiring</LI>
</UL>

For this project I relied heavily on: 
<UL>
<LI><A HREF="https://github.com/markszabo/IRremoteESP8266">Mark Szabo's IRSend Library</A></LI>
<LI><A HREF="https://github.com/esp8266/Arduino/blob/master/libraries/ESP8266HTTPClient/src/ESP8266HTTPClient.h">Marcus Sattler's HTTPClient Library</A></LI>
<LI><A HREF="http://coreymaynard.com/blog/creating-a-restful-api-with-php/"> Corey Maynard's blog post on RESTful APIs in PHP</A></LI>
</UL>
