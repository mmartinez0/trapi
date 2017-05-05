<?php
if ( ! defined( 'ABSPATH' ) )
	exit;

require_once( MIGUEL_PLUGIN_DIR_SRC . 'includes/TRCWCFSvc.php' );

$loginName = MIGUEL_Helper::get_post_value( 'loginName' );
$password = MIGUEL_Helper::get_post_value( 'password' ); ?>

<hr/>

<table class="form-table">
	<tr>
		<th scope="row">
			<label for="loginName">Login</label></th>
		<td>
			<input type="text" name="loginName" class="regular-text" value="<?php echo $loginName; ?>"/>
		</td>
	</tr>
	<tr>
		<th scope="row">
			<label for="password">Password</label></th>
		<td>
			<input type="text" name="password" class="regular-text" value="<?php echo $password; ?>"/>
		</td>
	</tr>
</table><?php

submit_button('Authenticate User');

if( ! empty( $loginName ) && ! empty( $password ) ) {

	echo '<hr/>';

	$result = TRCWCFSvc::authenticateUser( array( 'loginName' => $loginName, 'password' => $password) );

	if( $result->success === false ) {?>

		<h1><?php echo $result->error->code; ?></h1>
		<p><?php echo $result->error->message; ?></p><?php

	} else {

		$authenticatedUser = $result->data; ?>

		<table>
			<tbody>
				<tr><th><label>BTAddress1</label></th><td><?php echo $authenticatedUser->BTAddress1; ?></td></tr>
				<tr><th><label>BTAddress2</label></th><td><?php echo $authenticatedUser->BTAddress2; ?></td></tr>
				<tr><th><label>BTCity</label></th><td><?php echo $authenticatedUser->BTCity; ?></td></tr>
				<tr><th><label>BTCountry</label></th><td><?php echo $authenticatedUser->BTCountry; ?></td></tr>
				<tr><th><label>BTStateProvince</label></th><td><?php echo $authenticatedUser->BTStateProvince; ?></td></tr>
				<tr><th><label>BTZip</label></th><td><?php echo $authenticatedUser->BTZip; ?></td></tr>
				<tr><th><label>EMail</label></th><td><?php echo $authenticatedUser->EMail; ?></td></tr>
				<tr><th><label>ErrorFlag</label></th><td><?php echo $authenticatedUser->ErrorFlag; ?></td></tr>
				<tr><th><label>FirstName</label></th><td><?php echo $authenticatedUser->FirstName; ?></td></tr>
				<tr><th><label>LastName</label></th><td><?php echo $authenticatedUser->LastName; ?></td></tr>
				<tr><th><label>MemberID</label></th><td><?php echo $authenticatedUser->MemberID; ?></td></tr>
				<tr><th><label>MemberType</label></th><td><?php echo $authenticatedUser->MemberType; ?></td></tr>
				<tr><th><label>ProfLevel</label></th><td><?php echo $authenticatedUser->ProfLevel; ?></td></tr>
				<tr><th><label>STAddress1</label></th><td><?php echo $authenticatedUser->STAddress1; ?></td></tr>
				<tr><th><label>STAddress2</label></th><td><?php echo $authenticatedUser->STAddress2; ?></td></tr>
				<tr><th><label>STCity</label></th><td><?php echo $authenticatedUser->STCity; ?></td></tr>
				<tr><th><label>STCountry</label></th><td><?php echo $authenticatedUser->STCountry; ?></td></tr>
				<tr><th><label>STStateProvince</label></th><td><?php echo $authenticatedUser->STStateProvince; ?></td></tr>
				<tr><th><label>STZip</label></th><td><?php echo $authenticatedUser->STZip; ?></td></tr>
				<tr><th><label>UserName</label></th><td><?php echo $authenticatedUser->UserName; ?></td></tr>
				<tr><th><label>WorkPhone</label></th><td><?php echo $authenticatedUser->WorkPhone; ?></td></tr>
			</tbody>
		</table><?php

		$result = TRCWCFSvc::getUserSubscriptions( array('memberID' => $authenticatedUser->MemberID) );

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
	}
}?>
