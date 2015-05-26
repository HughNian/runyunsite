//动态详情控制器
mainApp.controller('dynamicdetailController',function($scope, $routeParams, $http, $sce, $location, $route)
{
	$("html,body").animate({scrollTop:0},1);
	var aid = parseInt($routeParams.aid);
	var typeid = $routeParams.typeid;
	var APIURL = config.host + config.dynamicdetail + '?aid=' + aid + '&typeid=' + typeid + '&' + config.jsoncallback;
	$http.jsonp(APIURL)
	.success(
		function(resp, status, header){
			if(resp.code == 200){
				var data = resp.data;
				$scope.detail  = data.detail;
				$scope.previd  = data.previd;
				$scope.nextid  = data.nextid;
				$scope.islast  = data.islast;
				$scope.isfirst = data.isfirst;
				$scope.body = $sce.trustAsHtml(data.detail.body);
				
				if(data.islast || data.isfirst){
					$(".col-md-6").attr('class', 'col-md-6 w100');
				}
				
				//上一篇
				$scope.prevdetail = function(previd){
					if(!data.isfirst){
						if(isDefine(previd)){
							$location.path('dynamicdetail/'+typeid+'/'+previd);
						} else {
							alert('参数错误');
						}
					} else {
						alert('没有上一篇');
					}
				};
				
				//下一篇
				$scope.nextdetail = function(nextid){
					if(!data.islast){
						if(isDefine(nextid)){
							$location.path('dynamicdetail/'+typeid+'/'+nextid);
						} else {
							alert('参数错误');
						}
					} else {
						alert('已是最后一篇');
					}
				};
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