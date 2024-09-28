<?php
if (empty($input)) {
    return;
}
if (!empty($input)) {
    $inputArray = explode(',', $input);
    $formattedDates = [];

    foreach ($inputArray as $date) {
        $date = trim($date);
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $dateTime = DateTime::createFromFormat('Y-m-d', $date);
            if ($dateTime) {
                $formattedDate = $dateTime->format('j F Y');
                $formattedDate = str_replace(
                    [
                        'January',
                        'February',
                        'March',
                        'April',
                        'May',
                        'June',
                        'July',
                        'August',
                        'September',
                        'October',
                        'November',
                        'December'
                    ],
                    [
                        'января',
                        'февраля',
                        'марта',
                        'апреля',
                        'мая',
                        'июня',
                        'июля',
                        'августа',
                        'сентября',
                        'октября',
                        'ноября',
                        'декабря'
                    ],
                    $formattedDate
                );
                $formattedDate = str_replace(' ', '&nbsp;', $formattedDate);
                $formattedDates[] = $formattedDate;
            } else {
                $formattedDates[] = 'Некорректная дата';
            }
        }
    }

    if (!empty($formattedDates)) {
        return implode(', ', $formattedDates);
    }
}

return 'Некорректные данные';
