# BMWVision: GPS- und Geräteorientierungsvisualisierungs-App

Die **BMWVision**-App dient der Analyse des GPS-Standorts und der Ausrichtung des Geräts, um die Fahrtrichtung auf der Kameraansicht in Echtzeit zu visualisieren.

## Beschreibung

Die **BMWVision**-App nutzt GPS-Daten sowie die Sensoren des Geräts (Gyroskop und Beschleunigungsmesser), um die aktuelle Fahrtrichtung zu bestimmen. Basierend auf diesen Daten wird auf dem Kamerabild ein Indikator angezeigt, der die aktuelle Fahrtrichtung des Geräts zeigt.

## Funktionen

- **GPS-Standortanalyse**: Die App erfasst GPS-Daten, um den aktuellen Standort zu bestimmen.
- **Geräteorientierungsanalyse**: Mit Hilfe von Gyroskop und Beschleunigungsmesser wird die Ausrichtung des Geräts ermittelt.
- **Fahrtrichtungsvisualisierung**: Ein Indikator, der die Fahrtrichtung auf dem Kamerabild anzeigt, wird in Echtzeit über das Kamerabild gelegt.
- **Kameraintegration**: Die App ermöglicht es, den Richtungsindikator direkt auf dem Bild von der Kamera in Echtzeit anzuzeigen.
- **Benutzeroberfläche**: Eine benutzerfreundliche Oberfläche zur Konfiguration der App und zur Überwachung von GPS-Daten und Geräteaussrichtungen.

## Anforderungen

- Android 8.0 (API 26) oder neuer / iOS 12.0 oder neuer
- Zugriff auf GPS und Sensoren (Gyroskop, Beschleunigungsmesser)
- Gerätekamera
- Internetverbindung (für den Abruf von Karten- und Standortdaten)

## Installation

1. Klonen Sie das Repository auf Ihr Gerät:

    ```bash
    git clone https://github.com/username/bmwvision.git
    ```

2. Installieren Sie die erforderlichen Abhängigkeiten:

    - Für Android:
    
        - Öffnen Sie das Projekt in Android Studio und führen Sie `gradle sync` aus, um die Abhängigkeiten zu installieren.
    
    - Für iOS:
    
        - Öffnen Sie das Projekt in Xcode und führen Sie `pod install` aus, um die Abhängigkeiten zu installieren.

3. Stellen Sie sicher, dass die erforderlichen Berechtigungen für den Zugriff auf GPS, Kamera und Sensoren erteilt sind:

    - Android: Fügen Sie die entsprechenden Berechtigungen in der `AndroidManifest.xml` hinzu.
    - iOS: Aktualisieren Sie die `Info.plist`, um Zugriff auf GPS und Kamera zu erhalten.

## Verwendung

1. Starten Sie die App auf Ihrem mobilen Gerät.
2. Die App erfasst automatisch GPS-Daten und die Ausrichtung des Geräts.
3. Auf dem Bildschirm wird das Kamerabild angezeigt, zusammen mit einem dynamischen Richtungsindikator, der sich basierend auf den GPS-Daten und den Sensordaten aktualisiert.
4. Sie können die App-Einstellungen anpassen, wie zum Beispiel die Empfindlichkeit des Indikators oder den Kartenmodus, im Einstellungsmenü.

## Beispielansichten

- **Kamerabild mit Fahrtrichtungsindikator**: Auf dem Kamerabild wird ein dynamischer Indikator angezeigt, der die Fahrtrichtung des Geräts anzeigt.
- **Standortansicht auf der Karte**: Möglichkeit, den aktuellen Standort auf einer Karte im Hintergrund anzuzeigen.

## Technologien

- **GPS**: Wird verwendet, um die geografischen Koordinaten des Geräts zu bestimmen.
- **Gyroskop und Beschleunigungsmesser**: Zur Messung der Geräteorientierung.
- **Kamera**: Wird verwendet, um das Bild in Echtzeit zu erfassen.
- **Karten**: Zeigt den Standort basierend auf den GPS-Daten an.
- **OpenGL/Metal**: Wird zur Darstellung des Indikators und zum Überlagern des Kamerabildes verwendet.

## Ressourcen

- [Android GPS Dokumentation](https://developer.android.com/guide/topics/location)
- [iOS Core Location Dokumentation](https://developer.apple.com/documentation/corelocation)
- [Google Maps API](https://developers.google.com/maps/documentation)

## Lizenz

Die App ist unter der MIT-Lizenz verfügbar. Weitere Details finden Sie in der [LICENSE](LICENSE)-Datei.
