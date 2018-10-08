<?php
/**
 * BuddyPress email template.
 *
 * Magic numbers:
 *  1.618 = golden mean.
 *  1.35  = default body_text_size multipler. Gives default heading of 20px.
 *
 * @since BuddyPress 2.5.0
 * @version 3.1.0
 *
 * @package BuddyBoss
 * @subpackage Core
 */

/*
Based on the Cerberus "Fluid" template by Ted Goas (http://tedgoas.github.io/Cerberus/).
License for the original template:


The MIT License (MIT)

Copyright (c) 2017 Ted Goas

Permission is hereby granted, free of charge, to any person obtaining a copy of
this software and associated documentation files (the "Software"), to deal in
the Software without restriction, including without limitation the rights to
use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
the Software, and to permit persons to whom the Software is furnished to do so,
subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$settings = bp_email_get_appearance_settings();

?><!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
	<meta charset="<?php echo esc_attr( get_bloginfo( 'charset' ) ); ?>">
	<meta name="viewport" content="width=device-width"> <!-- Forcing initial-scale shouldn't be necessary -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge"> <!-- Use the latest (edge) version of IE rendering engine -->
	<meta name="x-apple-disable-message-reformatting">  <!-- Disable auto-scale in iOS 10 Mail entirely -->
	<title></title> <!-- The title tag shows in email notifications, like Android 4.4. -->

	<!-- CSS Reset -->
	<style type="text/css">
		/* What it does: Remove spaces around the email design added by some email clients. */
		/* Beware: It can remove the padding / margin and add a background color to the compose a reply window. */
		html,
		body {
			Margin: 0 !important;
			padding: 0 !important;
			height: 100% !important;
			width: 100% !important;
		}

		/* What it does: Stops email clients resizing small text. */
		* {
			-ms-text-size-adjust: 100%;
			-webkit-text-size-adjust: 100%;
		}

		/* What is does: Centers email on Android 4.4 */
		div[style*="margin: 16px 0"] {
			margin: 0 !important;
		}

		/* What it does: Stops Outlook from adding extra spacing to tables. */
		table,
		td {
			mso-table-lspace: 0pt !important;
			mso-table-rspace: 0pt !important;
		}

		/* What it does: Fixes webkit padding issue. Fix for Yahoo mail table alignment bug. Applies table-layout to the first 2 tables then removes for anything nested deeper. */
		table {
			border-spacing: 0 !important;
			border-collapse: collapse !important;
			table-layout: fixed !important;
			Margin: 0 auto !important;
		}
		table table table {
			table-layout: auto;
		}

		/* What it does: Uses a better rendering method when resizing images in IE. */
		/* & manages img max widths to ensure content body images don't exceed template width. */
		img {
			-ms-interpolation-mode:bicubic;
			height: auto;
			max-width: 100%;
		}

		/* What it does: A work-around for email clients meddling in triggered links. */
		*[x-apple-data-detectors],  /* iOS */
		.x-gmail-data-detectors,    /* Gmail */
		.x-gmail-data-detectors *,
		.aBn {
			border-bottom: 0 !important;
			cursor: default !important;
			color: inherit !important;
			text-decoration: none !important;
			font-size: inherit !important;
			font-family: inherit !important;
			font-weight: inherit !important;
			line-height: inherit !important;
		}

		/* What it does: Prevents Gmail from displaying an download button on large, non-linked images. */
		.a6S {
			display: none !important;
			opacity: 0.01 !important;
		}

		/* If the above doesn't work, add a .g-img class to any image in question. */
			img.g-img + div {
			display: none !important;
		}

		/* What it does: Prevents underlining the button text in Windows 10 */
		.button-link {
			text-decoration: none !important;
		}

		/* Remove links underline */
		a {
			text-decoration: none !important;
		}
	</style>

</head>
<body class="email_bg" width="100%" bgcolor="<?php echo esc_attr( $settings['email_bg'] ); ?>" style="margin: 0; mso-line-height-rule: exactly;">
<table cellpadding="0" cellspacing="0" border="0" height="100%" width="100%" bgcolor="<?php echo esc_attr( $settings['email_bg'] ); ?>" style="border-collapse:collapse;" class="email_bg"><tr><td valign="top">
	<center style="width: 100%; text-align: <?php echo esc_attr( $settings['direction'] ); ?>;">

		<!-- Visually Hidden Preheader Text : BEGIN -->
		<div style="display: none; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;">
			{{email.preheader}}
		</div>
		<!-- Visually Hidden Preheader Text : END -->

		<div style="max-width: 600px; margin: auto;" class="email-container">
			<!--[if mso]>
			<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" align="center">
			<tr>
			<td>
			<![endif]-->

			<!-- Email Header : BEGIN -->
			<table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="100%" style="max-width: 600px;">
				<tr>
					<td style="text-align: left; padding: 50px 0 30px 0; font-family: sans-serif; mso-height-rule: exactly; font-weight: bold; color: <?php echo esc_attr( $settings['site_title_text_color'] ); ?>; font-size: <?php echo esc_attr( $settings['site_title_text_size'] . 'px' ); ?>" class="site_title_text_color site_title_text_size">
						<?php
						/**
						 * Fires before the display of the email template header.
						 *
						 * @since BuddyPress 2.5.0
						 */
						do_action( 'bp_before_email_header' );

						$blogname = bp_get_option( 'blogname' );
						$attachment_id = isset( $settings[ 'logo' ] ) ? $settings[ 'logo' ] : '';

						if ( !empty( $attachment_id ) ) {
							$image_src = wp_get_attachment_image_src( $attachment_id, array( 180, 41 ) );
							if ( !empty( $image_src ) ) {
								echo "<img src='" . esc_attr( $image_src[ 0 ] ) . "' alt='" . esc_attr( $blogname ) . "' style='margin:0; padding:0; border:none; display:block; max-height: 35px; width: auto;' border='0'>";
							} else {
								echo $blogname;
							}
						} else {
							echo $blogname;
						}

						/**
						 * Fires after the display of the email template header.
						 *
						 * @since BuddyPress 2.5.0
						 */
						do_action( 'bp_after_email_header' );
						?>
					</td>
					<td style="text-align: right; padding: 50px 0 30px 0; font-family: sans-serif; mso-height-rule: exactly; font-weight: normal; color: <?php echo esc_attr( $settings['recipient_text_color'] ); ?>; font-size: <?php echo esc_attr( $settings['recipient_text_size'] . 'px' ); ?>" class="recipient_text_color recipient_text_size">
						<?php
						/**
						 * Fires before the display of the email recipient.
						 *
						 * @since BuddyPress 3.1.1
						 */
						do_action( 'bp_before_email_recipient' );

						//echo bp_get_option( 'blogname' );
						if ( bp_is_email_customizer() ) {
							echo '{{recipient.name}} <img src="' . buddypress()->plugin_url . "bp-core/images/mystery-man.jpg" . '" width="42" height="42" style="border: 1px solid #b9babc; border-radius: 50%; margin-left: 10px; vertical-align: middle;" />';
						} else {
							bp_email_the_salutation( $settings );
						}

						/**
						 * Fires after the display of the email recipient.
						 *
						 * @since BuddyPress 3.1.1
						 */
						do_action( 'bp_after_email_recipient' );
						?>
					</td>
				</tr>
			</table>
			<!-- Email Header : END -->

			<!-- Email Body : BEGIN -->
			<table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" bgcolor="<?php echo esc_attr( $settings['body_bg'] ); ?>" width="100%" style="border-collapse: separate !important; max-width: 600px; border-radius: 5px; border: 1px solid <?php echo esc_attr( $settings['body_border_color'] ); ?>" class="body_bg">

				<!-- 1 Column Text : BEGIN -->
				<tr>
					<td>
						<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
							<tr>
								<td style="padding: 20px 40px; font-family: sans-serif; mso-height-rule: exactly; line-height: <?php echo esc_attr( floor( $settings['body_text_size'] * 1.618 ) . 'px' ); ?>; color: <?php echo esc_attr( $settings['body_text_color'] ); ?>; font-size: <?php echo esc_attr( $settings['body_text_size'] . 'px' ); ?>" class="body_text_color body_text_size">
									{{{content}}}
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<!-- 1 Column Text : BEGIN -->

			</table>
			<!-- Email Body : END -->

			<!-- Email Footer : BEGIN -->
			<br>
			<table role="presentation" cellspacing="0" cellpadding="0" border="0" align="<?php echo esc_attr( $settings['direction'] ); ?>" width="100%" style="max-width: 600px; border-radius: 5px;">
				<tr>
					<td style="padding: 20px 40px; width: 100%; font-size: <?php echo esc_attr( $settings['footer_text_size'] . 'px' ); ?>; font-family: sans-serif; mso-height-rule: exactly; line-height: <?php echo esc_attr( floor( $settings['footer_text_size'] * 1.618 ) . 'px' ); ?>; text-align: center; color: <?php echo esc_attr( $settings['footer_text_color'] ); ?>;" class="footer_text_color footer_text_size">
						<?php
						/**
						 * Fires before the display of the email template footer.
						 *
						 * @since BuddyPress 2.5.0
						 */
						do_action( 'bp_before_email_footer' );
						?>

						<span class="footer_text"><?php echo nl2br( stripslashes( $settings['footer_text'] ) ); ?></span>
						<p style="margin: 5px 0;"><?php _e( "If you don't want to receive these emails in the future, please ", 'buddyboss' ); ?><a href="{{{unsubscribe}}}" style="text-decoration: none;"><?php echo esc_html_x( 'unsubscribe', 'email', 'buddyboss' ); ?></a>.</p>

						<?php
						/**
						 * Fires after the display of the email template footer.
						 *
						 * @since BuddyPress 2.5.0
						 */
						do_action( 'bp_after_email_footer' );
						?>
					</td>
				</tr>
			</table>
			<!-- Email Footer : END -->

			<!--[if mso]>
			</td>
			</tr>
			</table>
			<![endif]-->
		</div>
	</center>
</td></tr></table>
<?php
if ( function_exists( 'is_customize_preview' ) && is_customize_preview() ) {
	wp_footer();
}
?>
</body>
</html>
