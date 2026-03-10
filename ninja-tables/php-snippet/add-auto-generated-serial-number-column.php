<?php

add_filter('ninja_table_rendering_table_vars',function($table_vars, $table_id, $tableArray){
$target_id = 1658;
if($tableArray['table_id'] != $target_id) { return $table_vars; }

$counter_column = [
'name' => 'serial',
'key' => 'serial',
'title' => 'Serial',
];
array_unshift($table_vars['columns'], $counter_column);
return $table_vars;
},10,3);

add_action('ninja_tables_get_public_data',function($formatted_data, $ID){

$i = 1;
foreach($formatted_data as $key=>$value){
$formatted_data[$key]['serial'] = $i++;
}
return $formatted_data;
},10,2);
