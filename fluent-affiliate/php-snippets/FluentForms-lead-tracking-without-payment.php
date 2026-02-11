

/**
 * Add as a PHP snippet (WPCode / Code Snippets), run on frontend (or everywhere).
 * Tracks FluentForms non-payment submissions as FluentAffiliate leads.
 */

function fa_ff_lead_find_email($data) {
    if (!is_array($data)) {
        return '';
    }

    foreach ($data as $value) {
        if (is_array($value)) {
            $email = fa_ff_lead_find_email($value);
            if ($email) {
                return $email;
            }
            continue;
        }

        $maybe = sanitize_email((string) $value);
        if ($maybe && is_email($maybe)) {
            return $maybe;
        }
    }

    return '';
}

function fa_ff_lead_extract_response($entry, $formData) {
    if (is_array($formData) && !empty($formData)) {
        return $formData;
    }

    if (is_object($entry) && isset($entry->response)) {
        if (is_array($entry->response)) {
            return $entry->response;
        }
        $decoded = json_decode((string) $entry->response, true);
        if (is_array($decoded)) {
            return $decoded;
        }
    }

    return [];
}

function fa_ff_track_non_paid_lead($entry, $formData = [], $form = null) {
    if (!class_exists('\FluentAffiliate\App\Models\Referral')) {
        return;
    }

    $entryId = 0;
    if (is_numeric($entry)) {
        $entryId = (int) $entry;
    } elseif (is_object($entry) && isset($entry->id)) {
        $entryId = (int) $entry->id;
    } elseif (is_array($entry) && isset($entry['id'])) {
        $entryId = (int) $entry['id'];
    }

    if (!$entryId) {
        return;
    }

    $affiliate = \FluentAffiliate\App\Helper\Utility::getCurrentCookieAffiliate();
    if (!$affiliate || $affiliate->status !== 'active') {
        return;
    }

    $provider = 'fluent_forms_lead';

    $exists = \FluentAffiliate\App\Models\Referral::where('provider', $provider)
        ->where('provider_id', $entryId)
        ->first();

    if ($exists) {
        return;
    }

    $response = fa_ff_lead_extract_response($entry, $formData);

    $email = '';
    if (!empty($response['email'])) {
        $email = sanitize_email($response['email']);
    }
    if (!$email) {
        $email = fa_ff_lead_find_email($response);
    }
    if (!$email) {
        return;
    }

    // Self-referral protection
    if (\FluentAffiliate\App\Helper\Utility::isDisabledSelfReferral()) {
        $affPayEmail = (string) ($affiliate->payment_email ?? '');
        $affUserEmail = (string) ($affiliate->user->user_email ?? '');
        if ($email === $affPayEmail || $email === $affUserEmail) {
            return;
        }
    }

    // Create/update customer
    $customer = \FluentAffiliate\App\Models\Customer::where('email', $email)->first();
    if (!$customer) {
        $customer = new \FluentAffiliate\App\Models\Customer();
        $customer->email = $email;
    }

    if (!empty($response['first_name'])) {
        $customer->first_name = sanitize_text_field($response['first_name']);
    } elseif (!empty($response['names']['first_name'])) {
        $customer->first_name = sanitize_text_field($response['names']['first_name']);
    }

    if (!empty($response['last_name'])) {
        $customer->last_name = sanitize_text_field($response['last_name']);
    } elseif (!empty($response['names']['last_name'])) {
        $customer->last_name = sanitize_text_field($response['names']['last_name']);
    }

    $customer->by_affiliate_id = (int) $affiliate->id;
    $customer->save();

    $visit = \FluentAffiliate\App\Helper\Utility::getCurrentCookieVisit();

    // Default lead payout is 0.00; change via filter if needed.
    $leadAmount = (float) apply_filters('fa_ff_lead_amount', 0.00, $entryId, $response, $affiliate);

    $referralData = [
        'affiliate_id' => (int) $affiliate->id,
        'customer_id'  => (int) $customer->id,
        'visit_id'     => $visit ? (int) $visit->id : null,
        'description'  => 'FluentForm Lead #' . $entryId,
        'status'       => 'unpaid',
        'type'         => 'lead',
        'amount'       => $leadAmount,
        'order_total'  => 0,
        'currency'     => \FluentAffiliate\App\Helper\Utility::getCurrency(),
        'utm_campaign' => $visit ? $visit->utm_campaign : null,
        'provider'     => $provider,
        'provider_id'  => $entryId,
        'products'     => [],
    ];

    // Use FluentAffiliate recorder (latest version)
    (new \FluentAffiliate\App\Modules\Integrations\BaseConnector())->recordReferral($referralData);
}

/**
 * Hook multiple FluentForms submission actions for compatibility.
 */

add_action('fluentform_submission_inserted', 'fa_ff_track_non_paid_lead', 10, 3);
add_action('fluentform/after_insert_submission', 'fa_ff_track_non_paid_lead', 10, 3);
add_action('fluentform/submission_inserted', 'fa_ff_track_non_paid_lead', 10, 3);
