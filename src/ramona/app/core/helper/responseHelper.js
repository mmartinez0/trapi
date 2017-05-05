(function () {
	'use strict';

	angular.module('app.core.helper')
		.factory('responseHelper', Factory);

	Factory.$inject = ['toastr'];

	function Factory(toastr) {

		console.log('responseHelper');

		var service = {
			normalize: normalize,
			failed: failed,
			showViolations: showViolations
		};

		return service;

		function normalize(response){

			console.log(response);

			if(response.data){
				console.log(response.data);	

				if( response.data.data){
					console.log(response.data.data);	
				}
			}

			if( response.status === 200 )
				return response.data;

			return failed(response);
		}

		function failed(response){
			if( response.data && response.data.error && response.data.error_description ){
				return {
					success: false,
					violations: response.data.error + ': ' + response.data.error_description
				};
			}
			return {
				success: false,
				violations: response.status + ': ' + response.statusText
			};
		}

		function showViolations(data){
			if( data && data.violations ){
				if(Array.isArray(data.violations)){
					var j = data.violations.length;
					for(var i=0; i<j; i++){
						var message = getMessageFromViolation(data.violations[i]);
						toastr.error(message, 'Error');
					}
				}
				else {
					toastr.error(data.violations, 'Error');
				}
			}
		}

		function getMessageFromViolation(violation){
			return (violation.field || '') + ': ' + (violation.message || '');
		}

	}
})();

