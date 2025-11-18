jQuery(document).ready(function($) {

    // Replace h2 in .fcal_slot_content with <p>
    $('.fcal_slot_content h2').each(function() {
        $(this).replaceWith('<p class="fcal_slot_content_title">' + $(this).html() + '</p>');
    });

    // Replace h1 in .fcal_slot_info with <p>
    $('.fcal_slot_info h1').each(function() {
        $(this).replaceWith('<p class="fcal_slot_info_title">' + $(this).html() + '</p>');
    });

    // Replace h3 in .fcal_date_event_details_header with <p>
    $('.fcal_date_event_details_header h3').each(function() {
        $(this).replaceWith('<p class="fcal_date_event_details_header_title">' + $(this).html() + '</p>');
    });

    // Replace h2 in .calendar-month-year with <p>
    $('.calendar-month-year h2').each(function() {
        $(this).replaceWith('<p class="calendar-month-year-title">' + $(this).html() + '</p>');
    });

    // Replace h1 in .fcal_slot_heading with <p>
    $('.fcal_slot_heading h1').each(function() {
        $(this).replaceWith('<p class="fcal_slot_heading_title">' + $(this).html() + '</p>');
    });

});
