define(function(require, exports, module) {
	var lib = require('lib'), tpl = require('tpl');
	
	require('jquery');
	require('jquery-ui');
	
	var M = {
        getFieldTable : function(loadType) {
        	var url = "diyconfig/getFieldTable";
            var data = M.getDbData();
            data.loadType = loadType;
            
            var $loadingDiv = lib.getLoadingDiv('tableDiv');
            lib.get(url, data, function(html) {
            	$loadingDiv.end();
            	$('#tableDiv').html(html);
            }, {
            	type : 'text'
            });
        },
	};
	
	var C = {
        init : function() {
            
        }
	};
	
	var V = {
		init : function(data) {
			var html = tpl.render('temp_list', data);
			$('#liField').html(html);
		}
	};
	
	C.init();
	
	function init(data) {
		// 排序自定义条件
    	$('#liField').sortable();
    	data && V.init(data);
	}
	
	exports.init = init;
	
});

