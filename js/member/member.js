//會員註冊
function register_member(identity) {
	if($("#user_id").val() == "") {
		alert("帳號不可空!");
		$("#user_id").focus();
		return false;
	} 

	if($("#user_pw").val() == "") {
		alert("密碼不可空!");
		$("#user_pw").focus();
		return false;
	}else {
		if ($("#user_pw").val() != $("#user_pw_ck").val()) {
			alert("密碼不相符，請重新輸入!");
			return false;
		}
	}

	if($("#user_nm").val() == "") {
		alert("姓名不可空!");
		$("#user_nm").focus();
		return false;
	}
	
	$.ajax({
			url: "/login/userlogin/register",
			data:{
				authority: identity,
				username: $('#user_id').val(),
				password: $("#user_pw").val(),
				membername: $("#user_nm").val(),
			},
			type: "post",
			dataType: 'text',
			success: function (msg) {
				// console.log(msg);
				if (msg == "OK") {
					location.href = "/login/userlogin";
					alert("新增完成!");
				} else if(msg == "error") {
					location.reload();
					alert("帳號已被註冊!");
				} 
			}
		});
}	

//會員登入
function login_member() {
	if($("#user_id").val() == "") {
		alert("未輸入帳號!");
		$("#user_id").focus();
		return false;
	} 

	if($("#user_pw").val() == "") {
		alert("未輸入密碼!");
		$("#user_pw").focus();
		return false;
	}
	
	$.ajax({
			url: "userlogin/login",
			data:{
				username: $('#user_id').val(),
				password: $("#user_pw").val()
			},
			type: "post",
			dataType: 'text',
			success: function (msg) {
				// alert(msg);
				if (msg == "OK") {
					location.href = "/message/board/showmessage";
					alert("登入成功!");

				} else if(msg == "error"){
					alert("密碼錯誤");
					// location.href = "/login";
				} else{
					alert("無此帳號");
					// location.href = "/login";
				}

			}
		});
}

//刪除會員
function mem_delete(mem_ID) {
	var del_fg = confirm("確定要刪除嗎?");
	if(del_fg) {
		$.ajax({
			url: "/member/data/deletemember",
			data: {id:mem_ID},
			type: "get",
			dataType: 'text',
			success: function (msg) {
				if (msg == "OK") {
					location.reload();
					// alert("已刪除");
				}
			}
		});
	}
}