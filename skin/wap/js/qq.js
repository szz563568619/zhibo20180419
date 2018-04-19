function get_param(url, name){
	var params = url.substring(1).toLowerCase();
	var paramList = [];
	var param = null;
	var parami;
	if(params.length > 0) {
		if(params.indexOf("&") >= 0) {
			paramList = params.split( "&" ); 
		}else{
			paramList[0] = params;
		}
		for(var i = 0,listLength = paramList.length;i < listLength;i++) {
			parami = paramList[i].indexOf(name+"=" );
			if(parami >= 0) {
				param = paramList[i].substr(parami+(name+"=").length);
				break;
			}
		}
	}
	return param;
}

function set_keywords(){
	if(self.location == top.location){
		var url = location.href;
	}else{
		var url = document.referrer;
	}
	var keywords = get_param(url, 'jinke');
	if(keywords != null) $.cookie('keywords', keywords, { expires: 365 });
}

set_keywords();

$(function (){
	$('.qq-click').click(function (){
		var keywords = $.cookie('keywords');
		if(keywords != undefined && $.cookie('click') == undefined){
			$.cookie('click', 1, {expires:365});
			$.get(base.url+'qq/index', {keywords:keywords});
		}

		$(document.getElementById('tencent').getElementsByTagName('iframe')[0].contentWindow.document).find('#launchBtn').click()
	})
})