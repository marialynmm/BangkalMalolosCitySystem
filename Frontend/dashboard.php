<!DOCTYPE html>
<html lang="en">
<?php include '../Backend/session.php'; ?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Frontend/css/fontawesome-free-6.6.0-web/css/all.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="icon" href="images/logo.png" type="image/x-icon">
    <script src="scripts/scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

    <title>Dashboard</title>
    <style>
        .logo-stamp {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.05;
            pointer-events: none;
        }

        .logo-stamp img {
            max-width: 100vw;
            max-height: 100vh;
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
        <?php include 'includes/sidebar.php'; ?>

        <!-- PHP Code to Query Population Count -->
        <?php
        // Get total population
        $sql = "SELECT SUM(No_of_Population) AS total_sum FROM brgy_bangkal_record_census_final";
        $population_result = $conn->query($sql);
        $population = $population_result->fetch_assoc()['total_sum'] ?? 0;

        // Count of males
        $sql_male = "SELECT COUNT(*) as total_male FROM brgy_bangkal_record_census_final WHERE GENDER = 'M'";
        $result_male = $conn->query($sql_male);
        $male_count = $result_male->fetch_assoc()['total_male'] ?? 0;

        // Count of females
        $sql_female = "SELECT COUNT(*) as total_female FROM brgy_bangkal_record_census_final WHERE GENDER = 'F'";
        $result_female = $conn->query($sql_female);
        $female_count = $result_female->fetch_assoc()['total_female'] ?? 0;

        // Close the connection
        $conn->close();
        ?>

        <div class="content" id="content">

            <div class="main-content">
                <section class="dashboard">
                    <!-- Population Cards -->
                    <div class="card">
                        <div class="card-content"><i class="icon"><i class="fa-solid fa-chart-pie"></i></i>
                            <div class="text-content">
                                <h3>Population</h3>
                                <p><?php echo $population; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-content"><i class="icon"><i class="fa-solid fa-mars"></i></i>
                            <div class="text-content">
                                <h3>Male</h3>
                                <p><?php echo $male_count; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-content"><i class="icon"><i class="fa-solid fa-venus"></i></i>
                            <div class="text-content">
                                <h3>Female</h3>
                                <p><?php echo $female_count; ?></p>
                            </div>
                        </div>
                    </div>

                    <div style="color:#333;" class="card">
                        <h3 class="chart-title">Add New Data</h3>

                        <?php if (isset($_SESSION['success_message'])): ?>
                            <div class="message" style="color: green;">
                                <strong>
                                    <?php
                                    echo $_SESSION['success_message'];
                                    unset($_SESSION['success_message']); // Clear the message after displaying it
                                    ?>
                                </strong>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['error_message'])): ?>
                            <div class="message" style="color: red;">
                                <strong>
                                    <?php
                                    echo $_SESSION['error_message'];
                                    unset($_SESSION['error_message']); // Clear the message after displaying it
                                    ?>
                                </strong>
                            </div>
                        <?php endif; ?>

                        <form action="../Backend/add_data.php" method="POST" class="form-grid">
                            <div class="form-group">
                                <label for="no_of_population">No of Population:</label>
                                <input type="number" id="no_of_population" name="no_of_population" placeholder="No of Population" required>
                            </div>

                            <div class="form-group">
                                <label for="no_of_household">No of Household:</label>
                                <input type="number" id="no_of_household" name="no_of_household" placeholder="No of Household" required>
                            </div>

                            <div class="form-group">
                                <label for="no_of_families">No of Families:</label>
                                <input type="number" id="no_of_families" name="no_of_families" placeholder="No of Families" required>
                            </div>

                            <div class="form-group">
                                <label for="purok_st_sitio_blk_lot">Purok/Street/Sitio/Block/Lot:</label>
                                <input type="text" id="purok_st_sitio_blk_lot" name="purok_st_sitio_blk_lot" placeholder="Purok/Street/Sitio/Block/Lot" required>
                            </div>

                            <div class="form-group">
                                <label for="name">Full Name:</label>
                                <input type="text" id="name" name="name" placeholder="Full Name" required>
                            </div>

                            <div class="form-group">
                                <label for="birthday">Birthday:</label>
                                <input type="date" id="birthday" name="birthday" required>
                            </div>

                            <div class="form-group">
                                <label for="age">Age:</label>
                                <input type="number" id="age" name="age" placeholder="Age" required>
                            </div>

                            <div class="form-group">
                                <label for="gender">Gender:</label>
                                <select id="gender" name="gender" required>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="occupation">Occupation:</label>
                                <input type="text" id="occupation" name="occupation" placeholder="Occupation" required>
                            </div>

                            <div class="form-group">
                                <label for="civil_status">Civil Status:</label>
                                <select id="civil_status" name="civil_status" required>
                                    <option value="single">Single</option>
                                    <option value="married">Married</option>
                                    <option value="widow">Widow</option>
                                    <option value="divorced">Divorced</option>
                                    <option value="live-in">Live-In</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="toilet_type">Toilet Type:</label>
                                <input type="text" id="toilet_type" name="toilet_type" placeholder="Toilet Type" required>
                            </div>

                            <button type="submit" class="submit-button">Add Data</button>
                        </form>
                    </div>

                    <div style="color:#333;" class="card">
                        <h3 class="chart-title">Update Data</h3>

                        <?php if (isset($_SESSION['success_message'])): ?>
                            <div class="message" style="color: green;">
                                <strong>
                                    <?php
                                    echo $_SESSION['success_message'];
                                    unset($_SESSION['success_message']); // Clear the message after displaying it
                                    ?>
                                </strong>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['error_message'])): ?>
                            <div class="message" style="color: red;">
                                <strong>
                                    <?php
                                    echo $_SESSION['error_message'];
                                    unset($_SESSION['error_message']); // Clear the message after displaying it
                                    ?>
                                </strong>
                            </div>
                        <?php endif; ?>

                        <form action="../Backend/add_data.php" method="POST" class="form-grid">
                            <div class="form-group">
                                <label for="no_of_population">No of Population:</label>
                                <input type="number" id="no_of_population" name="no_of_population" placeholder="No of Population" required>
                            </div>

                            <div class="form-group">
                                <label for="no_of_household">No of Household:</label>
                                <input type="number" id="no_of_household" name="no_of_household" placeholder="No of Household" required>
                            </div>

                            <div class="form-group">
                                <label for="no_of_families">No of Families:</label>
                                <input type="number" id="no_of_families" name="no_of_families" placeholder="No of Families" required>
                            </div>

                            <div class="form-group">
                                <label for="purok_st_sitio_blk_lot">Purok/Street/Sitio/Block/Lot:</label>
                                <input type="text" id="purok_st_sitio_blk_lot" name="purok_st_sitio_blk_lot" placeholder="Purok/Street/Sitio/Block/Lot" required>
                            </div>

                            <div class="form-group">
                                <label for="name">Full Name:</label>
                                <input type="text" id="name" name="name" placeholder="Full Name" required>
                            </div>

                            <div class="form-group">
                                <label for="birthday">Birthday:</label>
                                <input type="date" id="birthday" name="birthday" required>
                            </div>

                            <div class="form-group">
                                <label for="age">Age:</label>
                                <input type="number" id="age" name="age" placeholder="Age" required>
                            </div>

                            <div class="form-group">
                                <label for="gender">Gender:</label>
                                <select id="gender" name="gender" required>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="occupation">Occupation:</label>
                                <input type="text" id="occupation" name="occupation" placeholder="Occupation" required>
                            </div>

                            <div class="form-group">
                                <label for="civil_status">Civil Status:</label>
                                <select id="civil_status" name="civil_status" required>
                                    <option value="single">Single</option>
                                    <option value="married">Married</option>
                                    <option value="widow">Widow</option>
                                    <option value="divorced">Divorced</option>
                                    <option value="live-in">Live-In</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="toilet_type">Toilet Type:</label>
                                <input type="text" id="toilet_type" name="toilet_type" placeholder="Toilet Type" required>
                            </div>

                            <button type="submit" class="submit-button">Add Data</button>
                        </form>
                    </div>

                    <div style="color:#333;" class="card">
                        <h3 class="chart-title">Delete Data</h3>

                        <?php if (isset($_SESSION['success_message'])): ?>
                            <div class="message" style="color: green;">
                                <strong>
                                    <?php
                                    echo $_SESSION['success_message'];
                                    unset($_SESSION['success_message']); // Clear the message after displaying it
                                    ?>
                                </strong>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['error_message'])): ?>
                            <div class="message" style="color: red;">
                                <strong>
                                    <?php
                                    echo $_SESSION['error_message'];
                                    unset($_SESSION['error_message']); // Clear the message after displaying it
                                    ?>
                                </strong>
                            </div>
                        <?php endif; ?>

                        <form action="../Backend/add_data.php" method="POST" class="form-grid">
                            <div class="form-group">
                                <label for="no_of_population">No of Population:</label>
                                <input type="number" id="no_of_population" name="no_of_population" placeholder="No of Population" required>
                            </div>

                            <div class="form-group">
                                <label for="no_of_household">No of Household:</label>
                                <input type="number" id="no_of_household" name="no_of_household" placeholder="No of Household" required>
                            </div>

                            <div class="form-group">
                                <label for="no_of_families">No of Families:</label>
                                <input type="number" id="no_of_families" name="no_of_families" placeholder="No of Families" required>
                            </div>

                            <div class="form-group">
                                <label for="purok_st_sitio_blk_lot">Purok/Street/Sitio/Block/Lot:</label>
                                <input type="text" id="purok_st_sitio_blk_lot" name="purok_st_sitio_blk_lot" placeholder="Purok/Street/Sitio/Block/Lot" required>
                            </div>

                            <div class="form-group">
                                <label for="name">Full Name:</label>
                                <input type="text" id="name" name="name" placeholder="Full Name" required>
                            </div>

                            <div class="form-group">
                                <label for="birthday">Birthday:</label>
                                <input type="date" id="birthday" name="birthday" required>
                            </div>

                            <div class="form-group">
                                <label for="age">Age:</label>
                                <input type="number" id="age" name="age" placeholder="Age" required>
                            </div>

                            <div class="form-group">
                                <label for="gender">Gender:</label>
                                <select id="gender" name="gender" required>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="occupation">Occupation:</label>
                                <input type="text" id="occupation" name="occupation" placeholder="Occupation" required>
                            </div>

                            <div class="form-group">
                                <label for="civil_status">Civil Status:</label>
                                <select id="civil_status" name="civil_status" required>
                                    <option value="single">Single</option>
                                    <option value="married">Married</option>
                                    <option value="widow">Widow</option>
                                    <option value="divorced">Divorced</option>
                                    <option value="live-in">Live-In</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="toilet_type">Toilet Type:</label>
                                <input type="text" id="toilet_type" name="toilet_type" placeholder="Toilet Type" required>
                            </div>

                            <button type="submit" class="submit-button">Add Data</button>
                        </form>
                    </div>
                </section>
            </div>
        </div>

        <script>
            //ADDING DATA
            document.querySelector('.form-grid').addEventListener('submit', function(event) {
                const fields = [
                    'no_of_population',
                    'no_of_household',
                    'no_of_families',
                    'purok_st_sitio_blk_lot',
                    'name',
                    'birthday',
                    'age',
                    'gender',
                    'occupation',
                    'civil_status',
                    'toilet_type'
                ];

                let allFilled = true;

                fields.forEach(field => {
                    const input = document.getElementById(field);
                    if (!input.value) {
                        allFilled = false;
                        input.style.borderColor = 'red'; // Highlight empty fields
                    } else {
                        input.style.borderColor = ''; // Reset border color
                    }
                });

                if (!allFilled) {
                    event.preventDefault(); // Prevent form submission
                    alert('Please fill in all fields.');
                }
            });
        </script>
    </div>
</body>

</html>