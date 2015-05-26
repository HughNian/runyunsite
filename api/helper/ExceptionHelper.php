<?php
defined('APP_PATH') or die('No direct access allowed.');

/**
 * 异常处理类
 *
 * @author niansong
 *
 */
class ExceptionHelper
{
    /**
     * @todo 错误输出
     * @param mixed $error 错误
     * @return void
     * 
     */
    static public function halt($error) {
        $e = array();
        if (APP_DEBUG || IS_CLI) {
            //调试模式下输出错误信息
            if (!is_array($error)) {
                $trace          = debug_backtrace();
                $e['message']   = $error;
                $e['file']      = $trace[0]['file'];
                $e['line']      = $trace[0]['line'];
                ob_start();
                debug_print_backtrace();
                $e['trace']     = ob_get_clean();
            } else {
                $e              = $error;
            }
            if(IS_CLI){
                exit(iconv('UTF-8','gbk',$e['message']).PHP_EOL.'FILE: '.$e['file'].'('.$e['line'].')'.PHP_EOL.$e['trace']);
            }
        } else {
            die("系统内部错误,可开启调试模式进行调试！");
        }
        // 包含异常页面模板
        $exceptionFile =  ROOT_PATH .'Helper/Exception.tpl';
        include $exceptionFile;
        exit;
    }
    
    /**
     * @todo 自定义异常处理
     * @access public
     * @param mixed $e 异常对象
     */
    static public function appException($e) {
        $error = array();
        $error['message']   =   $e->getMessage();
        $trace              =   $e->getTrace();
        if('E'==$trace[0]['function']) {
            $error['file']  =   $trace[0]['file'];
            $error['line']  =   $trace[0]['line'];
        }else{
            $error['file']  =   $e->getFile();
            $error['line']  =   $e->getLine();
        }
        $error['trace']     =   $e->getTraceAsString();
        
        // 发送404信息
        header('HTTP/1.1 404 Not Found');
        header('Status:404 Not Found');
        self::halt($error);
    }
    
    /**
     * @todo 自定义错误处理
     * @access public
     * @param int $errno 错误类型
     * @param string $errstr 错误信息
     * @param string $errfile 错误文件
     * @param int $errline 错误行数
     * @return void
     */
    static public function appError($errno, $errstr, $errfile, $errline) {
        switch ($errno) {
            case E_ERROR:
            case E_PARSE:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
            default:
                ob_end_clean();
                $errorStr = "$errstr ".$errfile." 第 $errline 行.";
                self::halt($errorStr);
                break;
        }
    }
    
    /**
     * @todo 致命错误捕获
     * 
     */
    static public function fatalError() {
        if ($e = error_get_last()) {
            switch($e['type']){
                case E_ERROR:
                case E_PARSE:
                case E_CORE_ERROR:
                case E_COMPILE_ERROR:
                case E_USER_ERROR:
                    ob_end_clean();
                    self::halt($e);
                    break;
            }
        }
    }
    
    /**
     * @todo 自定义异常处理
     * @param string $msg 异常消息
     * @param string $tag 处理异常的方式,默认0用halt方法处理异常,1用php自带exception方法处理异常
     * @param integer $code 异常代码 默认为0
     * @return void
     * 
     */
    static function throw_exception($msg, $tag = 0, $code = 0) {
        if($tag = 1)
            throw new Exception($msg, $code);
        else
            self::halt($msg);
    }
}