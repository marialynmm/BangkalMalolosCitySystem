<?php
// Include your database connection
include "../Backend/connect.php";

// Get the request data
$data = json_decode(file_get_contents("php://input"), true);
$year = $data['year'] ?? null;
$service = $data['service'] ?? null;

// Function to get population count by filters
// Fetch population counts based on year and service
function getPopulationCount($year, $service, $gender)
{
    global $conn;

    // Prepare SQL query
    $sql = "SELECT SUM(`$year`) AS total 
            FROM ";

    // Determine which table to query based on gender
    if ($gender === 'MF') {
        $sql .= "v1_male_female WHERE Community_Services = ?";
    } elseif ($gender === 'M') {
        $sql .= "v2_male WHERE Community_Services = ?";
    } else {
        $sql .= "v3_female WHERE Community_Services = ?";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $service);
    $stmt->execute();
    $stmt->bind_result($total);
    $stmt->fetch();
    $stmt->close();

    return $total ?: 0;
}

// Get counts for each gender
$male_count = (int) getPopulationCount($year, $service, 'M'); // Ensure it's an integer
$female_count = (int) getPopulationCount($year, $service, 'F'); // Ensure it's an integer
$both_count = (int) getPopulationCount($year, $service, 'MF'); // Ensure it's an integer

// Return the counts as JSON
echo json_encode(['counts' => ['M' => $male_count, 'F' => $female_count, 'MF' => $both_count]]);
