window.onload = function() {
    var formlogin = document.getElementById('Login_in');
    var oInput = formlogin.getElementsByTagName('input');
    var aName = oInput[0];
    var apsw = oInput[1];
    var ologin = oInput[2];
    var aP = formlogin.getElementsByTagName('p');
    var aName_msg = aP[0];
    var apsw_msg = aP[1];
    var allgood = true;
    aName.ok = false;
    apsw.ok = false;

    //用户名
    aName.onfocus = function() {
        aName_msg.style.display = "inline";
        aName_msg.innerHTML = '<i class="material-icons">warning</i>请输入用户名';
    }
    aName.onblur = function() {
        if (this.value == "") {
            aName_msg.innerHTML = '<i class="material-icons">clear</i>不能为空';
            aName.ok = false;
        } else {
            aName_msg.innerHTML = '<i class="material-icons">done</i>初检通过了';
            aName.ok = true;
        }

    }

    //密码验证
    apsw.onfocus = function() {
        apsw_msg.style.display = "inline";
        apsw_msg.innerHTML = '<i class="material-icons">warning</i>请输入密码';
    }
    apsw.onblur = function() {
        apsw_msg.innerHTML = "";
        if (this.value == "") {
            apsw_msg.innerHTML = '<i class="material-icons">clear</i>不能为空';
        } else {
            apsw.ok = true;
        }
    }

	$("#login").click(function(){
		$.ajax({
			type: 'POST',
			data: $("#Login_in").serialize(),
			success: function (d) {
				if (d.ret == 200) {
					Materialize.toast(d.msg, 2000, 'rounded', function () {
						// location.href='./';
						history.back();
					});
				} else {
					Materialize.toast(d.msg, 2000, 'rounded');
				}
			}
		});
	});

    // ologin.onclick = function() {
    //     for (var i = 0; i < 3; i++) {
    //         if (!aInput[i].ok) {
    //             aInput[i].onfocus();
    //             aInput[i].value = "";
    //             return allgood = false;
    //         }
    //     }
    //     return allgood;
    // }

};
