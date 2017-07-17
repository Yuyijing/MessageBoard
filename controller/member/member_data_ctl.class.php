<?php
require_once "../classes/model/coreDB_memberinfo_model.class.php";

class Member_Data_Ctl extends Controller {
    
    //會員資料管理
    public function get_membermgmt() {
        $this->checkLogin();
        $iAuthority = Input::session('i', 'Authority');
        $oModel = new CoreDB_MemberInfo_Model;
        $aMemberList = $oModel->membermgmt();
        return Smarty_View::make('home/membermgmt.html', array('memberlist' => $aMemberList, 'authority' => $iAuthority));
    }

    //會員資料管理-刪除會員
    public function get_deletemember() {
        $iID = Input::get('i', 'id');
        $oModel = new CoreDB_MemberInfo_Model;
        $aMemberList = $oModel->deletemember($iID);
        return "OK";
    }

    //編輯會員資料
    public function get_editmemdata() {
        $this->checkLogin();
        session_start();
        $iAuthority = Input::session('i', 'Authority');
        $iID = Input::session('i', 'MemberID');
        $oModel = new CoreDB_MemberInfo_Model;
        $aMemberData = $oModel->fetchmemdata($iID);
        return Smarty_View::make('home/editmemdata.html', array('memberdata' => $aMemberData, 'authority' => $iAuthority));
    }

    public function post_editmemdata() {
        session_start();
        $iID = Input::session('i', 'MemberID');
        $sName = Input::post('s', 'name');
        $sPassword = Input::post('s', 'password');
        $sCkPassword = Input::post('s', 'ckpassword');
        if ($sPassword != $sCkPassword) {
            throw new HttpRedirect('/member/data/editmemdata');
        } else {
            $oModel = new CoreDB_MemberInfo_Model;
            $oModel->editmemdata($iID, $sName, $sPassword);
            throw new HttpRedirect('/message/board/showmessage');
        }
    }

    //判斷是否已登入
    public function checkLogin() {
        session_start();
        $iMemberID = Input::session('i', 'MemberID');
        if (!isset($iMemberID)) {
            //回登入畫面
            throw new HttpRedirect('/login/userlogin'); 
        }
    }
}