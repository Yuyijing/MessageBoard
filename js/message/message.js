//刪除留言
function msg_delete(msg_ID) {
	var del_fg = confirm("確定要刪除嗎?");
	if(del_fg) {
		$.ajax({
			url: "/message/board/deletemessage",
			data: {id:msg_ID},
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

//關閉子視窗，母視窗重載
function unLoad(){
    opener.window.location.reload();
    window.close();
    alert("OK!");
}

