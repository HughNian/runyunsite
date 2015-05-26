//模板列表控制器
mainApp.controller('templateController',function($scope, $routeParams, $http, $sce, $location, $route)
{
	$("html,body").animate({scrollTop:0},1);
	
	var APIURL = config.host + config.template + '?' + config.jsoncallback;
	$http.jsonp(APIURL)
	.success(
		function(resp, status, header){
			var data = resp.data;
			if(resp.code == 200){
				var tpls = data.tpl;
				var tplmenus = [];
	            angular.forEach(tpls, function(val, index){
	            	var names = new Object();
	            	names.index    = index;
	            	names.typeid   = val.id;
	            	names.typename = val.typename;
	            	tplmenus.push(names);
	            });
	            
	            //显示模板菜单
	            $scope.tplmenus = tplmenus;
	            
	            //显示首页默认的模板内容
	            var defaulttpl = tpls[0];
	            var defaultallcount = tpls[0].allcount;
	            $scope.key    = 0;
	            $scope.typeid = tpls[0].id;
	            $scope.page   = tpls[0].page;
	            $scope.ismore = true;
	            $scope.defaulttpl = defaulttpl.tpl;
	            if(defaulttpl.tpl.length >= defaultallcount){
	            	$scope.ismore = false;
	            }
	            
	            //切换模板
	            $scope.switchtpl = function(target){
	            	var key = target.getAttribute('data');
	            	var currenttpl = tpls[key].tpl;
	            	var allcount = tpls[key].allcount;
	            	var tplhtml = '';
	            	angular.forEach(currenttpl, function(val, index){
	            		tplhtml += '<a href="#templatedetail/'+val.id+'/'+val.typeid+'">'
	            			     + '<div class="col-md-4">'
	            			     + '<div class="member-item">'
	            			     + '<div class="member-thumb"> <img src="..'+val.litpic+'">'
	            			     + '<div class="overlay">'
	            			     + '<ul class="social-member">'
	            			     + '<li><img src="..'+val.qrcode+'" alt=""/></li>'
	            			     + '</ul>'
	            			     + '</div>'
	            			     + '</div>'
	            			     + '<div class="member-content">'
	            			     + '<h4>'+val.title+'</h4>'
	            			     + '<p>类别：'+val.typename+'</p>'
	            			     + '</div>'
	            			     + '</div>'
	            			     + '</div>'
	            			     + '</a>';
	            	});
	            	$("#currenttpl").html(tplhtml);
	            	$scope.key    = key;
	            	$scope.typeid = tpls[key].id;
	            	$scope.page   = tpls[key].page;
	            	$scope.ismore = true;
	            	if(currenttpl.length >= allcount){
		            	$scope.ismore = false;
		            }
	            };
	            
	            //更多模板
	            $scope.moreinfo = function(target){
	            	var typeid  = target.getAttribute('typeid');
	            	var key     = target.getAttribute('key');
	            	var page    = target.getAttribute('page');
	            	var nowpage = parseInt(page)+1;
	            	var dataurl = config.host + config.template + '?page=' + nowpage + '&' + config.jsoncallback;
	            	$http.jsonp(dataurl)
	            	.success(
	            		function(resp, status, header){
	            			var data = resp.data;
	            			var tpls = data.tpl;
	            			var moretpl = tpls[key].tpl;
	            			var allcount = tpls[key].allcount;
	            			var tplhtml = '';
	            			angular.forEach(moretpl, function(val, index){
	    	            		tplhtml += '<a href="#templatedetail/'+val.id+'/'+val.typeid+'">'
	    	            			     + '<div class="col-md-4">'
	    	            			     + '<div class="member-item">'
	    	            			     + '<div class="member-thumb"> <img src="..'+val.litpic+'">'
	    	            			     + '<div class="overlay">'
	    	            			     + '<ul class="social-member">'
	    	            			     + '<li><img src="..'+val.qrcode+'" alt=""/></li>'
	    	            			     + '</ul>'
	    	            			     + '</div>'
	    	            			     + '</div>'
	    	            			     + '<div class="member-content">'
	    	            			     + '<h4>'+val.title+'</h4>'
	    	            			     + '<p>类别：'+val.typename+'</p>'
	    	            			     + '</div>'
	    	            			     + '</div>'
	    	            			     + '</div>'
	    	            			     + '</a>';
	    	            	});
	            			$("#currenttpl").append(tplhtml);
	            			var nowlength = $('.col-md-4').length;
	            			$scope.ismore = true;
	    	            	if(nowlength >= allcount){
	    		            	$scope.ismore = false;
	    		            }
	    	            	$scope.page = nowpage;
	            		}	
	            	);
	            };
	            
	            //跳转到模板详情
	            $scope.tpldetail = function(aid, typeid){
	            	$location.path('templatedetail/'+aid+'/'+typeid);
	            };
			} else {
				console.log('error code:'+resp.code);
			}
		}
	)
	.error(
		function(resp){
		    console.log('get template error');
		}
	);
});
