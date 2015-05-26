//js公共函数库
function openWin(inWndName)
{
	var date = new Date();
	time = Date.parse(date);
	var time1 = isDefine(getLocVal("date"))?getLocVal("date"):0; 

	window.location.href = inWndName;
}

function closeWin()
{
	var date = new Date();
	time = Date.parse(date);
	var time1 = isDefine(getLocVal("date"))?getLocVal("date"):0;
	setLocVal("date",time);
	history.back();
}

function json2str(j)
{
	return JSON.stringify(j);
}

function str2json(s)
{
	return JSON.parse(s);
}

function isDefine(para)
{
	if(typeof para == 'undefined' || para == 'undefined' || para == '' || para == null || para == 'null' || para == 'NULL' || para == '(null)')
		return false;
	else
		return true;
}

function setLocVal(key,value)
{
	if(key=="serviceNo"||key=="openid"){
		window.localStorage[key] = value;
	}else{
		var before=window.localStorage['serviceNo'];
		window.localStorage[before+key] = value;
	}
}

function getLocVal(key){
	var before=window.localStorage['serviceNo'];
	if(key=="serviceNo"||key=="openid"){
		if(window.localStorage[key]){
			return window.localStorage[key];
		}
		else{
			return "";
		}
	}else{
	    if(window.localStorage[before+key]){
 			return window.localStorage[before+key];
 		}else{
			return "";
		}
	}
}

function trim(str){
	return str.replace(/(^\s*)|(\s*$)/g, "");
}

//消息框
function showmessage(msg,time){
	$("#popDiv").remove();
	if(!isDefine(time)){
		time=1500;
	}
	$("body").append('<div id="popDiv" class="showDivBack" style="display:none;">'+msg+' </div>');
	$("#popDiv").css("display","block");
	setTimeout(function(){
		$("#popDiv").css("display","none");
	},time);
}