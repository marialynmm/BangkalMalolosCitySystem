<?php
session_start();
include 'connect.php'; // Database connection

// Assuming you are using POST method
$no_of_population = $_POST['no_of_population'];
$no_of_household = $_POST['no_of_household'];
$no_of_families = $_POST['no_of_families'];
$purok_st_sitio_blk_lot = strtoupper($_POST['purok_st_sitio_blk_lot']); // Convert to uppercase
$birthday = $_POST['birthday'];
$age = $_POST['age'];

// Convert gender to 'M' or 'F'
$gender_input = strtoupper($_POST['gender']); // Convert to uppercase
$gender = ($gender_input === 'MALE') ? 'M' : (($gender_input === 'FEMALE') ? 'F' : (($gender_input === 'LGBTQ') ? 'LGBTQ' :
            $gender_input));

$occupation = strtoupper($_POST['occupation']); // Convert to uppercase
$civil_status = strtoupper($_POST['civil_status']); // Convert to uppercase
$toilet_type = strtoupper($_POST['toilet_type']); // Convert to uppercase
$name = strtoupper($_POST['name']); // Convert to uppercase

$sql = "UPDATE brgy_bangkal_record_census_final 
        SET No_of_Population = ?, No_of_Household = ?, No_of_Families = ?, 
            Purok_St_Sitio_Blk_Lot = ?, BIRTHDAY = ?, AGE = ?, 
            GENDER = ?, OCCUPATION = ?, CIVIL_STATUS = ?, 
            TOILET_TYPE = ? 
        WHERE Name = ?";

$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param(
        'iisssssssss',
        $no_of_population,
        $no_of_household,
        $no_of_families,
        $purok_st_sitio_blk_lot,
        $birthday,
        $age,
        $gender,
        $occupation,
        $civil_status,
        $toilet_type,
        $name // Make sure this variable is included
    );

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Data updated successfully!";
    } else {
        $_SESSION['error_message'] = "Failed to update data: " . $stmt->error;
    }
} else {
    $_SESSION['error_message'] = "Failed to prepare statement: " . $conn->error;
}

$stmt->close();
$conn->close();

// Redirect back to the dashboard or the previous page
header("Location: ../Frontend/dashboard.php");
exit();
