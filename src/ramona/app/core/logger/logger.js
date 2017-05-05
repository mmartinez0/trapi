(function () {
	'use strict';

	angular.module('app.core.logger')
		.factory('logger', Factory);

	Factory.$inject = ['$log', 'toastr'];

	function Factory($log, toastr) {

		console.log('Logger');

		var config = miguel.config.log;

		var service = {
			error: error,
			info: info,
			success: success,
			warning: warning,
			debug: debug,
			log: $log.log
		};

		return service;

		function error(message, data, title) {
			toastr.error(message, title);
			$log.error('Error: ' + message, data || '');
		}

		function warning(message, data, title) {
			toastr.warning(message, title);
			$log.warn('Warning: ' + message, data || '');
		}

		function info(message, data, title) {
			toastr.info(message, title);
			$log.info('Info: ' + message, data || '');
		}

		function success(message, data, title) {
			toastr.success(message, title);
			$log.info('Success: ' + message, data || '');
		}

		function debug(message, data, title) {

			if( config.level !== 'debug')
				return;

			if( config.debug.toastr )
				toastr.info(message, title);

			if( config.debug.console )
				$log.info('Debug: ' + message, data || '');
		}
	}
})();
