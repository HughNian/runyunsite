//动态列表控制器
mainApp.controller('dynamiclistController',function($scope, $routeParams, $http, $sce, $location, $route)
{
	var typeid = $routeParams.typeid;
	if(!isDefine(typeid)){
		var APIURL = config.host + config.dynamic + '?' + config.jsoncallback;
	} else {
		var APIURL = config.host + config.dynamic + '?typeid=' + typeid + '&' + config.jsoncallback;
	}
	console.log(APIURL);
	$http.jsonp(APIURL)
	.success(
		function(resp, status, header){
			if(resp.code == 200){
				var data = resp.data;
				$scope.types = data.types;
				$scope.dynamics = data.dynamics;
				
				$scope.$on('ngRepeatFinished', function (ngRepeatFinishedEvent){
					  if(typeof typeid == 'undefined'){
						  $(".col-md-4:first").attr('id', 'cur');
					  } else {
						  $(".col-md-4").removeAttr('id');
						  $("h4[typeid='"+typeid+"']").parent().attr('id', 'cur');
					  }
				});
				
				$scope.$on('ngRepeatFinishedList', function (ngRepeatFinishedEvent){
					systole();
				});
				
				$scope.tolist = function(target, typeid){
					$location.path("dynamiclist/"+typeid);
					
				}
			} else {
				showmessage(resp.msg);
			}
		}
	)
	.error(
		function(resp){
		    console.log('get templatedetail error');
		}
	);
});