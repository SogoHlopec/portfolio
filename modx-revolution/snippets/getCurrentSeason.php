<?php
// Get the current month in numeric format (1-12)
$currentMonth = date('n');

// Defining the season
if ($currentMonth >= 3 && $currentMonth <= 5) {
    $season = 'spring';
} elseif ($currentMonth >= 6 && $currentMonth <= 8) {
    $season = 'summer';
} elseif ($currentMonth >= 9 && $currentMonth <= 11) {
    $season = 'autumn';
} else {
    $season = 'winter';
}
return $season;
