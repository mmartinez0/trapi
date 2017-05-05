<?php
if ( ! defined( 'ABSPATH' ) ) 
	exit;

final class SubscriptionsShortcode {

	static public function shortcode( $atts = array(), $content = '' ) {
		
		if( ! is_user_logged_in() )
			return '<p>Please login or register</p>';

		global $wpdb;

		$user_id = get_current_user_id();
		$user = get_user_by( 'id', $user_id );
		$member_id = get_user_meta( $user_id, 'MemberID', true );

		if( empty( $member_id ) )
			return $content;

		$table = $wpdb->prefix . 'trapi_member_subscription';
		$rows = $wpdb->get_results( "SELECT item_code, subscription_type, access_level, created_at FROM $table WHERE member_id = $member_id" );

		ob_start();?>

		<h2><?php echo $user->user_login; ?> (<?php echo $member_id; ?>)</h2>

		<table>
			<thead>
				<tr>
					<th><label>item_code</label></th>
					<th><label>subscription_type</label></th>
					<th><label>access_level</label></th>
					<th><label>created_at</label></th>
				</tr>
			</thead>
			<tbody><?php
				foreach ($rows as $row) {?>
					<tr>
						<td><?php echo $row->item_code; ?></td>
						<td><?php echo $row->subscription_type; ?></td>
						<td><?php echo $row->access_level; ?></td>
						<td><?php echo $row->created_at; ?></td>
					</tr><?php
				}?>
			</tbody>
		</table><?php
		return ob_end_flush();
	}
}

