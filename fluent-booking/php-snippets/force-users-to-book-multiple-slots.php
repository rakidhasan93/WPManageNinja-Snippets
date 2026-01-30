
add_filter('fluent_booking/schedule_validation_rules_data', function($validationConfig, $postedData, $calendarEvent) {
    $startDate = $postedData['start_date'] ?? null;
    $minSlots = 4; // Minimum required slots
    
    // Check if start_date is an array (multiple slots)
    if (!is_array($startDate)) {
        // Force multiple slots - add validation rules
        $validationConfig['rules']['start_date'] = 'required|array|min:' . $minSlots;
        
        // Custom error messages - THIS IS THE KEY FIX
        $validationConfig['messages']['start_date.array'] = 'Please select minimum 4 slots.';
        $validationConfig['messages']['start_date.min'] = 'Please select minimum 4 slots.';
    } else {
        // Validate minimum number of slots
        $slotCount = count($startDate);
        if ($slotCount < $minSlots) {
            $validationConfig['rules']['start_date'] = 'required|array|min:' . $minSlots;
            $validationConfig['messages']['start_date.min'] = 'Please select minimum 4 slots.';
        }
    }
    
    return $validationConfig;
}, 10, 3);
