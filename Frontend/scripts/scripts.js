function navigate(url) {
    window.location.href = url;
}

// DASHBOARD 
// Ensure the DOM is fully loaded before executing the script
document.addEventListener('DOMContentLoaded', () => {
    // Initialize Chart.js
    const ctxPie = document.getElementById('pieChart').getContext('2d');
    const pieChart = new Chart(ctxPie, {
        type: 'pie',
        data: {
            labels: ['Vaccinated', 'Not Vaccinated', 'Booster Vaccinated'],
            datasets: [{
                data: [300, 50, 100],
                backgroundColor: ['#6aa84f', '#d0d0d0', '#FFCE56']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    const ctxBar = document.getElementById('barChart').getContext('2d');
    const barChart = new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: ['0-10 years', '11-20 years', '21-30 years', '31-40 years'],
            datasets: [{
                label: 'Number of People',
                data: [120, 190, 300, 150],
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function (tooltipItem) {
                            return `${tooltipItem.label}: ${tooltipItem.raw} people`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Age Groups'
                    }
                },
            }
        }
    });

    const ctxLine = document.getElementById('lineChart').getContext('2d');
    const lineChart = new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September'],
            datasets: [{
                label: 'Growth',
                data: [12000, 12200, 12300, 12500, 12600, 12650, 12800, 12900, 13100],
                borderColor: '#FF6384',
                fill: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Sidebar Toggle
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('toggleBtn');
    const indicator = document.getElementById('indicator');
    const menuItems = document.querySelectorAll('.sidebar ul li');

    toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('open');
        toggleBtn.innerHTML = sidebar.classList.contains('open') 
            ? '<i class="fa-solid fa-chevron-left"></i>' 
            : '<i class="fa-solid fa-chevron-right"></i>';
    });

    // Move the indicator line on hover
    menuItems.forEach((item) => {
        item.addEventListener('mouseover', () => {
            const itemHeight = item.offsetHeight;
            const offsetTop = item.offsetTop;
            indicator.style.top = `${offsetTop}px`;
            indicator.style.height = `${itemHeight}px`;
        });
    });

    // Modal functionality
    const modal = document.getElementById("editModal");
    const btn = document.getElementById("editPieChart");
    const span = document.getElementsByClassName("close")[0];
    const updateButton = document.getElementById('updateChart');
    const cancelButton = document.getElementById('cancelEdit');

    btn.onclick = () => {
        modal.style.display = "block";
    };

    span.onclick = () => {
        modal.style.display = "none";
    };

    window.onclick = (event) => {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    };

    function updateChart() {
        const vaccinated = parseInt(document.getElementById('vaccinated').value, 10) || 0;
        const notVaccinated = parseInt(document.getElementById('notVaccinated').value, 10) || 0;
        const boosterVaccinated = parseInt(document.getElementById('boosterVaccinated').value, 10) || 0;

        pieChart.data.datasets[0].data = [vaccinated, notVaccinated, boosterVaccinated];
        pieChart.update();
    }

    updateButton.addEventListener('click', () => {
        updateChart();
        modal.style.display = "none";
    });

    cancelButton.addEventListener('click', () => {
        modal.style.display = "none";
    });

    // Dashboard functionality
    document.getElementById('dashboard').addEventListener('click', (event) => {
        event.preventDefault();
        window.location.href = 'dashboard.php';
    });

     // Analytics functionality
     document.getElementById('analytics').addEventListener('click', (event) => {
        event.preventDefault();
        window.location.href = 'analytics.php';
    });

    // Logout functionality
    document.getElementById('logout').addEventListener('click', (event) => {
        event.preventDefault();
        window.location.href = 'index.php';
    });
});

// DASHBOARD END 
