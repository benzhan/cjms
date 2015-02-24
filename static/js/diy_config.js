define(function(require, exports, module) {
	var lib = require('lib');
	
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
        getDbData : function() {
            var data = {};
            data.tableId = $('#tableId').val() || $.getParam('tableId');
            var args = ['sourceHost', 'sourcePort', 'sourceUser', 'sourcePass', 'sourceDb', 'sourceTable'];
            for (var i in args) {
                data[args[i]] = $('#' + args[i]).val();
            }
            return data;
        },
	};
	
	var C = {
        init : function() {
        	$('#loadDb').click(M.getDbs);
            $('#loadTable').click(M.getTables);
            C.bindAddOption();
            
            $('.loadField').click(function() {
                var loadType = $(this).attr('loadType');
                M.getFieldTable(loadType);
            });
            
            $('[loadType=1]').click();
            $('#addField').click(function() {
            	require.async('js/diy_config_table.js', function(page) {
            		page.addRow();
            	});
            });
        },
        bindAddOption : function() {
        	$('.addOption').click(function() {
                var $span = $('#temp_addOption').clone();
                $span.show();
                
                var $parent = $(this).parent();
                $parent.hide();
                $parent.after($span);
                
                var $select = $parent.parent().find('select');
                $span.find('input').val($select.val());
                
                //取消按钮
                $span.find('#cancel').click(function() {
                    $span.prev().show();
                    $span.remove();
                });
                
               //添加按钮
                $span.find('#add').click(function() {
                    var val = $span.find('input').val();
                    var html = '<option value="' + val + '">' + val + '<option>';
                    var $option = $select.find('option[value="' + val + '"]');
                    $option.length || $select.append(html);
                    $select.val(val);
                    
                    $span.find('#cancel').click();
                });
            });
        }
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

