(function(){
	'use strict';
	
	angular
		.module('app.widget')
		.directive('fileSelect', Directive);

	function  Directive(){
		return {
			link: function($scope, element, attrs, ctrl){
				element.bind("change", function(e){
					var file = (e.srcElement || e.target).files[0];
					$scope.getFile( file );
				})
			}
		}
	}	
})();
