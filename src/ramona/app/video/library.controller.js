(function(){
    
    'use strict';

    angular.module('app.video')
        .controller('LibraryController',  Controller);

    Controller.$inject = [ '$rootScope', '$scope', '$http', 'DialogService', 'wordpress' ];

    function Controller( $rootScope, $scope, $http, DialogService, wordpress ) {

        console.log('LibraryController');

        $scope.video = DialogService.params().video;
        $scope.close = DialogService.resolve;
        $scope.library = null;

        var data = new FormData();
        data.append('action', 'query-attachments');
        data.append('post_id', '0');
        data.append('query[orderby]', 'date');
        data.append('query[post_mime_type]', 'image');
        data.append('query[order]', 'DESC');
        data.append('query[posts_per_page]', '40');
        data.append('query[paged]', '1');

        var config = {
            transformRequest: angular.identity,
            headers: {
                'Content-Type': undefined
            }
        };

        $http.post( wordpress.ajax_url, data, config )
            .then( done, done );

        function done( result ) {

            console.log( result );

            $scope.library = result.data.data;
        }
    }
})();
