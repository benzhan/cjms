define(function(require, exports, module) {
	var lib = require('lib');
	
	require('jquery');
	require('datetimepicker');
	
	
	exports.init = init;
	
	var M = {

	};
	
	var C = {
        init : function() {
        	var option = {
        		language: 'zh-CN',
                format: "yyyy-mm-dd",
                autoclose: true,
                todayBtn: true,
            };
        	$('[fieldType="datetime"]').datetimepicker(option);
        	
        	option['minView'] = 2;
        	$('[fieldType="date"]').datetimepicker(option);
        }
	}
	
	var V = {
		init : function(tempData) {
			
		}
	}
	
	C.init();
	
	function init() {
		V.init();
	}
	
});