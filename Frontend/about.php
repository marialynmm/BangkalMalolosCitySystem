<!DOCTYPE html>
<html lang="en">
<?php include '../Backend/session.php'; ?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Frontend/css/fontawesome-free-6.6.0-web/css/all.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/about.css">
    <link rel="icon" href="images/logo.png" type="image/x-icon">

    <title>Dashboard</title>
    <style>
        body {
            margin: 0;
            overflow: hidden;
            /* Prevent scrolling */
        }
    </style>
</head>

<body>
    <div id="map-container"></div>
    <button class="pause-btn" id="pauseMapBtn">Pause Map</button>

    <div class="overlay">
        <div class="title">
            <img src="images/logo.png" alt="Barangay Bangkal" />
            Barangay Officials
        </div>

        <div class="title">
            <img src="images/sk.png" alt="Barangay Bangkal" />
            Sangguniang Kabataan
        </div>
    </div>

    <script src="../Frontend/scripts/three.min.js"></script>
    <script>
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(15, window.innerWidth / window.innerHeight, 0.1, 1000);
        const renderer = new THREE.WebGLRenderer({
            antialias: true,
            alpha: true
        });
        renderer.setClearColor(0x000000, 0); // Transparent background

        const mapTexture = new THREE.TextureLoader().load('images/map.jpg');
        const mapGeometry = new THREE.PlaneGeometry(10, 10);
        const mapMaterial = new THREE.MeshBasicMaterial({
            map: mapTexture
        });
        const mapMesh = new THREE.Mesh(mapGeometry, mapMaterial);

        // Rotate the map to lay flat
        mapMesh.rotation.x = -Math.PI / 2;
        scene.add(mapMesh);

        // Set initial camera position
        camera.position.set(0, 5, 10);
        camera.lookAt(0, 0, 0);

        renderer.setSize(window.innerWidth, window.innerHeight);
        document.getElementById('map-container').appendChild(renderer.domElement);

        // Animation variables
        let angle = 0;
        const radius = 7;
        let isPaused = false; // Track whether the animation is paused

        // Animation function
        function animate() {
            requestAnimationFrame(animate);

            if (!isPaused) {
                // Update angle to create circular motion
                angle += 0.0001;
                camera.position.x = radius * Math.cos(angle);
                camera.position.z = radius * Math.sin(angle);
                camera.position.y = 5;
            }

            camera.lookAt(0, 0, 0);
            renderer.render(scene, camera);
        }

        // Start animation
        animate();

        // Handle window resize
        window.addEventListener('resize', () => {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
        });

        // Pause/Resume functionality
        document.getElementById('pauseMapBtn').addEventListener('click', () => {
            isPaused = !isPaused; // Toggle the pause state
            const buttonText = isPaused ? 'Resume Map' : 'Pause Map';
            document.getElementById('pauseMapBtn').textContent = buttonText; // Update button text
        });
    </script>
    <footer>
        <p>© 2024 Barangay Bangkal Lungsod ng Malolos Bulacan. All rights reserved.</p>
        <div>
            <a href="https://maloloscity.gov.ph/barangay-bangkal/" target="_blank" style="color: gray; text-decoration: none;">More Information</a>
        </div>
    </footer>

    <?php include 'includes/sidebar.php'; ?>
</body>

</html>