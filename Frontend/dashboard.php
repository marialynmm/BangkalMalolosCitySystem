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