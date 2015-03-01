define(function(require, exports, module) {
	var lib = require('lib'), tpl = require('tpl');
	
	exports.init = init;
	exports.setNodeId = function(nodeId) {
		lib.setParam('nodeId', nodeId);
		M.getSiteMap();
	}
	
	var M = {
		getSiteMap : function() {
			var url = lib.url + "default/getSiteMap";
			var data = {};
			data.nodeId = lib.getParam('nodeId');
			lib.post(url, data, function(objResult) {
				if (objResult.result) {
					var html = tpl.render("temp_breadcrumb", objResult);
					$('#breadcrumb').html(html);
				} else {
					lib.showTip(objResult.msg);
				}
			});
		}
	};
	
	var C = {
        init : function() {
           C.resizeFrames();
           C.bindBarEvent();
           C.bindNavEvent();
           
           $(window).on('resize', C.resizeFrames);

           // 复制main里面的url
           $('#copyUrl').copy({
               'getContent' : function(clip) {
            	   lib.showTip("复制成功!");
                   return top.main.location.href;
               }
           });

        },
        resizeFrames : function() {
            $('#mainDiv').width($(window).width() - $('#treeDiv').width() - $('#bar').width() - 3);
            var height = $(window).height() - $('#navbar').height() - 6;
            $('#bar').height(height);
            $('#treeDiv').height(height);
            $('#main').height(height - $('#breadcrumbDiv').height() - 3);
        },
        formatUrl : function(url, nodeId) {
    		if (url) {
            	var postfix = url.indexOf('?') >= 0 ? '&' : '?';
            	var index = url.indexOf('#');
            	if (index >= 0) {
            		var part1 = url.substr(0, index);
            		var part2 = url.substr(index);
            		url = part1 + postfix + "_nodeId=" + nodeId + '#' + part2;
            	} else {
            		url += postfix + "_nodeId=" + nodeId;
            	}
            }
    		
    		return url;
    	},
        bindNavEvent : function() {
        	 // 点击展现数据
            $('body').on(BDY.click, "[nodeId]", function() {
         	   var leftUrl = $(this).attr('leftUrl');
         	   var rightUrl = $(this).attr('rightUrl');
         	   var nodeId = $(this).attr('nodeId');
         	   
         	   lib.setParam('nodeId', nodeId);
         	   M.getSiteMap();
         	   
         	   leftUrl = C.formatUrl(leftUrl, nodeId);
         	   if (!leftUrl) {
         		   leftUrl = SITE_URL + 'default/menuTree?nodeId=' + nodeId;
         	   }
         	   $('#tree').attr('src', leftUrl);
         	   
         	   rightUrl = C.formatUrl(rightUrl, nodeId);
         	   if (rightUrl) {
         		   $('#main').attr('src', rightUrl);
         	   }
         	   
         	   if ($(this).parents('[nodeId]').length) {
         		   $('body').trigger('click');
         		   return false;
         	   }
            });
            
            // 顶部二级菜单绑定滑动事件
            $('.dropdown').on('mouseover', function() {
         	   $('.dropdown').removeClass('open');
         	   $(this).addClass('open');
            });
            
            // 顶部二级菜单绑定移出事件
            $('.dropdown').on('mouseout', function() {
         	   $('.dropdown').removeClass('open');
            });
        },
        bindBarEvent : function() {
        	 //侧边栏拖动事件
            $("#bar").draggable({
                stop: function(event, ui) {
                    $("#treeDiv").css("width", $("#bar").offset().left);
                    $("#bar").css('left', '');
                    C.resizeFrames();
                },
                axis: 'x',
                distance:0,
                iframeFix:true
            }).on('dblclick', function() {
            	$('#barImg').trigger(BDY.click);
            });
            
            // 点击关闭左导航逻辑
            $('#barImg').on(BDY.click, function() {
         	   var $tree = $("#treeDiv");
         	   var oldWidth = $tree.attr('oldWidth');
         	   if (oldWidth) {
         		   $tree.css("width", oldWidth);
         		   $tree.attr('oldWidth', null);
         		   $('#barImg').attr('src', "static/images/bar.gif");
         	   } else {
         		   $tree.attr('oldWidth', $tree.width());
         		   $tree.css("width", 0);
         		   $('#barImg').attr('src', "static/images/bar2.gif");
         	   }
         	   
         	   C.resizeFrames();
            });
            
            $('[data-toggle="tooltip"]').tooltip();
        }
	};
	
	var V = {
		init : function() {
			var nodeId = lib.getParam('nodeId');
			var $node = $('[nodeId="' + nodeId + '"]');
			if ($node.length) {
				$node.trigger(BDY.click);
			} else {
				// 后续完善
				
			}
		}
	};
	
	function init() {
		V.init();
	}
	
	C.init();
	
});