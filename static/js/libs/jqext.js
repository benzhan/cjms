/**
 * 这个脚本文件里面放一些小的公用jquery插件
 * 1、<input type="date" value="2011-08-20"/>                天选择器
 * 2、<input type="datetime" value="2011-08-20 00:00"/>      5分钟选择器
 * 3、<input type="text" placeholder="请输入邮箱"/>          placeholder为提示符
 */

/** 初始化逻辑放在此闭包内 **/
$(function() {  
    //初始化时间控件, .timeInput,.minuTimeInput
    if (typeof DELAY_INITIAL_TIME == 'undefined') {
        initialTime();
    }
    //下拉方式的5分钟
    $('.datetimeInput').datetime();
    $('.dateInput').datepicker({dateFormat: "yy-mm-dd"});
    
    //为所有样式为select的DOM加上搜索组件
    $('.select').live({
        'dblclick': function () {
            return;
            $(this).css('display','none');
            $(this).after($('<input type="text" />').bind({
                'keypress': function () {
                    if(parseInt(event.keyCode) == 13) {
                        var str = '<option>以下是搜索结果↓</option>';
                        var value = $(this).val();
                        $(this).prev().clone().find('option').each(function () {
                            if ($(this).text().match(new RegExp(value))) {
                                str += $('<div></div>').append($(this)).html();
                            }
                        });
                        $(this).prev().after($('<select></select>').html(str).bind({
                            'change': function () {
                                $(this).prev().val($(this).val()).css('display', '');
                                $(this).remove();
                            }
                        }));
                        $(this).css('display', 'none');
                        $(this).remove();
                    }
                }
            }));
            $(this).next().focus();
        }
    });

    //所有样式为multiSelect的Dom加上多选择组件
    $('.multiSelect').multiSelect();
});


/** 类似控件的msgbox */
(function($){
    var loadHtml = function(){
        if($('#__msgbox').length == 0){
            var html = '<div id="__msgbox">' +
                       '<div class="wrap">' +
                       '<div class="ico succ"></div>' +
                       '<div class="content"></div>' +
                       '<div class="end"></div>' +
                       '</div>' +
                       '</div>';
            $('body').append(html);
        }
    };
    var timeoutHander;
    var show = function(msg,arg) {
            loadHtml();
            $('#__msgbox .content').html(msg);
            $('#__msgbox').show();
            var timeout=3000;
            if(arg.length == 2){
                timeout=arg[1];
            }
            if(timeout > 0) {
                clearTimeout(timeoutHander);
                timeoutHander = setTimeout(function(){
                   hide();
                },timeout);
            }
    };
    var hide = function(){
         $('#__msgbox').hide();
    };
    $.msgbox ={
        succ:function(msg){
            show(msg,arguments);
            $('#__msgbox .ico').attr('class','ico succ');
        },
        warn:function(msg){
            show(msg,arguments);
            $('#__msgbox .ico').attr('class','ico warn');
        },
        err:function(msg){
            show(msg,arguments);
            $('#__msgbox .ico').attr('class','ico err');
        },
        loadStart:function(msg){
            msg = '<img src="' + SITE_URL + '/image/ajax-loader_b.gif"/>' + msg;
            show(msg,[msg,0]);
            $('#__msgbox .ico').attr('class','ico clear');
        },
        loadEnd:function(msg){
            hide();
        }
    };

})(jQuery);



/**
 * 将class为timeInput的input转化为没有分钟时间控件
 * 将class为minuTimeInput的input转化为含有分钟的时间控件
 * @author benzhan
 */
function initialTime() {
    if($('.timeInput,.minuTimeInput').length < 1) {
        return;
    }
    initTimeOption();
    /** 将class为timeInput的输入框变为天的时间输入框*/
    var $timeInput = $('.timeInput');
    if($timeInput.length) {
        $timeInput.each(function() {
            //检验是否初始化过
            if (!this.loaded) {
                var oldValue = $.trim(this.value);
                if (oldValue.length > 10) {
                    var parts = oldValue.split(' ');
                    this.value = parts[0];
                }
                
                this.loaded = true;
                var $wrap = $("<span class='time_block'>"
                          + "<img src='" + SITE_URL + "/rms/image/datepicker-prev.gif' class='time_arrow_left'/>"
                          + "<img src='" + SITE_URL + "/rms/image/datepicker-next.gif' class='time_arrow_right'/>"
                          + "</span>");
                $wrap.insertAfter(this);
                $wrap.find('.time_arrow_left').after(this);

                $(this).datepicker({ 
                    dateFormat: 'yy-mm-dd',     
                    changeMonth: true,
                    changeYear: true
                });
            }
        });
    }
    
    /** 将class为minuTimeInput的输入框变为天的时间输入框*/
    var $minuTimeInput = $('.minuTimeInput'); 
    if($minuTimeInput.length) {
        $minuTimeInput.each(function() {
            //检验是否初始化过
            if (!this.loaded) {
                this.loaded = true;
                var $wrap = $("<span class='time_block'>"
                          + "<img src='" + SITE_URL + "/rms/image/datepicker-prev.gif' class='time_arrow_left'/>"
                          + "<img src='" + SITE_URL + "/rms/image/datepicker-next.gif' class='time_arrow_right'/>"
                          + "</span>");
                $wrap.insertAfter(this);
                $wrap.find('.time_arrow_left').after(this);
    
                $(this).datepicker({ 
                    dateFormat: 'yy-mm-dd',     
                    changeMonth: true,
                    changeYear: true
                });
                
                var title = $minuTimeInput.attr('title');
                
                var html = "  <select class='datepicker-minu-detail' " + (title ? "title='" + title + "'" : "") + ">";
                for (var i = 0; i < 24; i++) {
                    var hour = i < 10 ? "0" + i : i;
                    for (var j = 0; j < 12; j++) {
                        var minu = hour + ":" + (j < 2 ? "0" + (j * 5) : j * 5) + ":00";
                        var text = " " + hour + " : " + (j < 2 ? "0" + (j * 5) : j * 5) + " ";
                        html += "<option value='" + minu + "'>" + text + "</option>";
                    }
                }
                html += "</select> ";
                $wrap.after(html);
                
                if (this.value) {
                    var oldValue = $.trim(this.value);
                    var parts = oldValue.split(' ');
                    this.value = parts[0];
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
            }
        });

    }
    
    $('.time_arrow_left').click(function() {
        var $dateField = $(this).next();
        $dateField.val(addDay($dateField.val(), -1));
    });
    
    $('.time_arrow_right').click(function() {
        var $dateField = $(this).prev();
        $dateField.val(addDay($dateField.val(), 1));
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
}


/**
 * 封装jQuery对象
 * @author benzhan
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
    
    //多选择组件
    $.fn.multiSelect = function(settings) {
        var defaults = {
            'selectRange' : undefined, 
            'selectedValue' : undefined, 
            'selectedDisplayMode' : undefined
        };
        settings = $.extend(defaults, settings);
        return this.each(function() {
        	$(this).addClass('input');
            if (settings['selectRange']) {
                $(this).attr('selectRange', settings['selectRange']);
            }
            if (settings['selectedValue']) {
                $(this).attr('selectedValue', settings['selectedValue']);
            } 
            if (settings['selectedDisplayMode']) {
                $(this).attr('selectedDisplayMode', settings['selectedDisplayMode']);
            }
            $(this).css('outline','none');
            var str = "";
            var splitKey = $(this).attr('splitKey') || ','; //分割KEY
            var selectRange = $.json.decode($(this).attr('selectRange')); //选择的key,value集合
            var selectedValue = $(this).attr('selectedValue') ? $(this).attr('selectedValue').split(splitKey) : []; //默认选择的key
            var selectedValueLength = selectedValue.length;
            if (Object.prototype.toString.apply(selectRange) === '[object Array]') {
                str += selectedValue;
            } else {
                //对象
                str = [];
                for (var key in selectRange) {
                    for (var i = 0; i < selectedValue.length; i++) {
                        if (selectedValue[i] === key) {
                            selectedValue.splice(i, 1);
                            str.push(selectRange[key]);
                            break;
                        }
                    }
                }
                str = str.join(splitKey);
            }
            $(this).val(str);
            $(this).bind({
                'click': function (evt) {
                    $(this).attr('disabled', 'disabled');
                    var _this = this;
                    var selectRange = $.json.decode($(this).attr('selectRange')); //选择的key,value集合
                    var splitKey = $(this).attr('splitKey') || ','; //分割KEY
                    var selectedValue = $(this).attr('selectedValue') ? $(this).attr('selectedValue').split(splitKey) : []; //默认选择的key
                    var selectedValueLength = selectedValue.length;
                    var str = "";
                    var len = 0;
                    if (Object.prototype.toString.apply(selectRange) === '[object Array]') {
                        //数组
                        for (var j = 0; j < selectRange.length; j++) {
                            len++;
                            var flag = false;
                            for (var i = 0; i < selectedValue.length; i++) {
                                if (selectedValue[i] === selectRange[j]) {
                                    flag = true;
                                    selectedValue.splice(i, 1);
                                    break;
                                }
                            }
                            str += "<label><input type='checkbox' optionFlag='1' " + (flag && 'checked') +  " key='" + selectRange[j] + "' val='" + selectRange[j] + "'>" + selectRange[j] + "</label><br>";
                        }
                    } else {
                        //对象
                        for (var key in selectRange) {
                            len++;
                            var flag = false;
                            for (var i = 0; i < selectedValue.length; i++) {
                                if (selectedValue[i] === key) {
                                    flag = true;
                                    selectedValue.splice(i, 1);
                                    break;
                                }
                            }
                            str += "<label><input type='checkbox' optionFlag='1' " + (flag && 'checked') +  " key='" + key + "' val='" + selectRange[key] + "'>" + selectRange[key] + "</label><br>";
                        }
                    }
                    
                    var $div = $('<div></div>');
                    $div.append($("<label><input type='checkbox' name='__allCheck__'" + (len === selectedValueLength ? 'checked' : '') + " />全选</label><br>").unbind().bind({
                        'click': function () {
                            $div.find('input[optionFlag=1]').each(function () {
                                $(this).attr('checked', $div.find('input[name=__allCheck__]').attr('checked'));
                            });
                        }
                    }));
                    $div.append(str);
                    $div.unbind('click').click(function(evt) {
                            evt.stopPropagation();
                    });
                    $div.css({'background':'white','zIndex':'16','left':$(this).offset().left,'min-width':$(this).width(),'maxHeight':'300','border':'1px solid gray','position':'absolute', 'overflow':'auto','margin-top':'-3px'});
                    $(this).after($div);
                    
                    evt.stopPropagation();
                    
                    $(document).bind('click', function() {
                        var checkedList = [];
                        var valueList = [];
                        $div.find('input[optionFlag=1]').each(function () {
                            if ($(this).attr('checked')) {
                                checkedList.push($(this).attr('key'));
                                valueList.push($(this).attr('val'));    
                            }
                        });
                        $(_this).attr('selectedValue', checkedList.join(splitKey));
                        if ($(_this).attr('selectedDisplayMode') == 'key') {
                            $(_this).val(checkedList.join(splitKey));
                        } else {
                            $(_this).val(valueList.join(splitKey)); 
                        }
                        $div.remove();
                        $(_this).attr('disabled','');
                    });
                }
            });
        });
    };
    
    //放大缩小输入框
    $.fn.zoomInput = function() {        
        return this.each(function() {
            init(this);
        });
        
        function init(obj) {
            var $obj = $(obj);
            $obj.click(function(event) {
                var $textarea = getTextarea($obj);
                $obj.css('visibility', 'hidden');
                $textarea.show().focus();
                
                $textarea.one('blur.zoomInput', function() {
                    var val = $textarea.val();
                    val = $obj.attr('dataType') == 'ip' ? formatIp(val) : val;
                    
                    $obj.css('visibility', '').val(val);
                    $textarea.hide();
                });
                
                return false;
            });
        }
        
        function getTextarea($obj) {
            var $textarea = $obj.next();
            if (!$textarea.attr('zoomInput')) {
                $textarea = $('<textarea class="textarea" zoomInput="true" style="position:absolute;height:80px;z-index:1;"></textarea>');
                $textarea.click(stopEvent);
                $obj.after($textarea);
            }
            
            $textarea.val($obj.val());
            var pos = $obj.position();
            $textarea.css('left', pos.left).css('top', pos.top);
            return $textarea;
        }
        
        /** 格式化ip的格式 */
        function formatIp(val) {
            //过滤ip
            var pattern = /((?:25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9])\.(?:(?:25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.){2}(?:25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9]))/g;
            var vals = val.match(pattern);
            return vals && vals.join("\n") || '';
        }
        
        function stopEvent() {
            return false;
        }
    };
    
    $.fn.relation = function(settings, args) {
        if (!settings) {
            return this.each(function() {
                init(this);
            });
        } 
        if (typeof settings == 'object') {
            var defaults = {
                'selectRange' : undefined, 
                'selectedValue' : undefined, 
                'selectedDisplayMode' : undefined
            };
            settings = $.extend(defaults, settings);    
        } else {
            if (settings === 'add') {
                return this.each(function() {
                    if (!$(this).attr('inited')) {
                        top.$.msgbox.err('Resource Uninited');
                        return false;
                    }
                    if (args) {
                    	getSwfObj('CmdbDemo').newItem(args.toString());	
                    }
                });
            } else if (settings === 'link') {
                return this.each(function() {
                    if (!$(this).attr('inited')) {
                        top.$.msgbox.err('Resource Uninited');
                        return false;
                    }
                    if (!args.length==2) {
                        top.$.msgbox.err('Params Error');
                        return false;
                    }
                    getSwfObj('CmdbDemo').linkItems(args[0], args[1]);
                });
            } else if (settings === 'unlink') {
                return this.each(function() {
                    if (!$(this).attr('inited')) {
                        top.$.msgbox.err('Resource Uninited');
                        return false;
                    }
                    if (!args.length==2){
                        top.$.msgbox.err('Params Error');
                        return false;
                    }
                    getSwfObj('CmdbDemo').unlinkItems(args[0], args[1]);
                });
            } else if (settings === 'itemSpace') {
            	return this.each(function() {
                    if (!$(this).attr('inited')) {
                        top.$.msgbox.err('Resource Uninited');
                        return false;
                    }
                    getSwfObj('CmdbDemo').setItemSpace(args);
                });
            } else {
                top.$.msgbox.err('Undefined Cmd');
            }
        }
        function getSwfObj(swfID) {
            var swfObj;
            if (navigator.appName.indexOf("Microsoft") > -1) {
                    swfObj = window[swfID];
            } else {
                    swfObj = document[swfID];
            }
            return swfObj;
        }
        
        function init(obj) {
            $.ajax({
                'url': '/rms/relation/',
                'type': 'get',
                'dataType': 'json',
                'async': false,
                'success': function (response) {
                    if (response.result) {
                        $(obj).attr('inited', true);
                        $(obj).html(response.data); 
                    }
                },
                'error': function () {
                    top.$.msgbox.err('Failed Loading Resource');
                }
            });
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



/**
    RTX Plugin for jQuery
    TNM2 2009-02-19

    $('span.englishname').rtxPresence();
*/
var rtx_frameRandom = 0;

function rtx_getUILocation(objSrc) {
    var obj = objSrc;
    var uiLocation = {};
    var uiX = 0;
    var uiY = 0;
    var objDX = 0;
    var fRtl = (document.dir == "rtl");
    var parentWindow = window;
    var doc = document;
    var scrollTop = 0;
    var scrollLeft = 0;
    
    if(rtx_frameRandom === 0) {
        var dt = new Date();
    
        rtx_frameRandom = dt.getSeconds() * 1000 + dt.getMilliseconds();
    }
    
    rtx_frameRandom++;
    
    while(obj && obj.rtxframeRandom != rtx_frameRandom) {
        obj.rtxframeRandom = rtx_frameRandom;
    
        scrollLeft = obj.scrollLeft;
        scrollTop = obj.scrollTop;
    
        if(obj.tagName == "BODY") {
            if(obj.scrollLeft === 0 && doc.documentElement.scrollLeft !== 0) {
                scrollLeft = doc.documentElement.scrollLeft;
            }
    
            if(obj.scrollTop === 0 && doc.documentElement.scrollTop !== 0) {
                scrollTop = doc.documentElement.scrollTop;
            }
        }
    
        if(fRtl) {
            if(obj.scrollWidth >= obj.clientWidth + scrollLeft) {
                objDX = obj.scrollWidth - obj.clientWidth - scrollLeft;
            }
            else {
                objDX = obj.clientWidth + scrollLeft - obj.scrollWidth;
            }
    
            uiX += obj.offsetLeft + objDX;
        }
        else {
            uiX += obj.offsetLeft - scrollLeft;
        }
    
        uiY += obj.offsetTop - scrollTop;
        obj = obj.offsetParent;
    
        if(!obj) {
            if(parentWindow.frameElement) {
                obj = parentWindow.frameElement;
    
                parentWindow = parentWindow.parent;
                doc = parentWindow.document;
            }
        }
    }
    
    if(parentWindow) {
        uiX += parentWindow.screenLeft;
        uiY += parentWindow.screenTop;
    }
    
    uiLocation.uiX = uiX;
    uiLocation.uiY = uiY;
    
    if(fRtl) {
        uiLocation.uiX += objSrc.offsetWidth;
    }
    
    return uiLocation;
}

(function($) {
    $.rtx = $.rtx || {};
    $.extend($.rtx, {
        nameCtrl: null,
        prefix: '_rtxname',
        id: 0,
        nextId: function() {
            var id_ = $.rtx.id;
            $.rtx.id = $.rtx.id + 1;
            return $.rtx.prefix + id_;
        },
        images: {},
        ensureNameCtrl: function() {
            if ($.rtx.nameCtrl) {
                return;
            }
            if (!$.browser.msie) {
                return;
            }
            /*
            if ($("#RTXNameCtrl").length > 0)
                return;
            var objhtml = "<object classid='clsid:8F8086BE-0925-481D-B3C1-06BCB4121A5E' codebase='cab/RTXName.dll#version=1,0,0,9' id='RTXNameCtrl' style='display:none;'></object>";
            $(objhtml).appendTo(document.body);
            */
    
            try {
                $.rtx.nameCtrl = new ActiveXObject("RTXName.NameCtrl");
                $.rtx.nameCtrl.OnStatusChange = $.rtx.onStatusChange;
                $(window).scroll(function() { $.rtx.nameCtrl.HideOOUI(); });
            } catch (e) {
                // not installed
                //alert(e.description);
            }
        },
    
        onStatusChange: function(nick, status, file, rtxnum) {
            alert(nick + " status change to " + status);
        },
    
        statusPattern: /(\d+)\-(\d+)\.bmp$/,
    
        getStatus: function(nick) {
            var file = $.rtx.nameCtrl.GetStatusImage(nick, "");
            var gender = "male";
            var status = "unknown";
            var m = file.match($.rtx.statusPattern);
            if (!m)
            {
                return [gender, status];
            }
            switch (parseInt(m[1],10))
            {
            case 127: gender = "male"; break;
            case 128: gender = "female"; break;
            }
            switch (parseInt(m[2],10))
            {
            case 1: status = "online"; break;
            case 2: status = "offline"; break;
            case 3: status = "away"; break;
            }
            return [gender, status];
        },
    
        updateStatus: function(nick, ids) {
            if (!ids) {
                return;
            }
            var multiFlag = false;
            var nickArr = [];
            if (nick) {
                nickArr = nick.split(';');  
            }
            
            if (nickArr.length > 2) {
                //说明是多人
                multiFlag = true;
            }
            var status = $.rtx.getStatus(nick);
            $.each(ids, function(i, id) {
                if (multiFlag) {
                    $("#" + id).attr("src", SITE_URL + "/script/rtx/multi.png");
                } else {
                    $("#" + id).attr("src", SITE_URL + "/script/rtx/rtx-" + status[0] + "-" + status[1] + ".gif");
                }    
            });
        },
    
        updateAllStatus: function() {
            if (!$.rtx.nameCtrl) {
                return;
            }
            $.each($.rtx.images, function(nick, ids) {
                $.rtx.updateStatus(nick, ids);
            });
        },
    
        addNick: function(obj) {
            var $obj = $(obj);
            var nick = $obj.text();
            if(nick === null || nick === '')
            {
                return;
            }
            var imgid = $.rtx.nextId();
            if(typeof($.rtx.images[nick]) == "undefined") {
                $.rtx.images[nick] = [];
            }
            var arr = $.rtx.images[nick];
            arr[arr.length] = imgid;
    
            var img = $("<img src='" + SITE_URL + "/script/rtx/rtx-blank.gif' style='width:16px;height:16px;margin:0 3px;vertical-align:middle;' id='" + imgid + "' class='rtx_icon' />");
            //img.attr("nick", nick);
            $obj.before(img);
    
            img.hover(
                    function() {
                        var loc = rtx_getUILocation(this);
                        $.rtx.nameCtrl.ShowOOUI(nick, 0, loc.uiX+1, loc.uiY+1);
                    },
                    function() {
                        $.rtx.nameCtrl.HideOOUI();
                    }
                );
    
        }
    
    });
    
    $.fn.rtxPresence = function() {
        if($.browser.msie)
        {
            $.rtx.ensureNameCtrl();
    
            this.each(function() {
                if (!$.rtx.nameCtrl) {
                    return this;
                }
                $.rtx.addNick(this);
            });
    
            $.rtx.updateAllStatus();
        }
        return this;
    
    };
    $.fn.disableLink = function() {
        $(this).addClass('disabled');
        $(this).attr('disabled', 'disabled');
    };
    $.fn.ableLink = function() {
        $(this).removeClass('disabled');
        $(this).attr('disabled', false);
    };

})(jQuery);
