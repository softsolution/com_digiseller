{add_css file='components/digiseller/css/digiseller.css'}

<h1 class="con_heading">� ��������</h1>
<div class=clear></div>

<table cellpadding="0" cellspacing="0" border="0" class="subheader">
<tr>
<td class="menu0"><a href="/digiseller" title="�������"><img src="/components/digiseller/images/home.gif"></a></td>
<td class="menu1" noWrap><a href="/digiseller/about">� ��������</a></td>
<td class="menu2" noWrap><a href="/digiseller/pay">������� ������</a></td>
<td class="menu3" noWrap><a href="/digiseller/contact">��������</a></td>
<td class="menuSearch">
	{$ShowSearchForm}
</td>
<td class="menuSeparator"><img src="pix/0.gif" alt="" width="10" height="1" border="0"></td>
<td class="menuPokupki"><div class="description"><noindex><a rel="nofollow" href="http://www.oplata.info" target="_blank">��� �������</a></noindex></div><div class="descriptionSub"><nobr>������� �������</nobr></div></td>

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