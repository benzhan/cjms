define(function(require, exports, module) {
	var lib = require('lib');
	
	require('jquery-ui');
	
	var M = {
		getDbs : function() {
			var url = lib.url + "diyconfig/getDbs";
            var data = M.getDbData();
            
            var $loadingDiv = lib.getLoadingDiv('sourceDb');
            lib.post(url, data, function(objResult) {
            	$loadingDiv.end();
            	if (objResult.result) {
                    V.buildSelectHtml('sourceDb', objResult.data);
                } else {
                    lib.showErrorTip(objResult.msg);
                }
            });
        },
        getTables : function() {
			var url = lib.url + "diyconfig/getTables";
            var data = M.getDbData();
            data.sourceDb = data.sourceDb || $('#sourceDb').attr('defaultValue');
            
            var $loadingDiv = lib.getLoadingDiv('sourceTable');
            lib.post(url, data, function(objResult) {
            	$loadingDiv.end();
            	if (objResult.result) {
                    V.buildSelectHtml('sourceTable', objResult.data);
                } else {
                    lib.showErrorTip(objResult.msg);
                }
            });
        },
        getDbData : function() {
            var data = {};
            data.tableId = $('#tableId').val() || $.getParam('tableId');
            var args = ['sourceHost', 'sourcePort', 'sourceUser', 'sourcePass', 'sourceDb', 'sourceTable'];
            for (var i in args) {
                data[args[i]] = $('#' + args[i]).val();
            }
            return data;
        }
	};
	
	var C = {
        init : function() {
        	$('#loadDb').click(M.getDbs);
            $('#loadTable').click(M.getTables);
        },
	};
	
	var V = {
		buildSelectHtml : function(id, data) {
			var $select = $('#' + id);
	        var value = $select.val();
	        
	        var html = '';
	        for (var i in data) {
	            if (data[i] == value) {
	            	html += '<option selected>' + data[i] + '</option>';
	            } else {
	            	html += '<option>' + data[i] + '</option>';
	            }
	        }
	        
	        $select.html(html);
	    }
	};
	
	C.init();
	
	function init(data) {
		
	}
	
	exports.init = init;
	
});

