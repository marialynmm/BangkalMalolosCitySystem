<!DOCTYPE html>
<html lang="en">
<?php include "../Backend/connect.php"; ?>
<?php session_start(); ?>

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

        // Get unique services
        $services = [];
        $service_names = [];
        $result = $conn->query("SELECT Community_Services FROM datawithservices");
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
        $result = $conn->query("SHOW COLUMNS FROM datawithservices");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                if (preg_match('/^\d{4}$/', $row['Field'])) {
                    $years[] = $row['Field'];
                }
            }
        }

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
                                <input type="number" id="no_of_population" name="no_of_population" required>
                            </div>

                            <div class="form-group">
                                <label for="no_of_household">No of Household:</label>
                                <input type="number" id="no_of_household" name="no_of_household" required>
                            </div>

                            <div class="form-group">
                                <label for="no_of_families">No of Families:</label>
                                <input type="number" id="no_of_families" name="no_of_families" required>
                            </div>

                            <div class="form-group">
                                <label for="purok_st_sitio_blk_lot">Purok/Street/Sitio/Block/Lot:</label>
                                <input type="text" id="purok_st_sitio_blk_lot" name="purok_st_sitio_blk_lot" required>
                            </div>

                            <div class="form-group">
                                <label for="name">Full Name:</label>
                                <input type="text" id="name" name="name" required>
                            </div>

                            <div class="form-group">
                                <label for="birthday">Birthday:</label>
                                <input type="date" id="birthday" name="birthday" required>
                            </div>

                            <div class="form-group">
                                <label for="age">Age:</label>
                                <input type="number" id="age" name="age" required>
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
                                <input type="text" id="occupation" name="occupation" required>
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
                                <input type="text" id="toilet_type" name="toilet_type" required>
                            </div>

                            <button type="submit" class="submit-button">Add Data</button>
                        </form>
                    </div>

                    <div class="card">
                        <h3 class="chart-title">Community Service Demand</h3>
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
                                <div id="genderSelect">
                                    <label><input type="checkbox" value="M"> Male</label>
                                    <label><input type="checkbox" value="F"> Female</label>
                                </div>
                            </div>
                        </div>
                        <canvas id="populationChart" width="350" height="350"></canvas>
                        <p id="servicesText" class="services-text"></p>

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

            //PIECHART
            const ctx = document.getElementById('populationChart').getContext('2d');
            const populationChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Male', 'Female'],
                    datasets: [{
                        label: 'Population by Gender',
                        data: [<?php echo $male_count; ?>, <?php echo $female_count; ?>],
                        backgroundColor: ['#36A2EB', '#FF6384'],
                        borderColor: '#fff',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: (tooltipItem) => {
                                    return `${tooltipItem.label}: ${tooltipItem.raw}`;
                                }
                            }
                        },
                        datalabels: {
                            color: '#fff',
                            anchor: 'center',
                            align: 'center',
                            formatter: (value, context) => {
                                const total = context.chart.data.datasets[0].data.reduce((acc, val) => acc + val, 0);
                                return value / total > 0.05 ? value : '';
                            },
                            font: {
                                size: 14,
                                weight: 'bold',
                                family: 'Poppins, sans-serif'
                            }
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });

            document.getElementById('serviceSelect').disabled = true;
            document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                checkbox.disabled = true;
            });

            // Enable service and gender options based on year selection
            document.getElementById('yearSelect').addEventListener('change', function() {
                const isYearSelected = this.value !== '';
                document.getElementById('serviceSelect').disabled = !isYearSelected;
                document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                    checkbox.disabled = !isYearSelected;
                });

                // Reset selections
                if (!isYearSelected) {
                    document.getElementById('serviceSelect').value = '';
                    document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                        checkbox.checked = false;
                    });
                    updateChart(); // Clear chart when year is deselected
                }
            });

            // Update chart based on selected filters
            // Update chart based on selected filters
            function updateChart() {
                const selectedYear = document.getElementById('yearSelect').value;
                const selectedService = document.getElementById('serviceSelect').value;
                const maleChecked = document.querySelector('input[value="M"]').checked;
                const femaleChecked = document.querySelector('input[value="F"]').checked;

                // Fetch data from server
                fetch('../Backend/get_population_services.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            year: selectedYear,
                            service: selectedService
                        }),
                    })
                    .then(response => response.json())
                    .then(data => {
                        const dataCounts = data.counts;
                        const maleCount = dataCounts.M || 0;
                        const femaleCount = dataCounts.F || 0;

                        // Disable gender checkboxes based on counts
                        document.querySelector('input[value="M"]').disabled = maleCount === 0;
                        document.querySelector('input[value="F"]').disabled = femaleCount === 0;

                        // Auto-select gender checkboxes
                        if (maleCount > 0 && femaleCount > 0) {
                            document.querySelector('input[value="M"]').checked = maleChecked; // Leave as is
                            document.querySelector('input[value="F"]').checked = femaleChecked; // Leave as is
                        } else if (maleCount > 0) {
                            document.querySelector('input[value="M"]').checked = true;
                            document.querySelector('input[value="F"]').checked = false;
                        } else if (femaleCount > 0) {
                            document.querySelector('input[value="M"]').checked = false;
                            document.querySelector('input[value="F"]').checked = true;
                        }

                        // Prepare chart data
                        const labels = [];
                        const chartData = [];
                        const backgroundColors = [];

                        if (!maleChecked && !femaleChecked) {
                            if (maleCount > 0) {
                                labels.push('M');
                                chartData.push(maleCount);
                                backgroundColors.push('#36A2EB');
                            }
                            if (femaleCount > 0) {
                                labels.push('F');
                                chartData.push(femaleCount);
                                backgroundColors.push('#FF6384');
                            }
                        } else {
                            if (maleChecked && maleCount > 0) {
                                labels.push('M');
                                chartData.push(maleCount);
                                backgroundColors.push('#36A2EB');
                            }
                            if (femaleChecked && femaleCount > 0) {
                                labels.push('F');
                                chartData.push(femaleCount);
                                backgroundColors.push('#FF6384');
                            }
                        }

                        populationChart.data.labels = labels;
                        populationChart.data.datasets[0].data = chartData;
                        populationChart.data.datasets[0].backgroundColor = backgroundColors;
                        populationChart.update();

                        // Update the summary text
                        const totalSum = (maleChecked && femaleChecked) ? parseInt(maleCount, 10) + parseInt(femaleCount, 10) : `${maleChecked ? parseInt(maleCount, 10) : ''}${femaleChecked ? (maleChecked ? ' & ' : '') + parseInt(femaleCount, 10) : ''}`;

                        const genderText = (maleChecked || femaleChecked) ?
                            `${maleChecked ? 'Male' : ''}${femaleChecked ? (maleChecked ? ' & ' : '') + 'Female' : ''}` :
                            '';

                        document.getElementById('servicesText').innerHTML =
                            `The pie chart illustrates the number of individuals in need of services within the community. For the year <b>${selectedYear}</b> and the service <b>${selectedService}</b>, the total number of individuals is <b>${totalSum}</b>, with the breakdown by gender <b>${genderText}</b>.`.trim();
                    });
            }

            // Event listeners
            document.getElementById('yearSelect').addEventListener('change', updateChart);
            document.getElementById('serviceSelect').addEventListener('change', updateChart);
            document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                checkbox.addEventListener('change', updateChart);
            });


            // Event listeners
            document.getElementById('yearSelect').addEventListener('change', updateChart);
            document.getElementById('serviceSelect').addEventListener('change', updateChart);
            document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                checkbox.addEventListener('change', updateChart);
            });

            // Event listeners
            document.getElementById('yearSelect').addEventListener('change', updateChart);
            document.getElementById('serviceSelect').addEventListener('change', updateChart);
            document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                checkbox.addEventListener('change', updateChart);
            });
        </script>
    </div>
</body>

</html>