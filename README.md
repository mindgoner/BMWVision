# BMWVision

BMWVision to aplikacja webowa, która łączy obraz z kamery z nałożonym obiektem 3D (strzałką) wskazującym kierunek ruchu oraz mapę z lokalizacją użytkownika.

## Funkcjonalność
- **Widok po lewej stronie:**
  - Obraz z kamery użytkownika.
  - Nakładka w postaci modelu 3D strzałki, wskazującej kierunek ruchu.
- **Widok po prawej stronie:**
  - Mapa Leaflet z oznaczoną lokalizacją użytkownika.
  - Możliwość dynamicznej aktualizacji pozycji.
- **Interaktywne sterowanie kamerą w scenie 3D:**
  - Suwaki pozwalające zmieniać pozycję i orientację kamery 3D.

## Struktura kodu

### Pliki używane w projekcie:
- `index.html` - Główna struktura aplikacji.
- `leaflet.css` - Arkusz stylów do mapy.
- `leaflet.js` - Biblioteka Leaflet do obsługi mapy.
- `three.js` - Biblioteka Three.js do renderowania obiektu 3D.
- `GLTFLoader.js` - Loader do wczytywania modelu strzałki.
- `demo.jpg` - Przykładowy obraz testowy.
- `arrow.glb` - Model 3D strzałki.

### Struktura HTML
- `#left` - Lewa część ekranu (kamera + obiekt 3D)
- `#right` - Prawa część ekranu (mapa + suwaki do sterowania kamerą)
- `#camera` - Element `<video>` do obsługi strumienia z kamery.
- `#map` - Kontener na mapę Leaflet.
- `#arrowCanvas` - Canvas dla renderowania obiektu 3D.

### Skrypty JavaScript
#### **Geolokalizacja**
- Inicjalizacja mapy Leaflet na podstawie domyślnych współrzędnych.
- Dodanie markera lokalizacji.
- Dynamiczne odświeżanie pozycji co sekundę.

#### **Kamera i model 3D**
- Pobieranie dostępu do kamery użytkownika.
- Tworzenie sceny Three.js z kamerą i światłem.
- Wczytanie modelu strzałki (`arrow.glb`) i ustawienie animacji obrotu.
- Funkcja `moveCam()` pozwala na dynamiczną zmianę pozycji i orientacji kamery 3D za pomocą suwaków.

## Instalacja i uruchomienie
1. Pobierz repozytorium.
2. Umieść pliki na serwerze.
3. Upewnij się, że masz dostęp do kamery oraz plików `three.js`, `leaflet.js`, `GLTFLoader.js`.
4. Otwórz `index.html` w przeglądarce.

## Wymagania
- Przeglądarka obsługująca WebRTC i WebGL.
- Dostęp do kamery dla pełnej funkcjonalności.
- Połączenie z Internetem do załadowania mapy OpenStreetMap.

## Autor
Projekt BMWVision.
