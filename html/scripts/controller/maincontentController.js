//右侧主页面控制器
mainApp.controller('maincontentController',function($scope, $routeParams, $http, $sce, $location)
{
	$("html,body").animate({scrollTop:0},1);
	
	var APIURL = config.host + config.mainsidebar + '?' + config.jsoncallback;
	$http.jsonp(APIURL)
	.success(
        function(resp, status, header, config){
        	var data = resp.data;
        	
        	$scope.title1 = data.menu[0].typename,
        	$scope.title2 = data.menu[1].typename,
        	$scope.title3 = data.menu[2].typename,
        	$scope.title4 = data.menu[3].typename;
        	
        	/**
        	 * 轮播图片内容
        	 * 
        	 */
        	var lunbos = data.lunbo;
        	var lunbohtml = '';
        	angular.forEach(lunbos, function(val){
        		var target = "";
        		if(val.target == 1){
        			target = "target='_blank'";
        		}
        		lunbohtml += '<li>'
		 			       + '<div class="slider-caption">'
					       + '<h2>'+val.title+'</h2>'
					       + '<p>'+val.shorttitle+'</p>'
					       + '</div>'
					       + '<a href="'+val.redirecturl+'" '+target+'><img src="..'+val.litpic+'" alt="Slide 1"></a>'
					       + '</li>';
        	});
        	//$scope.lunbohtml = $sce.trustAsHtml(lunbohtml);
        	$(".slides").html(lunbohtml);
        	//加载轮播效果
        	/************** FlexSlider *********************/
            $('.flexslider').flexslider({
        	    animation: "fade",
        	    directionNav: false
        	});
            
            /**
             * 模板库内容
             * 
             */
            var tpls = data.tpl;
            var tplmenus = [];
            angular.forEach(tpls, function(val, index){
            	var names = new Object();
            	names.index    = index;
            	names.typename = val.typename;
            	tplmenus.push(names);
            });
            //显示模板菜单
            $scope.tplmenus = tplmenus;
            //显示首页默认的模板内容
            var defaulttpl = tpls[0];
            $scope.defaulttpl = defaulttpl.tpl
            //切换模板
            $scope.switchtpl = function(target){
            	var key = target.getAttribute('data');
            	var currenttpl = tpls[key].tpl;
            	var tplhtml = '';
            	angular.forEach(currenttpl, function(val, index){
            		tplhtml += '<a href="#templatedetail/'+val.id+'/'+val.typeid+'">'
            			     + '<div class="col-md-3">'
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
            };
            //跳转到更多模板
            $scope.moretpl = function(){
            	//var path = $location.path();
            	$location.path('template');
            };
            //跳转到模板详情
            $scope.tpldetail = function(aid, typeid){
            	$location.path('templatedetail/'+aid+'/'+typeid);
            };
            
            /**
             * 润云动态
             * 
             */
            $scope.dynamic = data.dynamic;
            //跳转到更多动态
            $scope.moredynamic = function(){
            	$location.path('dynamiclist');
            };
            
            /**
             * 合作加盟
             * 
             */
            $scope.portcontent = $sce.trustAsHtml(data.portfolio.content);
            $scope.portimg = data.portfolio.img;
            var portimgtag = true;
            if(data.portfolio.img == "") portimgtag = false;
            $scope.portimgtag = portimgtag;
            
            /**
             * logo墙
             * 
             */
            $scope.logowall = data.logowall;
            
            /**
             * 关于润云
             * 
             */
            $scope.aboutcontent = $sce.trustAsHtml(data.about.content);
            $scope.aboutimg = data.about.img;
            var aboutimgtag = true;
            if(data.about.img == "") aboutimgtag = false;
            $scope.aboutimgtag = aboutimgtag;
            
        }
    )
 	.error(
	    function(resp){
	        console.log('get data error');
	    }
	);
});