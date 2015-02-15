<?php
class Data {

    private function decode($str) {
        $str = trim($str);
        for ($i = 0; $i < strlen($str); $i++) {
            if ($str[$i] === '{') {
                break;
            }
        }
        $str = substr($str, $i);
        
        $result = json_decode($str, true);
        return $result;
    }
    
    private function checkDate() {
        $resultHistory = new ResultHistory();
        
        $nextResultDate = $resultHistory->getNextResultDate();
        $nextResultDate2 = strtotime($nextResultDate);
        // 提前1个小时才需要搜索结果
        if ($nextResultDate2 - time() > 600) {
            Response::error(CODE_NORMAL_ERROR, '时间还没到，还不需要搜索');
        }
    }
    
    private function getResultDate($curResultNum) {
        $where = array (
            'result_num' => $curResultNum - 1
        );
        
        $resultHistory = new ResultHistory();
        $lastResult = $resultHistory->helper->getRow($where);
        if ($lastResult) {
            $result_date = $lastResult['next_result_date'];
        } else {
            $result_date = date('Y-m-d');
        }
        
        return $result_date;
    }
    
    /**
     * 获取开奖结果
     * @author benzhan
     * @return array('result_num', 'result_value', 'next_result_num', 'next_result_date')
     */
    private function getBmResult() {
        $url = 'http://111kj.com/bm/bmjg.js?_=' . time() . rand(100, 999);
        $headers = array(
            'X-Requested-With' => 'XMLHttpRequest',
            'User-Agent' => 'Mozilla/5.0 (Linux; U; Android 4.0.2; en-us; Galaxy Nexus Build/ICL53F) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30',
            'Referer' => 'http://111kj.com/bm/d1.html'
        );
        
        $str = Tool::get($url, $headers);
        $result = $this->decode($str);
        
        // {"k":"098,14,24,38,32,12,04,13,099,08,26,二,","t":"1000","QQ":"7136995"}
        if ($result && $result['k']) {
            $k = $result['k'];
            $arr = explode(',', $k);
            if (count($arr) >= 12) {
                for ($i = 0; $i < 11; $i++) {
                    if (!$arr[$i]) {
                        return false;
                    }
                }
                
                $result_num = $arr[0];
                $result_value = "$arr[1],$arr[2],$arr[3],$arr[4],$arr[5],$arr[6],$arr[7]";
                $matches = array();
                $flag = preg_match('/(\d{1,2}\,){6}\d{1,2}/', $result_value, $matches);
                if ($flag) {
                    // $next_result_num = $arr[8];
                    $next_result_date = date('Y') . "-{$arr[9]}-{$arr[10]}";
                    return compact('result_num', 'result_value', 'next_result_date', 'tema', 'mazi');
                } else {
                    return false;                    
                }
            }
        }
        
        return false;
    }

    /**
     * 从http://111kj.com/bm/bmjg.js读取数据
     * @author benzhan
     * @return boolean
     */
    public function retriveResult1() {
        set_time_limit(60);
        
        $oldMin = date('i');
        $this->checkDate();
        
        $objResultHistory = new ResultHistory();
        $newResultNum = $objResultHistory->getNextResultNum();
        
        do {
            $bmResult = $this->getBmResult();
            $bmResult && $result_num = $bmResult['result_num'];
            $min = date('i');
            // 休息1秒
            sleep(1);
        } while(!$bmResult && $min == $oldMin);
        
        if ($bmResult) {
            $result_year = $objResultHistory->getNewestYear();
            $where = compact('result_num', 'result_year');
            $flag = $objResultHistory->helper->exists($where);
            if ($flag) {
                Response::error(CODE_UNKNOW_ERROT, '已经有记录了');
            }
            
            $bmResult['result_date'] = $this->getResultDate($result_num);
            $bmResult['result_year'] = substr($bmResult['result_date'], 0, 4);
            // 插入数据
            $flag = $objResultHistory->helper->addObjectIfNoExist($bmResult, $where);
            $history_id = $objResultHistory->helper->getInsertId();
            
            // 发送通知
            $title = "六合彩{$result_num}期开奖";
            $parts = explode(',', $bmResult['result_value']);
            $tema = array_pop($parts);
            $mazi = join(',', $parts);
            $text = "特码:{$tema}，  码子:{$mazi}";
            $umeng = new Umeng();
            do {
                $umeng_result = $umeng->send($title, $text);
                $min = date('i');
            } while(!$umeng_result && $min == $oldMin);
            
            // 通知插入数据库
            $objResult = json_decode($umeng_result, true);
            $umeng_task_id = $objResult['data']['task_id'];
            $objUmengMsg = new UmengMsg();
            $args = compact('history_id', 'title', 'text', 'umeng_task_id', 'umeng_result');
            $objUmengMsg->helper->addObject($args);
            
            return $flag;
        } else {
            return false;
        }
    }
    
    
    public function retriveReport1() {
        $objResultHistory = new ResultHistory();
        $result_year = $objResultHistory->getNewestYear();
        $result_num = $objResultHistory->getNextResultNum();
        
        $objReport = new Report("$result_year-01-01");
        $maxResultNum = $objReport->getMaxResultNum();
        
        if ($maxResultNum == $result_num) {
            Response::error(CODE_NORMAL_ERROR, "has the newest report.");
        }
        
        $url = 'http://hkm6xforum.dnsdynamic.com/hkm6/newspaper.html';
        $str = Tool::get($url);
        
        require_once ROOT_PATH . 'extensions/simple_html_dom.php';
        $htmlDom = new simple_html_dom();
        $htmlDom->load($str);
        
        
        $letters = $htmlDom->find('.letter');
        $groups = $htmlDom->find('.newpaper_group');
        $len = count($letters);
        for ($i = 0; $i < $len; $i++) {
            $letter = $letters[$i];
            $pinyin_head = $letter->text();
            
            $group = $groups[$i];
            $nodes = $group->find('.link');
            
            $values = array();
            foreach ($nodes as $node) {
                $name = $node->text();
                $url = $node->attr['href'];
                $values[] = compact('name', 'url', 'pinyin_head', 'result_num', 'result_year');
            }
            
            $objReport->helper->addObjects2($values);
        }
    
        return false;
    }
    
    private $startTime = null;
    public function retriveReportUrl() {
        $this->startTime || $this->startTime = time();
        if (time() - $this->startTime > 300) {
            Response::error(CODE_NORMAL_ERROR, 'time out.');
        }
        
        $objResult = new ResultHistory();
        $nextResultDate = $objResult->getNextResultDate();
        
        $objReport = new Report($nextResultDate);
        $nextResultNum = $objResult->getNextResultNum();
        $datas = $objReport->getEmptyData($nextResultNum);
        if (!$datas) {
            Response::error(CODE_NORMAL_ERROR, 'no data.');
        }
        
        $index = rand(0, count($datas) - 1);
        $data = $datas[$index];
        $report_id = $data['report_id'];
        $where = compact('report_id');
        if ($data['url']) {
            $img = Tool::get($data['url'], null, 60);
            if ($img) {
                $url_size = strlen($img);
                $objReport->helper->updateObject(compact('url_size'), $where);
            }
        } else {
            $objReport->helper->delObject($where);
        }
        
        $this->retriveReportUrl();
    }
    
    public function retriveResultHistory() {
        set_time_limit(60);
        
        $url = "http://bm.116kj.com:81/kj/2014.html";
        $headers = array(
            'X-Requested-With' => 'XMLHttpRequest',
            'User-Agent' => 'Mozilla/5.0 (Linux; U; Android 4.0.2; en-us; Galaxy Nexus Build/ICL53F) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30',
            'Referer' => 'http://111kj.com/bm/d1.html'
        );
        $html = Tool::get($url, $headers);
        if (!$html) {
            $html = file_get_contents($url);
        }

        if (!$html) {
            Response::error(CODE_REQUET_TIME_OUT);
        }
        
        require_once ROOT_PATH . 'extensions/simple_html_dom.php';
        $htmlDom = new simple_html_dom();
        $htmlDom->load($html);
        
        $main = $htmlDom->find('#main');
        $trs = $main[0]->find('.infolist');
        
        $dateMap = array();
        $values = array();
        foreach ($trs as $tr) {
            $tds = $tr->find('td');
            $result_date = $tds[0]->text();
            $result_year = substr($result_date, 0, 4);
            $text = $tds[1]->text();
            $result_num = (int) $text;
            $dateMap[$result_num] = $result_date ? $result_date : '1970-01-01';
            
            $pArr = $tds[2]->find('.sortDown .hm');
            $pArr = array_merge($pArr, $tds[3]->find('.hm'));
            $result_values = array();
            foreach ($pArr as $p) {
                $result_values[] = $p->text();
            }
            $result_value = join(',', $result_values);
            
            $next_result_date = $dateMap[$result_num + 1];
            $next_result_date || $next_result_date = '1970-01-02';
            $values[] = compact('result_date', 'result_year', 'result_num', 'result_value', 'next_result_date');
        }
        
        $objResultHistory = new ResultHistory($result_date);
        $objResultHistory->helper->maxItem = 10;
        return $objResultHistory->helper->replaceObjects2($values);
    }
    

}

