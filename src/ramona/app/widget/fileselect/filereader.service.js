(function(){
	'use strict';
	
	angular
		.module('app.widget')
		.factory('FileReaderService', Factory);

	Factory.$inject = [ '$q' ];

	function Factory( $q ){

		var service = {
			readAsDataUrl: readAsDataURL
		};

		return service;

		function readAsDataURL(file, scope){

			var deferred = $q.defer();
			var reader = getReader(deferred, scope);
			reader.readAsDataURL(file);

			return deferred.promise;
		}

		function getReader(deferred, scope){
			var reader = new FileReader();
			reader.onload = onLoad(reader, deferred, scope);
			reader.onerror = onError(reader, deferred, scope);
			reader.onprogress = onProgress(reader, scope);
			return reader;
		}

		function onLoad(reader, deferred, scope) {
            return function () {
                scope.$apply(function () {
                    deferred.resolve(reader.result);
                });
            };
		}

		function onError(reader, deferred, scope) {
			return function () {
				scope.$apply(function () {
					deferred.reject(reader.result);
				});
			};
		}

		function onProgress(reader, scope) {
			return function (event) {
				scope.$broadcast("fileProgress", {
					total: event.total,
					loaded: event.loaded
				});
			};
		}
	}
})();