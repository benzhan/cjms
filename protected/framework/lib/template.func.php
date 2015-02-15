<?php
/**
 * 模板扩充函数
 *
 * Copyright(c) 2005 by 陈毅鑫(深空). All rights reserved
 *
 * To contact the author write to {@link mailto:shenkong@php.net}
 *
 * @author 陈毅鑫(深空)
 * @version $Id: template.func.php 1687 2008-07-07 01:16:07Z skchen $
 * @package common
 */

/**
 * 包含模板
 *
 * 当你需要在主模板文件里(有些模板引擎称之为layout布局模板,其实不是所有模板都是布局)
 * 再包含其他公共模板的时候,使用该函数进行包含,则所有已注册的变量均可在被包含文件里使
 * 用,貌似支持多层嵌套,没有测试过,参数可以使用数组,也可以使用多个参数,如:
 * <?=includeFile('user.header', 'user.main', 'user.footer')?> 或者
 * <?=includeFile(array('user.header', 'user.main', 'user.footer'))?>
 *
 * @param string|array $filename 模板名(module.templateName形式)
 */
function includeFile($templates) {
    $template = Template::init();
    if (is_array($templates)) {
        $template->includeFiles = $templates;
    } else {
        $template->includeFiles = func_get_args();
    }
    extract($template->vars);
    foreach ($template->includeFiles as $template->includeFile) {
        require $template->getPath($template->includeFile);
    }
}

//end of script
