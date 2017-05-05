<?php
if ( ! defined( 'ABSPATH' ) )
	exit;

require_once( MIGUEL_PLUGIN_DIR_SRC . 'includes/TRCWCFSvc.php' );

$memberID = MIGUEL_Helper::get_post_value( 'memberID' );?>

<hr/>

<table class="form-table">
	<tr>
		<th scope="row">
			<label for="memberID">Member ID</label></th>
		<td>
			<input type="text" name="memberID" class="regular-text" value="<?php echo $memberID; ?>"/>
		</td>
	</tr>
</table><?php

submit_button('Get Subscriptions');

if( ! empty( $memberID ) ) {

	echo '<hr/>';

	$result = TRCWCFSvc::getUserSubscriptions( array('memberID' => $memberID) );

	if( $result->success === false ) {?>

		<h1><?php echo $result->error->code; ?></h1>
		<p><?php echo $result->error->message; ?></p><?php

	} else {?>

		<table>
			<caption><h1>Subscriptions</h1></caption>
			<thead>
				<tr>
					<th><label>accessLevel</label></th>
					<th><label>itemCode</label></th>
					<th><label>subscriptionType</label></th>
				</tr>
			</thead>
			<tbody><?php
				foreach ($result->data->Subscriptions->ItemSubscription as $subscription) {?>
					<tr>
						<td><?php echo $subscription->accessLevel; ?></td>
						<td><?php echo $subscription->itemCode; ?></td>
						<td><?php echo $subscription->subscriptionType; ?></td>
					</tr><?php
				}?>
			</tbody>
		</table><?php
	}
}?>
