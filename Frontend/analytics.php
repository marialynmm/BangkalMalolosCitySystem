<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Frontend/css/fontawesome-free-6.6.0-web/css/all.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap">
    <link rel="stylesheet" href="css/analytics.css">
    <script src="scripts/scripts.js"></script>
    <link rel="icon" href="images/logo.png" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/interactjs@latest"></script>
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
    <title>Analytics</title>
</head>

<body>
    <div class="logo-stamp">
        <img src="images/logo.png" alt="Logo">
    </div>
    <div class="container">
        <div id="loadingIndicator" style="display: none;">Loading, please wait...</div>

        <!-- Sidebar -->
        <?php include 'includes/sidebar.php';
        include "../Backend/connect.php";

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

        $conn->close();
        ?>
        <div id="tooltip" class="tooltip" style="display: none;">Drag to move</div>

        <!-- Content -->
        <div class="content" id="content">
            <!-- <h1>Barangay Bangkal</h1> -->
            <div class="dashboard">

                <!-- Bar Chart Card -->
                <div class="card" id="barChartCard">
                    <button class="move-btn" title="Move Card">
                        <img src="images/move_ic.png" alt="Move Icon" />
                    </button>
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
                    <canvas id="populationChart" width="150" height="250"></canvas>
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

                <!-- Table Data Card -->
                <div class="card" id="dataTableCard">
                    <button class="move-btn" title="Move Card">
                        <img src="images/move_ic.png" alt="Move Icon" />
                    </button>
                    <div class="card-header">
                        <h3>Data Table</h3>
                        <div class="table-controls">
                            <input type="text" id="searchInput" placeholder="Search by NAME..." />
                        </div>
                    </div>

                    <div class="table-container">
                        <table id="dataTable">
                            <thead>
                                <tr>
                                    <?php
                                    include "../Backend/connect.php"; // Include your database connection

                                    // Fetch column names
                                    $sql = "SHOW COLUMNS FROM Census_tb"; // Replace with your table name
                                    $result = $conn->query($sql);

                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            $columnName = str_replace('_', ' ', $row['Field']);
                                            echo "<th>" . $columnName . "</th>";
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
                                $sql = "SELECT * FROM Census_tb WHERE Name IS NOT NULL AND Name != 'N/A'";

                                $result = $conn->query($sql);

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        foreach ($row as $data) {
                                            echo "<td>" . htmlspecialchars($data) . "</td>";
                                        }
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='10'>No data available</td></tr>";
                                }

                                $conn->close();
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

        <!-- Grid Overlay -->
        <div class="grid-overlay"></div>
    </div>
</body>


</html>