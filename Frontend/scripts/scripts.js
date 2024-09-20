function navigate(url) {
    window.location.href = url;
}

// DRAG

// Initialize Interact.js for draggable and resizable cards with snapping
const defaultLayout = [
    { "id": "barChartCard", "x": 0, "y": 0, "width": 438, "height": 521 },
    { "id": "lineChartCard", "x": 450, "y": 0, "width": 1325, "height": 520 },
    { "id": "dataTableCard", "x": -230, "y": 550, "width": 1781, "height": 520 }
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

    interact('.card')
        .draggable({
            listeners: {
                start(event) {
                    showGrid();
                    event.target.style.zIndex = 1000;
                },
                move(event) {
                    const target = event.target;
                    let x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx;
                    let y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy;

                    x = Math.round(x / 25) * 25;
                    y = Math.round(y / 25) * 25;

                    target.style.transform = `translate(${x}px, ${y}px)`;
                    target.setAttribute('data-x', x);
                    target.setAttribute('data-y', y);
                },
                end(event) {
                    hideGrid();
                    event.target.style.zIndex = '';
                    adjustCardPositions();
                    saveLayout();
                }
            },
            modifiers: [
                interact.modifiers.snap({
                    targets: [interact.snappers.grid({ x: 25, y: 25 })],
                    range: Infinity,
                    relativePoints: [{ x: 1, y: 1 }]
                }),
                interact.modifiers.restrictEdges({
                    outer: boundary
                })
            ]
        })
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

// Ensure interact is initialized after the DOM is fully loaded
document.addEventListener('DOMContentLoaded', () => {
    initializeInteract();
});

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

function initializeCharts() {
    // Destroy existing charts if they exist
    if (barChart && barChart.destroy) {
        barChart.destroy();
    }
    if (lineChart && lineChart.destroy) {
        lineChart.destroy();
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


// Combine initialization functions into a single DOMContentLoaded event
document.addEventListener('DOMContentLoaded', () => {
    initializeInteract();
    initializeCharts(); // Initialize charts without pieChart
    initializeSidebar();
    restoreLayout();
});
