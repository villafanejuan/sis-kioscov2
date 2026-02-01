<?php
// DEBUG FILE - Check what parameters are being received
echo "<h1>Filter Debug Info</h1>";
echo "<pre>";
echo "GET Parameters:\n";
print_r($_GET);

$period = $_GET['period'] ?? 'today';
$dateFrom = $_GET['date_from'] ?? '';
$dateTo = $_GET['date_to'] ?? '';

echo "\n\nExtracted Values:\n";
echo "Period: " . $period . "\n";
echo "Date From: " . $dateFrom . " (empty: " . (empty($dateFrom) ? 'YES' : 'NO') . ")\n";
echo "Date To: " . $dateTo . " (empty: " . (empty($dateTo) ? 'YES' : 'NO') . ")\n";

echo "\n\nCondition Check:\n";
echo "period === 'custom': " . ($period === 'custom' ? 'TRUE' : 'FALSE') . "\n";
echo "!empty(dateFrom): " . (!empty($dateFrom) ? 'TRUE' : 'FALSE') . "\n";
echo "!empty(dateTo): " . (!empty($dateTo) ? 'TRUE' : 'FALSE') . "\n";
echo "ALL CONDITIONS: " . (($period === 'custom' && !empty($dateFrom) && !empty($dateTo)) ? 'TRUE' : 'FALSE') . "\n";

if ($period === 'custom' && !empty($dateFrom) && !empty($dateTo)) {
    echo "\n✓ Custom filter WOULD be applied with dates: $dateFrom to $dateTo\n";
} else {
    echo "\n✗ Custom filter NOT applied - using predefined period: $period\n";
}

echo "</pre>";
?>