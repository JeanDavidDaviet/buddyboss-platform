<?php
/**
 * Admin Single Reported item screen
 *
 * @since   BuddyBoss 2.0.0
 * @package BuddyBoss
 */

$current_tab       = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_STRING );
$is_content_screen = ! empty( $current_tab ) && 'reported-content' === $current_tab;
$error             = isset( $_REQUEST['error'] ) ? $_REQUEST['error'] : false; // phpcs:ignore
$admins            = array_map( 'intval', get_users(
		array(
			'role'   => 'administrator',
			'fields' => 'ID',
		)
	) );
?>
<div class="wrap">
	<h1>
		<?php
		/* translators: accessibility text */
		if ( $is_content_screen ) {
			printf( esc_html__( 'View Reported Content', 'buddyboss' ) );
		} else {
			printf( esc_html__( 'View Blocked Member', 'buddyboss' ) );
		}
		?>
	</h1>

	<?php if ( ! empty( $moderation_request_data ) ) : ?>
		<div id="poststuff">
			<div id="post-body"
				class="metabox-holder columns-<?php echo 1 === (int) get_current_screen()->get_columns() ? '1' : '2'; ?>">
				<div id="post-body-content">
					<div id="postdiv">
						<div id="bp_moderation_action" class="postbox">
							<div class="inside">

								<?php if ( ! empty( $messages ) ) : ?>
									<div id="moderation"
										class="<?php echo ( ! empty( $error ) ) ? 'error' : 'updated'; ?>">
										<p><?php echo wp_kses_post( implode( "<br/>\n", $messages ) ); ?></p>
									</div>
								<?php endif; ?>

								<div class="bp-moderation-ajax-msg hidden notice notice-success">
									<p></p>
								</div>

								<table class="form-table">
									<tbody>
									<?php if ( $is_content_screen ) { ?>
										<tr>
											<td scope="row" style="width: 20%;">
												<label>
													<strong>
														<?php
														/* translators: accessibility text */
														esc_html_e( 'Content Type', 'buddyboss' );
														?>
													</strong>
												</label>
											</td>
											<td>
												<?php
												echo esc_html( bp_moderation_get_content_type( $moderation_request_data->item_type ) );
												?>
											</td>
										</tr>
										<tr>
											<td scope="row" style="width: 20%;">
												<strong><label>
														<?php
														/* translators: accessibility text */
														esc_html_e( 'Content ID', 'buddyboss' );
														?>
													</label></strong>
											</td>
											<td>
												<?php
												echo esc_html( $moderation_request_data->item_id );
												?>
											</td>
										</tr>
										<tr>
											<td scope="row" style="width: 20%;">
												<strong><label>
														<?php
														/* translators: accessibility text */
														esc_html_e( 'Content Owner', 'buddyboss' );
														?>
													</label></strong>
											</td>
											<td>
												<?php
												$user_id = bp_moderation_get_content_owner_id( $moderation_request_data->item_id, $moderation_request_data->item_type );
												printf( '<strong>%s</strong>', wp_kses_post( bp_core_get_userlink( $user_id ) ) );
												?>
											</td>
										</tr>
										<tr>
											<td scope="row" style="width: 20%;">
												<strong><label>
														<?php
														/* translators: accessibility text */
														esc_html_e( 'Permalink', 'buddyboss' );
														?>
													</label></strong>
											</td>
											<td>
												<?php
												echo wp_kses_post(
													sprintf(
														'<a href="%s" title="%s"> %s </a>',
														esc_url( bp_moderation_get_permalink( $moderation_request_data->item_id, $moderation_request_data->item_type ) ),
														esc_attr__( 'View', 'buddyboss' ),
														esc_html__( 'View Content', 'buddyboss' )
													)
												);
												?>
											</td>
										</tr>
										<tr>
											<td scope="row" style="width: 20%;">
												<strong><label>
														<?php
														/* translators: accessibility text */
														esc_html_e( 'Reported (Count)', 'buddyboss' );
														?>
													</label></strong>
											</td>
											<td>
												<?php
												/* translators: accessibility text */
												printf( esc_html( _n( '%s time', '%s times', $moderation_request_data->count, 'buddyboss' ) ), esc_html( number_format_i18n( $moderation_request_data->count ) ) );
												?>
											</td>
										</tr>
									<?php } else { ?>
										<tr>
											<td scope="row" style="width: 20%;">
												<strong><label>
														<?php
														/* translators: accessibility text */
														esc_html_e( 'Blocked Member', 'buddyboss' );
														?>
													</label></strong>
											</td>
											<td>
												<?php
												$user_id = bp_moderation_get_content_owner_id( $moderation_request_data->item_id, $moderation_request_data->item_type );
												printf( '<strong>%s</strong>', wp_kses_post( bp_core_get_userlink( $user_id ) ) );
												?>
											</td>
										</tr>
										<tr>
											<td scope="row" style="width: 20%;">
												<strong><label>
														<?php
														/* translators: accessibility text */
														esc_html_e( 'Times Blocked', 'buddyboss' );
														?>
													</label></strong>
											</td>
											<td>
												<?php
												/* translators: accessibility text */
												printf( esc_html( _n( '%s time', '%s times', $moderation_request_data->count, 'buddyboss' ) ), esc_html( number_format_i18n( $moderation_request_data->count ) ) );
												?>
											</td>
										</tr>
									<?php } ?>
									</tbody>
								</table>

								<?php
								$bp_moderation_report_list_table = new BP_Moderation_Report_List_Table();
								// Prepare the group items for display.
								$bp_moderation_report_list_table->prepare_items();
								$bp_moderation_report_list_table->views();
								$bp_moderation_report_list_table->display();

								$action_type  = ( 1 === (int) $moderation_request_data->hide_sitewide ) ? 'unhide' : 'hide';
								$action_label = ( 'unhide' === $action_type ) ? esc_html__( 'Unhide Content', 'buddyboss' ) : esc_html__( 'Hide Content', 'buddyboss' );
								?>
								<div class="bp-moderation-actions">
									<?php
									if ( $is_content_screen ) {

										$user_id          = bp_moderation_get_content_owner_id( $moderation_request_data->item_id, $moderation_request_data->item_type );
										$user_action_type = 'hide';
										$user_data        = BP_Moderation::get_specific_moderation( $user_id, BP_Moderation_Members::$moderation_type );
										$user_action_text = esc_html__( 'Suspend Content Author', 'buddyboss' );
										if ( ! empty( $user_data ) ) {
											$user_action_type = ( 1 === (int) $user_data->hide_sitewide ) ? 'unsuspend' : 'suspend';
											$user_action_text = ( 'unsuspend' === $user_action_type ) ? esc_html__( 'Unsuspend Content Author', 'buddyboss' ) : esc_html__( 'Suspend Content Author', 'buddyboss' );
										}
										?>
										<a href="javascript:void(0);"
											class="button button-primary bp-hide-request single-report-btn"
											data-id="<?php echo esc_attr( $moderation_request_data->item_id ); ?>"
											data-type="<?php echo esc_attr( $moderation_request_data->item_type ); ?>"
											data-nonce="<?php echo esc_attr( wp_create_nonce( 'bp-hide-unhide-moderation' ) ); ?>"
											data-action="<?php echo esc_attr( $action_type ); ?>"
											title="<?php echo esc_html( $action_label ); ?>">
											<?php
											echo esc_html( $action_label );
											?>
										</a>
										<?php
										if ( ! in_array( $user_id, $admins, true ) ) {
											?>
											<a href="javascript:void(0);"
												class="button button-primary bp-block-user single-report-btn content-author"
												data-id="<?php echo esc_attr( $user_id ); ?>" data-type="user"
												data-nonce="<?php echo esc_attr( wp_create_nonce( 'bp-hide-unhide-moderation' ) ); ?>"
												data-action="<?php echo esc_attr( $user_action_type ); ?>"
												title="<?php echo esc_attr( $user_action_text ); ?>">
												<?php
												echo esc_html( $user_action_text );
												?>
											</a>
											<?php
										}
									} else {
										if ( ! in_array( $user_id, $admins, true ) ) {
											$action_type        = ( 'unhide' === $action_type ) ? 'unsuspend' : 'suspend';
											$member_action_text = ( 'unsuspend' === $action_type ) ? esc_html__( 'Unsuspend Member', 'buddyboss' ) : esc_html__( 'Suspend Member', 'buddyboss' );
											?>
											<a href="javascript:void(0);"
												class="button button-primary bp-block-user single-report-btn"
												data-id="<?php echo esc_attr( $moderation_request_data->item_id ); ?>"
												data-type="user"
												data-nonce="<?php echo esc_attr( wp_create_nonce( 'bp-hide-unhide-moderation' ) ); ?>"
												data-action="<?php echo esc_attr( $action_type ); ?>"
												title="<?php echo esc_attr( $action_label ); ?>">
												<?php
												echo esc_html( $member_action_text );
												?>
											</a>
											<?php
										}
									}
									?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php else : ?>
		<p>
			<?php
			printf(
				'%1$s <a href="%2$s">%3$s</a>',
				esc_html__( 'No moderation found with this ID.', 'buddyboss' ),
				esc_url( bp_get_admin_url( 'admin.php?page=bp-moderation' ) ),
				esc_html__( 'Go back and try again.', 'buddyboss' )
			);
			?>
		</p>
	<?php endif; ?>
</div>