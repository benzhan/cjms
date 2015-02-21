define(function(require, exports, module) {
	var lib = require('lib'), tpl = require('tpl');
	
	require('jquery');
	require('datetimepicker');
	
	exports.init = init;
	
	var M = {

	};
	
	var C = {
        init : function() {
        	C.initCustomCondition();
        	
        	$(document).on(
				'focus.datetimepicker.data-api click.datetimepicker.data-api',
				'[fieldType="datetime"]',
				C.initDatetimePicker
			);
			
			$(document).on(
				'focus.datetimepicker.data-api click.datetimepicker.data-api',
				'[fieldType="date"]',
				C.initDatetimePicker
			);
			
        },
        initCustomCondition : function() {
        	// 添加自定义条件
        	$('#addCondition').on(BDY.click, function() {
        		var data = {};
        		data['fieldName'] = $('#fieldName').val();
        		var $option = $('#fieldName > [value="' + data['fieldName'] + '"]');
        		data['fieldCName'] = $option.text();
        		data['fieldType'] = $option.attr('fieldType');
        		
        		data['opt'] = $('#opt').val();
        	    $option = $('#opt > [value="' + data['opt'] + '"]');
        	    data['optCName'] = $option.text();
        	    
        	    var $field = $('[fieldName="' + data['fieldName'] + '"][opt="' + data['opt'] + '"]');
        	    if ($field.length) {
        	    	lib.showTip('已经存在相同的条件了!');
        	    	return;
        	    }
        	    
        	    data['value1'] = $('[name=value1]').val();
        	    var $value2 = $('[name=value2]');
        	    if ($value2.length) {
        	    	data['value2'] = $value2.val();
        	    }
        	    
        	    var html = tpl.render('temp_form_group', data);
        	    var $last = $('#advConditionForm .form-group:last');
        	    if ($last.length) {
        	    	$last.after(html);
        	    } else {
        	    	$('#advConditionForm').prepend(html);
        	    }
        	});
        	
        	// 删除自定义条件
        	$('#advConditionForm').on(BDY.click, '.glyphicon-remove', function() {
        		$(this).parents('.form-group').remove();
        	});
        },
        initDatetimePicker : function(e) {
			var $this = $(this);
			if ($this.data('datetimepicker')) return;
			e.preventDefault();
			
			var option = {
        		language: 'zh-CN',
                format: "yyyy-mm-dd hh:ii",
                autoclose: true,
                todayBtn: true,
            };
			
			if ($(this).attr('fieldType') == 'date') {
				option['format'] = "yyyy-mm-dd";
				option['minView'] = 2;
			}
			
			// component click requires us to explicitly show it
			$this.datetimepicker(option);
			$this.datetimepicker('show');
		}
	}
	
	var V = {
		init : function(data) {
			var tempData = [];
			for (var key in data.where) {
			    var value = data.where[key]; 
			    
			    var tData = {};
			    tData['fieldName'] = value[0]; 
			    var field = data.fields[value[0]];
			    tData['fieldCName'] = field['fieldCName']; 
			    tData['fieldType'] = field['fieldType'];
			    tData['opt'] = value[1]; 
			    tData['value1'] = value[2]; 
			    if (value.length > 3) {
			        tData['value2'] = value[3]; 
			    }
			    tData['optCName'] = data.opts[tData['opt']];
			    
			    tempData[key] = tData;
			}
			data.where = tempData;
			var html = tpl.render('temp_form_inline', data);
			if (data.isNormal) {
				$('#normalCondition').html(html);		
			} else {
				$('#advConditionForm').html(html);
			}
		}
	}
	
	C.init();
	
	function init(data) {
	    V.init(data);
	}
	
});