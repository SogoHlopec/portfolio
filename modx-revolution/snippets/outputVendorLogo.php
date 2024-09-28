<?php
$q = $modx->newQuery('msVendor');
$q->groupby('msVendor.id');
$q->sortby('name', 'ASC');
$q->select(array('msVendor.id', 'name', 'logo'));
$output = '';
if ($q->prepare() && $q->stmt->execute()) {
    while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($row['name'] === $input) {
            // $output .= '<p value="'.$row['id'].'">'.$row['name'].'</p>';
            $output .= $row['logo'];
        }
    }
}
return $output;
