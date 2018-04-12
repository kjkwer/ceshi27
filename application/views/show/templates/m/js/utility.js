
function getData(asyncFlag, type, url, formData, callback) {
	$.ajax({
		async: asyncFlag,
		type: type,
		url: url,
		data: formData,
		contentType: "application/x-www-form-urlencoded; charset=utf-8",
		success: function(data) {
			callback(data);
		}
	});
}


function rizi() {
	return '&xt_yhm=' + localStorage.yonghuming + '&token=' + localStorage.login_token;
}

function extend(obj1, obj2) {
    for(var i = 0; i < obj2.length; i++) {
        obj2.xuanz = false;
        obj1.push(obj2[i]);
    }
}


