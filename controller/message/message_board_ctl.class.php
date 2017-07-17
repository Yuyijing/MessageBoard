<?php
require_once "../classes/model/coreDB_messageinfo_model.class.php";

class Message_Board_Ctl extends Controller {
    
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


    //顯示留言
    public function get_showmessage() {
        $this->checkLogin();
        session_start();
        $iPage = Input::get('i', 'pagenum');
        $iAuthority = Input::session('i', 'Authority');
        $sMemberName = Input::session('s', 'MemberName');
      
        $oModel = new CoreDB_MessageInfo_Model;
        //留言筆數
        $aMsgCount = $oModel->countMessage();
        $iMsgCount = (int)$aMsgCount['COUNT(*)'];
        $iPerPage = 10;
        $iPageCount = ceil($iMsgCount / $iPerPage);
        //分頁
        $aPageData = $this->checkPage($iPage, $iPageCount, $iPerPage);
        $aMessageList = $oModel->showMessage($aPageData['offset'], $iPerPage);
        return Smarty_View::make(
            'home/showmessage.html', 
            array('messagelist' => $aMessageList, 
                  'authority' => $iAuthority, 
                  'membername' => $sMemberName,
                  'pages' => $iPageCount,
                  'left' => $aPageData['left'],
                  'right' => $aPageData['right']));
        
    }

    //搜尋
    public function post_showmessage() {
        session_start();
        $iPage = Input::get('i', 'spagenum');
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
        //留言筆數
        $aMsgCount = $oModel->countSearchMessage($condition, $sKeyword);
        $iMsgCount = (int)$aMsgCount['COUNT(*)'];
        $iPerPage = 10;
        $iPageCount = ceil($iMsgCount / $iPerPage);
        //分頁
        $aPageData = $this->checkPage($iPage, $iPageCount, $iPerPage);
        $aMessageList = $oModel->searchMessage($condition, $sKeyword, (int)$aPageData['offset'], $iPerPage);
        // var_dump($aMessageList);
        // exit();
        return Smarty_View::make(
            'home/showmessage.html', 
            array('messagelist' => $aMessageList, 
                  'authority' => $iAuthority, 
                  'membername' => $sMemberName,
                  'pages' => $iPageCount,
                  'left' => $aPageData['left'],
                  'right' => $aPageData['right']));
    }

    //編輯留言
    public function get_editmessage() {
        $iMessageID = Input::get('i', 'id');
        $oModel = new CoreDB_MessageInfo_Model;
        $aMessage = $oModel->getMessage($iMessageID);   
        return Smarty_View::make('home/leavemessage.html', array('message' => $aMessage, 'value' =>"修改完成"));
    }

    public function post_editmessage() {
        $sSubject = htmlspecialchars(Input::post('s', 'subject'));
        $sContent = htmlspecialchars(Input::post('s', 'content'));
        $iMessageID = Input::get('i', 'id');
        $oModel = new CoreDB_MessageInfo_Model;
        $aMessage = $oModel->editMessage($sSubject, $sContent, $iMessageID);
    }

    //刪除留言
    public function get_deletemessage() {
        $iMessageID = Input::get('i', 'id');
        $oModel = new CoreDB_MessageInfo_Model;
        $oModel->deleteMessage($iMessageID);
        return "OK";
    }

    //回覆留言
    public function get_replymessage() {
        $iMessageID = Input::get('i', 'id');
        $oModel = new CoreDB_MessageInfo_Model;
        $aMessage = $oModel->getMessage($iMessageID);
        return Smarty_View::make('home/replymessage.html', array('message' => $aMessage));
    }

    public function post_replymessage() {
        $iMessageID = Input::get('i', 'id');
        $sReplyContent = htmlspecialchars(Input::post('s', 'reply'));
        $oModel = new CoreDB_MessageInfo_Model;
        $oModel->replyMessage($iMessageID, $sReplyContent);
    }

    //判斷是否已登入
    public function checkLogin() {
        session_start();
        $iMemberID = Input::session('i', 'MemberID');
        if (!isset($iMemberID)) {
            //回登入畫面
            throw new HttpRedirect('/login/userlogin/login'); 
        }
    }

    //判斷分頁
    public function checkPage($_iPage, $_iPageCount, $_iPerPage) {

        $showpage = 5;
        //左右頁面數
        $cut = floor($showpage / 2);
        //目前所在頁面
        $_iPage = (isset($_iPage))? intval($_iPage) : 1;
        //若總頁數大於每次要顯示幾筆分頁則執行以下片段
        if ($_iPageCount > $showpage) {
            if ($_iPage <= $cut) {
                $left = 1;
                $right = $showpage;
            } else {
                if ($_iPage <= $_iPageCount - $cut) {
                    $left = $_iPage - $cut;
                    $right = $_iPage + $cut;
                } else {
                    $left = $_iPageCount - $showpage + 1;
                    $right = $_iPageCount;
                }
            }
        } else {
            $left = 1;
            $right = $_iPageCount;
        }
        //每一頁開始的序號
        $iOffset = ($_iPage - 1) * $_iPerPage;
        /*分頁結束*/
        $aPageData = array('left' => $left, 'right' => $right , 'offset' => $iOffset);
        return $aPageData;
    }
}