(function () {
	
	'use strict';

	angular.module('app')
		.controller('HomeController', Controller);

	Controller.$inject = [];

	function Controller() {
	
		console.log('HomeController');

        var vm = this;

        vm.message = 'Welcome!';
	}
})();