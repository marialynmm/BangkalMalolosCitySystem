<?php
// Include your database connection
include "../Backend/connect.php";

// Get the request data
$data = json_decode(file_get_contents("php://input"), true);
$year = $data['year'] ?? null;
$service = $data['service'] ?? null;

// Function to get population count by filters
function getPopulationCount($year, $service, $gender)
{
    global $conn;

    // Ensure the year column is valid
    $result = $conn->query("SHOW COLUMNS FROM datawithservices");
    $validYears = [];
    while ($row = $result->fetch_assoc()) {
        if (preg_match('/^\d{4}$/', $row['Field'])) { // Check if the field name is a 4-digit year
            $validYears[] = $row['Field'];
        }
    }

    // Now use $validYears for the in_array check
    if (!in_array($year, $validYears)) {
        return 0; // Return 0 for invalid years
    }

    // Prepare SQL query to sum the selected year for the specific service and gender
    $sql = "SELECT SUM(`$year`) AS total 
            FROM datawithservices 
            WHERE Gender = ?";

    // Add condition for the selected service if provided
    if ($service) {
        $sql .= " AND Community_Services = ?";
    }

    // Prepare the statement
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        // Error preparing the statement
        return 0;
    }

    // Bind parameters based on whether service is provided
    if ($service) {
        $stmt->bind_param("ss", $gender, $service);
    } else {
        $stmt->bind_param("s", $gender);
    }

    // Execute the query
    $stmt->execute();
    $stmt->bind_result($total);
    $stmt->fetch();
    $stmt->close();

    return $total ?: 0; // Return 0 if no total found
}

// Calculate counts for Male and Female
$male_count = getPopulationCount($year, $service, 'M');
$female_count = getPopulationCount($year, $service, 'F');

// Return the counts as JSON
echo json_encode(['counts' => ['M' => $male_count, 'F' => $female_count]]);
