//$(function(){})
function uriLoader(uri,flag)
	{
	$.ajax({
		url:'/ajax/link',
		dataType:'text',
		type:'post',
		data:{'link':uri},
		statusCode:
			{
			404: function()
				{
				window.location.replace("/page/error/404");
				},
			500: function()
				{
				window.location.replace("/page/error/500");
				}
			},
		success:function(data)
			{
			$('#cntnt').html(data);
			$('#errr').html('');
			if(flag)
				{
				window.history.pushState({'page':uri}, uri, uri);
				}
			}
		});
	}
$(document).on('click',"a.link[href*='/']",function()
	{
	uriLoader($(this).attr('href'),1);
	return false;
	})
window.onpopstate = function(event) 
	{
	uriLoader(document.location.pathname,0);
	};