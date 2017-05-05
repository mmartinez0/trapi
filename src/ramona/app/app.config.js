(function () {

	'use strict';

	angular.module('app')
		.config(Config);

	Config.$inject = [ '$logProvider', '$stateProvider', '$urlRouterProvider' ];

	function Config($logProvider, $stateProvider, $urlRouterProvider) {

		//
		// app.Config() runs before app.Run()
		//
		console.log('app.Config');

		$urlRouterProvider.otherwise('/video');

        $stateProvider
            .state('videos', {
                url: '/video',
                controller: 'VideoController',
                controllerAs: 'viewModel',
                templateUrl: 'app/video/video.php',
                resolve: {
                    videos: function( VideoService ) {
                        return VideoService.getAllVideos();
                    }
                },
            });
            /*
            .state('video_form', {
                url: '/video/{post_id}',
                controller: 'VideoController',
                controllerAs: 'viewModel',
                templateUrl: 'app/video/video.php',
                resolve: {
                    video: function( $stateParams, VideoService ) {
                        return VideoService.getVideo( $stateParams.post_id );
                    }
                }
            })
            .state('video_preview', {
                url: '/video/{post_id}/preview',
                controller: 'VideoPreviewController',
                controllerAs: 'viewModel',
                templateUrl: 'app/video/video.php',
                resolve: {
                    video: function( $stateParams, VideoService ) {
                        return VideoService.getVideo( $stateParams.post_id );
                    }
                }
            });*/

	}
})();
