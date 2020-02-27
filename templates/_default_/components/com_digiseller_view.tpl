{add_css file='components/digiseller/css/digiseller.css'}

<h1 class="con_heading">{$title}</h1>
<div class=clear></div>

<table cellpadding="0" cellspacing="0" border="0" class="subheader">
<tr>
<td class="menu0"><a href="/digiseller" title="Главная"><img src="/components/digiseller/images/home.gif"></a></td>
<td class="menu1" noWrap><a href="/digiseller/about" title="О магазине">О магазине</a></td>
<td class="menu2" noWrap><a href="/digiseller/pay" title="Способы оплаты">Способы оплаты</a></td>
<td class="menu3" noWrap><a href="/digiseller/contact" title="Контакты">Контакты</a></td>
<td class="menuSearch">
	{$ShowSearchForm}
</td>
<td class="menuSeparator"><img src="/components/digiseller/images/0.gif" alt="" width="10" height="1" border="0"></td>
<td class="menuPokupki"><div class="description"><a href="http://www.oplata.info" target="_blank">Мои покупки</a></div><div class="descriptionSub"><nobr>история покупок</nobr></div></td>

</tr>
</table>

<table cellpadding="0" cellspacing="0" border="0" class="content">
<tr>
<td><img src="pix/0.gif" alt="" width="1" height="45" border="0"></td>
<td class="filter">&nbsp;
	<table cellpadding="0" cellspacing="0" border="0" align="right" class="sorting">
	<tr>
	<td class="sortingTitle">Сортировка по:</td>
	<td class="sortingSelect">
		{$GoodsSort}
	</td>
	</tr>
	</table>
</td>
</tr>
<tr>
	<td class="contentLeft">
	<ul class="categories">
		{$ShowMenu}
	</ul>
	</td>
	<td class="contentRight">
		{$ListGoods}
	</td>
</tr>
</table>