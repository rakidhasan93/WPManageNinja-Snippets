var from = "from";      // name attribute of the start time field
var to = "to";          // name attribute of the end time field
var showHere = "showHere"; // name attribute of the field to show difference

let fpFrom = flatpickr("#ff_38_from", { enableTime: true, noCalendar: true, dateFormat: "H:i" });
let fpTo   = flatpickr("#ff_38_to",   { enableTime: true, noCalendar: true, dateFormat: "H:i" });

function getMinuteDiff(input_name_1, input_name_2) {
    var $date1 = $form.find('input[name='+input_name_1+']');
    var $date2 = $form.find('input[name='+input_name_2+']');

    var val1 = $date1.val();
    var val2 = $date2.val();

    if(!val1 || !val2) {
        return 0;
    }

    // Parse both times
    var time1 = new Date(fpFrom.parseDate(val1, $date1.data('format'))).getTime();
    var time2 = new Date(fpTo.parseDate(val2, $date2.data('format'))).getTime();

    // Difference in minutes
    return Math.floor((time2 - time1) / (1000 * 60));
}

// Trigger calculation on change
$form.find('input[name='+from+'],input[name='+to+']').on('change', function() {
    var diffMinutes = getMinuteDiff(from, to);
    if(diffMinutes < 0) diffMinutes = 0; // prevent negatives
    $form.find('input[name='+showHere+']').val(diffMinutes).trigger('change');
});
