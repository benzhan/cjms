define(function(require, exports, module) {
	var lib = require('lib');
	require('colortip');
	require('css/jquery.colortip.css');
	
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
			keyWord['_showGroupBy'] = lib.getParam('showGroupBy');
			
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
            
            $(document).on(BDY.click, '#oper [name=cal]', function() {
            	var showGroupBy = lib.getParam('showGroupBy');
            	if (showGroupBy) {
            		lib.removeParam('showGroupBy');
            		$('.icon').hide();
            	} else {
            		lib.setParam('showGroupBy', 1);
            		$('.icon').show();
            	}
            });
            
        	$(document).on('pager_change', M.loadTable);
        	
        	//group by图标
        	$(document).on(BDY.click, 'th a.icon', function(event) {
                var className = $(event.target).attr('class'); 
                if (/noGroupby/.test(className)) {
                    $(this).removeClass('noGroupby').addClass('groupby').attr('title', '分组计算');
                } else if (/groupby/.test(className)) {
                    $(this).removeClass('groupby').addClass('noGroupby').attr('title', '不分组计算');
                } else {
                	//这里是计算列的逻辑
                	var $select = $(this).find('select');
                	var index = $select.find('option:selected').index();
                	var len = $select.find('option').length;
                	var newIndex = (index + 1) % len;
                	//选择下一个
                	$select.val($select.find('option:eq(' + newIndex + ')').val());
                    return false;
                }
                
                //赋值到url的hash中
                var fieldNames = [];
                $('#table').find('th a.glyphicon-star').each(function(i) {
                    fieldNames[i] = $(this).parent().attr('fieldName');
                });
                
                if (fieldNames.length) {
                    lib.setParam('_groupby', fieldNames.join(','));
                } else {
                	lib.removeParam('_groupby');
                }
                
                return false;
            });
        	
        },
        initCal : function() {
        	// cal图标
            var $cal = $('.table').find('th a.cal, th a.noCal');
            $cal.colorTip && $cal.colorTip({
                'color' : 'blue',
                'timeout' : 100,
                'hideCallback' : function() {
                    var val = $(this).find('select').val();
                    
                    C.changeState($(this).parent().attr('fieldName'), val);
                    
                    var cals = {};
                    $('.cal').each(function() {
                        var val = $(this).find('select').val();
                        cals[val] = cals[val] || []; 
                        
                        cals[val].push($(this).parent().attr('fieldName'));
                    });
                    
                    lib.removeParam(['_max', '_min', '_sum', '_count', '_avg']);
                    for (var key in cals) {
                        lib.setParam(key, cals[key].join(','));
                    }
                }
           });
            
            // 防止冒泡
            $cal.find('select').click(function() {
            	return false;
            });
        },
        changeState : function(fieldName, val) {
        	var $table = $("#table");
            $table.find("th[fieldName='" + fieldName + "'] select").val(val);
            
            var $a = $table.find("th[fieldName='" + fieldName + "'] a.icon");
            var keys = ['_max', '_min', '_sum', '_count', '_avg'];
            for (var j in keys) {
            	$a.removeClass(keys[j]);
            }
            
            $a.addClass(val);
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
		C.initCal();
	}
	
	exports.init = init;
	exports.loadTable = M.loadTable;
	
});

