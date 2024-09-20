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
        // Query to get population counts
        $sql = "SELECT SUM(No_of_Population) AS total_sum FROM brgy_bangkal_record_census_final;";
        $result = $conn->query($sql);
        $population = $result->fetch_assoc()['total_sum'] ?? 0;

        // Count of males
        $sql_male = "SELECT COUNT(*) as total_male FROM brgy_bangkal_record_census_final WHERE GENDER = 'M';";
        $result_male = $conn->query($sql_male);
        $male_count = $result_male->fetch_assoc()['total_male'] ?? 0;

        // Count of females
        $sql_female = "SELECT COUNT(*) as total_female FROM brgy_bangkal_record_census_final WHERE GENDER = 'F';";
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

                    <div class="card">
                        <h3 class="chart-title">Services</h3>
                        <div class="filters">
                            <div class="filter-group">
                                <select id="yearSelect">
                                    <option value="">Select Year</option>
                                    <option value="2023">2023</option>
                                    <option value="2022">2022</option>
                                    <option value="2021">2021</option>
                                </select>
                                <select id="serviceSelect">
                                    <option value="">Select Service</option>
                                    <option value="Service1">Feeding Program</option>
                                    <option value="Service2">Pregnancy Prevention</option>
                                </select>
                                <div id="genderSelect">
                                    <label><input type="checkbox" value="M"> Male</label>
                                    <label><input type="checkbox" value="F"> Female</label>
                                </div>
                            </div>
                        </div>
                        <canvas id="populationChart" width="400" height="400"></canvas>
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
                            labels: {
                                padding: 10
                            }
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
                                return value / total > 0.05 ? value : ''; // Only show if it's a significant portion
                            },
                            font: {
                                size: 14,
                                weight: 'bold',
                                family: 'Poppins, sans-serif'
                            }
                        }
                    }
                },
                plugins: [ChartDataLabels] // Register the plugin
            });

            // Update chart based on selected filters (if required)
            function updateChart() {
                const selectedService = document.getElementById('serviceSelect').value;
                const maleChecked = document.querySelector('input[value="M"]').checked;
                const femaleChecked = document.querySelector('input[value="F"]').checked;

                const data = [];
                const labels = [];
                const backgroundColors = [];

                // If no gender is selected, show both
                if (!maleChecked && !femaleChecked) {
                    labels.push('Male', 'Female');
                    data.push(<?php echo $male_count; ?>, <?php echo $female_count; ?>);
                    backgroundColors.push('#36A2EB', '#FF6384'); // Both colors
                } else {
                    if (maleChecked) {
                        labels.push('Male');
                        data.push(<?php echo $male_count; ?>);
                        backgroundColors.push('#36A2EB'); // Male color
                    }
                    if (femaleChecked) {
                        labels.push('Female');
                        data.push(<?php echo $female_count; ?>);
                        backgroundColors.push('#FF6384'); // Female color
                    }
                }

                if (data.length > 0) {
                    populationChart.data.labels = labels;
                    populationChart.data.datasets[0].data = data;
                    populationChart.data.datasets[0].backgroundColor = backgroundColors; // Update colors
                    populationChart.update();
                } else {
                    // Clear chart if no data to show
                    populationChart.data.labels = [];
                    populationChart.data.datasets[0].data = [];
                    populationChart.data.datasets[0].backgroundColor = []; // Clear colors
                    populationChart.update();
                }
            }
            // Event listeners
            document.getElementById('serviceSelect').addEventListener('change', updateChart);
            document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                checkbox.addEventListener('change', updateChart);
            });
        </script>
    </div>
</body>

</html>