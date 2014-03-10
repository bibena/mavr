$(function()
	{
	menuSwitcher();
	})
function menuSwitcher()
	{
	$('.nav.navbar-nav').children('li').each(function()
		{
		$(this).removeClass('active');
		if(document.location.pathname=='' || document.location.pathname=='/')
			{
			if($(this).children('a').attr('href')==document.location.pathname)
				{
				$(this).addClass('active');
				}
			}
		else
			{
			if($(this).children('a').attr('href').split('/')[1]==document.location.pathname.split('/')[1])
				{
				if($(this).children('a').attr('href').split('/')[2]==document.location.pathname.split('/')[2])
					{
					$(this).addClass('active');
					}
				if(($(this).children('a').attr('href').split('/')[2]=='categories' && document.location.pathname.split('/')[2]=='category') || ($(this).children('a').attr('href').split('/')[2]=='categories' && document.location.pathname.split('/')[2]=='product'))
					{
					$(this).addClass('active');
					}
				if(document.location.pathname.split('/')[1]=='admin')
					{
					$(this).addClass('active');
					$('.nav.nav-pills.nav-stacked').children('li').each(function()
						{
						$(this).removeClass('active');
						if($(this).children('a').attr('href').split('/')[2]==document.location.pathname.split('/')[2])
							{
							$(this).addClass('active');
							}
						});
					}
				}
			}
		});
	}
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
			menuSwitcher();
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
