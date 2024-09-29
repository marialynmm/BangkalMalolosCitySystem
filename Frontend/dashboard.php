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
                        <div class="card-header" style="display: flex; align-items: center; justify-content: space-between;">
                            <h3 style="margin: 0;">Census Data Table</h3>
                            <div>
                                <button id="addDataButton" style="margin-right: 10px;">Add New Data</button>
                                <button id="toggleColumnButton" onclick="toggleColumnContainer()">Show/Hide Column Selection</button>
                                <input type="text" id="searchInput" placeholder="Search by..." />
                            </div>
                        </div>

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

                        <div id="columnToggleContainer" class="column-toggle-container" style="display: none;">
                            <h4 style="margin-bottom: 10px;">Select Columns to Display:</h4>
                            <div class="checkbox-group">
                                <div class="column">
                                    <label><input type="checkbox" checked onclick="toggleColumn(0)"> NO OF POPULATION</label>
                                    <label><input type="checkbox" checked onclick="toggleColumn(1)"> NO OF HOUSEHOLD</label>
                                    <label><input type="checkbox" checked onclick="toggleColumn(2)"> NO OF FAMILIES</label>
                                    <label><input type="checkbox" checked onclick="toggleColumn(3)"> PUROK ST SITIO BLK LOT</label>
                                    <label><input type="checkbox" checked onclick="toggleColumn(4)"> NAME</label>
                                    <label><input type="checkbox" checked onclick="toggleColumn(5)"> BIRTHDAY</label>
                                </div>
                                <div class="column">
                                    <label><input type="checkbox" checked onclick="toggleColumn(6)"> AGE</label>
                                    <label><input type="checkbox" checked onclick="toggleColumn(7)"> GENDER</label>
                                    <label><input type="checkbox" checked onclick="toggleColumn(8)"> CIVIL STATUS</label>
                                    <label><input type="checkbox" checked onclick="toggleColumn(9)"> OCCUPATION</label>
                                    <label><input type="checkbox" checked onclick="toggleColumn(10)"> TOILET TYPE</label>
                                </div>
                            </div>
                        </div>

                        <div class="table-container">
                            <table id="dataTable" style="width: 100%; border-collapse: collapse;">
                                <thead style="background-color: #4CAF50; color: white;">
                                    <tr>
                                        <?php
                                        include "../Backend/connect.php"; // Include your database connection

                                        // Fetch column names
                                        $sql = "SHOW COLUMNS FROM brgy_bangkal_record_census_final";
                                        $result = $conn->query($sql);

                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                $columnName = str_replace('_', ' ', $row['Field']);
                                                echo "<th style='padding: 12px; text-align: left;'>" . htmlspecialchars($columnName) . "</th>";
                                            }
                                        } else {
                                            echo "<tr><td>No columns found</td></tr>";
                                        }
                                        ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Fetch data from the table
                                    $sql = "SELECT * FROM brgy_bangkal_record_census_final WHERE Name IS NOT NULL AND Name != 'N/A'";
                                    $result = $conn->query($sql);

                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr data-name='" . htmlspecialchars($row['NAME']) . "' 
                                                      data-no-of-population='" . htmlspecialchars($row['No_of_Population']) . "' 
                                                      data-no-of-household='" . htmlspecialchars($row['No_of_Household']) . "' 
                                                      data-no-of-families='" . htmlspecialchars($row['No_of_Families']) . "' 
                                                      data-purok-st-sitio-blk-lot='" . htmlspecialchars($row['Purok_St_Sitio_Blk_Lot']) . "' 
                                                      data-birthday='" . htmlspecialchars($row['BIRTHDAY']) . "' 
                                                      data-age='" . htmlspecialchars($row['AGE']) . "' 
                                                      data-gender='" . htmlspecialchars($row['GENDER']) . "' 
                                                      data-civil-status='" . htmlspecialchars($row['CIVIL_STATUS']) . "' 
                                                      data-occupation='" . htmlspecialchars($row['OCCUPATION']) . "' 
                                                      data-toilet-type='" . htmlspecialchars($row['TOILET_TYPE']) . "'>";
                                            foreach ($row as $data) {
                                                echo "<td>" . htmlspecialchars($data) . "</td>";
                                            }
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='11'>No data available</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <div id="paginationControls">
                            <button id="prevPageButton">Previous</button>
                            <span id="pageButtons"></span>
                            <button id="nextPageButton">Next</button>
                        </div>
                    </div>


                </section>
            </div>
        </div>

        <div id="dataModal" class="modal">
            <div class="modal-content">
                <span class="close-button">&times;</span>
                <h3 class="chart-title">Add New Data</h3>
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
                            <option value="lgbtq">LGBTQ</option>
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
                    <button type="button" id="deleteDataButton" style="grid-column: span 2; color: white; display: none;">Delete Data</button>

                </form>
            </div>
        </div>
    </div>

</body>

</html>