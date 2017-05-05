(function () {
	'use strict';

	angular.module('app.core.exception')
	  .config(Config);

	Config.$inject = ['$provide'];

	function Config($provide) {
		console.log('app.core.exception.Config');
		$provide.decorator('$exceptionHandler', Decorator);
	}

	Decorator.$inject = ['$delegate','logger'];

	function Decorator($delegate, logger) {
		return function (exception, cause) {
			logger.error(exception.message, { exception: exception, cause: cause });
			$delegate(exception, cause);
		}
	}
})();
