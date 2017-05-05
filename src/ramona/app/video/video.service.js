(function () {
    
    'use strict';

    angular.module('app.video')
        .factory('VideoService', Factory);

    Factory.$inject = [ '$http', '$q', 'wordpress' ];

    function Factory( $http, $q, wordpress ) {

        console.log('app.VideoService');

        var _videos = null;
        var _cache = {};

        var service = {
            getAllVideos: _getAllVideos,
            getVideo: _getVideo,
            getMediaType: _getMediaType,
            getMediaStatus: _getMediaStatus
        };

        return service;

        function _getAllVideos() {

            if( _videos )
                return $q.when( _videos );

            return $http.get( wordpress.ajax_url + '?action=getAllVideos' )
                .then( done, done );

            function done( response ) {

                if( response.status != 200 ) {
                    console.log( response );
                    // log error
                    return [];
                }

                _videos = response.data.data;

                for(var i = _videos.length-1; i>-1; i--) {

                    var video = _videos[i];

                    video['video_file_url_type'] = _getMediaFileType( video.video_file_url );
                    video['mobile_video_file_url_type'] = _getMediaFileType( video.mobile_video_file_url );
                }
                return _videos;
            }
        }

        function _getMediaFileType( fileUrl ) {

            if( ! fileUrl )
                return;

            var arr = fileUrl.split('.');
            if( arr[arr.length-1].toLowerCase().trim() === 'mp4' )
                return 'video/mp4';
            return '';
        }

        function _getVideo( post_id ) {

            post_id = parseInt(post_id, 10);
            //console.log( 'post_id ' + post_id );
            //console.log( 'angular.isNumber(' + post_id + ') ' + angular.isNumber(post_id) );
            //console.log( '_cache[' + post_id + '] ' + (! _cache[post_id] ) );

            if( ! angular.isNumber(post_id) )
                return $q.when({}) ;

            return $http.get( wordpress.ajax_url + '?action=getVideo&post_id=' + post_id )
                .then( done, done );

            function done( response ) {
                
                var video = response.data.data;
                
                video['video_file_url_type'] = _getMediaFileType( video.video_file_url );
                video['mobile_video_file_url_type'] = _getMediaFileType( video.mobile_video_file_url );
                _cache[post_id] = video;

                return _cache[post_id];
            }
        }

        function _getMediaType( media_type_id ){

            if( media_type_id == '3' )
                return 'Flash';

            if( media_type_id == '10' )
                return 'HTML';
            
            if( media_type_id == '4' )
                return 'MP3';

            if( media_type_id == '6' )
                return 'PDF';
            
            return media_type_id; 
        }

        function _getMediaStatus( media_status ){

            if( media_status == '1' )
                return 'Active';

            if( media_status == '0' )
                return 'Disabled';
            
            return ''; 
        }
    }
})();
