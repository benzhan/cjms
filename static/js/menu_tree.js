define(function(require, exports, module) {
	var lib = require('js/libs/library.js');
	
	exports.init = init;
	
	var M = {
		getChildsByPId : function(tree, sender) {
			var url = lib.url + "menu/getChildsByPId";
		    var data = { nodeId : sender.data.nodeId };
		    lib.post(url, data, function(objResult) {
		    	if (objResult.result) {
	                tree.getNode(sender.index).eleSub.innerHTML = "";
	                tree.transDataing = true;
	                tree.transData(sender.index, objResult.data);
	                tree.transDataing = false;
	            } else {
	                lib.showTip(objResult.msg);
	            }
		    });
		}
	};
	
	var C = {
        init : function() {

        }
	}
	
	var V = {
		init : function(tempData) {
			var treeCfg = {
			    basePath : SITE_URL + 'static/js/libs/skytree/',
			    singleExpand : false,
			    hintBeforeDelete : false,
			    lang : 'zh-cn',
			    expandLevel : 2,
			    theme : 'Default',
			    autoCheck : true,
			    allowContextMenu : false,
			    readOnly : true,
			    allowDragDrop : false,
			    allowFocus : true,
			    tempData : tempData
			};

		    objTree1 = new SkyTree('objTree1', 'tree');
		    objTree1.afterinsert = setOnFocus;
		    objTree1.beforeExpandNode = expandNode;
		    objTree1.initialize(treeCfg);
		}
	}
	
	function setOnFocus(tree, sender) {
	    sender.onfocus = function(sender){
	        var leftUrl = sender.data.leftUrl;
	        leftUrl && $('#tree', parent.document).attr('src', leftUrl);
	        
	        var rightUrl = sender.data.rightUrl;
	        rightUrl && $('#main', parent.document).attr('src', rightUrl);
	    }
	}

	/** 展开右键节点*/
	function expandNode(tree, sender) {
	    if (tree.getNode(sender.index).firstChild) {
	        return false;
	    }
	    
	    M.getChildsByPId(tree, sender);
	}
	
	C.init();
	
	function init(tempData) {
		V.init(tempData);
	}
	
});