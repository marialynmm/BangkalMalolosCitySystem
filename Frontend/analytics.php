<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap">
    <link rel="stylesheet" href="css/analytics.css">
    <script src="scripts/scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Analytics</title>
</head>

<body>
    <div class="container">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Content -->
        <div class="content" id="content">
            <h1>Barangay Bangkal</h1>
            <div class="dashboard">
                <!-- Pie Chart Card -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Vaccine Doses</h3>
                        <button id="editPieChart" class="edit-button">Edit</button>
                    </div>
                    <canvas id="pieChart"></canvas>
                </div>
                <!-- Bar Chart Card -->
                <div class="card">
                    <h3>Age Group</h3>
                    <canvas id="barChart"></canvas>
                </div>
                <!-- Line Chart Card -->
                <div class="card">
                    <h3>Population Growth</h3>
                    <canvas id="lineChart"></canvas>
                </div>
                <!-- Table Data Card -->
                <div class="card">
                    <h3>Data Table</h3>
                    <table border="1" width="100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>John Doe</td>
                                <td>john@example.com</td>
                                <td>Active</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Jane Smith</td>
                                <td>jane@example.com</td>
                                <td>Inactive</td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>Bob Johnson</td>
                                <td>bob@example.com</td>
                                <td>Active</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Vaccine Doses Modal -->
        <div id="editModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h4>Edit Vaccine Doses Data</h4>
                <div>
                    <label for="vaccinated">Vaccinated:</label>
                    <input type="number" id="vaccinated" value="300" min="0">
                </div>
                <div>
                    <label for="notVaccinated">Not Vaccinated:</label>
                    <input type="number" id="notVaccinated" value="50" min="0">
                </div>
                <div>
                    <label for="boosterVaccinated">Booster Vaccinated:</label>
                    <input type="number" id="boosterVaccinated" value="100" min="0">
                </div>
                <button id="updateChart">Update Chart</button>
                <button id="cancelEdit">Cancel</button>
            </div>
        </div>
       
</body>

</html>