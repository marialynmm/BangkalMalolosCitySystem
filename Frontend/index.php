<?php
$error_message = isset($_SESSION['login_error']) ? $_SESSION['login_error'] : '';
if (!empty($error_message)) {
    unset($_SESSION['login_error']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/login.css">
    <title>Barangay Bangkal - Welcome</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: white;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.5);
        }

        .error-message {
            color: red;
            font-weight: bold;
            margin-top: 10px;
            margin-bottom: 20px;
        }

        @keyframes slideUp {
            from {
                transform: translateY(100%);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal {
            display: none;
            /* Hide by default */
            animation: slideUp 0.5s ease forwards;
            /* Apply the animation */
        }

        .modal.show {
            display: flex;
            /* Show modal */
        }

        .welcome-message {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            /* Center the message */
            text-align: center;
            /* Center text */
        }

        .pause-btn {
            position: fixed;
            bottom: 20px;
            /* Adjust as needed */
            right: 20px;
            /* Adjust as needed */
            padding: 10px 20px;
            /* Button padding */
            background-color: rgba(0, 0, 0, 0.7);
            /* Semi-transparent background */
            color: white;
            /* Text color */
            border: none;
            /* No border */
            border-radius: 5px;
            /* Rounded corners */
            cursor: pointer;
            /* Pointer cursor */
            font-size: 16px;
            /* Font size */
            z-index: 1000;
            /* Ensure it appears above other elements */
        }
    </style>
</head>

<body>

    <!-- Welcome Message -->
    <div class="welcome-message">
        <img src="images/logo.png" alt="Barangay Bangkal" />
        <h1>Welcome to Barangay Bangkal</h1>
        <p>Malolos, Bulacan</p>
        <!-- Login Button -->
        <br>
        <button class="login-btn" id="openModalBtn">Login</button>
    </div>
    <button class="pause-btn" id="pauseMapBtn">Pause Map</button>


    <div id="map-container"></div>

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

    <!-- Modal -->
    <div class="modal" id="loginModal" style="display: <?php echo !empty($_GET['error']) ? 'flex' : 'none'; ?>;">
        <div class="modal-content">
            <button class="close-btn" id="closeModalBtn">&times;</button>
            <h1>Login</h1>
            <form action="../Backend/login.php" method="post">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Enter your username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>
                <?php if (!empty($error_message)): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
                <?php endif; ?>
                <button type="submit" class="login-btn">Login</button>
            </form>
        </div>
    </div>

    <script>
        // Login Open modal
        document.addEventListener('DOMContentLoaded', () => {
            const openModalBtn = document.getElementById('openModalBtn');
            const closeModalBtn = document.getElementById('closeModalBtn');
            const loginModal = document.getElementById('loginModal');

            openModalBtn.addEventListener('click', () => {
                loginModal.classList.add('show'); // Add show class to display modal
                loginModal.style.display = 'flex'; // Set display to flex
            });

            closeModalBtn.addEventListener('click', () => {
                loginModal.style.display = 'none';
                loginModal.classList.remove('show'); // Remove show class when closing
            });

            window.addEventListener('click', (e) => {
                if (e.target === loginModal) {
                    loginModal.style.display = 'none';
                    loginModal.classList.remove('show'); // Remove show class when closing
                }
            });
        });
    </script>
</body>

</html>