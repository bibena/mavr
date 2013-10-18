$(function()
	{
	$("a.link[href*='/']").bind('click',function()
		{
		var url=$(this).attr('href');
		$.post(
				'/ajax/link',
				{'link':$(this).attr('href')},
				function(data)
					{
					$('#cntnt').html(data);
					window.history.pushState({'page':url}, url, url);
					},
				'text');
		return false;
		})
	})