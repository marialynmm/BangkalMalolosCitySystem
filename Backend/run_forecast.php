<?php
$output = [];
$return_var = null;

// Execute the batch file directly
exec('C:\\xampp\\htdocs\\BangkalMalolosCitySystem\\Backend\\python\\run_forecast.bat 2>&1', $output, $return_var);

// Return output for debugging
echo "<pre>";
print_r($output);
echo "Return status: " . $return_var;
echo "</pre>";
?>
