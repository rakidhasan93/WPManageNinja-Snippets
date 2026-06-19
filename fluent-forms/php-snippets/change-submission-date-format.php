add_action('admin_enqueue_scripts', function () {
    if (
        empty($_GET['page']) ||
        $_GET['page'] !== 'fluent_forms'
    ) {
        return;
    }

    $script = "
        window.fluent_forms_global_var = window.fluent_forms_global_var || {};
        window.fluent_forms_global_var.disable_time_diff = true;
        window.fluent_forms_global_var.wp_date_time_format = 'DD/MM/YYYY HH:mm';
    ";

    wp_add_inline_script('fluent_forms_global', $script, 'after');
    wp_add_inline_script('fluentform_form_entries', $script, 'before');
}, 999);
