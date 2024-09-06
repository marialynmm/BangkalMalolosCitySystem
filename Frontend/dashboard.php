<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap">
    <link rel="stylesheet" href="css/dashboard.css">
    <script src="scripts/scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Dashboard</title>

    <style>

        .logo-stamp {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.05;
            /* Adjust transparency here */
            pointer-events: none;
            /* Ensures that the stamp doesn't interfere with user interactions */
        }

        .logo-stamp img {
            max-width: 100vw;
            /* Ensure the image scales with the viewport width */
            max-height: 100vh;
            /* Ensure the image scales with the viewport height */
            width: 500px;
            height: 500px;
        }
    </style>
</head>

<body>
    <div class="logo-stamp">
        <img src="images/logo.png" alt="Logo">
    </div>
    <div class="container">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Content -->
        <div class="content" id="content">
            <div class="main-content">

                <section class="dashboard">
                    <div class="card">
                        <div class="card-content">
                            <i class="icon"><i class="fa-solid fa-chart-pie"></i></i>
                            <div class="text-content">
                                <h3>Population</h3>
                                <p>32,450</p>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-content">
                            <i class="icon"><i class="fa-solid fa-mars"></i></i></i>
                            <div class="text-content">
                                <h3>Male</h3>
                                <p>32,450</p>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-content">
                            <i class="icon"><i class="fa-solid fa-venus"></i></i></i>
                            <div class="text-content">
                                <h3>Female</h3>
                                <p>32,450</p>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-content">
                            <i class="icon"><i class="fa-solid fa-check-to-slot"></i></i></i>
                            <div class="text-content">
                                <h3>Voters</h3>
                                <p>32,450</p>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-content">
                            <i class="icon"><i class="fa-solid fa-circle-xmark"></i></i></i>
                            <div class="text-content">
                                <h3>Non Voters</h3>
                                <p>32,450</p>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-content">
                            <i class="icon"><i class="fa-solid fa-syringe"></i></i></i></i>
                            <div class="text-content">
                                <h3>Vaccinated</h3>
                                <p>32,450</p>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-content">
                            <i class="icon"><i class="fa-regular fa-syringe"></i></i></i></i>
                            <div class="text-content">
                                <h3>Not Vaccinated</h3>
                                <p>32,450</p>
                            </div>
                        </div>
                    </div>

                </section>
            </div>
        </div>

         <!-- Footer -->
         <!-- <?php include 'includes/footer.php'; ?> -->

</body>

</html>