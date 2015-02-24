define(function(require, exports, module) {
	var lib = require('lib'), tpl = require('tpl');
	var map, fieldTypes;
	
	require('jquery');
	require('jquery-ui');
	
	var C = {
        init : function() {
        	$('#liField').sortable();
        	
        	$('#liField').on(BDY.click, '[name=advOption]', function() {
        		var $item = $(this).parents('.list-group-item');
        		$item.find('.js-adv').slideToggle();
        	});
        	
        	$('#liField').on(BDY.click, '[name=delete]', function() {
        		var $item = $(this).parents('.list-group-item');
        		lib.confirm("确定要删除这个字段吗？", function() {
        			$item.slideUp();
        		});
        	});
        },
        addRow : function() {
        	var data = { map:map, fieldTypes:fieldTypes };
        	var html = tpl.render('temp_list_item', data);
			$('#liField').append(html);
        }
	};
	
	var V = {
		init : function(data) {
			var html = tpl.render('temp_list', data);
			$('#liField').html(html);
		}
	};
	
	function init(data) {
		// 排序自定义条件
		if (data) {
			V.init(data);
	        map = data.map;
	        fieldTypes = data.fieldTypes;
		}
		
    	C.init();
	}
	
	exports.init = init;
	exports.addRow = C.addRow;
	
});

