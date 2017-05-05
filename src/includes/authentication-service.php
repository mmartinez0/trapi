<?php
if ( ! defined( 'ABSPATH' ) ) 
	exit;

require_once( MIGUEL_PLUGIN_DIR_SRC . 'includes/TRCWCFSvc.php' );

final class AuthenticationService {

	static public function create( $loginName, $password ) {

		$service = new AuthenticationService();
		$service->wp_authenticate( $loginName, $password );
	}

	public function wp_authenticate( $loginName, $password ) {

		$result = TRCWCFSvc::authenticateUser( array( 'loginName' => $loginName, 'password' => $password) );

		if( $result->success === false )
			return;

		$authenticatedUser = $result->data;

		$user = get_user_by('login', $loginName);

		if( ! empty( $user ) ) {

			if( user_can( $user->ID, 'manage_options' ) ) // Administrator
				return;
		}

		switch( $authenticatedUser->ErrorFlag ) {

			case 'NoError': {
				//
				// Successful authentication, and member is eligible for benefit.
				//
				if( empty( $user ))
					$this->register_user( $authenticatedUser, $password );

				break;
			}

			case 'BadCredentials': {
				//
				// Invalid username or password.
				//
				break;
			}

			case 'MemberNotEligible': {
				//
				// The credentials were correct, but this member is a member type or billing category that is not eligible for this benefit.
				//
				break;
			}

			case 'DuesNotCurrent': {
				//
				// The credentials were correct and the member would be eligible, but dues for the current dues year have not been paid.
				//
				break;
			}
			
			case 'InternalError': {
				//
				// The TRAPI web service had an unexpected error when attempting to authenticate the member.
				//
				break;
			}
		}
	}

	public function register_user( $authenticatedUser, $password ) {

		$userdata = array(
				'user_login' => $authenticatedUser->UserName,
				'user_pass' => $password,
				'user_email' => $authenticatedUser->EMail,
				'role' => get_option('default_role'), // $authenticatedUser->MemberType,
				'first_name' => $authenticatedUser->first_name,
				'last_name' => $authenticatedUser->last_name,
				'display_name' => $authenticatedUser->first_name . ' ' . $authenticatedUser->last_name
			);

		//
		// https://codex.wordpress.org/Function_Reference/wp_insert_user
		//
		$user_id = wp_insert_user( $userdata );

		if( is_wp_error( $user_id) ) {
			//
			// save the error, and move on...
			return;
		}

		$this->update_user_meta( $user_id, $authenticatedUser, '_created_timestamp' );

		$this->update_user_subscriptions( $user_id, $authenticatedUser->MemberID );
	}

	public function update_user_meta( $user_id, $authenticatedUser, $timestamp ) {

		update_user_meta( $user_id, 'BTAddress1', $authenticatedUser->BTAddress1 );
		update_user_meta( $user_id, 'BTAddress2', $authenticatedUser->BTAddress2 );
		update_user_meta( $user_id, 'BTCity', $authenticatedUser->BTCity );
		update_user_meta( $user_id, 'BTCountry', $authenticatedUser->BTCountry );
		update_user_meta( $user_id, 'BTStateProvince', $authenticatedUser->BTStateProvince );
		update_user_meta( $user_id, 'BTZip', $authenticatedUser->BTZip );
		update_user_meta( $user_id, 'EMail', $authenticatedUser->EMail );
		update_user_meta( $user_id, 'ErrorFlag', $authenticatedUser->ErrorFlag );
		update_user_meta( $user_id, 'FirstName', $authenticatedUser->FirstName );
		update_user_meta( $user_id, 'LastName', $authenticatedUser->LastName );
		update_user_meta( $user_id, 'MemberID', $authenticatedUser->MemberID );
		update_user_meta( $user_id, 'MemberType', $authenticatedUser->MemberType );
		update_user_meta( $user_id, 'ProfLevel', $authenticatedUser->ProfLevel );
		update_user_meta( $user_id, 'STAddress1', $authenticatedUser->STAddress1 );
		update_user_meta( $user_id, 'STAddress2', $authenticatedUser->STAddress2 );
		update_user_meta( $user_id, 'STCity', $authenticatedUser->STCity );
		update_user_meta( $user_id, 'STCountry', $authenticatedUser->STCountry );
		update_user_meta( $user_id, 'STStateProvince', $authenticatedUser->STStateProvince );
		update_user_meta( $user_id, 'STZip', $authenticatedUser->STZip );
		update_user_meta( $user_id, 'UserName', $authenticatedUser->UserName );
		update_user_meta( $user_id, 'WorkPhone', $authenticatedUser->WorkPhone );

		update_user_meta( $user_id, $timestamp, gmdate('Y-m-d H:i:s') );
	}

	public function update_user_subscriptions( $user_id, $member_id ) {

		global $wpdb;

		$result = TRCWCFSvc::getUserSubscriptions( array('memberID' => $member_id) );

		if( $result->success === false ) {
			//
			// TODO: log error...
			//
			return;
		}

		$table = $wpdb->prefix . 'trapi_member_subscription';

		$wpdb->delete( $table, array( 'member_id' => $member_id ), array( '%d' ) );

		foreach( $result->data->Subscriptions->ItemSubscription as $subscription ) {

			$wpdb->insert( 
				$table,
				array( 
					'member_id' => $member_id,
					'item_code' => $subscription->itemCode,
					'subscription_type' => $subscription->subscriptionType,
					'access_level' => $subscription->accessLevel
				),
				array(
					'%d',
					'%s',
					'%s',
					'%d'
				) 
			);
		}

		update_user_meta( $user_id, '_subscription_updated_at', gmdate('Y-m-d H:i:s') );
	}
}

