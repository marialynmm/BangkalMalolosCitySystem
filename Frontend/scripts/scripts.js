document.addEventListener('DOMContentLoaded', () => {
    initializeInteract();
    initializeCharts();
    initializeSidebar();
    restoreLayout();

    // Function to terminate the existing Python process
    const terminateProcess = () => {
        fetch('../Backend/terminate_forecast.php')
            .then(response => response.text())
            .then(data => console.log("Terminated process:", data))
            .catch(error => console.error('Error:', error));
    };

    // Call terminateProcess on page unload
    window.addEventListener('beforeunload', terminateProcess);

    // Start the new Python process
    fetch('../Backend/run_forecast.php')
        .then(response => response.text())
        .then(data => {
            console.log(data);
            alert("New script executed. Check console for output.");
        })
        .catch(error => console.error('Error:', error));

    const serviceSelect = document.getElementById('serviceSelect');
    serviceSelect.addEventListener('change', updateChart);

    const yearSelect = document.getElementById('yearSelect');
    yearSelect.addEventListener('change', updateChart);
});


function showLoadingIndicator() {
    document.getElementById('loadingBackground').style.display = 'block'; // Show background
}

// Hide loading indicator function
function hideLoadingIndicator() {
    document.getElementById('loadingBackground').style.display = 'none'; // Hide background
}

function navigate(url) {
    window.location.href = url;
}

function initializeCardLocking() {
    const cards = document.querySelectorAll('.card');

    cards.forEach(card => {
        const lockBtn = card.querySelector('.move-btn');

        // Initialize lock state for each card
        cardLockStates[card.id] = true; // All cards start locked

        // Add event listener to the lock button
        lockBtn.addEventListener('click', (event) => {
            event.stopPropagation(); // Prevent click events from bubbling
            cardLockStates[card.id] = !cardLockStates[card.id]; // Toggle lock state
            updateCardLockState(card);
        });

        // Lock the card initially
        updateCardLockState(card);
    });
}

function updateCardLockState(card) {
    const isLocked = cardLockStates[card.id];
    const lockBtn = card.querySelector('.move-btn');

    if (isLocked) {
        lockBtn.innerHTML = 'Move'; // Lock icon
        card.classList.add('locked');
        card.style.pointerEvents = 'none'; // Disable interactions on the card
        card.style.opacity = '1'; // Visually indicate locked state
    } else {
        lockBtn.innerHTML = 'Move'; // Unlock icon
        card.classList.remove('locked');
        card.style.pointerEvents = 'auto'; // Enable interactions on the card
        card.style.opacity = '0'; // Reset opacity when unlocked

        // Initialize draggable only when unlocked
        initializeDraggable(lockBtn, card);
    }
}


// Initialize Interact.js for draggable and resizable cards with snapping
const defaultLayout = [
    { "id": "barChartCard", "x": 52, "y": 0, "width": 545, "height": 608 },
    { "id": "servicesBarChartCard", "x": 500, "y": 0, "width": 1215, "height": 607 },
    { "id": "lineChartCard", "x": -234, "y": 626, "width": 1777, "height": 520 },
    { "id": "dataTableCard", "x": -687, "y": 1163, "width": 1777, "height": 561 }
];

function applyDefaultLayout() {
    defaultLayout.forEach(item => {
        const card = document.getElementById(item.id);
        if (card) {
            card.style.width = `${item.width}px`;
            card.style.height = `${item.height}px`;
            card.style.transform = `translate(${item.x}px, ${item.y}px)`;
            card.setAttribute('data-x', item.x);
            card.setAttribute('data-y', item.y);
        }
    });
}

function initializeInteract() {
    const boundary = {
        top: 20,
        left: 80,
        right: window.innerWidth,
        bottom: window.innerHeight
    };

    const gridOverlay = document.createElement('div');
    gridOverlay.className = 'grid-overlay';
    document.body.appendChild(gridOverlay);

    const toolTip = document.getElementById('tooltip');

    function showGrid() {
        gridOverlay.style.display = 'block';
        toolTip.style.display = 'block';
    }

    function hideGrid() {
        gridOverlay.style.display = 'none';
        toolTip.style.display = 'none';
    }

    function adjustCardPositions() {
        const cards = Array.from(document.querySelectorAll('.card'));
        const gap = 1;
        const gridSize = 1;

        cards.forEach((card) => {
            const rect = card.getBoundingClientRect();
            const x = Math.round(rect.left / gridSize) * gridSize;
            const y = Math.round(rect.top / gridSize) * gridSize;

            card.style.left = `${x}px`;
            card.style.top = `${y}px`;
        });

        for (let i = 0; i < cards.length; i++) {
            for (let j = i + 1; j < cards.length; j++) {
                const rect1 = cards[i].getBoundingClientRect();
                const rect2 = cards[j].getBoundingClientRect();

                if (!(rect1.right < rect2.left ||
                    rect1.left > rect2.right ||
                    rect1.bottom < rect2.top ||
                    rect1.top > rect2.bottom)) {

                    cards[j].style.top = `${rect1.bottom + gap}px`; // Move below the first card
                }
            }
        }
    }

    interact('.move-btn')
        .draggable({
            listeners: {
                start(event) {
                    showGrid();
                    const card = event.target.closest('.card');
                    card.style.zIndex = 1000; // Increase z-index of the card
                },
                move(event) {
                    const button = event.target;
                    const card = button.closest('.card'); // Get the card element
                    let x = (parseFloat(card.getAttribute('data-x')) || 0) + event.dx;
                    let y = (parseFloat(card.getAttribute('data-y')) || 0) + event.dy;

                    x = Math.round(x / 25) * 25;
                    y = Math.round(y / 25) * 25;

                    card.style.transform = `translate(${x}px, ${y}px)`;
                    card.setAttribute('data-x', x);
                    card.setAttribute('data-y', y);
                },
                end(event) {
                    const card = event.target.closest('.card');
                    hideGrid();
                    card.style.zIndex = ''; // Reset z-index
                    adjustCardPositions();
                    saveLayout();
                }
            },
            modifiers: [
                interact.modifiers.snap({
                    targets: [interact.snappers.grid({ x: 25, y: 25 })],
                    range: Infinity,
                    relativePoints: [{ x: 0, y: 0 }]
                }),
                interact.modifiers.restrictEdges({
                    outer: boundary
                })
            ]
        });

    interact('.card')
        .resizable({
            edges: { left: true, right: true, bottom: true, top: true },
            listeners: {
                start(event) {
                    showGrid();
                    event.target.style.zIndex = 1000;
                },
                move(event) {
                    const target = event.target;
                    const minSize = 10;
                    let x = (parseFloat(target.getAttribute('data-x')) || 0);
                    let y = (parseFloat(target.getAttribute('data-y')) || 0);

                    const newWidth = Math.max(Math.round(event.rect.width / 1) * 1, minSize);
                    const newHeight = Math.max(Math.round(event.rect.height / 1) * 1, minSize);

                    target.style.width = `${newWidth}px`;
                    target.style.height = `${newHeight}px`;

                    x += event.deltaRect.left;
                    y += event.deltaRect.top;

                    x = Math.round(x / 1) * 1;
                    y = Math.round(y / 1) * 1;

                    target.style.transform = `translate(${x}px, ${y}px)`;
                    target.setAttribute('data-x', x);
                    target.setAttribute('data-y', y);

                    // Auto-fit content
                    const content = target.querySelector('.card-content');
                    if (content) {
                        content.style.width = `${newWidth - 1}px`;
                        content.style.height = `${newHeight - 1}px`;
                    }
                },
                end(event) {
                    hideGrid();
                    event.target.style.zIndex = '';
                    saveLayout();
                }
            },
            modifiers: [
                interact.modifiers.restrictEdges({
                    outer: boundary
                }),
                interact.modifiers.restrictSize({
                    min: { width: 1, height: 1 },
                }),
                interact.modifiers.snap({
                    targets: [interact.snappers.grid({ x: 1, y: 1 })],
                    range: Infinity,
                    relativePoints: [{ x: 0, y: 0 }]
                })
            ],
            inertia: true,
        });

    // Apply default layout on initialization
    applyDefaultLayout();
}

// Example of changing boundaries dynamically
function updateBoundary(newBoundary) {
    boundary = newBoundary;
    // Update any relevant interactions or constraints here if necessary
}

// Call this function to adjust boundaries
updateBoundary({ top: 0, left: 0, right: 800, bottom: 600 });

// CHARTS

let barChart;
let lineChart;
let servicesBarChart;

function initializeCharts() {
    // Destroy existing charts if they exist
    if (barChart && barChart.destroy) {
        barChart.destroy();
    }
    if (lineChart && lineChart.destroy) {
        lineChart.destroy();
    }
    if (servicesBarChart && servicesBarChart.destroy) {
        servicesBarChart.destroy();
    }

    // Initialize Bar Chart
    const ctxBar = document.getElementById('barChart').getContext('2d');
    barChart = new Chart(ctxBar, {
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

    // Initialize Line Chart
    const ctxLine = document.getElementById('lineChart').getContext('2d');
    lineChart = new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: ['2019', '2020', '2021', '2022', '2023', '2024'],
            datasets: [{
                label: 'Growth',
                data: [30000, 20000, 15000, 12000, 5000, 0],
                borderColor: '#FF6384',
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Initialize Horizontal Bar Chart
    const ctx = document.getElementById('populationChart').getContext('2d');
    populationChart = new Chart(ctx, {
        type: 'bar', // or 'horizontalBar', depending on your requirement
        data: {
            labels: ['Male', 'Female', 'Both'],
            datasets: [{
                label: 'Population by Gender',
                data: [0, 0, 0], // Initial values
                backgroundColor: ['#36A2EB', '#FF6384', '#4CAF50'],
                borderColor: '#fff',
                borderWidth: 1
            }]
        },
        options: {
            maintainAspectRatio: false,
            responsive: true,
            indexAxis: 'y',
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Gender'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Population'
                    }
                }
            },
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
                }
            }
        }
    });
}

// General update function for charts
function updateChart() {
    const selectedYear = document.getElementById('yearSelect').value;
    const selectedService = document.getElementById('serviceSelect').value;

    // Fetch data if a year is selected
    if (selectedYear) {
        if (selectedService) {
            // Data fetching logic for selected service and year
            if (selectedYear >= 2025) {
                const maleFemaleURL = `../Backend/python/forecasts/forecast_v1_male_female_${selectedYear}.json`;
                const maleURL = `../Backend/python/forecasts/forecast_v2_male_${selectedYear}.json`;
                const femaleURL = `../Backend/python/forecasts/forecast_v3_female_${selectedYear}.json`;

                // Fetch data from all three JSON files
                Promise.all([
                    fetch(maleFemaleURL),
                    fetch(maleURL),
                    fetch(femaleURL)
                ])
                    .then(responses => {
                        responses.forEach(response => {
                            if (!response.ok) {
                                showLoadingIndicator();
                                throw new Error(`Network response was not ok: ${response.statusText}`);
                            }
                        });
                        return Promise.all(responses.map(response => response.json()));
                    })
                    .then(dataArray => {
                        hideLoadingIndicator();
                        // Check if data exists
                        if (dataArray.some(data => !Array.isArray(data) || data.length === 0)) {
                            throw new Error('One or more data files are empty or not in expected format.');
                        }

                        const allData = [...dataArray[0], ...dataArray[1], ...dataArray[2]];
                        const filteredData = allData.filter(item => item.Community_Services.trim() === selectedService.trim());

                        let maleCount = 0, femaleCount = 0, bothCount = 0;

                        filteredData.forEach(item => {
                            if (item.Gender === 'M') maleCount += item['2025'];
                            else if (item.Gender === 'F') femaleCount += item['2025'];
                            else if (item.Gender === 'MF') bothCount += item['2025'];
                        });

                        updateChartData(maleCount, femaleCount, bothCount, selectedYear, selectedService);
                    })
                    .catch(error => {
                        generateForecast();
                        console.error('Error fetching data:', error);
                        document.getElementById('servicesText').innerHTML = 'Generating data, please wait...';
                    });
            } else {
                // PHP data fetching logic
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
                        const maleCount = parseInt(data.counts.M, 10) || 0;
                        const femaleCount = parseInt(data.counts.F, 10) || 0;
                        const bothCount = parseInt(data.counts.MF, 10) || 0;

                        updateChartData(maleCount, femaleCount, bothCount, selectedYear, selectedService);
                    });
            }
        } else {
            // Clear chart and message if no service is selected
            clearChart();
        }
    } else {
        // Clear chart and message if no year is selected
        clearChart();
    }
}

function generateForecast() {
    const currentYear = new Date().getFullYear(); // Get the current year

    fetch(`http://127.0.0.1:5000/forecast?year=${currentYear}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            console.log(data); // Process the data as needed
            document.getElementById('output').innerText = JSON.stringify(data, null, 2);
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('output').innerText = 'Failed to fetch data: ' + error.message;
        })
        .finally(() => {
            hideLoadingIndicator(); // Hide loading indicator after fetching data
        });
}

function updateChartData(maleCount, femaleCount, bothCount, selectedYear, selectedService) {
    populationChart.data.datasets[0].data = [maleCount, femaleCount, bothCount];
    populationChart.update();

    const totalSum = maleCount + femaleCount + bothCount;
    let genderText = '';

    if (bothCount > 0) genderText = 'Both Male & Female';
    else if (maleCount > 0 && femaleCount > 0) genderText = 'Male & Female';
    else if (maleCount > 0) genderText = 'Male';
    else if (femaleCount > 0) genderText = 'Female';

    let summaryText = `For the year <b>${selectedYear}</b> and the service <b>${selectedService}</b>, the total number of individuals is <b>${totalSum}</b>`;
    if (genderText) summaryText += `, with the breakdown by gender <b>${genderText}</b>.`;

    document.getElementById('servicesText').innerHTML = summaryText.trim();
}

function clearChart() {
    populationChart.data.datasets[0].data = [0, 0, 0];
    populationChart.update();
    document.getElementById('servicesText').innerHTML = '';
}


document.querySelector('.form-grid').addEventListener('submit', function (event) {
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

document.getElementById('serviceSelect').disabled = true;
document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
    checkbox.disabled = true;
});

// Enable service and gender options based on year selection
document.getElementById('yearSelect').addEventListener('change', function () {
    const selectedYear = this.value;
    const isYearSelected = selectedYear !== '';

    document.getElementById('serviceSelect').disabled = !isYearSelected;
    document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        checkbox.disabled = !isYearSelected;
    });

    // Reset selections and update chart if no year is selected
    if (!isYearSelected) {
        document.getElementById('serviceSelect').value = '';
        document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            checkbox.checked = false;
        });
    } else if (selectedYear === '2025') {
        // Show loading indicator
        document.getElementById('loadingIndicator').style.display = 'block';

        // Fetch data for the year 2025
        fetchDataForYear(selectedYear);
    } else {
        updateChart(); // Call updateChart to reflect changes for other years
    }
});

function fetchDataForYear(year) {
    fetch(`http://127.0.0.1:5000/forecast?year=${year}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            console.log(data); // Process the data as needed
            document.getElementById('output').innerText = JSON.stringify(data, null, 2);
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('output').innerText = 'Failed to fetch data: ' + error.message;
        })
        .finally(() => {
            // Hide loading indicator after the fetch is complete
            document.getElementById('loadingIndicator').style.display = 'none';
        });
}

// Event listeners
document.getElementById('yearSelect').addEventListener('change', updateChart);
document.getElementById('serviceSelect').addEventListener('change', updateChart);
document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
    checkbox.addEventListener('change', updateChart);
});


// Sidebar initialization
function initializeSidebar() {
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
}

// Function to save card layout
function saveLayout() {
    const cards = document.querySelectorAll('.card');
    const layoutData = [];

    cards.forEach(card => {
        const position = {
            x: parseFloat(card.getAttribute('data-x')) || 0,
            y: parseFloat(card.getAttribute('data-y')) || 0
        };
        const size = { width: card.offsetWidth, height: card.offsetHeight };

        layoutData.push({
            id: card.id,
            ...position,
            width: size.width,
            height: size.height
        });
    });

    console.log('Saving layout:', layoutData);
    localStorage.setItem('cardLayout', JSON.stringify(layoutData));
}

// Function to restore card layout
function restoreLayout() {
    const savedLayout = JSON.parse(localStorage.getItem('cardLayout'));
    if (savedLayout) {
        savedLayout.forEach(data => {
            const card = document.getElementById(data.id);
            if (card) {
                card.style.width = `${data.width}px`;
                card.style.height = `${data.height}px`;
                card.style.transform = `translate(${data.x}px, ${data.y}px)`;
                card.setAttribute('data-x', data.x);
                card.setAttribute('data-y', data.y);
            }
        });
    }
}

// TABLE
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchInput');
    const table = document.getElementById('dataTable');
    const tbody = table.querySelector('tbody');

    searchInput.addEventListener('input', () => {
        const searchTerm = searchInput.value.toLowerCase();
        const rows = Array.from(tbody.querySelectorAll('tr'));

        rows.forEach(row => {
            const cells = Array.from(row.querySelectorAll('td'));
            const nameCell = cells[4]; // Assuming 'NAME' is the 5th column (index 4)
            const match = nameCell && nameCell.textContent.toLowerCase().includes(searchTerm);

            row.style.display = match ? '' : 'none';
        });
    });
});