<?php
require_once "../classes/lib/mydbo.php";
/**
 * 會員帳號
 *
 */
class CoreDB_MemberInfo_Model extends MyDBModel
{
    
    protected $schema = array(
        'MemberID'       => 'int',
        'Authority'      => 'int',
        'MemberName'     => 'string',
        'Username'       => 'string',
        'Password'       => 'string',
    );

    //會員登入
    public function loginData($_sUsername) {
        return $this->get(array('Username' => $_sUsername));
    }

    //會員註冊
    public function checkRegisterData($_sAuthority) {
    	//取得已註冊帳號
    	$sSql = "select * from memberinfo where Authority = %i";
    	return $sUsername = $this->select_col('Username', $sSql, array($_sAuthority));
    }

    public function updateRegisterData($_iAuthority, $_sName, $_sUsername, $_sPassword) {
        //存入資料庫
        return $this->insert(array(
                'Authority' => $_iAuthority,
                'MemberName' => $_sName,
                'Username' => $_sUsername,
                'Password' => $_sPassword,
            ));
    }

    //會員資料管理
    public function membermgmt() {
        return $this->find(
                        array('Authority' => 2), 
                        array('field' => array('MemberID', 'MemberName', 'Username', 'Password'))
                      );
    }

    //刪除會員
    public function deletemember($_iID) {
        return $this->delete_data(array('MemberID' => $_iID));
    }

    //編輯會員資料
    public function fetchmemdata($_iID) {
        return $this->get(array('MemberID' => $_iID), array('MemberName', 'Password'));
    }

    public function editmemdata($_iID, $_sName, $_sPassword) {
        return $this->update(array('MemberName' => $_sName, 'Password' => $_sPassword), array('MemberID' => $_iID));
    }
}
