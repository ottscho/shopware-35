{extends file='frontend/checkout/cart_footer.tpl'}

{block name='frontend_checkout_cart_footer_tax_information'}
{if !$sUserData.additional.charge_vat}
<div class="grid_15 notice">{se name='CheckoutFinishTaxInformation'}{/se}</div>
{/if}
{/block}