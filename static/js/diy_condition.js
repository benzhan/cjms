define(function(require, exports, module) {
	var lib = require('lib'), tpl = require('tpl');
	
	require('jquery');
	require('jquery-ui');
	require('datetimepicker');
	
	exports.init = init;
	
	var M = {

	};
	
	var C = {
        init : function() {
        	C.initCustomCondition();
        	C.initDocumentEvent();
        	
        	$('#condition').on(BDY.click, '#search', function() {
				var $groups = $(this).parent().find('.form-group');
				var where = [];
				for (var i = 0; i < $groups.length; i++) {
					var $group = $($groups[i]);
					var fieldName = $group.attr('fieldName');
					var opt = $group.attr('opt');
					var $controls = $group.find('.form-control');
					where[i] = [fieldName, opt];
					for (var j = 0; j < $controls.length; j++) {
						where[i].push($($controls[j]).val());
					}
				}
				lib.setParam('where', JSON.stringify(where));
			})
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
        	    if (data['opt'] == ':') {
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
        	
        	$('#opt').on('change', function() {
        		var value = $(this).val();
        		$('#customCondition .input-group > *').hide();
        		if (value == ':') {
        			$('#customCondition .input-group > *').show();
        		} else {
        			$('[name=value1]').show();
        		}
        	});
        	
        	// 删除自定义条件
        	$('#advConditionForm').on(BDY.click, '.glyphicon-remove', function() {
        		$(this).parents('.form-group').remove();
        	});
        	
        	// 排序自定义条件s
        	$('#advConditionForm').sortable({placeholder: "form-group sortable-placeholder"});
        },
        initDocumentEvent : function() {
        	$('#condition').on(
				'focus.datetimepicker.data-api click.datetimepicker.data-api',
				'[fieldType="datetime"]',
				C.initDatetimePicker
			);
			
			$('#condition').on(
				'focus.datetimepicker.data-api click.datetimepicker.data-api',
				'[fieldType="date"]',
				C.initDatetimePicker
			);
			
			$('#condition').on(BDY.click, '#switchToNormal', function() {
				$('#normalCondition').html($('#advConditionForm > *'));
				$('#normalCondition').show();
				$('#advCondition').hide();
			}).on(BDY.click, '#switchToAdv', function() {
				$('#advConditionForm').html($('#normalCondition > *'));
				$('#normalCondition').hide();
				$('#advCondition').show();
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
			$('#normalCondition').html(html);
			
			var where = lib.getParam('where');
			if (where) {
				where = JSON.parse(where);
				for (var i in where) {
					var value = where[i];
					var $group = $('[fieldName="' + value[0] + '"][opt="' + value[1] + '"]');
					$group.find('.form-control').each(function(index) {
						$(this).val(value[index + 2]);
					});
				}
			}
		}
	}
	
	C.init();
	
	function init(data) {
	    V.init(data);
	}
	
});