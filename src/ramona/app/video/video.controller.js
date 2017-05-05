(function () {
    
    'use strict';

    angular.module('app.video')
        .controller('VideoController',  Controller);

    Controller.$inject = [ '$scope', '$http', '$location', '$timeout', 'FileReaderService', 'DialogService', 'VideoService', 'videos', 'wordpress' ];

    function Controller( $scope, $http, $location, $timeout, FileReaderService, DialogService, VideoService, videos, wordpress ) {

        console.log('app.VideoController');

        var viewModel = this;

        viewModel.videos = videos;
        viewModel.video = null;
        viewModel.addVideo = _addVideo;
        viewModel.playVideo = _playVideo;
        viewModel.save = _save;
        viewModel.cancel = _cancel;
        viewModel.selectFile = _selectFile;
        viewModel.showVideoLibrary = _showVideoLibrary;

        $scope.getFile = _getFile;


        $scope.$on('editVideo', function(event, row) {
            _editVideo(row);
        });

        function _addVideo() {
        }

        function _editVideo( row ) {

        	VideoService.getVideo( row.id )
        		.then( done, done );

			function done( video ) {

				if( video.thumbnail ){
        			$('#video-poster').append( '<img src="' + _getThumbnailFile(video) + '" />' );
				}

				viewModel.video = video;
			}
        }

        function _playVideo( fieldName ) {

            var promise = DialogService.open( "player", { video: viewModel.video, fieldName: fieldName } );

            promise.then(
                function handleResolve( response ) {

                    console.log( "DialogService.resolved." );
                },
                function handleReject( error ) {

                    console.warn( "DialogService.rejected!" );
                }
            );
        }

        function _close() {

        	console.log( 'VideoController._close()' );

        	viewModel.video = null;
			$('#video-poster').empty();
        }

        function _cancel() {

        	console.log( 'VideoController._cancel()' );
        	_close();
        }

        function _save() {

        	console.log( 'VideoController._save()' );
        	_close();
        }

        function _getThumbnailFile( video ) {
            
            if( ! video.thumbnail )
                return '';

            if( video.thumbnail.file.indexOf('http') > -1 )
                return video.thumbnail.file;

            return wordpress.baseurl + '/' + video.thumbnail.file;
        }

		function _selectFile( event ){
            event.stopPropagation();
            $timeout(function() {
                angular.element('#fileSelector').trigger('click');
            }, 100);
		}

    	function _getFile( file ){

    		console.log('app.VideoController._getFile() ', file.name);

			FileReaderService.readAsDataUrl(file, $scope)
				.then(function( blob ) {

		            var formData = new FormData();
		            formData.append('datafile', file);
		            formData.append('filename', file.name);

		            var request = {
		                transformRequest: angular.identity,
		                headers: {
		                    'Content-Type': undefined
		                }
		            };

		            $http.post(wordpress.ramona_url + 'attach.php', formData, request )
		                .success(done)
		                .error(done);

		            function done( result ) {

		            	console.log( result );
						
						viewModel.video.thumbnail_id = result.data.thumbnail_id;
						viewModel.video.thumbnail = result.data.thumbnail;

						$('#video-poster').empty();
	        			$('#video-poster').append( '<img src="' + _getThumbnailFile(viewModel.video) + '" />' );

		            	return result;
		            }
				});
    	}

    	function _showVideoLibrary() {

            var promise = DialogService.open( "library", { video: viewModel.video } );

            promise.then(
                function handleResolve( response ) {

                    console.log( "library.resolved." );
                },
                function handleReject( error ) {

                    console.warn( "library.rejected!" );
                }
            );
    	}
    }
})();
