//提交需求控制器
mainApp.controller('postdemandController',function($scope, $routeParams, $http, $sce, $location, $route)
{	
	//点击提交需求
	$scope.post = function()
	{
		var name      = $("input[name='name']").val(),
		    telephone = $("input[name='telephone']").val(),
		    email     = $("input[name='email']").val(),
		    company   = $("input[name='company']").val(),
		    msg       = $("#msg").val();
		   
		var postData = {
				"name"      : name,
				"telephone" : telephone,
				"email"     : email,
				"company"   : company,
				"msg"       : msg
		};
		
		var DATAURL = config.host + config.demand;
		
		//清空input框
		var clear = function(){
			$("input[name='name']").val("");
			$("input[name='telephone']").val("");
			$("input[name='email']").val("");
			$("input[name='company']").val("");
			$("#msg").val("");
		};
		
		$http({
            method : 'POST',
            url : DATAURL,
            params : {
            	name      : name,
            	telephone : telephone,
            	email     : email,
            	company   : company,
            	msg       : msg
            }
        }).success(function(resp) {
        	var ret = eval(resp);
        	if(ret.code == 200){
        		showmessage('提交成功');
        		new clear();
        	} else {
        		console.log(ret.msg);
        		showmessage(ret.msg);
        	}
        }).error(function() {
            console.log("post data error");
        });

	};
});