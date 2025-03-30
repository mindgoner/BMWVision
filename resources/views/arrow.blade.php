<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Obiekt 3D</title>
    <style>
        body { margin: 0; overflow: hidden; }
        canvas { display: block; }
    </style>
</head>
<body>
    <!-- Kontener na scenę 3D -->
    <script src="/js/three.js"></script>
    <script src="/js/GLTFLoader.js"></script>

    <script>
        // Ustawienia sceny 3D
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
        
        // Utworzenie renderera z przezroczystym tłem
        const renderer = new THREE.WebGLRenderer({ alpha: true });  // Dodajemy alpha: true, aby tło było przezroczyste
        renderer.setSize(window.innerWidth, window.innerHeight);
        renderer.domElement.id = "arrowCanvas";
        document.body.appendChild(renderer.domElement);

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
                    renderer.render(scene, camera);
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
        camera.position.z = 2.5;
        camera.position.y = 1;
        camera.rotation.x = -0.5; // Ustawienie kąta kamery

        // Responsywność (dopasowanie rozmiaru do okna)
        window.addEventListener('resize', () => {
            renderer.setSize(window.innerWidth, window.innerHeight);
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
        });
    </script>
</body>
</html>
