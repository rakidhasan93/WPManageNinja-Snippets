<?php

/**
 * Update user role to after Fluent Forms transaction status changes to 'paid'.
 *
 * Possible payment statuses:
 * - paid               (Paid)
 * - processing         (Processing)
 * - pending            (Pending)
 * - failed             (Failed)
 * - refunded           (Refunded)
 * - partially-refunded (Partial Refunded)
 * - cancelled          (Cancelled)
 *
 * @param string $new_status     The new payment status. Should be 'paid' to trigger role update.
 * @param object $submission     The form submission object.
 * @param int    $transaction_id The transaction ID.
 */

function tct_ff_update_user_role_after_paid_status( $new_status, $submission, $transaction_id ) {
	/*
	 * Set the role that you want to add/update.
	 */
	$role = 'subscriber';

	/*
	 * Set the Fluent Form ID where this should run.
	 */
	$target_form_id = 123;

	// Only proceed if the new status is 'paid'.
	if ( strtolower( $new_status ) !== 'paid' ) {
		return;
	}

	// Only run for the specific form.
	if ( empty( $submission->form_id ) || intval( $submission->form_id ) !== $target_form_id ) {
		return;
	}

	// Check if submission object contains user_id.
	if ( empty( $submission->user_id ) ) {
		return;
	}

	$user_id = intval( $submission->user_id );

	// Confirm user exists.
	$user = get_userdata( $user_id );
	if ( ! $user ) {
		return;
	}

	// Replace the user's role.
	$user->set_role( $role );
}
add_action( 'fluentform/after_transaction_status_change', 'tct_ff_update_user_role_after_paid_status', 10, 3 );

