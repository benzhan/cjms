define(function(require, exports, module) {
	var lib = require('lib');
	
    // require('jquery');
	require('jquery-ui');
	
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
			}, {
				type : 'text'
			});
		},
		exportCSV : function() {
			var url = lib.url + "diyData/exportCSV";
			var data = {};
			data.tableId = lib.getParam('tableId');
			data.where = lib.getParam('where');
			
			var keyWord = {};
			keyWord['_sortKey'] = lib.getParam('sortKey');
			keyWord['_sortDir'] = lib.getParam('sortDir');
			
			data.keyWord = JSON.stringify(keyWord);
			// 打开新页面下载csv
			window.open(url + '?' + $.param(data));
		}
	};
	
	var C = {
        init : function() {
        	$(document).on(BDY.click, '#table [sortKey]', function() {
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
        	
            $(document).on(BDY.click, '#oper [name=refresh]', M.loadTable);
        	
            $(document).on(BDY.click, '#oper [name=export]', M.exportCSV);
        	
        	$(document).on('pager_change', M.loadTable);
        	
        },
        initCopy : function() {
        	$('#oper [name=copy]').copy({
	            'getContent' : function(clip) {
	         	    lib.showTip("复制成功!");
	                //return top.main.location.href;
	            }
	        });
        }
	}
	
	C.init();
	
	function init(data) {
        // 复制main里面的url
		// setTimeout(C.initCopy, 100);
	}
	
	exports.init = init;
	exports.loadTable = M.loadTable;
	
});

