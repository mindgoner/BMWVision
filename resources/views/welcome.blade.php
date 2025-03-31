<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    <link rel="stylesheet" href="/css/leaflet.css" />
    <style>
        html{
            margin: 0;
            padding: 0;
            position: absolute;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
        }
        body {
            margin: 0;
            padding: 0;
            background-color: #000;
            color: #fff;
        }
        #container{
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        #left {
            position: absolute;
            top: 0;
            left: 0;
            width: 70%;
            height: 100%;
            background-color: #f4f4f4;
        }
        #right {
            position: absolute;
            top: 0;
            right: 0;
            width: 30%;
            height: 100%;
        }
        #map {
            width: 100%;
            height: calc(100% - 180px);
            
        }
        #camera {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        #arrowCanvas {
            position: absolute;
            bottom: 0%;
            left: 50%;
            width: 400px;
            pointer-events: none; /* Umożliwia interakcję z mapą pod sceną 3D */
            /* Testowo: */ border: 1px solid red;
            z-index: 100;
            transform: translate(-50%, 0);
        }

        #demoImage{
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 50;
        }
    </style>
</head>
<body>
    <div id="container">
        <!-- Lewa część - obraz z kamery -->
        <div id="left">
            <video id="camera" autoplay></video>
            <img id="demoImage" src="/img/demo.jpg" />
        </div>

        <!-- Prawa część - mapa Leaflet -->
        <div id="right">
            <div id="map"></div>
            <table style="width: 100%;">
                <tr>
                    <td><label for="camposx" id="camPosX">X:</label></td>
                    <td><input type="range" id="camposx" onchange="moveCam();" name="volume" value="0" min="0" max="3" step="0.1" /></td>
                </tr>
                <tr>
                    <td><label for="camposy" id="camPosY">Y:</label></td>
                    <td><input type="range" id="camposy" onchange="moveCam();" name="volume" value="1" min="0" max="3" step="0.1" /></td>
                </tr>
                <tr>
                    <td><label for="camposz" id="camPosZ">Z:</label></td>
                    <td><input type="range" id="camposz" onchange="moveCam();" name="volume" value="2.5" min="0" max="3" step="0.1" /></td>
                </tr>
                <tr>
                    <td><label for="camrotx" id="camRotX">RotX:</label></td>
                    <td><input type="range" id="camrotx" onchange="moveCam();" name="volume" value="-0.5" min="-3" max="3" step="0.1" /></td>
                </tr>
                <tr>
                    <td><label for="camroty" id="camRotY">RotY:</label></td>
                    <td><input type="range" id="camroty" onchange="moveCam();" name="volume" value="0" min="-3" max="3" step="0.1" /></td>
                </tr>
                <tr>
                    <td><label for="camrotz" id="camRotZ">RotZ:</label></td>
                    <td><input type="range" id="camrotz" onchange="moveCam();" name="volume" value="0" min="-3" max="3" step="0.1" /></td>
                </tr>
            </table>

        </div>
    </div>

    <script src="/js/leaflet.js"></script>
    <script>

        // TUTAJ JEST GEOLOKALIZACJA

        // Zmienna z danymi geolokalizacyjnymi
        let latitude = 52.2297;  // Przykładowa szerokość geograficzna
        let longitude = 21.0122; // Przykładowa długość geograficzna

        // Inicjalizacja kamery
        const camera = document.getElementById('camera');
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(stream => {
                camera.srcObject = stream;
            })
            .catch(error => {
                console.error("Błąd dostępu do kamery: ", error);
            });

        // Inicjalizacja mapy Leaflet
        const map = L.map('map').setView([latitude, longitude], 13); // Ustawienie mapy na początkową pozycję

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        const marker = L.marker([latitude, longitude]).addTo(map);

        // Funkcja do odświeżania pozycji co 1 sekundę
        setInterval(() => {
            marker.setLatLng([latitude, longitude]); // Ustawienie nowej pozycji markera
            map.setView([latitude, longitude]); // Ustawienie nowej pozycji widoku mapy
        }, 1000);

        // Przykład aktualizacji zmiennych latitude i longitude
        setInterval(() => {
            // Tu możesz ustawić nowe wartości zmiennych latitude i longitude
            latitude += 0.0008; // Zmiana szerokości geograficznej
            longitude += 0.0008; // Zmiana długości geograficznej
        }, 1000);
    </script>

    <!-- Kontener na scenę 3D -->
    <script src="/js/three.js"></script>
    <script src="/js/GLTFLoader.js"></script>

    <script>

        // A TUTAJ JEST STRZAŁECZKA!

        // Ustawienia sceny 3D
        const scene = new THREE.Scene();
        const scenecam = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
        
        // Utworzenie renderera z przezroczystym tłem
        const renderer = new THREE.WebGLRenderer({ alpha: true });  // Dodajemy alpha: true, aby tło było przezroczyste
        renderer.setSize("400", "216");
        renderer.domElement.id = "arrowCanvas";
        const container = document.getElementById("left");
        container.appendChild(renderer.domElement);

        // Dodanie światła
        const light = new THREE.AmbientLight(0xffffff, 1);
        scene.add(light);

        // Ładowanie modelu .glb (możesz zmienić na .gltf, .fbx, .bin)
        const loader = new THREE.GLTFLoader();

        loader.load(
            '/obj/arrow.glb', // Zamień na ścieżkę do swojego pliku
            function (gltf) {
                const model = gltf.scene;
                scene.add(model);
                model.scale.set(1, 1, 1); // Skalowanie modelu (dopasowanie do sceny)

                // Animacja obrotu modelu wokół osi Y (pionowej)
                function animate() {
                    requestAnimationFrame(animate);
                    model.rotation.y += 0.01; // Zmieniaj wartość, aby dostosować prędkość obrotu
                    renderer.render(scene, scenecam);
                }
                animate();
            },
            function (xhr) {
                // Sprawdzenie, czy wartość 'xhr.total' jest poprawna przed wyświetleniem postępu
                if (xhr.total > 0) {
                    console.log((xhr.loaded / xhr.total * 100) + '% załadowane');
                } else {
                    console.log('Ładowanie w toku...');
                }
            },
            function (error) {
                // Obsługa błędów ładowania
                console.error('Błąd ładowania modelu:', error);
                alert('Wystąpił błąd podczas ładowania modelu. Sprawdź konsolę w narzędziach developerskich!');
            }
        );

        // Ustawienie kamery
        scenecam.position.z = 2.5;
        scenecam.position.y = 1;
        scenecam.rotation.x = -0.5; // Ustawienie kąta kamery

        // Move cam (test only)
        function moveCam(){
            moveX = document.getElementById("camposx").value;
            moveY = document.getElementById("camposy").value;
            moveZ = document.getElementById("camposz").value;
            moveRotX = document.getElementById("camrotx").value;
            moveRotY = document.getElementById("camroty").value;
            moveRotZ = document.getElementById("camrotz").value;
            scenecam.position.x = moveX;
            scenecam.position.y = moveY;
            scenecam.position.z = moveZ;
            scenecam.rotation.x = moveRotX;
            scenecam.rotation.y = moveRotY;
            scenecam.rotation.z = moveRotZ;

            document.getElementById("camPosX").innerHTML = "X: " + moveX;
            document.getElementById("camPosY").innerHTML = "Y: " + moveY;
            document.getElementById("camPosZ").innerHTML = "Z: " + moveZ;
            document.getElementById("camRotX").innerHTML = "RotX: " + moveRotX;
            document.getElementById("camRotY").innerHTML = "RotY: " + moveRotY;
            document.getElementById("camRotZ").innerHTML = "RotZ: " + moveRotZ;

            // console.log("X: " + moveX + " Y: " + moveY + " Z: " + moveZ);
            // console.log("RotX: " + moveRotX + " RotY: " + moveRotY + " RotZ: " + moveRotZ);
        }
    </script>

</body>
</html>
