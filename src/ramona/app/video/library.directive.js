(function () {
    
    'use strict';

    angular.module('app.video')
        .directive('imageLibrary', Directive);

    Directive.$inject = [ ];

    function Directive( ) {

        console.log('imageLibrary.Directive');

        return {
            restrict: 'A',
            link: _link
        }

        function _link (scope, element, attrs, controller) {

            console.log('imageLibrary.Directive.link');


            scope.$watch('library', function(library, oldValue) {
                
                if( ! library )
                    return;

                console.log( library );

                var j = library.length-1;

                for(var i = 0; i<j; i++ ){
                    element.append('<img width="100" height="100" src="' + library[i].url + '"/>' );
                }
            });
        }
    }
})();
