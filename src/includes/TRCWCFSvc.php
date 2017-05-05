<?php 
if ( ! defined( 'ABSPATH' ) ) 
	exit;

require_once( MIGUEL_PLUGIN_DIR_SRC . 'includes/TRCWCFSvcLog.php' );

define ( 'SOAP_WSDL', 'https://extdata.trapi.com/TRCAPI/TRCWCFSvc.svc?wsdl' );
define ( 'SOAP_WSDL_LOGIN', 'login' );
define ( 'SOAP_WSDL_PASSWORD', 'password' );

class TRCWCFSvc {

	static private function __connect() {

		$connection = new SoapClient( SOAP_WSDL, array( 'login' => SOAP_WSDL_LOGIN, 'password' => SOAP_WSDL_PASSWORD ) );
		return $connection;
	}

	static public function authenticateUser( $params_array ) {

		$log = TRCWCFSvcLog::create( 'AuthenticateUser', $params_array['loginName'] );
		
		$result = Miguel_Result::create();

		try {

			$connection = TRCWCFSvc::__connect();
			$response = $connection->__soapCall( 'AuthenticateUser', array( $params_array ) );
			$result->data = $response->AuthenticateUserResult;

			$log->request()->success( $response->AuthenticateUserResult->ErrorFlag, $response->AuthenticateUserResult );

		} catch( Exception $exception ){

			$result->failed()->setError( 'Exception', $exception->getMessage() );

			$log->request()->failed( 'Exception', $exception->getMessage() );
		}

		return $result;
	}


	static public function getUserSubscriptions( $params_array ) {

		$log = TRCWCFSvcLog::create( 'GetUserSubscriptions', $params_array );

		$result = Miguel_Result::create();
		try {

			$connection = TRCWCFSvc::__connect();
			$response = $connection->__soapCall( 'GetUserSubscriptions', array( $params_array ) );
			$result->data = $response->GetUserSubscriptionsResult;

			$log->request()->success( $response->GetUserSubscriptionsResult->ErrorFlag, $response->GetUserSubscriptionsResult );

			if( $result->data->ErrorFlag === 'NoSubscriptions' )
				return $result->failed()->setError( $result->data->ErrorFlag, 'No Subscriptions' );

			if( $result->data->ErrorFlag === 'InternalError' )
				return $result->failed()->setError( $result->data->ErrorFlag, 'Internal Error' );

			if( $result->data->ErrorFlag !== 'NoError' )
				return $result->failed()->setError( 'UnknownErrorFlag', 'Unknown Error Flag: ' . $result->data->ErrorFlag );

		} catch( Exception $exception ){

			$result->failed()->setError( 'Exception', $exception->getMessage() );

			$log->request()->failed( 'Exception', $exception->getMessage() );
		}

		return $result;
	}
}

