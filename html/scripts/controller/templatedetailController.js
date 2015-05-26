//模板详情控制器
mainApp.controller('templatedetailController',function($scope, $routeParams, $http, $sce, $location, $route)
{
	$("html,body").animate({scrollTop:0},1);
	var aid = parseInt($routeParams.aid);
	var typeid = $routeParams.typeid;
	var APIURL = config.host + config.templatedetail + '?aid=' + aid + '&typeid=' + typeid + '&' + config.jsoncallback;
	$http.jsonp(APIURL)
	.success(
		function(resp, status, header){
			var data = resp.data;
			if(resp.code == 200){
				$scope.title       = data.title;
				$scope.description = data.description;
				$scope.typename    = data.typename;
				$scope.qrcode      = data.qrcode;
				$scope.imgurls     = data.imgurls;
				$scope.previd      = data.previd;
				$scope.nextid      = data.nextid;
				$scope.islast      = data.islast;
				$scope.isfirst     = data.isfirst;
				
				if(data.islast || data.isfirst){
					$(".col-md-6").attr('class', 'col-md-6 w100');
				}
				
				//上一个
				$scope.prevtpl = function(previd){
					if(!data.isfirst){
						if(typeof previd != 'undefined'){
							$location.path('templatedetail/'+previd+'/'+typeid);
						} else {
							alert('参数错误');
						}
					} else {
						alert('没有上一个');
					}
				};
				
				//下一个
				$scope.nexttpl = function(nextid){
					if(!data.islast){
						if(typeof nextid != 'undefined'){
							$location.path('templatedetail/'+nextid+'/'+typeid);
						} else {
							alert('参数错误');
						}
					} else {
						alert('已是最后一个');
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