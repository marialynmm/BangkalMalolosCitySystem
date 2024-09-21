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
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>
        <div id="tooltip" class="tooltip" style="display: none;">Drag to move</div>

        <!-- Content -->
        <div class="content" id="content">
            <!-- <h1>Barangay Bangkal</h1> -->
            <div class="dashboard">
                <!-- Bar Chart Card -->
                <div class="card" id="barChartCard">
                    <div class="card-header">
                        <h3>Age Group</h3>
                        <button id="editBarChart" class="edit-button">Edit</button>
                    </div>
                    <canvas id="barChart"></canvas>
                </div>
                <!-- Line Chart Card -->
                <div class="card" id="lineChartCard">
                    <div class="card-header">
                        <h3>Population Growth</h3>
                        <button id="editBarChart" class="edit-button">Edit</button>
                    </div>
                    <canvas id="lineChart"></canvas>
                </div>
                <!-- Table Data Card -->
                <div class="card" id="dataTableCard">
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