define(function(require, exports, module) {
	var lib = require('lib');
	
	require('jquery');
	require('jquery-ui');
	
	exports.init = init;
	
	var M = {

	};
	
	var C = {
        init : function() {
        	$('.table').on(BDY.click, '.js_sort', function() {
        		
        	});
        },
	}
	
	var V = {
		init : function(data) {
			
		}
	}
	
	C.init();
	
	function init(data) {
	    V.init(data);
	}
	
});