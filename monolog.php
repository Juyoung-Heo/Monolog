<html>
<head>
    <title>Monolog</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no"/>
</head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<style>
    body{margin:0;padding:0;outline:none;color:#333333; font-family: "Arial", sans-serif;}

    #mg_menu {z-index:1000; position: absolute; background-color:#ffffff; border: 1px solid #fccacb;}
    #mg_menu ul{margin:0; padding:0px;}
    #mg_menu ul li{width:70px; text-align:center; padding: 5px 10px; border-bottom:#cccccc; list-style: none; font-size:13px;}
    #mg_menu ul li:hover{background:#eeeeee; cursor:pointer;}

    #mg_menu_mobile{z-index:1000; position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); max-width:95%; box-shadow : rgba(0,0,0,0.5) 0 0 0 9999px, rgba(0,0,0,0.5) 0 0 0 0; border-radius: 3px; transition:.3s; background-color:#ffffff; border: 1px solid #fccacb;}
    #mg_menu_mobile ul{margin:0; padding:0px;}
    #mg_menu_mobile ul li{width:100px; text-align:center; padding: 5px 10px; border-bottom:#cccccc; list-style: none; font-size:13px;}
    #mg_menu_mobile ul li:hover{background:#eeeeee; cursor:pointer;}

    #notice_area{position:absolute; display:none; width:100%; height:55px; top:60px; background:#ffffff; opacity:0.7;}
    #notice_con{position:relative; top:50%; transform:translateY(-50%); text-align:center;}
    #notice_close{position:absolute; right:0; top:0; padding:5px; cursor:pointer;}

  /*heaer*/
    header{display:table; width:100%; height:60px; left:0; top:0; background:#24382d;}
    #h_title{text-align:center; vertical-align:middle; display: table-cell; color:#fcded6; font-size:27px; letter-spacing:5px;}
    #h_impor_area{position:absolute; left:80%; top:19px; color:#ecada6;}
    #h_impor_area input[type='checkbox']{vertical-align:middle;}
  /*view*/
    view{display:block; background:#dddddd; overflow-y:auto;}
    .message{float:right; margin:10px; padding:8px; border-radius:3px; min-width:100px; background:#ffffff; clear:both; box-shadow: 1px 1px 5px #aaa; word-break:break-all; font-size:15px;}
    .message .mg_time{font-size:11px; padding-top:10px; color:#aaaaaa; display:none;}
    .message.imp{border-left:3px solid #9b4d43;}
  /*write*/
    write{display:block; position:absolute; width:100%; bottom:0; background:#ffffff;}
    #w_textarea{display:inline-block; min-height:40px; outline:none; padding:5px; font-size:15px;}
    #w_eraser{position:absolute; right:0;top:0; margin:10px 105px 0 0; color:#333333; display:none;}
    #W_eraser:hover{cursor:pointer;}
    write input[type="button"]{width:100px;height:40px;float:right;top:0; margin:5px 5px 5px 0; background:#595959; border: 1px solid #595959; border-radius:3px; color:#eeeeee; letter-spacing:2px;}
    write input[type="button"]:hover{cursor:pointer;}

    @media (max-width: 560px) {
        #_text{display:none;}
    }
</style>
<body>
    <header>
        <div id="h_title">MONOLOG</div></div>
        <div id="h_impor_area">
            <span id="_text">중요글 보기</span>
            <input type="checkbox" onclick="importantShow(this.checked)">
        </div>
    </header>
    <view>
    <!--
        <div class="message">
            안녕
            <div class="mg_time" style="display:block;">2017-03-04 03:04</div>
        </div>
    -->
    </view>
    <write>
        <div id="w_textarea" contenteditable="true" spellCheck="false"></div>
        <span id="w_eraser">x</span>
        <input type="button" value="전송" onclick="writeMessage()">
    </write>
    <div id="notice_area">
        <span id="notice_close" onclick="noticeClose(this)">x</span>
        <div id="notice_con"></div>
    </div>
</body>
<script>
    function layoutSetting(){
        $("#w_textarea").css("width", document.body.clientWidth-115+"px");
        $("view").css("height", (document.body.clientHeight-$("header")[0].clientHeight-$("write")[0].clientHeight)+"px");
    }
    layoutSetting();
    $(window).resize(function(){
        layoutSetting();
    });
    var prevHeight = $('#w_textarea')[0].clientHeight;
    if(!checkMobileDevice()){
        $('#w_textarea').on('input change keydown paste',function(e){
            if(e.which == 13){
                e.preventDefault();
                writeMessage();
            }
            $("view").css("height", (document.body.clientHeight-$("header")[0].clientHeight-$("write")[0].clientHeight)+"px");
            var curHeight = $(this)[0].clientHeight;
            if (prevHeight !== curHeight) {
                prevHeight = curHeight;
            }
        });
    }
    $(document).ready(function(){
        $(".message").each(function(){
            this.addEventListener('mousedown', function(e){
                alert(e);
            });
        });
    });

    var clickedMg;
    //오른쪽 마우스
    $(document).bind("contextmenu", function(event) {
        if (event.target.classList.contains('message') || event.target.classList.contains('mg_time')){
            event.preventDefault();
            clickedMg = event.target;
            $("div#mg_menu").each(function(){
                this.parentNode.removeChild(this);
            });
            $("<div id='mg_menu'><ul><li onclick='removeMg(clickedMg)'>삭제</li><li onclick='Notice(clickedMg)'>공지</li><li onclick='important(clickedMg)'>중요표시</li></ul></div>")
                .appendTo("body")
                .css({top: event.pageY + "px", left: event.pageX + "px"});
        }
    }).bind("click", function(event) {
        $("div#mg_menu").hide();
            $("div#mg_menu_mobile").each(function(){
                this.parentNode.removeChild(this);
            });
    });

    var mg_cnt=1;
    function writeMessage(){
        if($("#w_textarea")[0].innerHTML){
            newMessage = document.createElement("div");
            newMessage.classList.add("message");
            newMessage.id = "mg_"+mg_cnt;

            var today = new Date();
            var year = today.getFullYear().toString();
            var month = (today.getMonth()+1).toString();
            var day = today.getDate().toString();
            var mmChars = month.split('');
            var ddChars = day.split('');
            var dateString = year + '-' + (mmChars[1]?month:"0"+mmChars[0]) + '-'
            + (ddChars[1]?day:"0"+ddChars[0]);
            
            var hour = today.getHours().toString();
            var minute = today.getMinutes().toString();
            var hourChars = hour.split('');
            var minChars = minute.split('');
            var timeString = (hourChars[1]?hour:"0"+hourChars[0]) + ":" + (minChars[1]?minute:"0"+minChars[0]);         

            nowTime = dateString + " " +timeString;
            newMessage.innerHTML = $("#w_textarea")[0].innerHTML+"<div class='mg_time'>"+nowTime+"</div>";
            $("view")[0].appendChild(newMessage);
            newMessage.addEventListener('click', function(event){
                if(event.target.children[0])
                    event.target.children[event.target.children.length-1].style.display = "block";
            });
            if(checkMobileDevice()){
                newMessage.addEventListener('dblclick', function(event){
                    if (event.target.classList.contains('message') || event.target.classList.contains('mg_time')){
                        event.preventDefault();
                        clickedMg = event.target;
                        $("div#mg_menu_mobile").each(function(){
                            this.parentNode.removeChild(this);
                        });
                        $("<div id='mg_menu_mobile'><ul><li onclick='removeMg(clickedMg)'>삭제</li><li onclick='Notice(clickedMg)'>공지</li><li onclick='important(clickedMg)'>중요표시</li></ul></div>")
                            .appendTo("body")
                            .css({top: event.pageY + "px", left: event.pageX + "px"});
                    }
                });
            }
            mg_cnt++;
            $("#w_textarea")[0].innerHTML = "";
            $("view")[0].scrollTop = $("view")[0].scrollHeight;
        }
        $("#w_textarea").focus();
    }

    //공지
    if(0){
        $("#notice_area").css('display','block');
        $("#notice_con")[0].innerHTML = "안녕";
    }
    function noticeClose(ele){
        if(confirm("공지사항을 삭제하시겠습니까?") == true){
            ele.parentNode.style.display = "none";
        }else{return;}
    }

    function checkMobileDevice() {
        var mobileKeyWords = new Array('Android', 'iPhone', 'iPod', 'BlackBerry', 'Windows CE', 'SAMSUNG', 'LG', 'MOT', 'SonyEricsson');
        for (var info in mobileKeyWords) {
            if (navigator.userAgent.match(mobileKeyWords[info]) != null) {
                return true;

            }
        }
        return false;
    }

    function removeMg(ele){
        ele.parentNode.removeChild(ele);
    }
    function Notice(ele){
        $("#notice_area").css('display','block');
        var contents = (ele.innerHTML).split('<');
        $("#notice_con")[0].innerHTML = contents[0];
    }
    function important(ele){
        if(ele.classList.contains('imp')){
            ele.classList.remove("imp");
        }else{
            ele.classList.add("imp");
        }
    }

    function importantShow(checked){
        if(checked){
            $(".message").each(function(){
                if(!this.classList.contains('imp')){
                    this.style.display = "none";
                }
            });
        }else{
            $(".message").each(function(){
                this.style.display = "block";
            });
        }
    }
</script>
</html>