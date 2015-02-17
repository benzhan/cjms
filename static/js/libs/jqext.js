/**
 * 这个脚本文件里面放一些小的公用jquery插件
 * 1、<input type="date" value="2011-08-20"/>                天选择器
 * 2、<input type="datetime" value="2011-08-20 00:00"/>      5分钟选择器
 * 3、<input type="text" placeholder="请输入邮箱"/>          placeholder为提示符
 */

(function($){
    var defaults = {
        'timeType' : 'day'
    };
    
    $.fn.timePicker = function(option) {
        //定义opt避免defaults被修改
        var opt = $.extend({}, defaults);
        option = $.extend(opt, option);
        initTimeOption();
        
        var $wrap = $("<span class='time_block'>"
                + "<img src='" + SITE_URL + "/rms/image/datepicker-prev.gif' class='time_arrow_left'/>"
                + "<img src='" + SITE_URL + "/rms/image/datepicker-next.gif' class='time_arrow_right'/>"
                + "</span>");
        
        this.each(function(i) {
            if (this.timeLoaded) { return; } 
            this.timeLoaded = true;
            
            if (option.timeType == 'day') {
                initDay(this);
            } else if (option.timeType == '5min') {
                init5min(this);
            }
        });
        
        function initDay(input) {
            var oldValue = input.value;
            //检验是否初始化过
            if (oldValue.length > 10) {
                var parts = oldValue.split(' ');
                input.value = parts[0];
            }
            
            $(input).addClass('timeInput');
            $wrap.insertAfter(input);
            $wrap.find('.time_arrow_left').after(input);

            $(input).datepicker({ 
                dateFormat: 'yy-mm-dd',     
                changeMonth: true,
                changeYear: true
            });
        }
        
        function init5min(input) {
            var oldValue = input.value;
              $wrap.insertAfter(input);
              $wrap.find('.time_arrow_left').after(input);
    
              var id = input.id;
              var title = $(input).attr('title');
              
              input.id = '';
              $(input).addClass('minuTimeInput');
              $(input).datepicker({ 
                  dateFormat: 'yy-mm-dd',     
                  changeMonth: true,
                  changeYear: true,
                  onSelect : setValue
              });
              
              var html = "<select class='datepicker-minu-detail' " + (title ? "title='" + title + "'" : "") + ">";
              for (var i = 0; i < 24; i++) {
                  var hour = i < 10 ? "0" + i : i;
                  for (var j = 0; j < 12; j++) {
                      var minu = hour + ":" + (j < 2 ? "0" + (j * 5) : j * 5) + ":00";
                      var text = " " + hour + " : " + (j < 2 ? "0" + (j * 5) : j * 5) + " ";
                      html += "<option value='" + minu + "'>" + text + "</option>";
                  }
              }
              html += "</select> ";
              html += "<input type='hidden' id='" + id + "'/>";;
              $wrap.after(html);
              
              var oldValue = $.trim(input.value);
              if (oldValue) {
                  var parts = oldValue.split(' ');
                  input.value = parts[0];
                  if (parts[1]) {
                      var temp = parts[1].split(':');
                      
                      //除去01~09时，parseInt变为0的尴尬
                      if (temp[0].length == 2 && temp[0][0] == '0') {
                          temp[0] = temp[0][1];
                      }
                      var hour = parseInt(temp[0]);
                      hour = hour < 10 ? "0" + hour : hour;
                      
                      //除去01~09时，parseInt变为0的尴尬
                      if (temp[1].length == 2 && temp[1][0] == '0') {
                          temp[1] = temp[1][1];
                      }
                      
                      var minu = parseInt(temp[1]);
                      minu = parseInt(minu / 5) * 5;
                      minu = minu < 10 ? "0" + minu : minu;
                      
                      $wrap.next().val(hour + ':' + minu + ":00");
                  }
              } else {
                  var date = new Date();
                  var hour = date.getHours();
                  hour = hour < 10 ? "0" + hour : hour;
                  var minu = parseInt(date.getMinutes() / 5) * 5;
                  minu = minu < 10 ? "0" + minu : minu;
                  $wrap.next().val(hour + ':' + minu + ":00");
              }
              
              setValue();
              $wrap.next().change(setValue);
        }
        
        function setValue() {
            //$wrap.next().next()是隐藏域，存放控件值
            $wrap.next().next().val($wrap.find('input').val() + ' ' + $wrap.next().val());
        };
                
        $wrap.find('.time_arrow_left').click(function() {
            var $dateField = $(this).next();
            $dateField.val(addDay($dateField.val(), -1));
            setValue();
        });
        
        $wrap.find('.time_arrow_right').click(function() {
            var $dateField = $(this).prev();
            $dateField.val(addDay($dateField.val(), 1));
            setValue();
        });
        
        function initTimeOption() {
            //如果加载过，则不再加载
            if (!$.datepicker.regional['zh-CN']) {
                $.datepicker.regional['zh-CN'] = {
                    clearText: '清除',
                    clearStatus: '清除已选日期',
                    closeText: '关闭',
                    closeStatus: '不改变当前选择',
                    prevText: '<上月',
                    prevStatus: '显示上月',
                    prevBigText: '<<',
                    prevBigStatus: '显示上一年',
                    nextText: '下月>',
                    nextStatus: '显示下月',
                    nextBigText: '>>',
                    nextBigStatus: '显示下一年',
                    currentText: '今天',
                    currentStatus: '显示本月',
                    monthNames: ['一月','二月','三月','四月','五月','六月', '七月','八月','九月','十月','十一月','十二月'],
                    monthNamesShort: ['一月','二月','三月','四月','五月','六月', '七月','八月','九月','十月','十一月','十二月'],
                    monthStatus: '选择月份',
                    yearStatus: '选择年份',
                    weekHeader: '周',
                    weekStatus: '年内周次',
                    dayNames: ['星期日','星期一','星期二','星期三','星期四','星期五','星期六'],
                    dayNamesShort: ['周日','周一','周二','周三','周四','周五','周六'],
                    dayNamesMin: ['日','一','二','三','四','五','六'],
                    dayStatus: '设置 DD 为一周起始',
                    dateStatus: '选择 m月 d日, DD',
                    dateFormat: 'yy-mm-dd',
                    firstDay: 1,
                    initStatus: '请选择日期',
                    isRTL: false
                };
                $.datepicker.setDefaults($.datepicker.regional['zh-CN']);
            }
        }
    };
    
    //复制的jquery插件，用法见index.html 202行
    $.fn.copy = function(opts) {
        var defaults = {
            //'trigger' : 'mousedown', //触发事件，可为mousedown、mouseover、mouseout、mouseup
            'getContent' : function() { return '复制的内容！'; },
            'appendElem' : undefined, //默认把flash加在body中
            'callBack' : function() { 
                var msgbox = top.$.msgbox || $.msgbox;
                msgbox && msgbox.succ("你已复制链接!", 1000); 
            }
        };
        opts = $.extend(defaults, opts);
        

        var zeroClipboard = window.ZeroClipboard || top.ZeroClipboard; 
        var clip = {};
        if (window.clipboardData) {
            clip.setText = function(text) {
                window.clipboardData.setData("text", text);
            };
            
            this.click(copyEvent);
        } else if (zeroClipboard) {
            clip = new zeroClipboard.Client();
            clip.setHandCursor(true);
            clip.glue(this[0], opts.appendElem);
            clip.addEventListener('mousedown', copyEvent);
        } else {
            this.click(copyEvent);
        }
        
        return this;
        
        function copyEvent() {
            var text = opts.getContent(clip);
            if (text === false) { return false; }
            clip.setText ? clip.setText(text) : alert(text);
            opts.callBack();
            return false;
        }
    };
    
    
    /**
     * ajax表格排序
     * @param string containerId 表格id或父div的id
     * @param function callback 排序事件的回调函数 
     * @author benzhan
     */
    $.fn.ajaxSortTable = function(option) {
        var defaults = {
            'callback' : function(){},
            'attrName' : 'sortKey'
        };
        
        option = $.extend(defaults, option);
        
        var ascPng = "<span id='arrow' style='color:red;'>↑</span>";
        var descPng = "<span id='arrow' style='color:red;'>↓</span>";

        $this = this;
        var $ths = $this.find("th[" + option.attrName + "]");
        $ths.css('cursor', 'pointer');
        $ths.attr('title', '点击排序');
               
        //鼠标点击事件   
        $ths.click(function() {
            var $arrow = $(this).find('#arrow');
            if ($arrow.length) {
                if ($.getParam('_sortDir') == 'DESC') {
                    $.setParam('_sortDir', 'ASC');
                } else {
                    $.setParam('_sortDir', 'DESC');
                }
            } else {
                $.setParam('_sortDir', 'DESC');
            }
            $.setParam('_sortKey', $(this).attr(option.attrName));
            option.callback();
        });
        
        //可能有预先排序
        if ($.getParam('_sortKey')) {
            var $sortField = $this.find("th[" + option.attrName + "='" +  $.getParam('_sortKey') + "']");
            if ($sortField.find('#arrow').length == 0) {
                if ($.getParam('_sortDir') == 'ASC') {
                    $sortField.append(ascPng);
                } else {
                    $sortField.append(descPng);
                }
            }
        }
        
        return $this;
    };
})(jQuery);

