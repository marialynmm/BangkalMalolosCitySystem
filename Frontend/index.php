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
    <div class="modal" id="loginModal">
        <div class="modal-content">
            <button class="close-btn" id="closeModalBtn">&times;</button>
            <h1>Login</h1>
            <form>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" placeholder="Enter your username">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" placeholder="Enter your password">
                </div>
                <button type="submit" class="login-btn" id="login">Login</button>
            </form>
        </div>
    </div>

    <script>
        //Login
        document.getElementById('login').addEventListener('click', function (event) {
            // Prevent form submission if you want to use this button outside a form
            event.preventDefault();

            // Redirect to the Dashboard.html
            window.location.href = 'dashboard.php';
        });

        // Open modal
        const openModalBtn = document.getElementById('openModalBtn');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const loginModal = document.getElementById('loginModal');

        openModalBtn.addEventListener('click', () => {
            loginModal.style.display = 'flex';
        });

        closeModalBtn.addEventListener('click', () => {
            loginModal.style.display = 'none';
        });

        window.addEventListener('click', (e) => {
            if (e.target === loginModal) {
                loginModal.style.display = 'none';
            }
        });
    </script>
</body>

</html>