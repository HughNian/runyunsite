//左侧菜单页面控制器
mainApp.controller('mainsidebarController',function($scope, $routeParams, $http, $sce, $location, $rootScope, $window, $anchorScroll)
{
	$("html,body").animate({scrollTop:0},1);
	
	var APIURL = config.host + config.mainconent + '?' + config.jsoncallback;
	$http.jsonp(APIURL)
	.success(
        function(resp, status, header, config){
        	var data = resp.data;
        	if(resp.code == 200){
        		//title
        		$("title").html(data.siteinfo[6].value);
        		
        		//菜单显示
        		var menu = data.menu;
        		for(var key in menu){
        			if(key == 0)
        				$scope.home = menu[key].typename;
        			if(key == 1)
        				$scope.services = menu[key].typename;
        			if(key == 2)
        				$scope.dynamic = menu[key].typename;
        			if(key == 3)
        				$scope.portfolio = menu[key].typename;
        			if(key == 4)
        				$scope.about = menu[key].typename;
        			if(key == 5)
        				$scope.contact = menu[key].typename;
        		}
        		
        		//菜单点击平滑上下滚动
        		$scope.scrollto = function(target){
        			mid = target.getAttribute('mid');
        			var name = angular.element(target).html();//动态获取name
        			if(document.all){ //判断IE浏览器
        				$("#"+mid).find('h2').html(name);
            			var old = $location.hash();
            			$location.hash(mid);
                        $anchorScroll();
            			$location.hash(old);
        			} else {
        				$("#"+mid).find('h2').html(name);
            			$('.main-menu a').removeClass("active");
            			target.setAttribute('class', 'active');
            			var height = Math.ceil($("#"+mid).offset().top);
            			$("html,body").animate({scrollTop:height},150);
        			}
        		};
        		
        		//页面滚动时赋样式
		        angular.element($window).bind("scroll", function(){
		        	var idheight = [];
	        		$(".main-menu").children().each(function(index, e){
                        var idname = $(e).find("a").attr('mid');
                        var classname = $(e).attr("class");
                        height = {};
                        height['idname'] = idname;
                        height['classname'] = classname;
                        if(isDefine($("#"+idname).offset())) height['height'] = Math.round($("#"+idname).offset().top);
                        idheight.push(height);
                    });
                    
                    var scrolltop = document.body.scrollTop | document.documentElement.scrollTop; //兼容火狐和chrome浏览器
                    var maxheight = scrolltop+75;
                    var minheight = scrolltop-20;
                    angular.forEach(idheight, function(val, index){
                        if(val.height >= minheight && val.height <= maxheight){
                            $('.main-menu a').removeClass("active");
                            var obj = $('.'+val.classname).find('a').addClass("active");
                        }
                    });
		        });
		        
        		//菜单跳转
        		$scope.scrolltoindex = function(target){
        			$location.url('/');
        			
        			//跳转开始时执行
        			this.$on('$routeChangeStart', function(evt, next, current){
                        mid = target.getAttribute('mid');
                        var name = angular.element(target).html();//动态获取name
                        $("#"+mid).find('h2').html(name);
                    });
        			
        			//跳转成功后执行
        			$rootScope.$on('$routeChangeSuccess', function(evt, current, previous) {
        				mid = target.getAttribute('mid');
                        var name = angular.element(target).html();//动态获取name
                        $("#"+mid).find('h2').html(name);
                        
                        //等待图片加载完成后,正确获取到页面高度时,再将页面滚动到相应位置
                        var imgNum = $('img').length;
                        $("img").load(function(){
                        	if(!--imgNum){
                        		var i = 0;
                            	$("html,body").animate({scrollTop:1},1);
                            	var htag = $(document).scrollTop();
                                $(window).scroll(function(){
                                    if(i == 0){
                                    	var height = 0;
                                    	if(isDefine($("#"+mid).offset())) height = Math.ceil($("#"+mid).offset().top);
                                        var _htag = $(document).scrollTop();
                                        if(_htag >= 0){
                                        	$("html,body").animate({scrollTop:height},1);
                                        }
                                    }
                                    i++;
                                });
                                i = 0;
                                $('.main-menu a').removeClass("active");
                                target.setAttribute('class', 'active');
                        	}
                        });
        			}); 
        		};
        		
        		//点击展示下拉菜单
        		$scope.menutoggle = function(target)
        		{
        			$('.responsive-menu').stop(true,true).slideToggle();
        		};
        		
        	} else {
        		showmessage(resp.msg);
        		console.log('error code:'+resp.code);
        	}
		}
    )
    .error(
        function(resp){
            console.log('get menu error');
        }
    );
});