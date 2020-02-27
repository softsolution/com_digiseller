{add_css file='components/digiseller/css/digiseller.css'}

<h1 class="con_heading">О магазине</h1>
<div class=clear></div>

<table cellpadding="0" cellspacing="0" border="0" class="subheader">
<tr>
<td class="menu0"><a href="/digiseller" title="Главная"><img src="/components/digiseller/images/home.gif"></a></td>
<td class="menu1" noWrap><a href="/digiseller/about">О магазине</a></td>
<td class="menu2" noWrap><a href="/digiseller/pay">Способы оплаты</a></td>
<td class="menu3" noWrap><a href="/digiseller/contact">Контакты</a></td>
<td class="menuSearch">
	{$ShowSearchForm}
</td>
<td class="menuSeparator"><img src="pix/0.gif" alt="" width="10" height="1" border="0"></td>
<td class="menuPokupki"><div class="description"><noindex><a rel="nofollow" href="http://www.oplata.info" target="_blank">Мои покупки</a></noindex></div><div class="descriptionSub"><nobr>история покупок</nobr></div></td>

</tr>
</table>

<table cellpadding="0" cellspacing="0" border="0" class="content">
<tr>
	<td class="contentLeft">
	<ul class="categories">
		{$ShowMenu}
	</ul>
	</td>
	<td class="contentRight">

	 {$cfg.fio}<br />
         WMID: <a href=wmk:msgto?to='.$wmid.'&bringtofront=Y>{$cfg.wmid}</a><br />
	 email: <a href=mailto:{$cfg.email}>{$cfg.email}</a><br />
         
	</td>
</tr>
</table>