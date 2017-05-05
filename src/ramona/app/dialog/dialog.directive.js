(function () {
    
    'use strict';

    angular.module('app.dialog')
        .directive('bnModals', Directive);

    Directive.$inject = [ '$rootScope', 'DialogService' ];

    function Directive( $rootScope, DialogService ) {

        return( _link );

        function _link( scope, element, attributes ) {

            scope.subview = null;

            element.on(
                "click",
                function handleClickEvent( event ) {

                    if ( element[ 0 ] !== event.target )
                        return;
                    
                    scope.$apply( DialogService.reject );
                }
            );

            $rootScope.$on(
                "dialog.open",
                function handleModalOpenEvent( event, modalType ) {

                    scope.subview = modalType;
                }
            );

            $rootScope.$on(
                "dialog.close",
                function handleModalCloseEvent( event ) {

                    scope.subview = null;
                }
            );
        }
    }
})();
