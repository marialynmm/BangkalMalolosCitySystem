<!DOCTYPE html>
<html lang="en">

<?php include '../Backend/session.php'; ?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Frontend/css/fontawesome-free-6.6.0-web/css/all.css">
    <link rel="stylesheet" href="css/analytics.css">
    <link rel="icon" href="images/logo.png" type="image/x-icon">
    <script src="../Frontend/css/chart.min.js"></script>
    <script src="../Frontend/css/interact.js"></script>
    <style>
        .logo-stamp {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.05;
            /* Adjust transparency here */
            pointer-events: none;
            /* Ensures it doesn't interfere with user interactions */
            text-align: center;
            /* Center text below the logo */
        }

        .logo-stamp img {
            max-width: 100vw;
            /* Scale with viewport width */
            max-height: 100vh;
            /* Scale with viewport height */
            width: 500px;
            /* Fixed width */
            height: 500px;
            /* Fixed height */
        }

        .loading-text {
            text-align: center;
            position: fixed;
            /* Use fixed positioning */
            top: 50%;
            /* Center vertically */
            left: 50%;
            /* Center horizontally */
            transform: translate(-50%, -50%);
            /* Adjust positioning */
            z-index: 9999;
            /* Ensure text is on top */
            color: white;
            /* Change to suit your design */
            font-size: 24px;
            /* Adjust size */
            margin-top: 10px;
            /* Space between logo and text */
            animation: fadeIn 1s infinite;
            /* Fade in animation */
        }

        @keyframes fadeIn {
            0% {
                opacity: 0.5;
            }

            50% {
                opacity: 1;
            }

            100% {
                opacity: 0.5;
            }
        }

        /* Background for loading effect */
        .loading-background {
            display: none;
            /* Hidden by default */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            /* Semi-transparent background */
            backdrop-filter: blur(5px);
            /* Blurs the background */
            z-index: 9998;
            /* Ensure it's below the logo stamp */
        }
    </style>
</head>

<body>

    <div class="loading-background" id="loadingBackground">
        <div class="loading-text">Generating data, please wait...</div>
    </div>

    <div class="logo-stamp" id="logoStamp">
        <img src="images/logo.png" alt="Logo">
    </div>
    <div class="container">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php';

        // Get unique services
        $services = [];
        $service_names = [];
        $result = $conn->query("SELECT Community_Services FROM v1_male_female UNION SELECT Community_Services FROM v2_male UNION SELECT Community_Services FROM v3_female");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $service_name = $row['Community_Services'];
                if (!empty($service_name) && !in_array($service_name, $service_names)) {
                    $services[] = ['name' => $service_name];
                    $service_names[] = $service_name;
                }
            }
        }

        // Get years from column names
        $years = [];
        $tables = ['v1_male_female', 'v2_male', 'v3_female'];

        foreach ($tables as $table) {
            $result = $conn->query("SHOW COLUMNS FROM $table");
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    // Check if the column name matches a year format (4 digits)
                    if (preg_match('/^\d{4}$/', $row['Field'])) {
                        $years[] = $row['Field'];
                    }
                }
            } else {
                // Debug: Check for errors in the query
                echo "Error: " . $conn->error;
            }
        }

        // Remove duplicates and keep unique years
        $years = array_unique($years);

        //AGE GROUP
        $ageGroups = [];
        $ageQuery = $conn->query("SELECT AGE FROM brgy_bangkal_record_census_final");
        if ($ageQuery) {
            while ($row = $ageQuery->fetch_assoc()) {
                $age = (int)$row['AGE'];
                // Group ages into categories
                if ($age >= 0 && $age <= 9) {
                    $ageGroups['0-9'] = ($ageGroups['0-9'] ?? 0) + 1;
                } elseif ($age >= 10 && $age <= 19) {
                    $ageGroups['10-19'] = ($ageGroups['10-19'] ?? 0) + 1;
                } elseif ($age >= 20 && $age <= 29) {
                    $ageGroups['20-29'] = ($ageGroups['20-29'] ?? 0) + 1;
                } elseif ($age >= 30 && $age <= 39) {
                    $ageGroups['30-39'] = ($ageGroups['30-39'] ?? 0) + 1;
                } elseif ($age >= 40 && $age <= 49) {
                    $ageGroups['40-49'] = ($ageGroups['40-49'] ?? 0) + 1;
                } elseif ($age >= 50) {
                    $ageGroups['50+'] = ($ageGroups['50+'] ?? 0) + 1;
                }
            }
        }


        $conn->close();
        ?>
        <div id="tooltip" class="tooltip" style="display: none;">Drag to resize</div>

        <!-- Content -->
        <div class="content" id="content">
            <!-- <h1>Barangay Bangkal</h1> -->
            <div class="dashboard">

                <!-- Bar Chart Card -->
                <div class="card" id="barChartCard">
                    <button class="move-btn" title="Move Card">
                        <img src="images/move_ic.png" alt="Move Icon" />
                    </button>

                    <script>
                        // Prepare data in PHP
                        const ageData = <?php echo json_encode($ageGroups); ?>;
                    </script>

                    <div class="card-header">
                        <h3>Age Group</h3>
                    </div>
                    <canvas id="barChart"></canvas>
                </div>

                <!-- Horizontal Bar Chart Card -->
                <div class="card" id="servicesBarChartCard">
                    <button class="move-btn" title="Move Card">
                        <img src="images/move_ic.png" alt="Move Icon" />
                    </button>
                    <div class="card-header">
                        <h3>Community Services Demand</h3>
                    </div>
                    <div class="filters">
                        <div class="filter-group">
                            <select id="yearSelect">
                                <option value="">Select Year</option>
                                <?php foreach ($years as $year): ?>
                                    <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <select id="serviceSelect">
                                <option value="">Select Service</option>
                                <?php foreach ($services as $service): ?>
                                    <option value="<?php echo $service['name']; ?>"><?php echo $service['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div style="display:none;" id="genderSelect">
                                <label><input type="checkbox" value="M"> Male</label>
                                <label><input type="checkbox" value="F"> Female</label>
                            </div>
                        </div>
                    </div>
                    <div style="height:390px;">
                        <canvas id="populationChart" width="150" height="250"></canvas>
                    </div>
                    <p id="servicesText" class="services-text"></p>
                </div>

                <script>
                    const yearsFromDB = <?php echo json_encode(array_values($years)); ?>;

                    function initializeYearSelect() {
                        const yearSelect = document.getElementById('yearSelect');
                        const currentYear = new Date().getFullYear();

                        // Clear existing options
                        yearSelect.innerHTML = '';

                        // Create a Set to avoid duplicates
                        const yearsSet = new Set(yearsFromDB);

                        // Add existing years from the database to the dropdown
                        yearsSet.forEach(year => {
                            const option = document.createElement('option');
                            option.value = year;
                            option.textContent = year;
                            yearSelect.appendChild(option);
                        });

                        // Check and add current year and next year if not already present
                        [currentYear + 1].forEach(year => {
                            if (!yearsSet.has(year)) {
                                const option = document.createElement('option');
                                option.value = year;
                                option.textContent = year;
                                yearSelect.appendChild(option);
                            }
                        });

                        // Set the default selection to the current year
                        yearSelect.value = currentYear;
                    }

                    initializeYearSelect();
                </script>

                <!-- Line Chart Card -->
                <div class="card" id="lineChartCard">
                    <button class="move-btn" title="Move Card">
                        <img src="images/move_ic.png" alt="Move Icon" />
                    </button>
                    <div class="card-header">
                        <h3>Population Growth</h3>
                    </div>
                    <canvas id="lineChart"></canvas>
                </div>

            </div>
        </div>

        <!-- Grid Overlay -->
        <div class="grid-overlay"></div>
    </div>

</body>


</html>