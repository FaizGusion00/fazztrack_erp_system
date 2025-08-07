<?php

require_once 'vendor/autoload.php';

use Carbon\Carbon;

// Test duration calculation
echo "Testing duration calculation...\n\n";

$startTime = Carbon::now()->subMinutes(30); // 30 minutes ago
$endTime = Carbon::now(); // now

echo "Start time: " . $startTime->format('Y-m-d H:i:s') . "\n";
echo "End time: " . $endTime->format('Y-m-d H:i:s') . "\n\n";

// Test the old (incorrect) method
$oldDuration = $endTime->diffInMinutes($startTime);
echo "Old method (incorrect): " . $oldDuration . " minutes\n";

// Test the new (correct) method
$newDuration = $startTime->diffInMinutes($endTime, false);
echo "New method (correct): " . $newDuration . " minutes\n\n";

if ($newDuration > 0) {
    echo "✓ Duration calculation is now working correctly!\n";
} else {
    echo "✗ Duration calculation still has issues\n";
} 