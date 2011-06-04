{extends file="backend/ext_js/index.tpl"}

{block name="backend_index_css" append}
<style type="text/css">
.action_icon {
	cursor: pointer;
	margin-right: 4px;
}
</style>
{/block}

{block name="backend_index_javascript" append}
<script type="text/javascript">
	{include file="backend/plugins/benchmark/grid.tpl"}
	{include file="backend/plugins/benchmark/form.tpl"}
	Ext.define('Ext.app.Monitor', {
			extend: 'Ext.container.Viewport',
			initComponent: function(){
				this.grid = Ext.create('Ext.app.Monitor.Grid');

				this.form = Ext.create('Ext.app.Monitor.Form');
				this.grid.form = this.form;
				Ext.apply(this, {
					title: 'Test',
					layout: {
						type: 'border',
						padding: '0 0 11 0'
					},
					items: [this.grid,this.form]
				});
				this.callParent(arguments);
			}
	});
	Ext.onReady(function(){
		Ext.create('Ext.app.Monitor');
	}); 
</script>
{/block}