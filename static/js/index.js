define(function(require, exports, module) {
	var C = {
        init : function() {
           C.resizeFrames();
           C.bindBarEvent();
           
           $(window).on('resize', C.resizeFrames);
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