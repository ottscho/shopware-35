<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta name="author" content=""/>
<meta name="copyright" content="" />

<title></title>
<style type="text/css">
body {
	{$Containers.Body.style}
}

div#head_logo {
	{$Containers.Logo.style}
}

div#head_sender {
	{$Containers.Header_Recipient.style}
}

div#header {
	{$Containers.Header.style}
}

div#head_left {
	{$Containers.Header_Box_Left.style}
}

div#head_right {
	{$Containers.Header_Box_Right.style}
}

div#head_bottom {
	{$Containers.Header_Box_Bottom.style}
}

div#content {
	{$Containers.Content.style}
}

td {
	{$Containers.Td.style}
}

td.name {
	{$Containers.Td_Name.style}
}

td.line {
	{$Containers.Td_Line.style}
}

td.head  {
	{$Containers.Td_Head.style}
}

#footer {
	{$Containers.Footer.style}
}

#amount {
	{$Containers.Content_Amount.style}
}

#sender {
	{$Containers.Header_Sender.style}
}

#info {
	{$Containers.Content_Info.style}
}
</style>

<body>
{foreach from=$Pages item=postions name="pagingLoop" key=page}
	<div id="head_logo">
		{$Containers.Logo.value}
	</div>
	<div id="header">
		<div id="head_left">
		{if $smarty.foreach.pagingLoop.first}
			{block name="document_index_selectAdress"}
				{assign var="address" value="billing"}
			{/block}
			<div id="head_sender">
				<p class="sender">{$Containers.Header_Sender.value}</p>
				{$User.$address.company}<br />
				{$User.$address.firstname} {$User.$address.lastname}<br />			
				{$User.$address.street} {$User.$address.streetnumber}<br />
				{$User.$address.zipcode} {$User.$address.city}<br />
				{$User.$address.country.countryen}<br />
			</div>
		{/if}
		</div>
		<div id="head_right">
				<strong>
				{block name="document_index_head_right"}
					{$Containers.Header_Box_Right.value}
					{s name="DocumentIndexCustomerID"}Kunden-Nr.:{/s} {$User.billing.customernumber|string_format:"%06d"}<br />
					{if $User.billing.ustid}
					{s name="DocumentIndexUstID"}USt-IdNr.:{/s} {$User.billing.ustid|replace:" ":""|replace:"-":""}<br />
					{/if}
					{s name="DocumentIndexOrderID"}Bestell-Nr.:{/s} {$Order._order.ordernumber}<br />
					{s name="DocumentIndexDate"}Datum:{/s} {$Document.date}<br />
					{if $Document.deliveryDate}{s name="DocumentIndexDeliveryDate"}Liefertermin:{/s} {$Document.deliveryDate}<br />{/if}
				{/block}
				</strong>
		</div>
	</div>
	
	<div id="head_bottom" style="clear:both">
		{block name="document_index_head_bottom"}
			<h1>{s name="DocumentIndexInvoiceNumber"}Rechnung Nr. {$Document.id}{/s}</h1>
			{s name="DocumentIndexPageCounter"}Seite {$page+1} von {$Pages|@count}{/s}
		 {/block}
	</div>

	<div id="content">

	<table cellpadding="0" cellspacing="0" width="100%">
	<tbody valign="top">
	<tr>
		{block name="document_index_table_head_pos"}
			<td align="left" width="5%" class="head">
				<strong>{s name="DocumentIndexHeadPosition"}Pos.{/s}</strong>
			</td>
		{/block}
		{block name="document_index_table_head_nr"}
			<td align="left" width="10%" class="head">
				<strong>{s name="DocumentIndexHeadArticleID"}Art-Nr.{/s}</strong>
			</td>
		{/block}
		{block name="document_index_table_head_name"}
			<td align="left" width="48%" class="head">
				<strong>{s name="DocumentIndexHeadName"}Bezeichnung{/s}</strong>
			</td>
		{/block}
		{block name="document_index_table_head_quantity"}
			<td align="right" width="5%" class="head">
				<strong>{s name="DocumentIndexHeadQuantity"}Anz.{/s}</strong>
			</td>
		{/block}
		{block name="document_index_table_head_tax"}
			{if $Document.netto != true}
				<td align="right" width="6%" class="head">
					<strong>{s name="DocumentIndexHeadTax"}MwSt.{/s}</strong>
				</td>
			{/if}
		{/block}
		{block name="document_index_table_head_price"}
			{if $Document.netto != true && $Document.nettoPositions != true}
			    <td align="right" width="10%" class="head">
					<strong>{s name="DocumentIndexHeadPrice"}Brutto Preis{/s}</strong>
				</td>
			    <td align="right" width="12%" class="head">
					<strong>{s name="DocumentIndexHeadAmount"}Brutto Gesamt{/s}</strong>
				</td>
			{else}
				 <td align="right" width="10%" class="head">
					<strong>{s name="DocumentIndexHeadNet"}Netto Preis{/s}</strong>
				 </td>
			     <td align="right" width="12%" class="head">
					<strong>{s name="DocumentIndexHeadNetAmount"}Netto Gesamt{/s}</strong>
				 </td>
			{/if}
		{/block}
	</tr>
	{foreach from=$postions item=position key=number}
	{block name="document_index_table_each"}
	<tr>
		{block name="document_index_table_pos"}
			<td align="left" width="5%" valign="top">
				{$number+1}
			</td>
		{/block}
		{block name="document_index_table_nr"}
			<td align="left" width="10%" valign="top">
				{$position.articleordernumber|truncate:14:""}
			</td>
		{/block}
		{block name="document_index_table_name"}
			<td align="left" width="48%" valign="top">
				{$position.name|nl2br}
			</td>
		{/block}
		{block name="document_index_table_quantity"}
			<td align="right" width="5%" valign="top">
				{$position.quantity}
			</td>
		{/block}
		{block name="document_index_table_tax"}
			{if $Document.netto != true}
				<td align="right" width="6%" valign="top">
					{$position.tax} %
				</td>
			{/if}
		{/block}
		{block name="document_index_table_price"}
			{if $Document.netto != true && $Document.nettoPositions != true}
			    <td align="right" width="10%" valign="top">
					{$position.price|currency}
				</td>
			    <td align="right" width="12%" valign="top">
			    	{$position.amount|currency}
				</td>
			{else}
				<td align="right" width="10%" valign="top">
					{$position.netto|currency}
				</td>
			    <td align="right" width="12%" valign="top">
			    	{$position.amount_netto|currency}
				</td>
			{/if}
		{/block}
	</tr>
	{/block}
	{/foreach}
	</tbody>
	</table>
	</div>
	
	{if $smarty.foreach.pagingLoop.last}
		{block name="document_index_amount"}
		 	<div id="amount">
			  <table width="300px" cellpadding="0" cellspacing="0">
			  <tbody>
			  <tr>
			  	<td align="right" width="100px" class="head">{s name="DocumentIndexTotalNet"}Gesamtkosten Netto:{/s}</td>
			  	<td align="right" width="200px" class="head">{$Order._amountNetto|currency}</td>
			  </tr>
			  {if $Document.netto == false}
				  {foreach from=$Order._tax key=key item=tax}
				  <tr>
				  	<td align="right">{s name="DocumentIndexTax"}zzgl. {$key} % MwSt:{/s}</td>
				  	<td align="right">{$tax|currency}</td>
				  </tr>
				  {/foreach}
			  {/if}
			  {if $Document.netto == false}
				  <tr>
				    <td align="right"><b>{s name="DocumentIndexTotal"}Gesamtkosten:{/s}</b></td>
				    <td align="right"><b>{$Order._amount|currency}</b></td>
				  </tr>
			  {else}
			 	  <tr>
				    <td align="right"><b>{s name="DocumentIndexTotal"}Gesamtkosten:{/s}</b></td>
				    <td align="right"><b>{$Order._amountNetto|currency}</b></td>
				  </tr>
			  {/if}
			  </tbody>
			  </table>
			</div>
		{/block}
		{block name="document_index_info"}
			<div id="info">
			{block name="document_index_info_comment"}
				{if $Document.comment}
					<div style="font-size:11px;color:#333;font-weight:bold">
						{$Document.comment|replace:"�":"&euro;"}
					</div>
				{/if}
			{/block}
			{block name="document_index_info_net"}
				{if $Document.netto == true}
				<p>{s name="DocumentIndexAdviceNet"}Hinweis: Der Empf�nger der Leistung schuldet die Steuer.{/s}</p>
				{/if}
				<p>{s name="DocumentIndexSelectedPayment"}Gew&auml;hlte Zahlungsart{/s} {$Order._payment.description}</p>
			{/block}
			{block name="document_index_info_voucher"}
				{if $Document.voucher}
				  	<div style="font-size:11px;color:#333;">
				  	{s name="DocumentIndexVoucher"}
						F�r den n�chsten Einkauf schenken wir Ihnen einen {$Document.voucher.value} {$Document.voucher.prefix} Gutschein
						mit dem Code "{$Document.voucher.code}".<br />
					{/s}
					</div>
				{/if}
			{/block}
			{block name="document_index_info_ordercomment"}
				{if $Order._order.comment}
					<div style="font-size:11px;color:#333;">
						{s name="DocumentIndexComment"}Kommentar:{/s}
						{$Order._order.comment|replace:"�":"&euro;"}
					</div>
				{/if}
			{/block}
			{block name="document_index_info_dispatch"}
				{if $Order._dispatch.name}
					<div style="font-size:11px;color:#333;">
						{s name="DocumentIndexSelectedDispatch"}Gew�hlte Versandart:{/s}
						{$Order._dispatch.name}
					</div>
				{/if}
			{/block}
				
				{$Containers.Content_Info.value}
			{block name="document_index_info_currency"}
				{if $Order._currency.factor > 1}{s name="DocumentIndexCurrency"}
					<br>Euro Umrechnungsfaktor: {$Order._currency.factor|replace:".":","}
					{/s}
				{/if}
			{/block}
			</div>
		{/block}
	{/if}
	
	<div id="footer">
	{$Containers.Footer.value}
	</div>
	{if !$smarty.foreach.pagingLoop.last}
		<pagebreak />
	{/if}
{/foreach}


</body>
</html>