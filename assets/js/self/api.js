//平台端的接口，JS获取在此处修改，php获取在MesContro
URL_API = "http://112.74.34.150:8080/"

function getApiIp(){
    return URL_API;
}

function getProMesAll(dataPho) {
    $.ajax({
        type:"post",
        url:URL_API+"TongXinweb/project/AllPro",
        async:true,
        dataType:'json',
        success:function(data){
            if(data['success']) {
                var Mes  = '['
                for (var i=0;i<data['data'].length;i++) {
                	if(data['data'][i]['mobile'] == dataPho)
                	{
//              	    console.log(JSON.stringify(data['data'][i]))
                	    Mes += JSON.stringify(data['data'][i])
                	    Mes += ','
                	}
                }
                Mes = Mes.substr(0,Mes.length-1)
                Mes += ']'
                var MesObj = eval('(' + Mes + ')');
                tableProAll(MesObj)
            }
        },
        error:function(s,e,t){
            console.log(s,e,t);
        }
    });
}

function getProMesSel(data) {
    $.ajax({
        type:"post",
        url:URL_API+"TongXinweb/project/GetProById",
        async:true,
        data:{
            "projectId":"0b5c5b47-0927-48ec-a336-9b925881ec54",
        },
        success:function(data){
            console.log(data);
//          return dara;
        },
        error:function(s,e,t){
            console.log(s,e,t);
        }
    });
}

function getFomMesAll() {
    
    $.ajax({
        type:"post",
        url:URL_API+"TongXinweb/form/Allform",
        async:true,
        data:'',
        dataType:'json',
        success:function(data){
            
        },
        error:function(s,e,t){
            console.log(s,e,t);
        }
    });
}

function getFomMesSel_ProId() {
    var proId = sessionStorage.getItem('projectId');
    $.ajax({
        type:"post",
        url:URL_API+"TongXinweb/form/getFormByPid",
        async:true,
        data:{
            "projectId":proId,
        },
        dataType:'json',
        success:function(data){
            if(data['success']) {
                tabMesShow_Pro(data['data']);
            }
        },
        error:function(s,e,t){
            console.log(s,e,t);
        }
    });
}

function getFomMesSel_FomId() {
    $.ajax({
        type:"post",
        url:URL_API+"TongXinweb/form/getFormByFid",
        async:true,
        data:{
            "formId":"36dde2bb-d8bc-4cf2-aa7e-3c8fe2a8bb0b",
        },
        success:function(data){
            console.log(data);
//          return dara;
        },
        error:function(s,e,t){
            console.log(s,e,t);
        }
    });
}

function getAllNote() {
    $.ajax({
    	type:"post",
    	url:URL_API+"TongXinweb/Tree/AllNode",
    	async:true,
    	dataType:'json',
    	success:function(data){
    	    console.log(data)
    	},
    	error:function(s,e,t){
    	    console.log(s)
    	}
    });
}
