<?php
if (empty($input)) {
    return;
}
if (!empty($input)) {
    $months = [
        'Январь',
        'Февраль',
        'Март',
        'Апрель',
        'Май',
        'Июнь',
        'Июль',
        'Август',
        'Сентябрь',
        'Октябрь',
        'Ноябрь',
        'Декабрь'
    ];

    $seasons = [
        'winter' => [12, 1, 2],
        'spring' => [3, 4, 5],
        'summer' => [6, 7, 8],
        'autumn' => [9, 10, 11]
    ];

    $seasonsText = [
        'winter' => 'Зима',
        'spring' => 'Весна',
        'summer' => 'Лето',
        'autumn' => 'Осень'
    ];

    $dates = explode(',', $input);
    $currentDate = time(); // current date as a timestamp

    // Grouping dates by month
    $groupedDates = array_fill_keys($months, []);
    foreach ($dates as $date) {
        $timestamp = strtotime($date); // Converting a string to a timestamp
        // Check if the date has passed relative to the current date
        if ($timestamp > $currentDate) {
            $monthNumber = date('n', strtotime($date));
            $month = $months[$monthNumber - 1];
            $groupedDates[$month][] = date('d', strtotime($date));
        }
    }

    // Output the result
    $output = '';
    foreach ($seasons as $season => $monthsInSeason) {
        $countNoDirections = 0;
        $outputSeason = '';
        $outputSeason .= '<li class="tour__season ' . $season . '">
                            <ul>
                                <li class="season__title">
                                ' . $seasonsText[$season] . '
                                 </li>';
        $outputMonths = '';
        foreach ($monthsInSeason as $monthIndex) {
            $month = $months[$monthIndex - 1];
            if (!empty($groupedDates[$month])) {
                $outputMonths .= '<li>
                                    <span>' . $month . ':</span>' . implode(' ', $groupedDates[$month]) . '
                                </li>';
            } else {
                $countNoDirections++;
                $outputMonths .= '<li class="no-dates">
                                    <span>' . $month . ':</span>отправлений нет
                                </li>';
            }
        }
        if ($countNoDirections === 3) {
            $outputSeason = '<li class="tour__season no-dates ' . $season . '">
                            <ul>
                                <li class="season__title">
                                ' . $seasonsText[$season] . '
                                 </li>
                                 <li>отправлений нет</li>';;
        } else {
            $outputSeason .= $outputMonths;
        }
        $outputSeason .= '</ul></li>';
        $output .= $outputSeason;
    }
    return $output;
}
