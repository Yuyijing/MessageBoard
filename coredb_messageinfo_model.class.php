<?php

/**
 * 留言資訊
 *
 */
class CoreDB_MessageInfo_Model extends MyDBModel
{
    protected $schema = array(
        'MessageID'      => 'int',
        'Name'     		 => 'string',
        'Subject'        => 'string',
        'Content'        => 'string',
        'LeaveTime'		 => 'datetime',
        'ReplyContent'   => 'string',
        'ReplyTime'		 => 'datetime',
    );

    //留言
    public function leaveMessage($_sSubject, $_sContent, $_sName) {
    	//取得現在時間
 		$leaveTime = date("Y-m-d H:i:s",time()+28800);
    	return $this->insert(array(
    		'Name' => $_sName, 
    		'Content' => $_sContent, 
    		'Subject' => $_sSubject,
    		'LeaveTime' => $leaveTime
    ));}

    //顯示留言
    public function showMessage($_iOffset, $i_PerPage) {

    	$sSql = "SELECT * FROM messageinfo ORDER BY LeaveTime desc LIMIT %i,%i";
    	// var_export($iMsgCount);
    	// exit();
    	$aMessageList = $this->select_all($sSql, array($_iOffset, $i_PerPage));
    	return $aMessageList;
    }

    //留言筆數
    public function countMessage() {
    	$sSql = "SELECT COUNT(*) FROM messageinfo";
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

 	//回覆留言
 	public function replyMessage($_iMessageID, $_sReplyContent) {
 		//取得現在時間
 		$replyTime = date("Y-m-d H:i:s",time()+28800);
 		$this->update(
    		array('ReplyContent' => $_sReplyContent, 'ReplyTime' => $replyTime),
    		array('MessageID' => $_iMessageID)
    	);
 	}

 	//搜尋留言
 	public function searchMessage($_condition, $_keyword) {
 		$sStr = "%".$_keyword."%";
 		$sSql = "SELECT * FROM messageinfo WHERE $_condition LIKE %s";
    	$aMessageList = $this->select_all($sSql, array($sStr));
    	return $aMessageList;
 	}

}
