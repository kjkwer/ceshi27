$(function () {
    {
        let c = document.documentElement.clientWidth;
        if (c > 750) {
            c = 750;
        }
        document.documentElement.style.fontSize = c / 7.5 + "px";
    }
    $(window).resize(function () {
        let c = document.documentElement.clientWidth;
        if (c > 750) {
            c = 750;
        }
        document.documentElement.style.fontSize = c / 7.5 + "px";
        let cc = $(".Bumen_li_show").width();
        $(".Bumen_li_show li").css("width", cc / 2 + "px");
        $(".Bumen_li_show li img").css("width", cc / 2 + "px");
    });

    $(".H_body li").click(function () {
        let p = $(this).hasClass("active");
        if (p) {
            $(this).removeClass("active");
        } else {
            $(this).addClass("active");
        }
    });

    $(".bofang").click(function () {
        $(this).hide();
        $(".video_t video")[0].play();
    });

    //监听 视频 播放完成
    let videoa = $(".video_t video")[0];
    if (videoa) {
        videoa.addEventListener("ended", function () {
            $(".bofang").show();
        });
    }

    var time = 60;
    var Timerun = null;
    var pdn = true;
    $(".Yanz").click(function () {
        var tel=$('.Input_t1').val();
        if (!tel){
            alert('电话不能为空');return false;
        }
          $.get('index.php?p=show&c=login&a=usercheck',{tel:tel},function (msg) {
              if (msg=='9'){
                  $('.Tanc_').show(); return false;
              }else {
                  if (pdn) {

                      $.post('index.php?p=show&c=sms&a=sms',{tel:tel});
                      pdn = false;
                      Timerun = setInterval(function () {
                          let str = time + "秒后重试";
                          $(".Yanz").text(str);
                          if (time <= 0) {
                              clearInterval(Timerun);
                              pdn = true;
                              time = 60;
                              return false;
                          }
                          time--;
                      }, 1000);
                  }
              }
          })


    });

    $(".login_t1 span").click(function () {
        $(this).toggleClass("active");
    });

    $(".Ta_close").click(function () {
        $(".zhezhao").hide();
        $(".Tanc_").hide();
        $(".Tanc_t1").hide();
        $(".Tanc_t2").hide();
    });

    var Timek = $(".Timek");
    var zong = 0;
    var zongtime = null;
    if (Timek) {
        let tmm = $(".Daojishi .tmm").text();
        tmm = new Date(tmm);
        tmm = tmm.getTime() / 1000;

        let now = new Date();
        now = parseInt(now.getTime() / 1000);
        zong = tmm - now;
        if (zong<=0){

            // $('.Timek').remove();
        }
        if (zong > 0) {
            zongtime = setInterval(timebout, 1000);
        }

        function timebout() {
            zong--;
            let tian = Math.floor(zong / (24 * 60 * 60));
            let shi = Math.floor((zong % (60 * 60 * 24)) / (60 * 60));
            let fen = Math.floor((zong % (24 * 60)) / 60);
            let miao = Math.floor(zong % 60);
            if (tian >= 10) {
                $(".Timek .t1").text(Math.floor(tian / 10));
                $(".Timek .t2").text(tian % 10);
            } else {
                $(".Timek .t1").text(0);
                $(".Timek .t2").text(tian);
            }
            if (shi >= 10) {
                $(".Timek .t3").text(Math.floor(shi / 10));
                $(".Timek .t4").text(shi % 10);
            } else {
                $(".Timek .t3").text(0);
                $(".Timek .t4").text(shi);
            }
            if (fen >= 10) {
                $(".Timek .t5").text(Math.floor(fen / 10));
                $(".Timek .t6").text(fen % 10);
            } else {
                $(".Timek .t5").text(0);
                $(".Timek .t6").text(fen);
            }
            if (miao >= 10) {
                $(".Timek .t7").text(Math.floor(miao / 10));
                $(".Timek .t8").text(miao % 10);
            } else {
                $(".Timek .t7").text(0);
                $(".Timek .t8").text(miao);
            }
            if (zong <= 0) {
                window.location.href = '/?c=single&m=act_wqsp';
                clearInterval(zongtime);
            }
        }
    }

    // $(".Diqu_  li").click(function () {
    //     $(".Diqu_  li").removeClass("active");
    //     $(this).addClass("active");
    // });
    $(".Diqxx h1 span").click(function () {
        $(".Diqxx").hide();
    });

    // $(".Lis_ys").click(function () {
    //     $(".Diqxx").show();
    // });

    $(".H_shaix .t1").click(function () {
        let c = $(".Shaixia").css("display");
        if (c == "none") {
            $(".zhezhao").hide();
        }
        $(".Shaixiadiqu").hide();
        $(".zhezhao")
            .stop()
            .fadeToggle();
        $(".Shaixia")
            .stop()
            .slideToggle();
    });

    $(".H_shaix .t2").click(function () {
        let c = $(".Shaixiadiqu").css("display");
        if (c == "none") {
            $(".zhezhao").hide();
        }
        $(".Shaixia").hide();
        $(".zhezhao")
            .stop()
            .fadeToggle();
        $(".Shaixiadiqu")
            .stop()
            .slideToggle();
    });

    $(".Shaixia li").click(function () {
        $(".Shaixia li").removeClass("active");
        $(this).addClass("active");
        let c = $(this).text();
        $(".H_shaix .t1 label").text(c);
        $(".zhezhao")
            .stop()
            .fadeToggle();
        $(".Shaixia")
            .stop()
            .slideToggle();
    });
    $(".Shaixiadiqu li").click(function () {
        $(".Shaixiadiqu li").removeClass("active");
        $(this).addClass("active");
        let c = $(this).text();
        $(".H_shaix .t2 label").text(c);
        $(".zhezhao")
            .stop()
            .fadeToggle();
        $(".Shaixiadiqu")
            .stop()
            .slideToggle();
    });

    $(".Zpzs .t2").click(function () {
        $(this)
            .find(".m1")
            .hide();
        $(this)
            .find(".m2")
            .show();
    });

    $(".Zhaopianxq .t3 .fd").click(function () {
        $(".Tanctu").css({
            display: "flex",
            display: "-webkit-flex"
        });
    });
    $(".Tanctu").click(function () {
        $(".Tanctu").hide();
    });

    $(".Dianz").click(function () {
        $(".Dianz .m1").hide();
        $(".Dianz .m2").css("display", "block");
    });

    $(".Tancys .t2").click(function () {
        Closea();
    });
    $(".Tancys .t5").click(function () {
        Closea();
    });
    $(".Tancys .guanx").click(function () {
        Closea();
    });

    function Closea() {
        $(".zhezhao").hide();
        $(".Tancys").hide();
    }

    $(".Jiangp_list .head_ span").click(function () {
        $(".Jiangp_list .head_ span").removeClass("active");
        $(this).addClass("active");
        $(".Tab_jp").hide();
        $(".Tab_jp")
            .eq($(this).index())
            .show();
    });

    $(".Jp_xuz .t2").click(function () {
        $(".Jp_xuz .t1").toggleClass("zhankai");
    });

    $(".Tijiao_xuz").click(function () {
        let c = $(".Jp_xuz .login_t1 span").hasClass("active");
        console.log(c);
        if (!c) {
            $(".zhezhao").show();
            $(".Tanc_").show();
        }else {
            var reg1 = /(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/
            var reg = /(^1[34578]\d{9}$)/;
            var name=$('#name').val();
            var tel=$('#tel').val();
            var idcard=$('#idcard').val();
            var id=$('#id').val();
            if (!reg.test(tel)){
                alert('电话格式错误');return false;
            }
            if (tel==''){
                alert('手机不能为空');return false;
            }
            if (name==''){
                alert('姓名不能为空');return false;
            }
            if (idcard==''){
                alert('身份证不能为空');return false;
            }
            if (!reg1.test(idcard)){
                alert('身份证输入有误');return false;
            }

           $.get('index.php?p=show&c=index&a=lqdj',{name:name,tel:tel,idcard:idcard,id:id},function (msg) {
               if (msg=='1'){
                   alert('领取成功,礼品将在7个工作日内发出')
                   window.location.href = '/?c=single&m=index_wdjp';
               }else if(msg=='3'){
                   alert('请勿重复提交')
               }else{
                   alert('系统繁忙,请稍后再试')
               }
           })

        }
    });
    $(".Tanc_a").click(function () {

        $(".zhezhao").hide();
        $(".Tanc_").hide();
    });

    // $('#dzp').click(function(){
    //
    //
    //     $.getJSON('index.php?p=show&c=index&a=check',function (msg) {
    //         if (msg=='0'){
    //             window.location.href = '/?c=single&m=act_dzp';
    //         }else {
    //             $('#num').text(msg)
    //             $(".zhezhao").show();
    //             $(".Tanc_").show();
    //         }
    //     })
    //
    // })
    
    // var a=function () {
    //
    // }
});