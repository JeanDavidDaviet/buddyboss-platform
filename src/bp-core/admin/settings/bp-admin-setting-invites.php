<?php

class BP_Admin_Setting_Invites extends BP_Admin_Setting_tab {

	public function initialize() {
		$this->tab_label = __( 'Invites', 'buddyboss' );
		$this->tab_name  = 'bp-invites';
		$this->tab_order = 15;
	}

	public function is_active() {
		return bp_is_active( 'invites' );
	}

	public function register_fields() {
		$this->add_section( 'bp-invites', __( 'User Invites Settings', 'buddyboss' ) );

		// Allow members to change the email subject.
		$this->add_field( 'bp-disable-invite-member-email-subject', __( 'Email Subject', 'buddyboss' ), 'bp_admin_setting_callback_member_invite_email_subject', 'intval' );

		// Allow members to change the email content.
		$this->add_field( 'bp-disable-invite-member-email-content', __( 'Email Content', 'buddyboss' ), 'bp_admin_setting_callback_member_invite_email_content', 'intval' );

		if ( true === bp_member_type_enable_disable() ) {

			// Allow members to invite member type.
			$this->add_field( 'bp-disable-invite-member-type', __( 'Invite Profile Type', 'buddyboss' ), 'bp_admin_setting_callback_member_invite_member_type', 'intval' );
		}
	}
}

return new BP_Admin_Setting_Invites;
