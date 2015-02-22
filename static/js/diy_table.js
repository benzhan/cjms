define(function(require, exports, module) {
	var lib = require('lib');
	
	require('jquery');
	require('jquery-ui');
	
	exports.init = init;
	
	var M = {
		loadTable : function() {
			var url = lib.url + "diyData/table";
			var data = {};
			data.tableId = lib.getParam('tableId');
			data.where = lib.getParam('where');
			
			var keyWord = {};
			keyWord['_page'] = lib.getParam('page');
			keyWord['_pageSize'] = lib.getParam('pageSize');
			keyWord['_sortKey'] = lib.getParam('sortKey');
			keyWord['_sortDir'] = lib.getParam('sortDir');
			
			data.keyWord = JSON.stringify(keyWord);
			var $loadingDiv = lib.getLoadingDiv('table');
			lib.get(url, data, function(html) {
				$loadingDiv.end();
				$('#table').after(html).remove();
			});
		}
	};
	
	var C = {
        init : function() {
        	$(document).on(BDY.click, '[sortKey]', function() {
        		var sortKey = $(this).attr('sortKey');
        		var oldSortKey = lib.getParam('sortKey');
        		if (sortKey == oldSortKey) {
        			var oldSortDir = lib.getParam('sortDir');
        			if (oldSortDir == 'DESC') {
        				lib.setParam('sortDir', 'ASC');
        			} else {
        				lib.setParam('sortDir', 'DESC')
        			}
        		} else {
        			lib.setParam('sortKey', sortKey);
        			lib.setParam('sortDir', 'ASC');
        		}
        		
        		M.loadTable();
        	});
        	
        	$(document).on('pager_change', M.loadTable);
        },
	}
	
	C.init();
	
	function init(data) {
		
	}
	
});