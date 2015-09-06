<?php
/**
 * 性能分析器
 * @author kelezyb
 */
class XHProf {
	private static $flag = XHPROF_FLAGS_NO_BUILTINS;
	private static $xhprof_data = null;
	
	/**
	 * 开始性能监视
	 */
	public static function start() {
		xhprof_enable(XHProf::$flag, array('ignored_functions' => array('memory_get_usage', 'xhprof_disable' , 'microtime', 'XHProf::end')));
	}
	
	/**
	 * 结束性能监视
	 */
	public static function end() {
		$datas = xhprof_disable();
		return $datas;
	}

    /**
     * array转换为Tree
     * @param $arr
     * @param $fid
     * @param string $fparent
     * @param string $fchildrens
     * @param bool $console
     * @return array
     */
    private static function array_to_tree($arr, $fid, $fparent = 'parent_id',
	$fchildrens = 'childrens', $console=false) {
		$pkvRefs = array();
		foreach ($arr as $offset => $row) {
			$pkvRefs[$row[$fid]] =& $arr[$offset];
		}
		
		$tree = array();
		foreach ($arr as $offset => &$row) {
			$parentId = $row[$fparent];
			if ($parentId) {
				if (!isset($pkvRefs[$parentId])) {
					continue;
				}
				$parent =& $pkvRefs[$parentId];
				$parent[$fchildrens][$row['key']] =& $arr[$offset];
			} else {
				$tree[$row['key']] =& $arr[$offset];
			}
            if ($console){
                unset($row['ct']);
                unset($row['wt']);
                unset($row['cpu']);
                unset($row['mu']);
                unset($row['key']);
                unset($row['pmu']);
                unset($row['fid']);
                unset($row['id']);
            }
		}
        unset($row);
		return $tree;
	}
	
	/**
	 * 每行数据转换为HTML表格(遍历树结构)
	 * 
	 * @param array $datas
	 * @param int $wt 总消耗时间
	 * @param int $count 当前调用层级
	 * @return string
	 */
	private static function for_datas($datas, $wt, $count = 0) {
		$buf = array();
		
		foreach($datas as $key => $data) {
			$label = str_repeat('&nbsp;&nbsp;&nbsp;|', $count) . $data['key'];
			$cbase = hexdec('666666');
			$cdel = hexdec('101010') * intval($count/2) + hexdec('010101') * intval(($count+1)/2);
			$bcolor = dechex($cbase + $cdel);
			$buf[] = '<tr style="background:#' . $bcolor . '"><td>' . $label . '</td><td>' . $data['ct'] .
			'</td><td>' . ($data['wt'] / 1000) . 'ms</td></td><td>' . 
			sprintf('%0.2f%%', $data['wt'] / $wt * 100) . '</td><td>' . $data['cpu'] . 
			'</td><td>' . sprintf('%0.1f K', ($data['mu'] / 1024)) . '</td></tr>';
			
			if(isset($data['childs'])) {
				$fcount = $count + 1;
				$buf[] = XHProf::for_datas($data['childs'], $wt, $fcount);
			}
		}
		
		return join('', $buf);
	}

    public static function consoles($xh_datas = null) {
        $buf = array();

        if(null !== $xh_datas) {
            $wt = 0;
            $main = $call = 'main()';
            $wt = $xh_datas['main()']['wt'];
            foreach($xh_datas as $key => &$data) {
                $calls = explode('==>', $key);
                if(2 == count($calls)) {
                    $main = $calls[0];
                    $call = $calls[1];
                } else {
                    $main = '';
                    $call = $calls[0];
                }
                $data['耗时比'] = sprintf('%0.2f%%', $data['wt'] / $wt * 100);
                $data['耗时'] = ($data['wt'] / 1000)."ms";
                $data['次数'] = $data['ct'];
                $data['内存'] = sprintf('%0.1f K', ($data['mu'] / 1024));
                $data['fid'] = $main;
                $data['id'] = $call;
                $data['key'] = $key;
            }
            unset($data);
            $datas = XHProf::array_to_tree($xh_datas, 'id', 'fid', 'childs', true);
            TXLogger::info($datas);
        }
    }
	
	/**
	 * 显示性能数据表格
	 * 
	 * @param array $xh_datas 性能数据
	 * @param bool $echo 是否输出
	 * @return 性能数据表格
	 */
	public static function display($xh_datas = null, $echo = true) {
		$buf = array();
		
		if(null !== $xh_datas) {
			$wt = 0;
			$main = $call = 'main()';
			
			$buf[] = '<table cellpadding="1" cellspacing="1" bgcolor="#000000"  border="0" width="100%">';
			$buf[] = '<tr style="background:#bbbbbb"><th>调用关系</th><th>调用次数</th><th>耗时</th><th>耗时比</th><th>CPU</th><th>内存</th></tr>';
			$wt = $xh_datas['main()']['wt'];
			foreach($xh_datas as $key => &$data) {
				$calls = explode('==>', $key);
				if(2 == count($calls)) {
					$main = $calls[0];
					$call = $calls[1];
				} else {
					$main = '';
					$call = $calls[0];
				}
				$data['fid'] = $main;
				$data['id'] = $call;
				$data['key'] = $key;
			}
			unset($data);
			$datas = XHProf::array_to_tree($xh_datas, 'id', 'fid', 'childs');
//            TXLogger::info($datas);exit;
			$buf[] = XHProf::for_datas($datas, $wt); 
			
			$buf[] = '</tr>';
		}
		
		$ret = join('', $buf);
		if(true === $echo) {
			echo $ret;
		}
		
		return $ret;
	}
}