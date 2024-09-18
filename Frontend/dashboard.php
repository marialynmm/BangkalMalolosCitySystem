<!DOCTYPE html>
<html lang="en">
<?php include "../Backend/connect.php"; ?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Frontend/css/fontawesome-free-6.6.0-web/css/all.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="icon" href="images/logo.png" type="image/x-icon">
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
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- PHP Code to Query Population Count -->
        <?php
        // Query to get the total population count
        $sql = "SELECT SUM(No_of_Population) AS total_sum FROM brgy_bangkal_record_census_final;"; // Replace with your table name
        $result = $conn->query($sql);

        // Fetch the result for population
        $population = 0;
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $population = $row['total_sum'];
        }

        // Query to get the count of males
        $sql_male = "SELECT COUNT(*) as total_male FROM brgy_bangkal_record_census_final WHERE GENDER = 'M'"; // Replace with your table name
        $result_male = $conn->query($sql_male);

        // Fetch the result for male population
        $male_count = 0;
        if ($result_male->num_rows > 0) {
            $row_male = $result_male->fetch_assoc();
            $male_count = $row_male['total_male'];
        }


        // Query to get the count of males
        $sql_female = "SELECT COUNT(*) as total_female FROM brgy_bangkal_record_census_final WHERE GENDER = 'F'"; // Replace with your table name
        $result_female = $conn->query($sql_female);

        // Fetch the result for male population
        $female_count = 0;
        if ($result_female->num_rows > 0) {
            $row_female = $result_female->fetch_assoc();
            $female_count = $row_female['total_female'];


            if (isset($_POST['sort'])) {
                // Get the selected years, services, and genders
                $selected_years = isset($_POST['sort-year']) ? $_POST['sort-year'] : [];
                $selected_services = isset($_POST['sort-service-type']) ? $_POST['sort-service-type'] : [];
                $selected_genders = isset($_POST['sort-gender']) ? $_POST['sort-gender'] : [];
            
                // Convert arrays to comma-separated strings for SQL
                $years_string = implode(",", $selected_years); // Handles years
                $services_string = "'" . implode("','", $selected_services) . "'"; // Handles services
                $genders_string = "'" . implode("','", $selected_genders) . "'"; // Handles genders
            
                // Construct the query
                $sql = "SELECT SUM(`$years_string`) AS total_sort 
                        FROM DataWithServices 
                        WHERE Community_Services IN ($services_string) 
                        AND Gender IN ($genders_string);";
            
                $result = $conn->query($sql);
            
                // Fetch the result for population
                $sort_sum = 0;
                if ($result && $result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $sort_sum = $row['total_sort'];
                } else {
                    // Handle the case where no results are found
                    $sort_sum = "No data found";
                }
            }
            
            
        }

        // Close the connection
        $conn->close();
        ?>

        <!-- Content -->
        <div class="content" id="content">
            <div class="main-content">
                <section class="dashboard">
                    <!-- Population Card with Dynamic Data -->
                    <div class="card">
                        <div class="card-content">
                            <i class="icon"><i class="fa-solid fa-chart-pie"></i></i>
                            <div class="text-content">
                                <h3>Population</h3>
                                <p><?php echo $population; ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Male Population Card with Dynamic Data -->
                    <div class="card">
                        <div class="card-content">
                            <i class="icon"><i class="fa-solid fa-mars"></i></i>
                            <div class="text-content">
                                <h3>Male</h3>
                                <p><?php echo $male_count; ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Other Cards -->
                    <div class="card">
                        <div class="card-content">
                            <i class="icon"><i class="fa-solid fa-venus"></i></i>
                            <div class="text-content">
                                <h3>Female</h3>
                                <p><?php echo $female_count ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="card">

                    <form method="POST">
    <div class="card-content">

        <div class="sorting-checkbox">
            <h4>Sort Community Services By:</h4>

            <fieldset>
                <legend>Year</legend>
                <label>
                    <input type="checkbox" id="sort-year-2021" name="sort-year[]" value="2021">
                    2021
                </label>
                <label>
                    <input type="checkbox" id="sort-year-2022" name="sort-year[]" value="2022">
                    2022
                </label>
                <label>
                    <input type="checkbox" id="sort-year-2023" name="sort-year[]" value="2023">
                    2023
                </label>
            </fieldset>

            <fieldset>
                <legend>Type of Services</legend>
                <label>
                    <input type="checkbox" id="sort-service-type-education" name="sort-service-type[]" value="Daycare & Kinder Feeding Program">
                    Daycare & Kinder Feeding Program
                </label>
                <label>
                    <input type="checkbox" id="sort-service-type-health" name="sort-service-type[]" value="Free Circumsition Operation">
                    Free Circumsition Operation
                </label>
                <label>
                    <input type="checkbox" id="sort-service-type-environment" name="sort-service-type[]" value="Free Dental Check-Up & Tooth Extraction">
                    Free Dental Check-Up & Tooth Extraction
                </label>
                <label>
                    <input type="checkbox" id="sort-service-type-condom" name="sort-service-type[]" value="Condom Distribution Program">
                    Condom Distribution Program
                </label>
                <label>
                    <input type="checkbox" id="sort-service-type-pregnancy" name="sort-service-type[]" value="Anti-Teenage Pregnancy Program">
                    Anti-Teenage Pregnancy Program
                </label>
            </fieldset>

            <fieldset>
                <legend>Gender</legend>
                <label>
                    <input type="checkbox" id="sort-gender-male" name="sort-gender[]" value="M">
                    Male
                </label>
                <label>
                    <input type="checkbox" id="sort-gender-female" name="sort-gender[]" value="F">
                    Female
                </label>
            </fieldset>
        </div>

        <button type="submit" name="sort" id="sort">Sort</button>

    </div>
</form>





                        <div class="card-categoires">
                            <div class="card-content">
                                <i class="icon"><i class="fa-solid fa-table"></i></i>
                                <div class="text-content">
                                    <h4 id="selected-title">Choose Type of Service</h4>
                                    <p id="selected-count"><?php echo $sort_sum ?></p>
                                </div>
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