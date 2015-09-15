<?php
/**
 * Created by PhpStorm.
 * User: billge
 * Date: 15-8-3
 * Time: 上午11:50
 */
class TXFilter
{
    const valueKey = "__values__";

    /**
     * @var TXSingleDAO|TXDoubleDAO
     */
    protected $DAO;
    protected $conds = [];

    private $extracts = ['=', '>', '>=', '<', '<=', '!=', '<>', 'is', 'not is'];
    protected $calcs = ['max', 'min', 'sum', 'avg', 'count'];

    /**
     * 静态创建
     * @param $DAO
     * @param $filter
     * @param string $type
     * @param null $cond
     * @return TXDoubleFilter|TXSingleFilter
     * @throws TXException
     */
    public static function create($DAO, $filter, $type="__and__", $cond=null)
    {
        if ($DAO instanceof TXSingleDAO){
            return new TXSingleFilter($DAO, $filter, $type, $cond);
        } elseif ($DAO instanceof TXDoubleDAO) {
            return new TXDoubleFilter($DAO, $filter, $type, $cond);
        } else {
            throw new TXException(1013, gettype($DAO));
        }
    }

    /**
     * 构造函数
     * @param TXSingleDAO|TXDoubleDAO $DAO
     * @param TXFilter $filter
     * @param string $type
     * @param null $cond
     * @throws TXException
     */
    public function __construct($DAO, $filter, $type="__and__", $cond=null)
    {
        if (!($DAO instanceof TXSingleDAO || $DAO instanceof TXDoubleDAO)){
            throw new TXException(1013, gettype($DAO));
        }
        if (!$filter){
            throw new TXException(1017);
        } elseif (is_array($filter)){
            if ($cond){
                $this->conds = [[$type => [[self::valueKey => $filter], $cond]]];
            } else {
                $this->conds = [[$type => [[self::valueKey => $filter]]]];
            }
        } elseif (null === $cond) {
           throw new TXException(1016, gettype($filter));
        } elseif (!$filter instanceof TXFilter) {
            throw new TXException(1014, gettype($filter));
        } elseif ($filter->getDAO() !== $DAO) {
            throw new TXException(1015);
        } elseif ($cond) {
            $this->conds = [[$type => [$filter->getConds()[0], $cond]]];
        } else {
            $this->conds = [[$type => [$filter->getConds()[0]]]];
        }
        $this->DAO = $DAO;

    }

    /**
     * 查找不重复的项
     * @param string $fields
     * @return TXSqlData
     */
    public function distinct($fields='*'){
        $where = $this->buildWhere($this->conds);
        return $this->DAO->distinct($fields, $where);
    }


    /**
     * 找单条数据
     * @param string $fields
     * @return TXObject
     */
    public function find($fields = '*')
    {
        $where = $this->buildWhere($this->conds);
        return $this->DAO->find($fields, $where);
    }

    /**
     * 查询数据
     * @param array $limit array(40, 20)
     * @param array $orderBy array("id"=>"desc", "name"=>"asc")
     * @param string $fields id,name,fields
     * @return TXSqlData
     */
    public function query($limit = array(), $orderBy = array(), $fields = '*')
    {
        $where = $this->buildWhere($this->conds);
        return $this->DAO->query($limit, $orderBy, $fields, $where);
    }

    /**
     * group语句
     * @param array $adds ['sum'=>['id'=>'s_id']]
     * @param $fields
     * @param array $groupBy ['id']
     * @param array $having ['>='=>['s_id'=>10]]
     * @param array $limit [10, 10]
     * @param array $orderBy ['id'=>'desc'] ['id'=>['desc', 'gbk']]
     * @return TXSqlData
     */
    public function group($adds=array(), $fields='', $groupBy=array(), $having=array(), $limit = array(), $orderBy = array())
    {
        $where = $this->buildWhere($this->conds);
        return $this->DAO->group($adds, $fields, $groupBy, $having, $limit, $orderBy, $where);
    }

//    /**
//     * 查询数量
//     * @return int
//     */
//    public function total()
//    {
//        $where = $this->buildWhere($this->conds);
//        return $this->DAO->total($where);
//    }

//    /**
//     * 查询条件
//     * @param $method ['max', 'min', 'sum', 'avg', 'count']
//     * @param $field
//     * @return mixed
//     */
//    public function calc($method, $field='0')
//    {
//        if (in_array($method, $this->calcs)){
//            return $this->$method($field);
//        }
//    }

    /**
     * 查询条件
     * @param $method ['max', 'min', 'sum', 'avg', 'count']
     * @param $args
     * @return mixed
     * @throws TXException
     */
    public function __call($method, $args)
    {
        if (in_array($method, $this->calcs)){
            if (!$args){
                $args = [0];
            }
            $args[] = $this->buildWhere($this->conds);
            return call_user_func_array([$this->DAO, $method], $args);
        } else {
            throw new TXException(2020, array($method, __CLASS__));
        }
    }

    /**
     * 连表Where
     * @param $conds
     * @param string $type
     * @return string
     */
    protected function buildWhere($conds, $type='and')
    {
        $wheres = [];
        foreach ($conds as $values){
            foreach ($values as $key => $cond){
                if ($key == "__and__" || $key == "__or__"){
                    $key = str_replace("_", "", $key);
                    $sCond = $this->buildWhere($cond, $key);
                    if ($sCond){
                        $wheres[] = $sCond;
                    }
                } elseif ($key == self::valueKey){
                    if ($this->DAO instanceof TXDoubleDAO){
                        $sCond = $this->buildDoubleWhere($cond, $type);
                        if ($sCond){
                            $wheres[] = $sCond;
                        }
                    } else {
                        $sCond = $this->buildSingleWhere($cond, $type);
                        if ($sCond){
                            $wheres[] = $sCond;
                        }
                    }
                }
            }
        }
        if (!$wheres){
            return '';
        } elseif (count($wheres) == 1){
            return $wheres[0];
        }
        return "(" . join(") {$type} (", $wheres) . ")";
    }

    /**
     * 拼装where
     * @param $cond
     * @param $type
     * @return string
     */
    private function buildSingleWhere($cond, $type)
    {
        if (empty($cond)) {
            return '';
        } else {
            $where = array();
            foreach($cond as $key => $value) {;
                if (in_array(strtolower($key), $this->extracts)){
                    foreach ($value as $arrk => $arrv){
                        if (is_null($arrv)){
                            $where[] = "`{$arrk}`{$key} NULL";
                        }elseif (is_string($arrv)){
                            $arrv = $this->real_escape_string($arrv);
                            $where[] = "`{$arrk}`{$key}'{$arrv}'";
                        } else {
                            $where[] = "`{$arrk}`{$key}{$arrv}";
                        }
                    }
                } elseif ($key === '__like__'){
                    foreach ($cond[$key] as $arrk => $arrv){
                        $arrv = $this->real_like_string($arrv);
                        $where[] = "`{$arrk}` like '%{$arrv}%'";
                    }
                } elseif (is_null($value)){
                    $where[] = "`{$key}`is NULL";
                } elseif (is_string($value)) {
                    $value = $this->real_escape_string($value);
                    $where[] = "`{$key}`='{$value}'";
                } elseif (is_array($value)){
                    if (!$value){
                        $where[] = 'FALSE';
                        continue;
                    }
                    foreach ($value as &$val){
                        if (is_string($val)){
                            $val = "'{$this->real_escape_string($val)}'";
                        }
                    }
                    unset($val);
                    $value = "(". join(',', $value).")";
                    $where[] = "`{$key}` in {$value}";
                } else {
                    $where[] = "`{$key}`={$value}";
                }
            }

            return join(" {$type} ", $where);
        }
    }

    /**
     * 拼装复合数据表 where
     * @param $conds
     * @param $type
     * @return string
     */
    private function buildDoubleWhere($conds, $type)
    {
        if (empty($conds)) {
            return '';
        }
        $doubles = $this->DAO->getDoubles();
        $where = array();
        foreach ($conds as $k => $cond){
            if (is_string($k) && in_array($k, $doubles)){
                $table = $k;
            } else if (isset($doubles[$k])){
                $table = $doubles[$k];
            } else {
                continue;
            }
            foreach($cond as $key => $value) {
                if (in_array(strtolower($key), $this->extracts)){
                    foreach ($value as $arrk => $arrv){
                        if (is_null($arrv)){
                            $where[] = "{$table}.`{$arrk}`{$key} NULL";
                        }
                        else if (is_string($arrv)){
                            $arrv = $this->real_escape_string($arrv);
                            $where[] = "{$table}.`{$arrk}`{$key}'{$arrv}'";
                        } else {
                            $where[] = "{$table}.`{$arrk}`{$key}{$arrv}";
                        }
                    }
                } elseif ($key === '__like__'){
                    foreach ($cond[$key] as $arrk => $arrv){
                        $arrv = $this->real_like_string($arrv);
                        $where[] = "{$table}.`{$arrk}` like '%{$arrv}%'";
                    }
                } elseif (is_null($value)){
                    $where[] = "{$table}.`{$key}`is NULL";
                } elseif (is_string($value)) {
                    $value = $this->real_escape_string($value);
                    $where[] = "{$table}.`{$key}`='{$value}'";
                } elseif (is_array($value)){
                    if (!$value){
                        $where[] = 'FALSE';
                        continue;
                    }
                    foreach ($value as &$val){
                        if (is_string($val)){
                            $val = "'{$this->real_escape_string($val)}'";
                        }
                    }
                    unset($val);
                    $value = "(". join(",", $value).")";
                    $where[] = "{$table}.`{$key}` in {$value}";
                } else {
                    $where[] = "{$table}.`{$key}`={$value}";
                }
            }
        }
        return join(" {$type} ", $where);
    }

    /**
     * real_escape_string
     * @param $string
     * @return mixed
     */
    private function real_escape_string($string){
        return addslashes($string);
    }

    /**
     * real_like_string
     * @param $str
     * @return mixed
     */
    private function real_like_string($str){
        return str_replace(["_", "%"], ["\\_", "\\%"], addslashes($str));
    }

    public function getDAO()
    {
        return $this->DAO;
    }

    public function getConds()
    {
        return $this->conds;
    }

    /**
     *
     * @return string
     */
    public function __toLogger()
    {
        return ['DAO' => $this->DAO->getTable(), 'conds' => $this->conds];
    }
}