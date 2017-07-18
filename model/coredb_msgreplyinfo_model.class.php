<?php
require_once "../classes/lib/mydbo.php";
/**
 * 留言回覆
 *
 */
class CoreDB_MsgReplyInfo_Model extends MyDBModel
{
    
    protected $schema = array(
        'ReplyID'       => 'int',
        'ReplyContent'  => 'string',
        'ReplyTime'     => 'datetime',
        'RepMsgID'      => 'int',
        'RepMemID'      => 'int',
    );

    //存入回覆留言
 	public function replyMessage($_iMessageID, $_sReplyContent, $_iMessageID) {
 		//取得現在時間
 		$replyTime = date("Y-m-d H:i:s",time()+28800);
 			session_start();
 			$iRepMemID = input::session('i', 'MemberID');
 		return $this->insert(array(
            'ReplyContent' => $_sReplyContent, 
            'ReplyTime' => $replyTime, 
            'RepMsgID' => $_iMessageID,
            'RepMemID' => $iRepMemID
            ));
 	}

 	//取得回覆留言
 	public function getMsgReply($_imsgID) {

 		$sSql = "SELECT A1.ReplyContent, A1.ReplyTime, A2.MemberName 
                FROM msgreplyinfo A1, memberinfo A2 
                WHERE A1.RepMemID = A2.MemberID AND A1.RepMsgID = %i
                ORDER BY ReplyTime desc";

    	$aReplyList = $this->select_all($sSql, array($_imsgID));
 		return $aReplyList;
 	}


}