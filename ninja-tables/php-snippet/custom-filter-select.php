<?php
add_filter('ninja_tables_get_public_data', function ($rows, $tableId) {
    if ((int) $tableId !== 41795) {
        return $rows;
    }

    return array_values(array_filter($rows, function ($row) {
        return isset($row['location'], $row['cat2'])
            && (string) $row['location'] === 'SCV'
            && (string) $row['cat2'] === 'LBTB Acting Academy';
    }));
}, 10, 2);
>?
