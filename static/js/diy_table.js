define(function(require, exports, module) {
	var lib = require('lib');
	require('colortip');
	require('css/jquery.colortip.css');
	
    // require('jquery');
	require('jquery-ui');
	var keys = ['_max', '_min', '_sum', '_count', '_avg'];
	
	var M = {
		loadTable : function() {
			var url = lib.url + "diyData/table";
			var data = {};
			data.tableId = lib.getParam('tableId');
			var where = lib.getParam('where');
			where && (data.where = where);

			var keyWord = {};
			var showGroupBy = lib.getParam('showGroupBy');

			var keys = ['_sortKey', '_sortDir', '_page', '_pageSize'];
	        var keys2 = ['_groupby', '_max', '_min', '_count', '_sum', '_avg', '_hideNoGroupBy'];
	        showGroupBy && (keys = keys.concat(keys2));
	        
	        for (var i in keys) {
	            var val = lib.getParam(keys[i]);
	            val && (keyWord[keys[i]] = val);
	        }
			
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
			keyWord['_sortKey'] = lib.getParam('_sortKey');
			keyWord['_sortDir'] = lib.getParam('_sortDir');
			
			data.keyWord = JSON.stringify(keyWord);
			// 打开新页面下载csv
			window.open(url + '?' + $.param(data));
		}
	};
	
	var C = {
        init : function() {
        	$(document).on('pager_change', M.loadTable);
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
            
        	C.initTableSort();
        	C.initGroupBy();
        },
        initTableSort : function() {
        	$(document).on(BDY.click, '[sortKey]', function() {
        		var sortKey = $(this).attr('sortKey');
        		var oldSortKey = lib.getParam('_sortKey');
        		if (sortKey == oldSortKey) {
        			var oldSortDir = lib.getParam('_sortDir');
        			if (oldSortDir == 'DESC') {
        				lib.setParam('_sortDir', 'ASC');
        			} else {
        				lib.setParam('_sortDir', 'DESC');
        			}
        		} else {
        			lib.setParam('_sortKey', sortKey);
        			lib.setParam('_sortDir', 'ASC');
        		}
        		
        		M.loadTable();
        	});
        },
        initGroupBy : function() {
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
                $('th a.groupby').each(function(i) {
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
                    
                    V.changeState($(this).parent().attr('fieldName'), val);
                    
                    var cals = {};
                    $('.cal').each(function() {
                        var val = $(this).find('select').val();
                        if (val) {
                        	cals[val] = cals[val] || []; 
                        	cals[val].push($(this).parent().attr('fieldName'));
                        }
                    });
                    
                    lib.removeParam(keys);
                    for (var key in cals) {
                        lib.setParam(key, cals[key].join(','));
                    }
                }
           });
            
            // 防止冒泡
            $cal.find('select').click(function() {
            	return false;
            });
            
           	//从url参数中绑定计算图标
        	if (!lib.getParam('showGroupBy')) { 
        		$("th a.icon").hide();
            } else {
            	$("th a.icon").show();
            }
            
        },
        initCopy : function() {
        	$('#oper [name=copy]').copy({
	            'getContent' : function(clip) {
	         	    lib.showTip("复制成功!");
	                //return top.main.location.href;
	            }
	        });
        }
	};
	
	var V = {
		init : function() {
            var groupby = lib.getParam('_groupby');
            if (groupby) {
                groupby = groupby.split(',');
                for (var i in groupby) {
                    $("th[fieldName='" + groupby[i] + "'] a.icon").addClass('groupby').removeClass('noGroupby').attr('title', '分组计算');
                }
            } 
            
            for (var i in keys) {
                var key = keys[i];
                var val = lib.getParam(key);
                if (!val) { continue; }
                                
                val = val.split(',');
                for (var k in val) {
                	V.changeState(val[k], key);
                }
            }
		},
        changeState : function(fieldName, val) {
            $("th[fieldName='" + fieldName + "'] select").val(val);
            
            var $a = $("th[fieldName='" + fieldName + "'] a.icon");
            
            for (var j in keys) {
            	$a.removeClass(keys[j]);
            }
            
            $a.addClass(val);
        }
	};
	
	C.init();
	
	function init(data) {
        // 复制main里面的url
		// setTimeout(C.initCopy, 100);
		C.initCal();
		V.init();
	}
	
	exports.init = init;
	exports.loadTable = M.loadTable;
	
});

