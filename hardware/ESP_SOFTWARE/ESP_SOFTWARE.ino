#include <ESP8266WiFi.h>
#include <ESP8266WebServer.h>
#include <ArduinoJson.h>  // Biblioteka do obsługi JSON

const char* ssid = "Minerva";  // Wpisz nazwę swojej sieci Wi-Fi
const char* password = "La'R0s3s";  // Wpisz hasło do swojej sieci Wi-Fi

ESP8266WebServer server(80);  // Tworzymy serwer HTTP na porcie 80

#define BUFFER_SIZE 2000  // Pamiętamy ostatnie 2000 znaków
char buffer[BUFFER_SIZE + 1] = {0};  // Bufor na dane JSON
int bufferIndex = 0;  // Aktualna pozycja w buforze

void setup() {
  Serial.begin(115200);
  WiFi.begin(ssid, password);

  // Czekamy na połączenie z Wi-Fi
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.println("Łączenie z Wi-Fi...");
  }
  Serial.println("Połączono z Wi-Fi!");

  // Endpoint zwracający dane JSON
  server.on("/", HTTP_GET, []() {
    StaticJsonDocument<2048> doc;
    doc["bufor"] = buffer;  // Wrzucamy zawartość bufora do JSON-a

    String jsonResponse;
    serializeJson(doc, jsonResponse);  // Konwertujemy JSON do stringa
    server.send(200, "application/json", jsonResponse);
  });

  // Rozpoczynamy serwer
  server.begin();
}

void loop() {
  server.handleClient();

  // Odczytujemy dane z portu szeregowego
  while (Serial.available()) {
    char c = Serial.read();

    // Jeśli bufor się przepełnił, przesuwamy dane w lewo
    if (bufferIndex >= BUFFER_SIZE - 1) {
      memmove(buffer, buffer + 1, BUFFER_SIZE - 1);
      bufferIndex = BUFFER_SIZE - 1;
    }

    // Dodajemy nowy znak do bufora
    buffer[bufferIndex++] = c;
    buffer[bufferIndex] = '\0';  // Zakończenie stringa
  }
}
