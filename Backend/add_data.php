<?php
session_start();
include "../Backend/connect.php"; // Ensure your connection is here

// Get form data and convert to uppercase
$no_of_population = $_POST['no_of_population'];
$no_of_household = $_POST['no_of_household'];
$no_of_families = $_POST['no_of_families'];
$purok_st_sitio_blk_lot = $_POST['purok_st_sitio_blk_lot'];
$name = strtoupper($_POST['name']); // Convert to uppercase
$birthday = $_POST['birthday']; // Format: YYYY-MM-DD
$age = $_POST['age'];
$gender = strtoupper($_POST['gender']); // Convert to uppercase
$occupation = strtoupper($_POST['occupation']); // Convert to uppercase
$civil_status = strtoupper($_POST['civil_status']); // Convert to uppercase
$toilet_type = strtoupper($_POST['toilet_type']); // If you need to convert this as well, add strtoupper

// Convert birthday to the desired format
$birthday_datetime = $birthday . ' 00:00:00'; // Append time

// Prepare and execute your SQL statement
$sql = "INSERT INTO brgy_bangkal_record_census_final 
        (No_of_Population, No_of_Household, No_of_Families, Purok_St_Sitio_Blk_Lot, 
         Name, Birthday, Age, Gender, Occupation, Civil_Status, Toilet_Type) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    'iisssssssss',
    $no_of_population,
    $no_of_household,
    $no_of_families,
    $purok_st_sitio_blk_lot,
    $name,
    $birthday_datetime,
    $age,
    $gender,
    $occupation,
    $civil_status,
    $toilet_type
);

if ($stmt->execute()) {
    $_SESSION['success_message'] = "Data added successfully!";
} else {
    $_SESSION['error_message'] = "Failed to add data.";
}

$stmt->close();
$conn->close();

header(header: "Location: ../Frontend/dashboard.php");
exit();
