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
1. Pobierz repozytorium, wpisując poniższe w interesujacym Cię miejscu (możesz dodać kropkę na końcu, żeby wypakować w tym samym folderze):
```bash
git clone https://github.com/mindgoner/BMWVision.git
```
2. Przejdź do folderu z projektem i zainstaluj zależności composera:
```bash
composer update
```
3. Skopiuj plik .env.example do .env
4. Uruchom serwer poleceniem
```bash
php artisan serve
```
6. W przeglądarce przejdź na adres http://127.0.0.1:8000.


## Wymagania
- [Git](https://git-scm.com/downloads)
- [Composer](https://getcomposer.org/)
- PHP 8.2 ^
- Przeglądarka obsługująca WebRTC i WebGL (prawie każda)
- Dostęp do kamery dla pełnej funkcjonalności.
- Połączenie z Internetem do załadowania mapy OpenStreetMap.

## Autorzy
[Bartosz Bieniek](https://github.com/mindgoner)
[Dawid Klimek](https://github.com/skruty)
[Mariusz Jagosz](https://github.com/mariuszjagosz)
