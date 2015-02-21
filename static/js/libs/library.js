define(function(require, exports, module) {
	
    exports.getParam = getParam;
    exports.getParam2 = getParam2;
    exports.setParam = setParam;
    exports.setParam2 = setParam2;
    exports.removeParam = removeParam;
    
    exports.post = post;
    exports.get = get;
    exports.getLocalData = getLocalData;
    
    exports.setTimeout = _setTimeout;
    exports.setInterval = _setInterval;
    
    exports.redirect = redirect;
    exports.redirectClient = redirectClient;
    exports.onNavBtnClick = onNavBtnClick;
    exports.historyBack = historyBack;
    BDY.historyBack = historyBack;
    exports.openUrl = openUrl;
    
    exports.showTip = showTip;
    exports.showErrorTip = showErrorTip;
    exports.showLoading = showLoading;
    exports.hideLoading = hideLoading;
    exports.showSelectDialog = showSelectDialog;
    exports.confirm = _confirm;
    exports.prompt = _prompt;
    
    exports.openLogin = openLogin;
    exports.reportPv = reportPv;
    exports.template = template;
    
    exports.init = init;
    

    //+++++++++++++++++++++++++++ hash 参数控制 +++++++++++++++++++++++++++++++++++++
    /**
     * js获取url参数的值，(函数内部decodeURIComponent值)
     * @author benzhan
     * @param {string} name 参数名
     * @return {string} 参数值
     */
    function getParam(name) {
        //先获取#后面的参数
        var str = document.location.hash.substr(2);
        var value = getParam2(name, str);
        if (value == null) {
            str = document.location.search.substr(1);
            value = getParam2(name, str);
        }

        return value;
    };

    function getParam2(name, str) {
        //获取参数name的值
        var reg = new RegExp("(^|!|&)" + name + "=([^&]*)(&|$)");

        //再获取?后面的参数
        r = str.match(reg);
        if (r != null) {
            try {
                return decodeURIComponent(r[2]);
            } catch (e) {
                console.log(e + "r[2]:" + r[2]);
                return null;
            }
        }
        return null;
    }

    /**
     * js设置url中hash参数的值, (函数内部encodeURIComponent传入的value参数)
     * @author benzhan
     * @param {string} name 参数名
     * @return {string} value 参数值
     */
    function setParam(name, value, causeHistory) {
        var hash = document.location.hash.substr(2);
        if ($.isPlainObject(name)) {
            // 支持 setParam(value, causeHistory)的写法
            causeHistory = value;
            value = name;
            
            for (var key in value) {
               hash = setParam2(key, value[key], hash);
            }
        } else {
            hash = setParam2(name, value, hash);
        }
        
        if (causeHistory) {
            document.location.hash = "!" + hash;
        } else {
            if (history.replaceState) {
                history.replaceState({}, null, "#!" + hash);
            } else {
                console.error("history.replaceState:" + history.replaceState);
            }
        }
    };
    
    function setParam2(name, value, str) {
        if ($.isPlainObject(name)) {
            // 支持 setParam(value, causeHistory)的写法
            str = value;
            value = name;
            for (var key in value) {
               str = setParam2(key, value[key], str);
            }
            return str;
        } else {
            var prefix = str ? "&" : "";
            var reg = new RegExp("(^|!|&)" + name + "=([^&]*)(&|$)");
            r = str.match(reg);
            if (r) {
                if (r[2]) {
                    var newValue = r[0].replace(r[2], value);
                    str = str.replace(r[0], newValue);
                } else {
                    var newValue = prefix + name + "=" + value + "&";
                    str = str.replace(r[0], newValue);
                }
            } else {
                var newValue = prefix + name + "=" + value;
                str += newValue;
            }
            
            return str;
        }
    }

    /**
     * 删除锚点后的某个参数
     * @author benzhan
     * @param {string} name 参数名
     */
    function removeParam(name, causeHistory) {
        var hash = document.location.hash.substr(2);
        var reg = new RegExp("(^|!|&)" + name + "=([^&]*)(&|$)");
        r = hash.match(reg);
        if (r) {
            hash.replace(r[0], "");
        }
        
        if (causeHistory) {
            document.location.hash = "!" + hash;
        } else {
            if (history.replaceState) {
                history.replaceState({}, null, hash);
            } else {
                console.error("history.replaceState:" + history.replaceState);
            }
        }
    };
    
    function parseHash(strUrl) {
        strUrl = strUrl ? strUrl : document.location.hash.substr(2);
        
        //获取参数name的值
        var reg = new RegExp("(^|!|#|&)(\\w*)=([^&]*)", "g");
        
        //先获取#后面的参数
        var r = reg.exec(strUrl);
        var datas = {};
        
        var i = 0;
        while (r != null) {
            datas[r[2]] = decodeURIComponent(r[3]);
            r = reg.exec(strUrl);            
        }
        
        return datas;
    };
 // +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


// +++++++++++++++++++++++++++ get 和 post +++++++++++++++++++++++++++++++++++++   
    function _getDefaultData(data) {
        data = data || {};
        
        data['g_appid'] = getParam('g_appid');
        
        data['username'] = getLocalData('username');
        data['password'] = getLocalData('password');
        data['osinfo'] = getLocalData('osinfo');
        data['oauthCookie'] = getLocalData('oauthCookie');
        
        // 获取验证码
        data.channel = getParam('channel');
        data.platform = getParam('platform');
        
        for (var k in data) {
            var v = data[k];
            if (v == null || typeof v === 'undefined') {
                delete data[k];
            } else {
                if (typeof v === 'string') {
                    data[k] = $.trim(v);
                }
            }
        }
        
        return data;
    }

    /**
     * option object {'cache', 'loading', 'onTimeout'}
     * cache为every、once
     */
    function post(url, data, callback, option) {
        option = option || {};
        // 支持postCross(url, callback)的写法;
        if ( typeof data == 'function') {
            option = callback || {};
            callback = data;
            data = {};
        }

        // data = _getDefaultData(data);
        if (window.KLCommon && KLCommon.post) {
            KLCommon.post(url, data, function(objResult) {
                _handleResult(callback, objResult);
            }, option);
        } else {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", url);
            
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            // 兼容ios6.0会缓存
            if (window.KalaGame) {
                xhr.setRequestHeader("Cache-Control", "no-cache");
            }
            data = $.param(data);
            
            //console.log('post: ' + url);
            //console.log(data);
            
            if (option['loading']) {
                xhr.timeout = 15000;
                showLoading();
            }
            
            xhr.send(data);
            xhr.ontimeout = option['onTimeout'];
        
            // 响应处理
            xhr.onload = function() {
                option['loading'] && hideLoading();
                var objResult = _getAjaxResult(xhr.responseText);
                _handleResult(callback, objResult);
            };
            
        }
    }

    function get(url, callback, option) {
        option = option || {};
        
        if (window.KLCommon && KLCommon.get) {
            KLCommon.get(url, callback, option);
        } else {
            var xhr = new XMLHttpRequest();
            xhr.url = url;
            option['loading'] && showLoading();
            xhr.onreadystatechange = function() {
                if (xhr.readyState != 4) {
                    return;
                }
                if ((xhr.status >= 200 && xhr.status < 300) || xhr.status == 304 || (xhr.status == 0 && location.protocol == 'file:')) {
                    option['loading'] && hideLoading();
                    callback(xhr.responseText);
                }
            };
            xhr.open("GET", url);
            xhr.send();
        }
    }
    

    function _getAjaxResult(text) {
        var objResult = {};
        var objError = {
            result : 0,
            msg : "系统繁忙，请稍后再试！"
        };

        try {
            objResult = JSON.parse(text);
            if (typeof objResult !== 'object' || objResult === null) {
                objResult = objError;
            }
        } catch (ex) {
            //非json的处理
            objResult = objError;
        }

        return objResult;
    }

    function _handleResult(callback, objResult) {
        callback && callback(objResult);
        
        if (!objResult.result && objResult.ret == -201) {
            showConfirmBind();
        }
    }
    
    function getLocalData(key) {
        return getParam(key) || localStorage.getItem(key);
    }

    // +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    
    
    /**
     * 设置定时器
     * @param callback 定时器触发的函数
     * @param timeout 执行callback前的时间
     */
    function _setTimeout(callback, timeout) {
        var timer = setTimeout(callback, timeout);
        BDY.timeout.push(timer);
        return timer;
    }

    function _setInterval(callback, timeout) {
        var timer = setInterval(callback, timeout);
        BDY.interval.push(timer);
        return timer;
    }
    
    // 清除页面定时器
    function _clearTimer() {
        var len;
        var timeout = BDY.timeout;
        var interval = BDY.interval;
        len = timeout.length;
        for (var i = 0; i < len; i++) {
            clearTimeout(timeout[i]);
        }

        len = interval.length;
        for (var i = 0; i < len; i++) {
            clearInterval(interval[i]);
        }

        // 重置timer
        BDY.timeout = [];
        BDY.interval = [];
    }
    
    
    // ++++++++++++++++++++++++++++++++ 业务逻辑 +++++++++++++++++++++++++++++++++++++
    var pages = {}, oldPage;;

    function init() {
        // 这里巨坑
        if (!document.body) {
            setTimeout(init, 10);
            console.warn("webview is slow, setTimeout(init, 10)");
            return;
        }
        
        _initEvent();
        _initUrl();
    }
       
    function _initEvent() {
        // 点击事件
        $('body').off(BDY.click + ".data-href").on(BDY.click + ".data-href", '[data-href]', function() {
            var $this = $(this);
            
            var href = $this.attr('data-href') || $this.attr('href');
            // 不合理的href，不特殊处理
            if (href && href.indexOf("#!") !== -1) {
                var clientParam = $this.attr('data-client');
                if (clientParam != null) {
                    redirectClient(href, clientParam);
                } else {
                    redirect(href);
                }
                return false;
            }
        });
        
        // 点击上报
        $('body').off(BDY.click + ".data-report").on(BDY.click + ".data-report", '[data-report]', function() {
            var $this = $(this);
            var page = getParam('page');
            var event = 'click_' + $this.attr('id') || $this.attr('name');
            reportPv(page, event);
        });
        
        $('body').off(BDY.click + ".handler").on(BDY.click + ".handler", '.handler', function() {
            //隐藏加载更多按钮
            $(this).hide();
            //显示loading标签
            $(this).prev().show();
        });
        
        // 支持浏览器后退
        $(window).off('hashchange.body').on('hashchange.body', function() {
            var newPage = getParam('page') || 'user-center';
            if (oldPage !== newPage) {
                _redirectPage(newPage);
            }
        });
        
        _initActiveEvent();
    }
    
    function _initActiveEvent() {
        var selector = "input[type='submit'], [data-active]";
        var $elem, $activeElem;
        var timeHandler = 0;
        var LONG_TAP_TIME = 500;
        
        $("body").off(BDY.touchstart + ".body").on(BDY.touchstart + ".body", function(e) {
            $elem = $(e.target);
            $activeElem = $elem;

            if (!$activeElem.filter(selector).length) {
            	$activeElem = $activeElem.parents(selector);                
            } 
            
        	var activeStyle = $activeElem.attr("data-active") || "active";
            $activeElem.addClass(activeStyle);
  
            timeHandler = _setTimeout(function() {
               $elem && $elem.trigger('longTap');
               _removeActive();
            }, LONG_TAP_TIME);
        });
        
        $("body").off("touchmove.body").on("touchmove.body", selector, function(event) {
            clearTimeout(timeHandler);
            timeHandler = 0;
            
            _removeActive();
            $elem = null;
            /*
            if (!$elem) {
                return;
            }
            
            var lastX = event.touches[0].clientX;
            var lastY = event.touches[0].clientY;
           
            var top = $elem.offset().top;
            var left = $elem.offset().left;
            if (lastX < left || lastX > (left + $elem.width()) || lastY < top || lastY > (top + $elem.height())) {
            	console.log("touchmove _removeActive");
                //console.log("lastX:" + lastX + ", lastY:" + lastY + ", top:" + top + ", left:" + left);
                //console.log("width:" + $elem.width() + ", height:" + $elem.height());
                _removeActive();
                $elem = null;
            }
            */
        });
        
        $("body").off("mouseout.body").on("mouseout.body", selector, function(event) {
        	_removeActive();
            $elem = null;
        });
        
        var lastTime = 0;
        $(document.body).off(BDY.touchend + ".body").on(BDY.touchend + ".body", function(e) {
           clearTimeout(timeHandler);
           timeHandler = 0;
            
           if ($elem) {
               var currentTime = (new Date()).getTime();
               var duration = currentTime - lastTime;
               var MIN_TRIGGER_INTERVAL = 300;
               
               if (duration > MIN_TRIGGER_INTERVAL) {
                   $elem.trigger('touchclick');
                   // 记录当前时间
                   lastTime = currentTime;
               }
           }
           
           _removeActive();
        });
        
        
        $(document).off('scroll.body').on("scroll.body", _removeActive);
        
        function _removeActive() {
            if ($activeElem != null) {
                var activeStyle = $activeElem.attr("data-active") || "active";          
                $activeElem.blur();
                $activeElem.removeClass(activeStyle);
                $activeElem = null;
            }
            $elem = null;
        }
        
        ['longTap', 'touchclick'].forEach(function(m) {
           $.fn[m] = function(callback) {
               return this.on(m, callback);
           };
        });
    }
    
    function _initUrl() {
        var env = getLocalData("env") || "production";
        switch (env) {
            case "test":
            case "sandbox":
                exports.url = "http://test.mgame.mbox.duowan.com/";
                break;
            case "new":
                exports.url = "http://new.mgame.mbox.duowan.com/";
                break;
            default:
                exports.url = SITE_URL;
                break;
        }
        
        // exports.url = "http://test.m.pai.duowan.com/";
    }
    
    function historyBack() {
        var curPage = getParam("page");
        pages[curPage] = pages[curPage] || {};
        pages[curPage]['scrollTop'] = 0;
        if (window.Duowan && Duowan.historyBack) {
            Duowan.historyBack();
        } else {
            history.back();
        }
    }
    
    function redirect(param) {
        oldPage = getParam("page");
        pages[oldPage] = pages[oldPage] || {};
        pages[oldPage]['scrollTop'] = _getScrollTop();
        var values = parseHash(param);
        setParam(values, true);
    }
    
    function _getScrollTop() {
        return document.documentElement.scrollTop || document.body && document.body.scrollTop;        
    }
    
    function redirectClient(param, clientParam) {
        oldPage = getParam("page");
        
        var values = parseHash(param);
        if (window.DuowanUi && DuowanUi.openActivity) {
            var hash = document.location.hash;
            hash = setParam2(values, hash);
            if (clientParam) {
                hash += clientParam;
            }
            
            hash = hash.substr(2);
            DuowanUi.openActivity(hash);
        } else {
            pages[oldPage] = pages[oldPage] || {};
            pages[oldPage]['scrollTop'] = _getScrollTop();
            setParam(values, true); 
        }
    }

    function openUrl(url, title, needTopBar) {
        if (window.DuowanUi && DuowanUi.openUrl) {
            DuowanUi.openUrl(url, title, needTopBar);
        } else {
            window.open(url);
        }
    }
    

    function _redirectPage(_page) {
        pages[_page] = pages[_page] || {};
        if (!pages[_page]['dom']) {
            _getNewPage(_page);
        } else {
            _changePage(_page);
        }
    }
    
    function _getNewPage(_page, option) {
        var url = _page.replace('-', '/') + '.html';
        var path = document.location.href;

        var pos = path.indexOf("/main/");
        if (pos == -1) {
            pos = path.indexOf("/cacheMain/");
        }
        url = path.substr(0, pos) + "/" + url;
        
        get(url, function(responseText) {
            //如果有页面信息时才缓存
            pages[_page]['dom'] = responseText;
            _changePage(_page, option);
        });
    }

    function _changePage(_page) {
        var $pageswarp = $("#container");
        var $page = $pageswarp.children();
        if (oldPage && $page.length) {
            pages[oldPage] = pages[oldPage] || {};
            //保存滚动条位置$pageswarp
            // pages[oldPage]['scrollTop'] = document.body.scrollTop;
            //console.log("oldPage:" + oldPage + ", scrollTop:" + pages[oldPage]['scrollTop']);
            // pages[oldPage]['height'] = $pageswarp.height();
            pages[oldPage]['script'] = $page.filter("script");
            
            var $fragment = $(document.createDocumentFragment());
            pages[oldPage]['dom'] = $fragment.append($page.filter("div"));
            
            _clearTimer();
        }

        _renderPage(_page);
        reportPv(_page, 'open');
        
        $(document).trigger(BDY.pageChange, _page);
        oldPage = _page;
    }
    
    function _renderPage(_page) {
        var $pageswarp = $("#container");
        //回调放在页面js的init方法之前执行，不然会覆盖init里面的setParam
        try {
            var dom = pages[_page]['dom'];
            if ( typeof dom == 'string') {
                scroll(0, 0);
                $pageswarp.html(dom);
            } else {
                //还原高度、滚动条信息
                // $pageswarp.height(pages[_page]['height']);
                var scrollTop = pages[_page]['scrollTop'];
                scroll(0, scrollTop);
    
                //还原dom元素
                $pageswarp.html(dom);
                var script = pages[_page]['script'];
                script && $pageswarp.append(script);
            }
        } catch(e) {
            if (seajs.data.debug) {
                throw e;
            } else {
                console.error(e); 
            }
        }
    }
    
    function onNavBtnClick(callback) {
        $(document).on(BDY.navBtnClick + "_" + getParam("page"), callback);
    }
    
    function showLoading(text, timeout, cancelable) {
        timeout = timeout || 15000;
        if (cancelable == null) {
            cancelable = true;
        } else {
            cancelable = !!cancelable;
        }
        if (window.DuowanUi && DuowanUi.showLoading) {
            DuowanUi.showLoading(text, timeout, cancelable);
        }
    }
    
    function hideLoading() {
        if (window.DuowanUi && DuowanUi.hideLoading) {
            DuowanUi.hideLoading();
        }
    }
    
    function showErrorTip(msg, timeout) {
    	if (!msg) { return; }
        timeout = timeout || 3000;
        if (window.DuowanUi && DuowanUi.showErrorTip) {
            DuowanUi.showErrorTip(msg, timeout);
        } else {
            alert(msg);
        }
    }
    
    function showTip(msg, timeout) {
    	if (!msg) { return; }
        timeout = timeout || 3000;
        if (window.DuowanUi && DuowanUi.showTip) {
            DuowanUi.showTip(msg, timeout);
        } else {
            alert(msg);
        }
    }
    
    function showSelectDialog(option, callback) {
        if (window.DuowanUi && DuowanUi.showSelectDialog) {
            DuowanUi.showSelectDialog(option, callback);
        }
    }
    
    function _confirm(msg, confirmCallback, cancelCallback, title, buttonLabels) {
    	title = title || "提示";
    	buttonLabels = buttonLabels || "确定,取消";
        if (window.DuowanUi && DuowanUi.confirm) {
            DuowanUi.confirm(msg, confirmCallback, cancelCallback, title, buttonLabels);
        } else {
            if (confirm(msg)) {
                confirmCallback && confirmCallback(1);
            } else {
                cancelCallback && cancelCallback(2);
            }
        }
    }
    
    function _prompt(title, hint, confirmCallback, cancelCallback, buttonLabels) {
        title = title || "提示";
        buttonLabels = buttonLabels || "确定,取消";
        if (window.DuowanUi && DuowanUi.prompt) {
            DuowanUi.prompt(title, hint, confirmCallback, cancelCallback, buttonLabels);
        } else {
            var msg = prompt(title);
            if (msg) {
                confirmCallback(msg);
            } else {
                cancelCallback("cancel");
            }
        }
    }
    
    function openLogin(callback) {
        if (window.YB && YB.openLogin) {
            YB.openLogin(callback);
        } else {
            if (!$.cookie('oauthCookie') || !$.cookie('username')) {
                var js = "http://www.duowan.com/public/assets/sys/js/udb.v1.0.js";
                require(js, function() {
                    Navbar.login(document.location.href);
                });
            }
        }
    }
    
    function reportPv(page, event, content) {
	    if (window.Duowan && Duowan.reportPv) {
            Duowan.reportPv(page, event, content);
        } else {
            // do nothing
        }
    }
    
    
    
    /**
     * 简单的模板
     *
     * @param array
     *            data 有键值的数组
     * @param string/dom
     *            模板选择器
     */
    function template(data, selector) {
        selector = selector || document;
        var $doc = $(selector);
        for (var key in data) {
            var $item = $doc.find("[name='" + key + "']");
            if (!$item.length) {
                $item = $doc.find("#" + key);
                if (!$item.length) {
                    continue;
                }
            }
            
            $item.each(function() {
                var $this = $(this);
                switch ($this[0].nodeName) {
                    case 'INPUT':
                        var strType = $this.attr('type');
                        if (strType == 'checkbox') {
                            $this.prop("checked", data[key]);
                        } else if (strType == 'radio') {
                            break;
                        } else {
                            $this.val(data[key]);
                        }
                        break;
                    case 'TEXTAREA':
                    case 'SELECT':
                        $this.val(data[key]);
                        break;
                    case 'IMG':
                        // 设置默认图标
                        if ($this.attr('defaultIcon') == 'true') {
                            $this.attr('src', 'gc.defaultIcon');
                        }
                        if ($this.attr('data-original') != null) {
                            $this.attr('data-original', data[key]);
                        } else {
                            $this.attr('src', data[key]);
                        }
                        break;
                    case 'IFRAME':
                        $this.attr('src', data[key]);
                        break;
                    default:
                        $this.html(data[key]);
                }
            });
        }
    }
    
    init();
    
});

