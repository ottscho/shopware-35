<?php
define('sAuthFile', 'sGUI');
define('sConfigPath',"../../../../");
include("../../../backend/php/check.php");
$result = new checkLogin();
$result = $result->checkUser();
if ($result!="SUCCESS"){
echo "
	<html><title>Time-Out</title><head></head>
	<script language=\"javascript\">
	parent.location.reload();
	</script>
	<body>Bitte loggen Sie sich neu ein!</body></html>
	";
	die();
}
// *****************

?>
<html>
<head>
  <title>User-List</title>
 	<link rel="stylesheet" type="text/css" href="../../../vendor/ext/resources/css/ext-all.css" />
	<link href="../../../backend/css/modules.css" rel="stylesheet" type="text/css" />
	
	<script type="text/javascript" src="../../../vendor/ext/adapter/ext/ext-base.js"></script>

	
	<script type="text/javascript" src="../../../vendor/ext/ext-all.js"></script>
 	<link href="../../../backend/css/icons.css" rel="stylesheet" type="text/css" />
 	<script type="text/javascript" src="../../../backend/js/mootools.js"></script>
 	
	<style type="text/css">
	html, body {
        font:normal 12px verdana;
        margin:0;
        padding:0;
        border:0 none;
        overflow:hidden;
        height:100%;
    }
	p {
	    margin:5px;
	}
    .settings {
        background-image:url(../shared/icons/fam/folder_wrench.png);
    }
    .nav {
        background-image:url(../shared/icons/fam/folder_go.png);
    }
    </style>
	<script>
	function deleteVote(arg1,arg2){
		Ext.Ajax.request({
		   url: "<?php echo $_SERVER["SERVER_PORT"] == "80" ? "http" : "https" ?>://<?php echo $sCore->sCONFIG['sBASEPATH']?>/engine/backend/ajax/deleteVote.php",
		   success: function(){
		   		myExt.reload();
		   },
		   params: { 'delete': arg1 }
		});
	}
	function confirmVote(arg1,arg2){
		
		var myAjax = new Ajax("<?php echo $_SERVER["SERVER_PORT"] == "80" ? "http" : "https" ?>://<?php echo $sCore->sCONFIG['sBASEPATH']?>/engine/backend/ajax/confirmVote.php",{method: 'post', onComplete: function(json){
		}}).request('id='+arg1);
		myExt.reload();
	}
	</script>
	<script type="text/javascript">
	var myExt = function(){
		var store;
		var storeid;
		var myTab;
	return {
	reload : function(){
    	store.load({params:{start:0,id:storeid}});
    },
    markAll: function(){
    	$$('.markedArticles').each(function(e){
    		e.checked = true;
    	});
    },
    filterByChar: function(key){
    	store.baseParams["search"] = key;
	    store.lastOptions.params["start"] = 0;
	    store.reload();
    },
    filterGroup: function(key){
    	store.baseParams["group"] = key;
	    store.lastOptions.params["start"] = 0;
	    store.reload();
    },
    deleteVotes: function(){
    	Ext.MessageBox.confirm('Best�tigung', 'Sollen die markierten Bewertungen wirklich gel�scht werden?', function deleteClientConfirmed(btn){
	    	
    		if (btn=="yes"){
    			
	    		var deleted = false;
		    	$$('.markedArticles').each(function(e){
		    		if (e.checked){
		    			var articleID = e.getProperty('value');
		    			var myAjax = new Ajax("<?php echo $_SERVER["SERVER_PORT"] == "80" ? "http" : "https" ?>://<?php echo $sCore->sCONFIG['sBASEPATH']?>/engine/backend/ajax/deleteVote.php",{method: 'post', onComplete: function(json){
						}}).request('delete='+articleID);
		    			// DELETE ARTICLES
		    			deleted = true; 
		    		}
		    	});
		    	if (deleted){
		    		// Reload Grid
		    		myExt.reload();
		    	}
	    	}
    	
    	});
    },

	init : function(){
       Ext.state.Manager.setProvider(new Ext.state.CookieProvider());

       store = new Ext.data.Store({
	        url: '<?php echo $_SERVER["SERVER_PORT"] == "80" ? "http" : "https" ?>://<?php echo $sCore->sCONFIG['sBASEPATH']?>/engine/backend/ajax/getVotes.php',
	        baseParams: {pagingID:storeid},
	        // create reader that reads the Topic records
	        reader: new Ext.data.JsonReader({
	            root: 'votes',
	            totalProperty: 'totalCount',
	            id: 'id',
	            fields: [
	                'id','datum','articleName','name','headline','points','comment','active'
	            ]
	        }),
	
	        // turn on remote sorting
	        remoteSort: true
    	});
    
    store.setDefaultSort('lastpost', 'desc');
    
     var expander = new Ext.grid.RowExpander({
        tpl : new Ext.Template(
 			'{comment}'             
        )
    });
    
    function renderActive(value, p, r){
    	var id = r.data.active;
    	if (id==1){
    		return "<a class='ico accept'></a>";
    	}else {
    		return "<a class='ico exclamation'></a>";
    	}
    	
    }
    
    var cm = new Ext.grid.ColumnModel([
   		expander,
    	{
    		header: "#",
    		width: 30,
    		sortable: false,
    		locked:true,
    		renderer: function (v,p,r,rowIndex,i,ds){
    			return '<input type="checkbox" class="markedArticles" name="markedArticles" value="'+r.data.id+'"/>';
    		}
    	},
   		{
           id: 'status', 
           header: "Status",
           dataIndex: 'active',
           width: 60,
           renderer: renderActive
        },
    	{
           id: 'datum', 
           header: "Datum",
           dataIndex: 'datum',
           width: 60
        },
        {
           id: 'article', 
           header: "Artikel",
           dataIndex: 'articleName',
           width: 120
        },
    	{
           id: 'writer', 
           header: "Verfasser",
           dataIndex: 'name',
           width: 120
        },
		{
           id: 'headline', 
           header: "�berschrift",
           dataIndex: 'headline',
           width: 120
        },
        {
           id: 'points', 
           header: "Punkte",
           dataIndex: 'points',
           width: 120
        },
        {
           header: "Optionen",
           dataIndex: 'options',
           width: 150,
           renderer: renderOptions
        }
        ]);
    cm.defaultSortable = true;
 
    function renderOptions(value, p, r){
		var id = r.data.id;
		var name = r.data.lastname;
		if (r.data.active==1){
			return String.format(
			'<a class="ico delete" style="cursor:pointer" onclick="deleteVote({0},{1})"></a>',id,"'"+name+"'","'articles'","'{article:"+id+"}'"
			);
		}else {
			return String.format(
			'<a class="ico add" style="cursor:pointer" onclick="confirmVote({0},{1})"></a><a class="ico delete" style="cursor:pointer" onclick="deleteVote({0},{1})"></a>',id,"'"+name+"'","'articles'","'{article:"+id+"}'"
			);
		}
    }
  
   var limitArray = [['25'],['50'],['100'],['250'],['500']];
	var limitStore = new Ext.data.SimpleStore({
        fields: ['limitArray'],
        data : limitArray
    });
    
    var pager = new Ext.PagingToolbar({
        pageSize: 25,
        store: store,
        displayInfo: true,
        displayMsg: 'Bewertungen: {0} - {1} Gesamt: {2}',
        emptyMsg: "Keine Bewertungen in Ansicht",
        items:[
            'Suche: ',
        {
        	xtype: 'textfield',
        	id: 'search',
        	title:'Test',
        	selectOnFocus: true,
        	width: 120,
        	listeners: {
            	'render': {fn:function(ob){
            		ob.el.on('keyup', searchFilter, this, {buffer:500});
            	}, scope:this}
        	}
        },
        '-',
        'Anzahl Artikel',
        { 
        	xtype: 'combo',
        	id: 'status',
        	fieldLabel: 'Last Name',
        	typeAhead: false,
        	title:'Anzahl Artikel',
        	forceSelection: false,
        	triggerAction: 'all',
        	store: limitStore,
        	displayField: 'limitArray',
        	lazyRender: false,
        	lazyInit: false,
        	mode:'local',
        	width: 120,
        	selectOnFocus:true,
        	listClass: 'x-combo-list-small',
        	listeners: {
            	'change' : {fn: limitFilter, scope:this}
        	}
    	},
    	'-',
         new Ext.Button  ({
            	text: 'Alle markieren',
           		handler: myExt.markAll
            })
        ,
        '-'
        ,
			new Ext.Button  ( {
            	text: 'Markierte Bewertungen l�schen',
                handler: myExt.deleteVotes
        })	           
        ]
    });
    
    var grid = new Ext.grid.GridPanel({
      	region:'center',
        width:700,
        height:500,
        title:'Artikelbewertungen',
        store: store,
        cm: cm,
        trackMouseOver:true,
        plugins:[expander],
        sm: new Ext.grid.RowSelectionModel({selectRow:Ext.emptyFn}),
        loadMask: true,
        
    	bbar: pager

    });

  	function limitFilter () {
	    var status = Ext.getCmp("status");
	    grid.store.baseParams["limit"] = status.getValue();
	    pager.pageSize = parseInt(status.getValue());
	    grid.store.lastOptions.params["start"] = 0;
	    grid.store.reload();
	}
	
	function searchFilter () {
	    var search = Ext.getCmp("search");
	    store.baseParams["search"] = search.getValue();
	    store.lastOptions.params["start"] = 0;
	    store.reload();
	}	
    // trigger the data store load
    store.load({params:{start:0, limit:25}});
    
	       
	  
	    	
	   var viewport = new Ext.Viewport({
	        layout:'border',
	        items:[
	            grid
	         ]
	    });
       
       
         
}};
}();
	Ext.grid.CheckColumn = function(config){
		Ext.apply(this, config);
		if(!this.id){
			this.id = Ext.id();
		}
		this.renderer = this.renderer.createDelegate(this);
	};	
	
	Ext.grid.CheckColumn.prototype = {
		init : function(grid){
			this.grid = grid;
			this.grid.on('render', function(){
				var view = this.grid.getView();
				view.mainBody.on('mousedown', this.onMouseDown, this);
			}, this);
		},
	
		onMouseDown : function(e, t){
			if(t.className && t.className.indexOf('x-grid3-cc-'+this.id) != -1){
				e.stopEvent();
				var index = this.grid.getView().findRowIndex(t);
				var record = this.grid.store.getAt(index);
				record.set(this.dataIndex, !record.data[this.dataIndex]);
			}
		},
	
		renderer : function(v, p, record){
			p.css += ' x-grid3-check-col-td';
			return '<div class="x-grid3-check-col'+(v?'-on':'')+' x-grid3-cc-'+this.id+'">&#160;</div>';
		}
	};
	Ext.grid.RowExpander = function(config){
		Ext.apply(this, config);
	
		this.addEvents({
			beforeexpand : true,
			expand: true,
			beforecollapse: true,
			collapse: true
		});
	
		Ext.grid.RowExpander.superclass.constructor.call(this);
	
		if(this.tpl){
			if(typeof this.tpl == 'string'){
				this.tpl = new Ext.Template(this.tpl);
			}
			this.tpl.compile();
		}
	
		this.state = {};
		this.bodyContent = {};
	};

	Ext.extend(Ext.grid.RowExpander, Ext.util.Observable, {
	    header: "",
	    width: 20,
	    sortable: false,
	    fixed:true,
	    menuDisabled:true,
	    dataIndex: '',
	    id: 'expander',
	    lazyRender : true,
	    enableCaching: true,
	
	    getRowClass : function(record, rowIndex, p, ds){
	        p.cols = p.cols-1;
	        var content = this.bodyContent[record.id];
	        if(!content && !this.lazyRender){
	            content = this.getBodyContent(record, rowIndex);
	        }
	        if(content){
	            p.body = content;
	        }
	        return this.state[record.id] ? 'x-grid3-row-expanded' : 'x-grid3-row-collapsed';
	    },
	
	    init : function(grid){
	        this.grid = grid;
	
	        var view = grid.getView();
	        view.getRowClass = this.getRowClass.createDelegate(this);
	
	        view.enableRowBody = true;
	
	        grid.on('render', function(){
	            view.mainBody.on('mousedown', this.onMouseDown, this);
	        }, this);
	    },
	
	    getBodyContent : function(record, index){
	        if(!this.enableCaching){
	            return this.tpl.apply(record.data);
	        }
	        var content = this.bodyContent[record.id];
	        if(!content){
	            content = this.tpl.apply(record.data);
	            this.bodyContent[record.id] = content;
	        }
	        return content;
	    },
	
	    onMouseDown : function(e, t){
	        if(t.className == 'x-grid3-row-expander'){
	            e.stopEvent();
	            var row = e.getTarget('.x-grid3-row');
	            this.toggleRow(row);
	        }
	    },
	
	    renderer : function(v, p, record){
	        p.cellAttr = 'rowspan="2"';
	        return '<div class="x-grid3-row-expander">&#160;</div>';
	    },
	
	    beforeExpand : function(record, body, rowIndex){
	        if(this.fireEvent('beforeexpand', this, record, body, rowIndex) !== false){
	            if(this.tpl && this.lazyRender){
	                body.innerHTML = this.getBodyContent(record, rowIndex);
	            }
	            return true;
	        }else{
	            return false;
	        }
	    },
	
	    toggleRow : function(row){
	        if(typeof row == 'number'){
	            row = this.grid.view.getRow(row);
	        }
	        this[Ext.fly(row).hasClass('x-grid3-row-collapsed') ? 'expandRow' : 'collapseRow'](row);
	    },
	
	    expandRow : function(row){
	        if(typeof row == 'number'){
	            row = this.grid.view.getRow(row);
	        }
	        var record = this.grid.store.getAt(row.rowIndex);
	        var body = Ext.DomQuery.selectNode('tr:nth(2) div.x-grid3-row-body', row);
	        if(this.beforeExpand(record, body, row.rowIndex)){
	            this.state[record.id] = true;
	            Ext.fly(row).replaceClass('x-grid3-row-collapsed', 'x-grid3-row-expanded');
	            this.fireEvent('expand', this, record, body, row.rowIndex);
	        }
	    },
	
	    collapseRow : function(row){
	        if(typeof row == 'number'){
	            row = this.grid.view.getRow(row);
	        }
	        var record = this.grid.store.getAt(row.rowIndex);
	        var body = Ext.fly(row).child('tr:nth(1) div.x-grid3-row-body', true);
	        if(this.fireEvent('beforcollapse', this, record, body, row.rowIndex) !== false){
	            this.state[record.id] = false;
	            Ext.fly(row).replaceClass('x-grid3-row-expanded', 'x-grid3-row-collapsed');
	            this.fireEvent('collapse', this, record, body, row.rowIndex);
	        }
	    }
	});
    Ext.onReady(function(){
    	myExt.init();
    });
	</script>
</head>
<body>


</body>
</html>


