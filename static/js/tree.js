define(function(require, exports, module) {
	var lib = require('js/libs/library.js');
	
	exports.init = init;
	
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
	        var leftUrl = getValidUrl(sender.data.leftUrl);
	        leftUrl && $('#tree', parent.document).attr('src', leftUrl);
	        
	        var rightUrl = getValidUrl(sender.data.rightUrl);
	        rightUrl && $('#main', parent.document).attr('src', rightUrl);
	    }
	}

	function getValidUrl(url) {
	    if (!url) { return false; }
	    if (/^http[s]*:\/\//.test(url)) {
	        return url;
	    } else if (/^javascript:/.test(url)) {
	        eval(url);
	        return false;
	    } else {
	        return SITE_URL + url;
	    }
	}

	/** 展开右键节点*/
	function expandNode(tree, sender) {
	    if (tree.getNode(sender.index).firstChild) {
	        return false;
	    }
	    
	    var data = {'showCgAndSc': true};
	    data.id = sender.value;
	    data.startIndex = 0;
	    data.itemNum = treeCfg.itemNum;
	    
	    var $loadingDiv = $.getLoadingDiv();
	    var option = {
	        'type': 'get',
	        'dataType': 'json',
	        'data': data,
	        'url': './menuManage.ajax.php?diy=1&act=getChildsByPId&pId=' + sender.data.nodeId,
	        'success': function(objData) {
	            if (objData.ret) {
	                tree.getNode(sender.index).eleSub.innerHTML = "";
	                tree.transData(sender.index, objData.data);
	            } else {
	                redirectLogin(objData);
	            }
	            $loadingDiv && $loadingDiv.end();
	        }
	    };

	    $.ajax(option);
	}
	
	C.init();
	
	function init(tempData) {
		V.init(tempData);
	}
	
});