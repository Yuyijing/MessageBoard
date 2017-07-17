<?php
require_once "../classes/model/coreDB_memberinfo_model.class.php";

class Login_Userlogin_Ctl extends Controller {
    
    //登入
    public function get_index() {
        $title ='會員登入';  
        return Smarty_View::make('home/index.html', array('title' => $title));
    }

    public function post_login() {
        
        $sUsername = Input::post('s', 'username');
        $sPassword = Input::post('s', 'password');

        // return $sUsername;
        // exit;
        $oModel = new CoreDB_MemberInfo_Model;
        $aUserData = $oModel->loginData($sUsername);

        if ($aUserData["Username"] == "") {
            return "noID";
        } else if($aUserData["Password"] != $sPassword){
            return "error";
        } else {
            session_start();
            $_SESSION["MemberID"] = $aUserData["MemberID"];
            $_SESSION["Authority"] = $aUserData["Authority"];
            $_SESSION["Username"] = $aUserData["Username"];
            $_SESSION["Password"] = $aUserData["Password"];
            $_SESSION["MemberName"] = $aUserData["MemberName"];
            return "OK";
        }
    }

    //會員及管理者註冊
    public function get_register() {
        $sIdentity = Input::get('s', 'identity');
        if ($sIdentity == "user") {
            $title = '會員註冊';
            return Smarty_View::make('home/register.html', array('title' => $title, 'identity' => 2));
        } else {
            $title = '管理者註冊';
            return Smarty_View::make('home/register.html', array('title' => $title, 'identity' => 1));
        }
        
    }

    public function post_register() {  
        $iAuthority = Input::post('i', 'authority');     
        $sUsername = Input::post('s', 'username');
        $sPassword = Input::post('s', 'password');
        $sName = Input::post('s', 'membername');
        // return $sUsername;
        $oModel = new CoreDB_MemberInfo_Model;
        $aUsernameList = $oModel->checkRegisterData($iAuthority);
        foreach ($aUsernameList as $key => $value) {
            if ($sUsername == $value) {
                return "error";
            }
        }
        //新增註冊
        $oModel->updateRegisterData($iAuthority, $sName, $sUsername, $sPassword);
        return "OK";
    }

    //登出
    public function get_logout() {
        session_start();
        session_destroy();
        //回登入畫面
        throw new HttpRedirect('/login/userlogin');
    }
}