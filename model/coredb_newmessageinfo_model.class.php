<?php
require_once "../classes/lib/mydbo.php";
/**
 * 留言資訊
 *
 */
class CoreDB_NewMessageInfo_Model extends MyDBModel
{
    protected $schema = array(
        'MessageID'      => 'int',
        'Subject'        => 'string',
        'Content'        => 'string',
        'LeaveTime'		 => 'datetime',
        'MsgMemID'       => 'int',
    );

    //留言
    public function leaveMessage($_sSubject, $_sContent, $_iMemberID) {
    	//取得現在時間
 		$leaveTime = date("Y-m-d H:i:s",time()+28800);
    	return $this->insert(array(
    		'MsgMemID' => $_iMemberID, 
    		'Content' => $_sContent, 
    		'Subject' => $_sSubject,
    		'LeaveTime' => $leaveTime
    ));}

    //顯示留言
    public function showMessage($_iOffset, $i_PerPage) {

    	$sSql = "SELECT A1.Subject, A1.Content, A1.LeaveTime, A1.MessageID, A2.MemberName 
                FROM newmessageinfo A1, memberinfo A2 
                WHERE A1.MsgMemID = A2.MemberID
                ORDER BY LeaveTime desc LIMIT %i,%i";

    	$aMessageList = $this->select_all($sSql, array($_iOffset, $i_PerPage));
    	return $aMessageList;
    }

    //留言筆數
    public function countMessage() {
    	$sSql = "SELECT COUNT(*) FROM newmessageinfo";
    	return $this->select_one($sSql);
    }

    //取得單筆留言資料
    public function getMessage($_iMessageID) {
    	return $this->get(array('MessageID' => $_iMessageID), array('Name','Subject', 'Content'));
    }

    //留言編輯
    public function editMessage($_sSubject, $_sContent, $_iMessageID) {
    	$this->update(
    		array('Subject' => $_sSubject, 'Content' => $_sContent),
    		array('MessageID' => $_iMessageID)
    	);
 	}

 	//刪除留言
 	public function deleteMessage($_iMessageID) {
 		$this->delete_data(array('MessageID' => $_iMessageID));
 	}

 	//搜尋留言
 	public function searchMessage($_condition, $_skeyword, $_iOffset, $_iPerPage) {
 		$sStr = "%".$_skeyword."%";
 		// $sSql = "SELECT * FROM newmessageinfo WHERE $_condition LIKE %s ORDER BY LeaveTime desc LIMIT %i,%i";
        $sSql = "SELECT A1.Subject, A1.Content, A1.LeaveTime, A1.MessageID, A2.MemberName 
                FROM newmessageinfo A1, memberinfo A2 
                WHERE A1.MsgMemID = A2.MemberID AND $_condition LIKE %s
                ORDER BY LeaveTime desc LIMIT %i,%i";

    	$aMessageList = $this->select_all($sSql, array($sStr, $_iOffset, $_iPerPage));
    	return $aMessageList;
 	}

    //搜尋結果數
    public function countSearchMessage($_condition, $_keyword) {
        $sStr = "%".$_keyword."%";
        $sSql = "SELECT COUNT(*) FROM newmessageinfo WHERE $_condition LIKE %s";
        return $this->select_one($sSql, array($sStr));
    }

}
