(function(){
    
    'use strict';

    angular.module('app.video')
        .controller('PlayerController',  Controller);

    Controller.$inject = [ '$rootScope', '$scope', '$http', 'DialogService', 'wordpress' ];

    function Controller( $rootScope, $scope, $http, DialogService, wordpress ) {

        console.log('PlayerController');

        $scope.fieldName = DialogService.params().fieldName;
        $scope.video = DialogService.params().video;
        $scope.close = DialogService.resolve;
        $scope.capture = _capture;
        $scope.trapiUrl = wordpress.trapi_url + $scope.video[$scope.fieldName];

        console.log('PlayerController.trapiUrl ', $scope.trapiUrl);

        $('#trapi-video').append( '<video id="video" autoplay controls><source src="' + $scope.trapiUrl + '" type="video/mp4" /></video>' );

        function _capture(){

            var canvas = document.getElementById('canvas');
            var video = document.getElementById('video');

            canvas.getContext('2d').drawImage(video, 0, 0, video.videoWidth, video.videoHeight);

            //$scope.videoWidth = video.videoWidth;
            //$scope.videoHeight = video.videoHeight;

            //
            // http://stackoverflow.com/questions/13198131/how-to-save-a-html5-canvas-as-image-on-a-server
            //
            var dataUrl  = null;

            try {
                dataUrl = canvas.toDataURL('image/png')
            }
            catch( e ) {
                console.log( e );
                alert( e.message );
                return;
            }

            var request = {
                data: dataUrl
            };

            return $http.post( wordpress.ajax_url + '?action=captureImage&post_id=' + $scope.video.id, request )
                .then( done, done );

            function done( response ) {
                
                console.log( response );
            }
        }
    }
})();
