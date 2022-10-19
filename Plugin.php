<?php
/**
 * PushDeer！
 * 
 * @package PushDeer
 * @author 吾爱bug
 * @version 1.0.0
 * @link https://www.5ibug.net
 */
class PushDeer_Plugin implements Typecho_Plugin_Interface
{
    protected static $comment;
    protected static $active;

    public static function activate()
    {
        Typecho_Plugin::factory('Widget_Feedback')->comment = [__CLASS__, 'pushServiceReady'];
        Typecho_Plugin::factory('Widget_Feedback')->finishComment = [__CLASS__, 'pushServiceGo'];
        return _t('pushdeer插件启用成功');
    }
    
    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     * 
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function deactivate(){
    }

    public static function config(Typecho_Widget_Helper_Form $form)
    {
         $serviceTitle = new Typecho_Widget_Helper_Layout('div', array('class=' => 'typecho-page-title'));
         $serviceTitle->html('<h2>推送服务配置</h2>');
         $form->addItem($serviceTitle);
         $ApiUrl = new Typecho_Widget_Helper_Form_Element_Text('ApiUrl', NULL, "https://api2.pushdeer.com/message/push", _t('接口地址'), _t("必须填写,接口地址"));
         $form->addInput($ApiUrl);
         $pushKey = new Typecho_Widget_Helper_Form_Element_Text('pushKey', NULL, NULL, _t('推送key'), _t("必须填写"));
         $form->addInput($pushKey);
    }
    
        
    /**
     * 个人用户的配置面板
     * 
     * @access public
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form){}
    
    public static function pushServiceReady($comment, $active)
    {
        self::$comment = $comment;
        self::$active = $active;

        return $comment;
    }
    
    public static function pushServiceGo($comment)
    {
        $options = Helper::options();
        $plugin = $options->plugin('PushDeer');
        
        self::pushdeer_send(self::$comment["text"],self::$active,"text",$plugin->pushKey,$plugin->ApiUrl);
    }
    
    
    public static function pushdeer_send($text, $desp = '', $type='text', $key = '',$api = "https://api2.pushdeer.com/message/push")
    {
        $postdata = http_build_query(array('text' => $text, 'desp' => $desp, 'type' => $type , 'pushkey' => $key));
        $opts = array('http' =>
        array(
            'method'  => 'POST',
            'header'  => 'Content-type: application/x-www-form-urlencoded',
            'content' => $postdata));
        
        $context  = stream_context_create($opts);
        return $result = file_get_contents($api, false, $context);
    }
    

}