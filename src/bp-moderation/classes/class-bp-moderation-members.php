<?php
/**
 * BuddyBoss Moderation Members Classes
 *
 * @since   BuddyBoss 2.0.0
 * @package BuddyBoss\Moderation
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Database interaction class for the BuddyBoss moderation Members.
 *
 * @since BuddyBoss 2.0.0
 */
class BP_Moderation_Members extends BP_Moderation_Abstract {

	/**
	 * Item type
	 *
	 * @var string
	 */
	public static $moderation_type = 'user';

	/**
	 * BP_Moderation_Members constructor.
	 *
	 * @since BuddyBoss 2.0.0
	 */
	public function __construct() {

		parent::$moderation[ self::$moderation_type ] = self::class;
		$this->item_type                              = self::$moderation_type;

		add_filter( 'bp_moderation_content_types', array( $this, 'add_content_types' ) );

		/**
		 * Moderation code should not add for WordPress backend or IF component is not active or Bypass argument passed for admin
		 */
		if ( ( is_admin() && ! wp_doing_ajax() ) || self::admin_bypass_check() ) {
			return;
		}

		/**
		 * If moderation setting enabled for this content then it'll filter hidden content.
		 */
		add_filter( 'bp_suspend_member_get_where_conditions', array( $this, 'update_where_sql' ), 10, 2 );

		// Code after below condition should not execute if moderation setting for this content disabled.
		if ( ! bp_is_moderation_member_blocking_enable( 0 ) ) {
			return;
		}

		// Update report button.
		add_filter( "bp_moderation_{$this->item_type}_button", array( $this, 'update_button' ), 10, 2 );
		add_filter( 'bp_init', array( $this, 'restrict_member_profile' ), 4 );

		add_filter( 'bp_core_get_user_domain', array( $this, 'bp_core_get_user_domain' ), 9999, 2 );
		add_filter( 'get_the_author_user_nicename', array( $this, 'get_the_author_name' ), 9999, 2 );
		add_filter( 'get_the_author_user_login', array( $this, 'get_the_author_name' ), 9999, 2 );
		add_filter( 'get_the_author_user_email', array( $this, 'get_the_author_name' ), 9999, 2 );
		add_filter( 'get_the_author_display_name', array( $this, 'get_the_author_name' ), 9999, 2 );
		add_filter( 'bp_core_get_user_displayname', array( $this, 'get_the_author_name' ), 9999, 2 );
		add_filter( 'get_avatar_url', array( $this, 'get_avatar_url' ), 9999, 3 );
		add_filter( 'bp_core_fetch_avatar_url_check', array( $this, 'bp_fetch_avatar_url' ), 1005, 2 );

		// Validate item before proceed.
		add_filter( "bp_moderation_{$this->item_type}_validate", array( $this, 'validate_single_item' ), 10, 2 );
	}

	/**
	 * Get permalink
	 *
	 * @since BuddyBoss 2.0.0
	 *
	 * @param int $member_id member id.
	 *
	 * @return string
	 */
	public static function get_permalink( $member_id ) {
		return add_query_arg( array( 'modbypass' => 1 ), bp_core_get_user_domain( $member_id ) );
	}

	/**
	 * Get Content owner id.
	 *
	 * @since BuddyBoss 2.0.0
	 *
	 * @param integer $member_id Group id.
	 *
	 * @return int
	 */
	public static function get_content_owner_id( $member_id ) {
		return ( ! empty( $member_id ) ) ? $member_id : 0;
	}

	/**
	 * Add Moderation content type.
	 *
	 * @since BuddyBoss 2.0.0
	 *
	 * @param array $content_types Supported Contents types.
	 *
	 * @return mixed
	 */
	public function add_content_types( $content_types ) {
		$content_types[ self::$moderation_type ] = __( 'User', 'buddyboss' );

		return $content_types;
	}

	/**
	 * Update where query remove blocked users
	 *
	 * @since BuddyBoss 2.0.0
	 *
	 * @param string $where   blocked users Where sql.
	 * @param object $suspend suspend object.
	 *
	 * @return array
	 */
	public function update_where_sql( $where, $suspend ) {
		$this->alias = $suspend->alias;

		$sql = $this->exclude_where_query();
		if ( ! empty( $sql ) ) {
			$where['moderation_where'] = $sql;
		}

		return $where;
	}

	/**
	 * Function to modify the button class
	 *
	 * @since BuddyBoss 2.0.0
	 *
	 * @param array  $button      Button args.
	 * @param string $is_reported Item reported.
	 *
	 * @return string
	 */
	public function update_button( $button, $is_reported ) {
		if ( $is_reported ) {
			$button['button_attr']['class'] = 'blocked-member';
		} else {
			$button['button_attr']['class'] = 'block-member';
		}

		return $button;
	}

	/**
	 * If the displayed user is marked as a blocked, Show 404.
	 *
	 * @since BuddyBoss 2.0.0
	 */
	public function restrict_member_profile() {
		$user_id = bp_displayed_user_id();
		if ( bp_moderation_is_user_blocked( $user_id ) ) {
			buddypress()->displayed_user->id = 0;
			bp_do_404();

			return;
		}
	}

	/**
	 * Restrict User domain of blocked member.
	 *
	 * @since BuddyBoss 2.0.0
	 *
	 * @param string $domain  User domain link.
	 * @param int    $user_id User id.
	 *
	 * @return string
	 */
	public function bp_core_get_user_domain( $domain, $user_id ) {
		if ( bp_moderation_is_user_blocked( $user_id ) ) {
			return '';
		}

		return $domain;
	}

	/**
	 * Restrict User meta of blocked member.
	 *
	 * @since BuddyBoss 2.0.0
	 *
	 * @param string $value   User meta.
	 * @param int    $user_id User id.
	 *
	 * @return string
	 */
	public function get_the_author_meta( $value, $user_id ) {
		if ( bp_moderation_is_user_blocked( $user_id ) ) {
			return '';
		}

		return $value;
	}

	/**
	 * Restrict User meta name of blocked member.
	 *
	 * @since BuddyBoss 2.0.0
	 *
	 * @param string $value   User meta.
	 * @param int    $user_id User id.
	 *
	 * @return string
	 */
	public function get_the_author_name( $value, $user_id ) {

		$username_visible = isset( $_GET['username_visible'] ) ? sanitize_text_field( wp_unslash( $_GET['username_visible'] ) ) : false;
		if ( ! empty( $username_visible ) || ( bp_is_my_profile() && 'blocked-members' === bp_current_action() ) ) {
			return $value;
		}

		if ( ! bp_moderation_is_user_suspended( $user_id ) && bp_moderation_is_user_blocked( $user_id ) ) {
			return esc_html__( 'Blocked User', 'buddyboss' );
		}

		return $value;
	}

	/**
	 * Remove Profile photo for block member.
	 *
	 * @since BuddyBoss 2.0.0
	 *
	 * @param  string $retval      The URL of the avatar.
	 * @param  mixed  $id_or_email The Gravatar to retrieve. Accepts a user_id, gravatar md5 hash,
	 *                             user email, WP_User object, WP_Post object, or WP_Comment object.
	 * @param  array  $args        Arguments passed to get_avatar_data(), after processing.
	 * @return string
	 */
	public function get_avatar_url( $retval, $id_or_email, $args ) {
		$user = false;

		// Ugh, hate duplicating code; process the user identifier.
		if ( is_numeric( $id_or_email ) ) {
			$user = get_user_by( 'id', absint( $id_or_email ) );
		} elseif ( $id_or_email instanceof WP_User ) {
			// User Object.
			$user = $id_or_email;
		} elseif ( $id_or_email instanceof WP_Post ) {
			// Post Object.
			$user = get_user_by( 'id', (int) $id_or_email->post_author );
		} elseif ( $id_or_email instanceof WP_Comment ) {
			if ( ! empty( $id_or_email->user_id ) ) {
				$user = get_user_by( 'id', (int) $id_or_email->user_id );
			}
		} elseif ( is_email( $id_or_email ) ) {
			$user = get_user_by( 'email', $id_or_email );
		}

		// No user, so bail.
		if ( false === $user instanceof WP_User ) {
			return $retval;
		}

		if ( bp_moderation_is_user_blocked( $user->ID ) ) {
			return buddypress()->plugin_url . 'bp-core/images/mystery-man.jpg';
		}

		return $retval;
	}

	/**
	 * Get dummy URL from DB for Group and User
	 *
	 * @since BuddyBoss 2.0.0
	 *
	 * @param string $avatar_url URL for a locally uploaded avatar.
	 * @param array  $params     Array of parameters for the request.
	 *
	 * @return string $avatar_url
	 */
	public function bp_fetch_avatar_url( $avatar_url, $params ) {

		$item_id = ! empty( $params['item_id'] ) ? absint( $params['item_id'] ) : 0;
		if ( ! empty( $item_id ) && isset( $params['avatar_dir'] ) ) {

			// check for user avatar.
			if ( 'avatars' === $params['avatar_dir'] ) {
				if ( bp_moderation_is_user_blocked( $item_id ) ) {
					$avatar_url = buddypress()->plugin_url . 'bp-core/images/mystery-man.jpg';
				}
			}
		}

		return $avatar_url;
	}

	/**
	 * Filter to check the member is valid or not.
	 *
	 * @since BuddyBoss 2.0.0
	 *
	 * @param bool   $retval  Check item is valid or not.
	 * @param string $item_id item id.
	 *
	 * @return bool
	 */
	public function validate_single_item( $retval, $item_id ) {
		if ( empty( $item_id ) ) {
			return $retval;
		}

		$user = get_userdata( (int) $item_id );
		if ( empty( $user ) || ! $user->exists() ) {
			return false;
		}

		return $retval;
	}
}
