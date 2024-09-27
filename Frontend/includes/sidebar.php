<script src="scripts/scripts.js"></script>
<div class="sidebar-wrapper">
    <div class="sidebar" id="sidebar">
        <ul>
            <div class="profile">
                <img src="images/logo.png" alt="logo">
            </div>
            <li onclick="navigate('<?php echo ('dashboard.php'); ?>')">
                <i class="icon"><i class="fa-solid fa-table-columns"></i></i><span>Dashboard</span>
            </li>
            <li onclick="navigate('<?php echo ('analytics.php'); ?>')">
                <i class="icon"><i class="fa-solid fa-chart-pie"></i></i><span>Analytics</span>
            </li>
            <li onclick="navigate('<?php echo ('about.php'); ?>')">
                <i class="icon"><i class="fa-solid fa-circle-info"></i></i><span>About</span>
            </li>
            <li onclick="navigate('<?php echo ('index.php'); ?>')">
                <i class="icon"><i class="fa-solid fa-right-from-bracket"></i></i><span>Logout</span>
            </li>
        </ul>
    </div>
    <button class="toggle-btn" id="toggleBtn">
        <i class="fa-solid fa-chevron-right"></i>
    </button>
</div>