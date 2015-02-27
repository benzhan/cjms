define(function(require, exports, module) {
	var lib = require('lib'), form = require('form');
	var map;
	
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
        	var url = lib.url + "diyconfig/getFieldTable";
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
            data.tableId = $('#tableId').val() || lib.getParam('tableId');
            var args = ['sourceHost', 'sourcePort', 'sourceUser', 'sourcePass', 'sourceDb', 'sourceTable'];
            for (var i in args) {
                data[args[i]] = $('#' + args[i]).val();
            }
            return data;
        },
        _getFieldsData : function() {
            var fields = [];
            $('#liField').find('.list-group-item:visible').each(function(i) {
                var fieldMap = map[$(this).find('select[name=fieldMap]').val()]['func'];
                
                var fieldDisplay1 = $(this).find('input[name=fieldDisplay1]').prop('checked') ? 1 : 0;
                var fieldDisplay2 = $(this).find('input[name=fieldDisplay2]').prop('checked') ? 1 : 0;
                
                fields.push({
                    'fieldId': $(this).attr('fieldId'),
                    'fieldName': $(this).find('input[name=fieldName]').val(),
                    'fieldCName': $(this).find('input[name=fieldCName]').val(),
                    'fieldSortName': $(this).find('input[name=fieldSortName]').val(),
                    'defaultSortOrder': $(this).find('select[name=defaultSortOrder]').val(),
                    'fieldType': $(this).find('select[name=fieldType]').val(),
                    'fieldLength': $(this).find('textarea[name=fieldLength]').val(),
                    'callBack': $(this).find('textarea[name=callBack]').val(),
                    'fieldDisplay': (fieldDisplay2 << 1) + fieldDisplay1,
                    'needMerge': ($(this).find('input[name=needMerge]').prop('checked') ? 1 : 0),
                    'fieldVirtualValue': $(this).find('input[name=fieldVirtualValue]').val(),
                    'defaultValue': $(this).find('input[name=defaultValue]').val(),
                    'fieldMap': fieldMap,
                    'fieldPosition' : i
                });
            });
            
            return fields;
        },
        'saveTable' : function () {
            if (!form.validateForm('#tableForm') || !confirm('确定要保存？')) {  return false;  }
            
            var fields = M._getFieldsData();
            //var extraJsCss = $('#extraJsCss').val().replace(/\&/g,"%26").replace(/\+/g,"%2B");
            //var sourceCallBack = $('#sourceCallBack').val().replace(/\&/g,"%26").replace(/\+/g,"%2B");
            
            var url = lib.url + "diyConfig/saveTableAndFields";
            var data = {
                'tableId': $('#tableId').val() || lib.getParam('tableId'),
                'tableName': $('#tableCName').val(),
                'tableCName': $('#tableCName').val(),
                'pagination':$('#pagination').val(),
                'tableInfo': $('#tableInfo').val(),
                'extraJsCss': $('#extraJsCss').val(),
                'sourceHost': $('#sourceHost').val(),
                'sourcePort': $('#sourcePort').val(),
                'sourceDb': $('#sourceDb').val(),
                'sourceType': $('#sourceType').val(),
                'sourceTable': $('#sourceTable').val(),
                'sourceUser': $('#sourceUser').val(),
                'sourcePass': $('#sourcePass').val(),
                'sourceCallBack': $('#sourceCallBack').val(),
                'editFlag': $('#editFlag').attr('checked') ? 1 : 0,
                'bookFlag': $('#bookFlag').attr('checked') ? 1 : 0,
                'excelFlag': $('#excelFlag').attr('checked') ? 1 : 0,
                'groupFlag': $('#groupFlag').attr('checked') ? 1 : 0,
                'chartFlag': $('#chartFlag').attr('checked') ? 1 : 0,
                'fields': JSON.stringify(fields)
            };
            
            lib.post(url, data, function(objResult) {
            	if (objResult.result) {
            		var url = SITE_URL + 'DiyData/report?tableId=' + objResult.data;
            		$('#linkUrl').html(url).attr('href', url);
            		$('#tableId').val(objResult.data);
            	}
            	
            	lib.showTip(objResult.msg);
            }, {
            	loading : true
            });
        }
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
            
            // 加载数据
            if (lib.getParam('tableId')) {
            	$('[loadType=1]').click();
            }
            
            $('#addField').click(function() {
            	require.async('js/diy_config_table.js', function(page) {
            		page.addRow();
            	});
            });
            
            $('#saveTable').on(BDY.click, M.saveTable);
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
		map = data;
	}
	
	exports.init = init;
	
});

