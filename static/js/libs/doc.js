define(function(require, exports, module) {
    var tpl = require("tpl");
    require("treeTable");
    var $treeTable;

    var M = {
        getParam : function(funcName, pid) {
            var url = BDY.url + funcName + "?doc=func";
            $.get(url, function(objResult) {
                if (!objResult.result) {
                    alert(objResult.msg);
                } else {
                    objResult.data.pid = pid;
                    var html = tpl.render("table_tr", objResult.data);
                    $treeTable.addChilds(html);
                }
            }, 'json');
        }
    };

    var C = {
        init : function() {
            var options = { 
                column : 0,
                expandLevel : 2
            };
            
            $treeTable = $('#class_list').treeTable(options);
            $treeTable.on(BDY.click, "[controller=true]", function(event) {
                var target = event.target;
                var $tr = $(target).parents("tr");
                var pid = $tr.attr("id");

                // 判断是否加载过
                if (!$("tr[pId='" + pid + "']").length) {
                    var funcName = $(target).text().trim();
                    M.getParam(funcName, pid);
                }
            });
        }
    };

    C.init();
});