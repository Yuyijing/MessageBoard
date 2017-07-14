<?php
require_once "../classes/model/coreDB_memberinfo_model.class.php";
require_once "../classes/model/coreDB_messageinfo_model.class.php";
// require_once "../classes/model/coreDB_msgreplyinfo_model.class.php";

class Home_Ctl extends Controller
{
/*    public function get_index() {
        return Smarty_View::make('home/showmessage.html');
    }
*/
    //登入
    public function get_login() {
        $title ='會員登入';  
        return Smarty_View::make('home/index.html', array('title' => $title));
    }

    public function post_login() {
        $sUsername = Input::post('s', 'username');
        $sPassword = Input::post('s', 'password'); 
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
            throw new HttpRedirect('/editmemdata');
        } else {
            $oModel = new CoreDB_MemberInfo_Model;
            $oModel->editmemdata($iID, $sName, $sPassword);
            throw new HttpRedirect('/message');
        }
    }

    //留言
    public function get_leavemessage() {
        $this->checkLogin();
        $sStr = array(
                    'subject' => "Subject", 
                    'content' => "Additional Content...", 
                );
        return Smarty_View::make('home/leavemessage.html', array('str' => $sStr, 'value' =>"留言"));
    }

    public function post_leavemessage() {
        session_start();
        // $sSubject = Input::post('s', 'subject');
        // $sContent = Input::post('s', 'content');
        $sSubject = htmlspecialchars(Input::post('s', 'subject'));
        $sContent = htmlspecialchars(Input::post('s', 'content'));
        $sName = Input::session('s', 'MemberName');
        $oModel = new CoreDB_MessageInfo_Model;
        $oModel->leaveMessage($sSubject, $sContent, $sName);
    }

    //Paging顯示留言
    public function get_message() {
        $this->checkLogin();
        session_start();
        $iPage = Input::get('i', 'pagenum');
        $iAuthority = Input::session('i', 'Authority');
        $sMemberName = Input::session('s', 'MemberName');
      
        $oModel = new CoreDB_MessageInfo_Model;
        /*分頁*/
        //留言筆數
        $aMsgCount = $oModel->countMessage();
        $iMsgCount = (int)$aMsgCount['COUNT(*)'];
        $iPerPage = 10;
        $iPageCount = ceil($iMsgCount / $iPerPage);
        $showpage = 5;
        //左右頁面數
        $cut = floor($showpage / 2);
        //目前所在頁面
        $iPage = (isset($iPage))? intval($iPage) : 1;
        //若總頁數大於每次要顯示幾筆分頁則執行以下片段
        if ($iPageCount > $showpage) {
            if ($iPage <= $cut) {
                $left = 1;
                $right = $showpage;
            } else {
                if ($iPage <= $iPageCount - $cut) {
                    $left = $iPage - $cut;
                    $right = $iPage + $cut;
                } else {
                    $left = $iPageCount - $showpage + 1;
                    $right = $iPageCount;
                }
            }
        } else {
            $left = 1;
            $right = $iPageCount;
        }
        //每一頁開始的序號
        $iOffset = ($iPage - 1) * $iPerPage;
        /*分頁結束*/
        //取得留言資料
        $aMessageList = $oModel->showMessage($iOffset, $iPerPage);
        return Smarty_View::make(
            'home/showmessage.html', 
            array('messagelist' => $aMessageList, 
                  'authority' => $iAuthority, 
                  'membername' => $sMemberName,
                  'pages' => $iPageCount,
                  'left' => $left,
                  'right' => $right));
        
    }

    //搜尋
    public function post_message() {
        session_start();
        $iAuthority = Input::session('i', 'Authority');
        $sMemberName = Input::session('s', 'MemberName');
        $sKeyword = Input::post('s', 'keyword');

        if (isset($sKeyword)) {
            $sSelect = Input::post('s', 'searchKind');
            switch ($sSelect) {
              case 'Name':
                $condition = "Name";
                break;

              case 'Subject':
                $condition = "Subject";
                break;
              
              default:
                $condition = "Content";
                break;
            }
        }

        $oModel = new CoreDB_MessageInfo_Model;
        $aMessageList = $oModel->searchMessage($condition, $sKeyword);
        return Smarty_View::make(
            'home/showmessage.html', 
            array('messagelist' => $aMessageList, 'authority' => $iAuthority, 'membername' => $sMemberName));
    }

    //編輯留言
    public function get_edit() {
        $iMessageID = Input::get('i', 'id');
        $oModel = new CoreDB_MessageInfo_Model;
        $aMessage = $oModel->getMessage($iMessageID);   
        return Smarty_View::make('home/leavemessage.html', array('message' => $aMessage, 'value' =>"修改完成"));
    }

    public function post_edit() {
        $sSubject = htmlspecialchars(Input::post('s', 'subject'));
        $sContent = htmlspecialchars(Input::post('s', 'content'));
        $iMessageID = Input::get('i', 'id');
        $oModel = new CoreDB_MessageInfo_Model;
        $aMessage = $oModel->editMessage($sSubject, $sContent, $iMessageID);
        //回留言畫面
        throw new HttpRedirect('/message');
    }

    //刪除留言
    public function get_delete() {
        $iMessageID = Input::get('i', 'id');
        $oModel = new CoreDB_MessageInfo_Model;
        $oModel->deleteMessage($iMessageID);
        //回留言畫面
        return "OK";
        // throw new HttpRedirect('/message');

    }

    //回覆留言
    public function get_reply() {
        $iMessageID = Input::get('i', 'id');
        $oModel = new CoreDB_MessageInfo_Model;
        $aMessage = $oModel->getMessage($iMessageID);
        return Smarty_View::make('home/replymessage.html', array('message' => $aMessage));
    }

    public function post_reply() {
        $iMessageID = Input::get('i', 'id');
        $sReplyContent = htmlspecialchars(Input::post('s', 'reply'));
        $oModel = new CoreDB_MessageInfo_Model;
        $oModel->replyMessage($iMessageID, $sReplyContent);
        //回留言畫面
        throw new HttpRedirect('/message');
    }

    //登出
    public function get_logout() {
        session_start();
        session_destroy();
        //回登入畫面
        throw new HttpRedirect('/login');
    }	

    //判斷是否已登入
    public function checkLogin() {
        session_start();
        $iMemberID = Input::session('i', 'MemberID');
        if (!isset($iMemberID)) {
            //回登入畫面
            throw new HttpRedirect('/login'); 
        }
    }
}
