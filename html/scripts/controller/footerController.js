 //左侧页面控制器
mainApp.controller('footerController',function($scope, $routeParams, $http, $sce, $location, $rootScope, $window)
{
	var APIURL = config.host + config.links + '?' + config.jsoncallback;
	$http.jsonp(APIURL)
	.success(
		function(resp, status, header){
			var data = resp.data;
			if(resp.code == 200){
				$scope.links = data;
			} else {
				console.log('error code:'+resp.code);
			}
		}
	)
	.error(
		function(resp){
		    console.log('get templatedetail error');
		}
	);
});