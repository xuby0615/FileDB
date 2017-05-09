<?php
/**
 * 文件数据库操作类
 */
class File
{
    /**
     *---------------------------------------------------------------
     * 数据库名称                                                    |
     *---------------------------------------------------------------
     * 要求：
     *      1、Linux下权限为600，保证服务器对该目录拥有者可读写不可执行，所属group其他用户不可读写执行，所有人不可读写执行；
     *      2、保证数据库内文件存储数据格式为标准json，否则会出现解析错误；
     *      3、配置.htaccess文件，确保文件不可经由外部url访问。
     */
    
    private $__db;

    /**
     * 配置数据表前缀
     */
    private static $__db_prefix = 'db_';   

    /**
     * 构造方法
     * 功能：类初始化，检测数据文件地址是否存在；
     * @param string $path 数据文件地址
     */
    public function __construct($path = '')
    {
       $this->initialization($path);
    }

    /**
     * object对象转为数组
     * @param object $object 待转化的对象
     * return array  $array  对象转化后的数组
     */
    public function obj2array($object = 0)
    {
        if(is_object($object))
        {
            $array = array();
            foreach($object as $attr => $value)
            {
                $array[$attr] = $this->obj2array($value);
            }
            return $array;
        }
        else
        {
            return $object;
            exit;
        }
    }

    /**
     * 写入
     * @param array     $content     写入信息
     * @param string    $table       表名
     * return boolean   true/false   操作结果
     */
    public function insert($content = array() , $table = '')
    {
        $result = $this->obj2array($this->getAll($table));
        array_push($result,$content);
        $content = json_encode($result);

        $_path = str_replace('\\','/',$table);
        $_path .= '.json';
        $db_url = $this->__db.'/'.self::$__db_prefix.$_path;
        
        $result = file_put_contents($db_url, $content);
        if($result)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * 删除
     * @param array     $cond         删除条件
     * @param string    $table        表名
     * return boolean   true/false    操作结果
     */
    public function delete($cond = array() , $table = '')
    {
        $result = $this->obj2array($this->getAll($table));
        $cond_array = array();
        foreach($result as $key => $value)
        {
            if(array_intersect($value , $cond))
            {
                array_push($cond_array , $key);
            }
        }
        if(count($cond_array) > 0)
        {
            foreach ($cond_array as $value) 
            {
                unset($result[$value]);
            }
            $result = $this->replace($result , $table);
            if($result)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    /**
     * 修改
     * @param array     $cond         修改条件
     * @param array     $content      修改内容
     * @param string    $table        表名
     * return boolean   true/false    操作结果
     */
     public function update($cond = array() , $content = array() , $table = '')
     {
        $result = $this->obj2array($this->getAll($table));
        $cond_array = array();
        foreach($result as $key => $value)
        {
            if(array_intersect($value , $cond))
            {
                array_push($cond_array , $key);
            }
        }
        if(count($cond_array) > 0)
        {
            foreach ($cond_array as $value) 
            {
                foreach($content as $key => $v)
                {
                    $result[$value][$key] = $v;
                }
            }
            $result = $this->replace($result , $table);
            if($result)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
     }
    
    /**
     * 查询
     * @param string $table     数据表名
     * return object $result    查询到的结果
     */
    public function getAll($table = '')
    {
        $file = self::chkFile($table);
        return ($file) ? self::getFileContents($file) : false;
    }
    
    
    // ----------------- 以下方法为类中私有方法 ----------------- //
    /**
     * 初始化
     * @param string $path 数据库主目录地址
     */
    private function initialization($path)
    {
       try
       {
            $path = str_replace('\\','/',$path);
            if (is_dir($path))
            {
                $this->__db = $path;    
            }
            else
            {
                throw new Exception(self::error('db_error'));
            }
        }
        catch (exception $e)
        {
            exit($e->getMessage());
        }
    }

    /**
     * 检查数据表文件是否存在；
     * @param string   $file       数据表文件
     * return boolean  true/false  返回数据表文件路径
     */
    private function chkFile($path = '')
    {
        try
        {
            $_path = str_replace('\\','/',$path);
            $_path .= '.json';
            $file = $this->__db.'/'.self::$__db_prefix.$_path;
            if(file_exists($file))
            {
                return $file;
            }
            else
            {
                $message = 'table "'.$path.'" does not exists!!!';
                throw new Exception($message);
            }
        }
        catch (exception $e)
        {
            exit($e->getMessage());
        }
    }

    /**
     * 替换信息
     * @param array  $content   写入信息
     * @param string $table     表名
     * return boolean  true/false  返回数据表文件路径
     */
    private function replace($content = array() , $table = '')
    {
        $content = json_encode($content);

        $_path = str_replace('\\','/',$table);
        $_path .= '.json';
        $db_url = $this->__db.'/'.self::$__db_prefix.$_path;
        
        $result = file_put_contents($db_url, $content);
        if($result)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * 读取文件内存储的数据信息
     * @param string  $file    数据表
     * return object  $result  返回读取结果
     */
    private static function getFileContents($file = '')
    {
        try
        {
            $content = file_get_contents($file);    //  获取数据表信息
            $result  = json_decode($content);       //  解析json
            if(!$result)
            {
                throw new Exception(self::error('db_table'));
            }
            return (object)$result;
        }
        catch (exception $e)
        {
            exit($e->getMessage());
        }
    }

    /**
     * 错误提示
     * @param  string  $lv   错误代码
     * return  string  $msg  错误信息
     */
    private static function error($lv = 'null_tip')
    {
        $error_msg = array(
            'null_tip' => '错误信息不存在',
            'db_error' => '数据库文件目录不存在',
            'db_table' => '数据表数据格式错误'
        );
        if(!array_key_exists($lv , $error_msg))
        {
            return $error_msg['null_tip'];
        }
        return $error_msg[$lv];
    }

}
?>