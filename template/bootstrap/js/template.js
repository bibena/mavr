$(function()
	{
	$("a.link[href*='/']").bind('click',function()
		{
		var url=$(this).attr('href');
		$.ajax(
			{
			url:'/ajax/link',
			dataType:'text',
			type:'post',
			data:{'link':$(this).attr('href')},
			statusCode:
				{
				404: function()
					{
					window.location.replace("page/error/404");
					},
				500: function()
					{
					window.location.replace("page/error/500");
					}
				},
			success:function(data)
				{
				$('#cntnt').html(data);
				window.history.pushState({'page':url}, url, url);
				}
			});
		return false;
		})
	})