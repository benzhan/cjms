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
	
	function formatUrl(url, nodeId) {
		if (url) {
        	var postfix = url.indexOf('?') >= 0 ? '&' : '?';
        	var index = url.indexOf('#');
        	if (index >= 0) {
        		var part1 = url.substr(0, index);
        		var part2 = url.substr(index + 1);
        		url = part1 + postfix + "_nodeId=" + nodeId + '#' + part2;
        	} else {
        		url += postfix + "_nodeId=" + nodeId;
        	}
        }
		
		return url;
	}
	
	function setOnFocus(tree, sender) {
	    sender.onfocus = function(sender){
	    	if (!sender.data) {
	    		return;
	    	}
	    	
	        var leftUrl = formatUrl(sender.data.leftUrl, sender.data.nodeId);
	        if (leftUrl) {
	        	$('#tree', parent.document).attr('src', leftUrl);
	        }
	        
	        var rightUrl = formatUrl(sender.data.rightUrl, sender.data.nodeId);
	        if (rightUrl) {
	        	$('#main', parent.document).attr('src', rightUrl);
	        }
	        
	        top.seajs.use('js/index.js', function(page) {
	        	page.setNodeId(sender.data.nodeId);
	        });
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