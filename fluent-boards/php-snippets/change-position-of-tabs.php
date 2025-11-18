add_filter('fluent_boards/task_tabs', function($tabs)  { 
             $reorderTabs = [
                'comment' => $tabs['comment'],
                'activity' => $tabs['activity'],
                'all' => $tabs['all'],
          ];
         return $reorderTabs;
});
