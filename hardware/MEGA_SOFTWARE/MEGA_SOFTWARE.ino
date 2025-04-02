#include <ArduinoJson.h>

void setup() {
  Serial.begin(115200);
  Serial3.begin(115200);
  Serial2.begin(9600);
}

void loop() {
  while (Serial2.available()) {
    String line = Serial2.readStringUntil('\n');
    
    // $GPGSV processing
    if (line.startsWith("$GPGSV")) {
      // Create a JSON object
      StaticJsonDocument<200> doc;

      // Split the sentence into parts
      int fieldIndex = 0;
      char* token = strtok(line.c_str(), ",");
      
      // GPGSV-specific fields
      while (token != NULL) {
        fieldIndex++;
        
        if (fieldIndex == 2) {
          // Number of messages
          doc["GPGSV"]["message_count"] = atoi(token);
        }
        else if (fieldIndex == 3) {
          // Message number
          doc["GPGSV"]["message_number"] = atoi(token);
        }
        else if (fieldIndex == 4) {
          // Number of satellites in view
          doc["GPGSV"]["satellites_in_view"] = atoi(token);
        }
        else if (fieldIndex >= 5 && fieldIndex <= 5 + (4 * 3)) {
          // Satellite information: PRN, elevation, azimuth, SNR
          int satelliteIndex = (fieldIndex - 5) / 4;
          int subIndex = (fieldIndex - 5) % 4;
          if (subIndex == 0) {
            doc["GPGSV"]["satellites"][satelliteIndex]["prn"] = atoi(token);
          } else if (subIndex == 1) {
            doc["GPGSV"]["satellites"][satelliteIndex]["elevation"] = atoi(token);
          } else if (subIndex == 2) {
            doc["GPGSV"]["satellites"][satelliteIndex]["azimuth"] = atoi(token);
          } else if (subIndex == 3) {
            doc["GPGSV"]["satellites"][satelliteIndex]["snr"] = atoi(token);
          }
        }
        
        token = strtok(NULL, ",");
      }

      // Serialize to JSON string and print
      String jsonString;
      serializeJson(doc, jsonString);
      Serial3.println(jsonString); // Send to Serial3 (for example, another device)
      Serial.println(jsonString);  // Print to Serial Monitor
    }

    // $GPGLL processing
    else if (line.startsWith("$GPGLL")) {
      // Create a JSON object
      StaticJsonDocument<200> doc;

      // Split the sentence into parts
      int fieldIndex = 0;
      char* token = strtok(line.c_str(), ",");
      
      while (token != NULL) {
        fieldIndex++;
        
        if (fieldIndex == 2) {
          // Latitude
          doc["GPGLL"]["latitude"] = token;
        }
        else if (fieldIndex == 3) {
          // Latitude hemisphere (N/S)
          doc["GPGLL"]["latitude_hemisphere"] = token;
        }
        else if (fieldIndex == 4) {
          // Longitude
          doc["GPGLL"]["longitude"] = token;
        }
        else if (fieldIndex == 5) {
          // Longitude hemisphere (E/W)
          doc["GPGLL"]["longitude_hemisphere"] = token;
        }
        else if (fieldIndex == 6) {
          // Time (UTC)
          doc["GPGLL"]["time"] = token;
        }
        else if (fieldIndex == 7) {
          // Status (A=active, V=void)
          doc["GPGLL"]["status"] = token;
        }
        
        token = strtok(NULL, ",");
      }

      // Serialize to JSON string and print
      String jsonString;
      serializeJson(doc, jsonString);
      Serial3.println(jsonString); // Send to Serial3 (for example, another device)
      Serial.println(jsonString);  // Print to Serial Monitor
    }
  }
  delay(10);
}
