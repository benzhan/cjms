define(function(require, exports, module) {
	var lib = require('js/libs/library.js');
	
	var C = {
        init : function() {
           C.resizeFrames();
           C.bindBarEvent();
           
           $(window).on('resize', C.resizeFrames);
           
           // 点击展现数据
           $('#navbar').on('click.navbar', "[nodeId]", function() {
        	   var leftUrl = $(this).attr('leftUrl');
        	   var rightUrl = $(this).attr('rightUrl');
        	   if (!leftUrl) {
        		   var nodeId = $(this).attr('nodeId');
        		   leftUrl = SITE_URL + 'default/tree?nodeId=' + nodeId;
        	   }
        	   $('#tree').attr('src', leftUrl);
        	   
        	   if (rightUrl) {
        		   $('#main').attr('src', rightUrl);
        	   }
        	   
        	   if ($(this).parents('[nodeId]').length) {
        		   $('body').trigger('click');
        		   return false;
        	   }
           });
           
           $('.dropdown').on('mouseover', function() {
        	   $('.dropdown').removeClass('open');
        	   $(this).addClass('open');
           });
           
           $('.dropdown').on('mouseout', function() {
        	   $('.dropdown').removeClass('open');
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
            var height = $(window).height() - $('#navbar').height() - 1;
            $('#bar').height(height);
            $('#treeDiv').height(height);
            $('#mainDiv').height(height);
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
            });
        }
	}
	
	C.init();
	
});