
function navigate(url) {
    window.location.href = url;
}



// DRAG

// Initialize Interact.js for draggable and resizable cards with snapping
function initializeInteract() {
    // Define boundary variable
    const boundary = {
        top: 0,
        left: 80,
        right: window.innerWidth, // You can set this to any desired value
        bottom: window.innerHeight // You can set this to any desired value
    };

    const gridOverlay = document.createElement('div');
    gridOverlay.className = 'grid-overlay';
    document.body.appendChild(gridOverlay);

    const toolTip = document.getElementById('tooltip');

    function showGrid() {
        gridOverlay.style.display = 'block';
        toolTip.style.display = 'block'; // Use 'block' to make the tooltip visible
    }

    function hideGrid() {
        gridOverlay.style.display = 'none';
        toolTip.style.display = 'none'; // Use 'none' to hide the tooltip
    }

    function adjustCardPositions() {
        const cards = Array.from(document.querySelectorAll('.card'));
        const gap = 20; // Desired gap between cards
        const gridSize = 50; // Size of grid cells

        cards.forEach((card) => {
            const rect = card.getBoundingClientRect();
            const x = Math.round(rect.left / gridSize) * gridSize;
            const y = Math.round(rect.top / gridSize) * gridSize;

            card.style.left = `${x}px`;
            card.style.top = `${y}px`;
        });

        // Ensure cards do not overlap
        for (let i = 0; i < cards.length; i++) {
            for (let j = i + 1; j < cards.length; j++) {
                const rect1 = cards[i].getBoundingClientRect();
                const rect2 = cards[j].getBoundingClientRect();

                if (!(rect1.right < rect2.left ||
                    rect1.left > rect2.right ||
                    rect1.bottom < rect2.top ||
                    rect1.top > rect2.bottom)) {

                    // Adjust positions to avoid overlap
                    cards[j].style.top = `${rect1.bottom + gap}px`; // Move below the first card
                }
            }
        }
    }

    interact('.card')
        .draggable({
            listeners: {
                start(event) {
                    showGrid(); // Show grid when dragging starts
                    event.target.style.zIndex = 1000; // Bring the card to the front
                },
                move(event) {
                    const target = event.target;
                    let x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx;
                    let y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy;

                    x = Math.round(x / 50) * 50;
                    y = Math.round(y / 50) * 50;

                    target.style.transform = `translate(${x}px, ${y}px)`;
                    target.setAttribute('data-x', x);
                    target.setAttribute('data-y', y);
                },
                end(event) {
                    hideGrid(); // Hide grid after dragging ends
                    event.target.style.zIndex = ''; // Reset the z-index
                    adjustCardPositions(); // Adjust card positions after drag ends
                    saveLayout(); // Save layout after dragging ends
                }
            },
            modifiers: [
                interact.modifiers.snap({
                    targets: [interact.snappers.grid({ x: 50, y: 50 })],
                    range: Infinity,
                    relativePoints: [{ x: 0, y: 0 }]
                }),
                interact.modifiers.restrictEdges({
                    outer: boundary // Use the boundary variable
                })
            ]
        })
        .resizable({
            edges: { left: true, right: true, bottom: true, top: true },
            listeners: {
                start(event) {
                    showGrid(); // Show grid when resizing starts
                    event.target.style.zIndex = 1000; // Bring the card to the front
                },
                move(event) {
                    const target = event.target;
                    const minSize = 150;
                    let x = (parseFloat(target.getAttribute('data-x')) || 0);
                    let y = (parseFloat(target.getAttribute('data-y')) || 0);

                    const newWidth = Math.max(Math.round(event.rect.width / 50) * 50, minSize);
                    const newHeight = Math.max(Math.round(event.rect.height / 50) * 50, minSize);

                    target.style.width = `${newWidth}px`;
                    target.style.height = `${newHeight}px`;

                    x += event.deltaRect.left;
                    y += event.deltaRect.top;

                    x = Math.round(x / 50) * 50;
                    y = Math.round(y / 50) * 50;

                    target.style.transform = `translate(${x}px, ${y}px)`;
                    target.setAttribute('data-x', x);
                    target.setAttribute('data-y', y);

                    // Auto-fit content
                    const content = target.querySelector('.card-content');
                    if (content) {
                        content.style.width = `${newWidth - 40}px`; // Adjust padding
                        content.style.height = `${newHeight - 40}px`; // Adjust padding
                    }
                },
                end(event) {
                    hideGrid(); // Hide grid after resizing ends
                    event.target.style.zIndex = ''; // Reset the z-index
                    saveLayout(); // Save layout after resizing ends
                }
            },
            modifiers: [
                interact.modifiers.restrictEdges({
                    outer: boundary // Use the boundary variable
                }),
                interact.modifiers.restrictSize({
                    min: { width: 150, height: 150 },
                }),
                interact.modifiers.snap({
                    targets: [interact.snappers.grid({ x: 50, y: 50 })],
                    range: Infinity,
                    relativePoints: [{ x: 0, y: 0 }]
                })
            ],
            inertia: true,
        });
}

// Example of changing boundaries dynamically
function updateBoundary(newBoundary) {
    boundary = newBoundary;
    // Update any relevant interactions or constraints here if necessary
}

// Call this function to adjust boundaries
updateBoundary({ top: 0, left: 0, right: 800, bottom: 600 });

// CHARTS

let pieChart; // Declare chart variables outside functions for global access
let barChart;
let lineChart;

function initializeCharts() {
    // Destroy existing charts if they exist
    if (pieChart && pieChart.destroy) {
        pieChart.destroy();
    }
    if (barChart && barChart.destroy) {
        barChart.destroy();
    }
    if (lineChart && lineChart.destroy) {
        lineChart.destroy();
    }

    // Initialize Pie Chart
    const ctxPie = document.getElementById('pieChart').getContext('2d');
    pieChart = new Chart(ctxPie, {
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
}

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

function initializeModal(pieChart) {
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

    // Update Chart Function
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
    const pieChart = initializeCharts(); // Initialize charts and get pieChart instance
    initializeSidebar();
    initializeModal(pieChart);
    restoreLayout();
});
