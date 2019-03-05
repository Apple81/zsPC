//get mes
function FormGetMes(uri){
    $.ajax({
        type:"post",
        url:uri,
        async:true,
        dataType:'json',
        success:function(data){
//          console.log(data)
            showFormMes(data)
        },
        error:function(s,e,t){
            console.log('出现错误')
        }
    });
}

//show form mes
function showFormMes(MesPack){
    
    //fill change model
    $('#ChaFormName').attr('value',MesPack['base'][0]['TabNam'])
    $('#ChaFormName').val(MesPack['base'][0]['TabNam'])
    $('#ChaFormType').text(MesPack['type'])
    $('#datepickerForm').attr('value',MesPack['base'][0]['TabDTm'])
    $('#datepickerForm').val(MesPack['base'][0]['TabDTm'])
    $('#ChaTabEls').text(MesPack['base'][0]['TabEls'])
    
    //show baseMes
//  $('#ModelName').text(MesPack['base'][0]['TabNam'])
    $('#FormName').text(MesPack['base'][0]['TabNam'])
    $('#FormPage').text(MesPack['base'][0]['TabNam'])
    $('#FormType').text(MesPack['type'])
    $('#ReTime').text(MesPack['base'][0]['TabCTm'])
    $('#DLtime').text(MesPack['base'][0]['TabDTm'])
    $('#TabEls').text(MesPack['base'][0]['TabEls'])
        //show pic
    $('.clearfix').html('')
    var EleText = ''
    var ImgUrl = MesPack['base'][0]['imgurl'].split('(')
    var ApiUrl = getApiIp()
    for (var i=0;i<MesPack['base'][0]['page'];i++) {
    	EleText += "<li>"
        EleText += "<a target='_blank' href='"+ApiUrl+ImgUrl[0]+'('+(i+1)+')'+'.png'+"' data-rel='colorbox'>"
        EleText += "<img width='400' height='600' alt='400x600' src='"+ApiUrl+ImgUrl[0]+'('+(i+1)+')'+'.png'+"' />"
        EleText += "<div class='text'>"
        EleText += "<div class='inner'>点击放大</div>"
        EleText += "</div>"
        EleText += "</a>"
        EleText += "</li>"
    }
    $('.clearfix').append(EleText)
    //clear circleMes
    $('.steps').html('')
    //show circleMes
    EleText = ''
    var MesSta = '';
    for (var i=0;i<MesPack['cirNum'];i++) {
//      console.log(MesPack['cirDetali'][i]['DepNam'])
//      console.log(MesPack['cirDetali'][i]['SigSta'])
        if(MesPack['cirDetali'][i]['SigSta'] == 5) {
            MesSta = 'active'
        }else{
            MesSta = ''
        }
        EleText += "<li data-step='"+(i+1)+"' class='"+MesSta+"'>"
        EleText += "<span class='step'>"+(i+1)+"</span>"
        EleText += "<span class='title'>"+MesPack['cirDetali'][i]['DepNam']+"</span>"
        EleText += "</li>"
    }
    $('.steps').append(EleText)
    
    return;
}
//show his mes
//get urlMes[type of GET]
function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");  
    var r = window.location.search.substr(1).match(reg);  //获取url中"?"符后的字符串并正则匹配
    var context = "";  
    if (r != null)  
         context = r[2];  
    reg = null;  
    r = null;  
    return context == null || context == "" || context == "undefined" ? "" : context;  
}

//page back
function PageBack(){
    window.close()
}
