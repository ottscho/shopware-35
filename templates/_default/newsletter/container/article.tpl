<h2 style="color:#e14900; font-size:18px; font-weight:normal; margin:15px 0 15px 0px;text-transform:uppercase;">
	{include file="string:`$sCampaignContainer.description`"}
</h2>

<table width="560" border="0" cellpadding="0" cellspacing="0" style="margin:0;padding:0;">
	<tr>
	<td width="100%">            	                 

     <table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin:0;padding:0;"> 
     {foreach from=$sCampaignContainer.data item=sArticle name=artikelListe}
     {if $sArticle@index%3==0}<tr>{/if}
     <!-- ANFANG Artikel  -->
       <td height="320" width="180" align="center" valign="top" border="0" cellpadding="0" cellspacing="0" style="border: 1px solid #c6c6c6; padding:0;margin:0; width:180px;">
       
       <!--ARTIKEL CONTENT-->
                                    
        <table width="100%" height="100%" align="center" border="0" cellpadding="0" cellspacing="0" style="padding:0;margin:0;background-color:#ffffff;">
        
        <tr>
        <td height="180" valign="center" style="background-color:#fff;">
        
        <div align="center" style="overflow:hidden;">
        <a target="_blank" href="{$sArticle.linkDetails|rewrite:$sArticle.articleName}" title="{$sArticle.articleName}">
        {if $sArticle.image.src}
        	<img src="{$sArticle.image.src.2}"  border="0">
        {else}
       		<img src="{link file='frontend/_resources/images/no_picture.jpg' fullPath}" alt="Kein Bild vorhanden" border="0" />
       {/if}
       </a>
        </div>
      
        </tr>
        
        <tr>
        <td valign="top" style="color:#000; font-size:12px; background-color:#fff; height:5px; padding: 8px 10px 5px 10px; font-weight:bold;">
       {$sArticle.articleName|truncate:20:"[..]"}
        
        </td>
        </tr>
        <tr>
        <td height="50" valign="top" style="font-size:11px; color:#8c8c8c; padding: 0px 10px 8px 10px;">
		{$sArticle.description_long|truncate:80:"..."}
        </td>
        </tr>

        
        <tr>
        <td height="40" style="text-align:left; padding:10px;">
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td width="95">
        {if $sArticle.pseudoprice}
        <span style="color:#000; font-size:11px; line-height:13px;"><s>{$sArticle.pseudoprice}</s></span><br />
        {/if}
        <strong style="color:#000;font-size:14px;">
        	{if $sArticle.priceStartingFrom}{se name='NewsletterBoxArticleStartsAt'}ab{/se} {/if}
        	{$sArticle.price|currency}*
        </strong>
        <!--##Attribute# -->
              
               </td>
              <td>
              <a href="{$sArticle.linkDetails|rewrite:$sArticle.articleName}" target="_blank" title="{$sArticle.articleName}" style="color:#8c8c8c;text-decoration:underline;font-size:11px;">{se name='NewsletterBoxArticleLinkDetails'}Mehr Infos{/se}</a>
             
              </td>
            </tr>
          </table></td>
		</tr>
        </table>

        
        <!--END#ARTIKEL CONTENT-->   
        </td>
		{if $sArticle@index%3==2}
			</tr>
			{if !$sArticle@last}
			<tr>
				<td style="height:10px;"></td>
			</tr>
			{/if}
		{elseif !$sArticle@last}
			<td style="width:10px;height:10px;"></td>
		{/if}				

          {/foreach}  
         </table>
      <!--CONTENT-->
     </td>
     </tr>
 </table>     
<div style="height:25px;">&nbsp;</div>