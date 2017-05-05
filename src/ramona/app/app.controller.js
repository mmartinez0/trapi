(function () {
	
	'use strict';

	angular.module('app')
		.controller('AppController', Controller);

	Controller.$inject = ['$scope', '$state', '$stateParams'];

	function Controller($scope, $state, $stateParams) {
	
		console.log('AppController');

		$scope.$state = $state;
		$scope.$stateParams = $stateParams;
	}
})();