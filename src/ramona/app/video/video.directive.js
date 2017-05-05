(function () {
    
    'use strict';

    angular.module('app.video')
        .directive('videos', Directive);

    Directive.$inject = [ 'VideoService' ];

    function Directive( VideoService ) {

        console.log('videos.Directive');

        return {
            restrict: 'A',
            link: _link
        }

        function _link (scope, element, attrs, controller) {

            console.log('videos.Directive.link');

            element.DataTable({
                data: scope.viewModel.videos,
                columns: [
                    { data: 'id', title: 'ID', render: _renderId, width: '20px' },
                    { data: 'post_title', title: 'Title' },
                    { data: 'media_type_id', title: 'Media Type', render: _renderMediaType, width: '20px' },
                    { data: 'media_status', title: 'Media Status', render: _renderMediaStatus, width: '20px' },
                    { data: 'presenter', title: 'Presenter', width: '60px' },
                    { data: 'thumbnail_id', title: 'Poster', width: '20px' },
                    { data: 'access_level', title: 'Access Level', width: '20px' },
                    { data: 'post_status', title: 'Publish', width: '20px' },
                    { data: 'post_modified', title: 'Modified', width: '20px' }
                ],
                rowSelectable: true
            });

            element.on('click', '[data-emit]', function(ev) {
                //console.log('click');
                ev.stopPropagation();
                _emit(ev, scope, element);
            });

        }

        function _renderId( data, type, row ) {
            //return '<a href="#/video/' + data + '">' + data + '</a>';
            return '<a href="#" data-emit="editVideo" >' + data + '</a>';
        }

        function _renderMediaType( data, type, row ) {

            return VideoService.getMediaType( data );
        }

        function _renderMediaStatus( data, type, row ) {

            return VideoService.getMediaStatus( data );
        }

        function _emit(ev, scope, $table) {

            var element = $(ev.target);
            scope.$emit( element.attr('data-emit'), $table.DataTable().row(element.closest('tr')).data() );
        }
    }
})();
