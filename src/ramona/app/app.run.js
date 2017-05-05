(function () {

	'use strict';

	angular.module('app')
		.run(Run);

	Run.$inject = [ '$rootScope', '$state', '$stateParams', '$anchorScroll' ];

	function Run( $rootScope, $state, $stateParams, $anchorScroll ){

		//
		// app.Run() runs after app.Config()
		//
		console.log('app.Run');

		//
	    // It's very handy to add references to $state and $stateParams to the $rootScope
	    // so that you can access them from any scope within your applications.For example,
	    // <li ng-class="{ active: $state.includes('contacts.list') }"> will set the <li>
	    // to active whenever 'contacts.list' or one of its decendents is active.
	    //
	    $rootScope.$state = $state;
	    $rootScope.$stateParams = $stateParams;

	    $anchorScroll.yOffset = 50;
	}
})();