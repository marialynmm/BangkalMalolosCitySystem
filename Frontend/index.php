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
            background: url('images/bg.png') no-repeat center center/cover;
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
    </style>
</head>

<body>

    <!-- Welcome Message -->
    <div class="welcome-message">
        <img src="images/logo.png" alt="Barangay Bangkal" />
        <h1>Welcome to Barangay Bangkal</h1>
        <p>Malolos, Bulacan</p>
    </div>

    <!-- Login Button -->
    <button class="login-btn" id="openModalBtn">Login</button>

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