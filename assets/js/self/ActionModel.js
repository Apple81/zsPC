/*
 * model
 */
//override dialog's title function to allow for HTML titles
$.widget("ui.dialog", $.extend({}, $.ui.dialog.prototype, {
    _title: function(title) {
        var $title = this.options.title || '&nbsp;'
        if( ("title_html" in this.options) && this.options.title_html == true )
            title.html($title);
        else title.text($title);
    }
}));

/*
 * reset
 */
$( "#id-btn-dialog" ).on('click', function(e) {
    e.preventDefault();

    var dialog = $( "#dialog-message" ).removeClass('hide').dialog({
        modal: true,
        title: "<div class='widget-header widget-header-small'><h4 class='smaller'><i class='ace-icon fa fa-check'></i>工作流设置</h4></div>",
        title_html: true,
        buttons: [ 
            {
                text: "取消",
                "class" : "btn btn-minier",
                click: function() {
                    $( this ).dialog( "close" ); 
                } 
            },
            {
                text: "确定",
                "class" : "btn btn-primary btn-minier",
                click: function() {
                    
//                      $('#CirMes tr').eq(1).find('td').eq(2).find('select').val();
                    $( this ).dialog( "close" );
                } 
            }
        ],
        width: "1200px",
    });
});

/*
 * 修改工作流
 */
$( "#id-btn-dialog-new" ).on('click', function(e) {
    e.preventDefault();
    
    //创建新的表格信息
    var TypeId = $('#TypeId-new').attr('value');
    console.log(TypeId)
    $.ajax({
    	type:"post",
    	url:DomMesGet,
    	async:true,
    	dataType:'json',
    	data:{
    	    TypeId:TypeId
    	},
    	success:function(data){
    	    console.log(data)
//  	    console.log(data['departMes'][0]['RolNam'])
            //显示节点数
            var RowNum = parseInt(data['RowNum'])
            
            //删除旧的表格信息
            $('#TurnNum-new option').removeAttr('selected')
            $('#CirMes-new').find('tbody').html('')
            //显示选中和行数
            $('#TurnNum-new option').eq(RowNum).attr('selected',true)
            if(RowNum == 0){
                RowNum = 1
            }
//          alert(RowNum)
            $('#TurnNum-new').val(RowNum)
    	    //创建节点
    	    var EleText = ''
    	    for (var i=0;i<data['RowNum'];i++) {
    	    	EleText += '<tr class="">'
                EleText += '<td class="">'+(i+1)+'</td>'
                EleText += '<td class="">'
                EleText += '<select>'
                EleText += '<option></option>'
                for (var y=0;y<data['departMes'].length;y++) {
                    if(data['departMes'][y]['RolNam'] == data[i]['RolNam']){
                        EleText += '<option selected="selected">'+data['departMes'][y]['RolNam']+'</option>'
                        continue;
                    }
                	EleText += '<option>'+data['departMes'][y]['RolNam']+'</option>'
                }
                EleText += '</select>'
                EleText += '</td>'
                EleText += '</tr>'
    	    }
    	    //如果信息行数等于零
    	    if(data['RowNum'] == 0){
    	        EleText += '<tr class="">'
                EleText += '<td class="">1</td>'
                EleText += '<td class="">'
                EleText += '<select>'
                EleText += '<option></option>'
                for (var y=0;y<data['departMes'].length;y++) {
                    EleText += '<option>'+data['departMes'][y]['RolNam']+'</option>'
                }
                EleText += '</select>'
                EleText += '</td>'
                EleText += '</tr>'
    	    }
    	    $('#CirMes-new').find('tbody').html(EleText)
//  	    console.log(EleText)
    	},
    	error:function(s,e,t){
    	    console.log(e)
    	}
    });
//  alert(DomMesGet)
    
    var dialog = $( "#dialog-message-new" ).removeClass('hide').dialog({
        modal: true,
        title: "<div class='widget-header widget-header-small'><h4 class='smaller'><i class='ace-icon fa fa-check'></i>工作流设置</h4></div>",
        title_html: true,
        buttons: [ 
            {
                text: "取消",
                "class" : "btn btn-minier",
                click: function() {
                    $( this ).dialog( "close" ); 
                } 
            },
            {
                text: "确定",
                "class" : "btn btn-primary btn-minier",
                click: function() {
                    //获取工作流信息
                    var RNum = $('#TurnNum-new').val();
                    var uri = $('#uriOwn-new').val();
                    var Data = [];
                    for (var i=0;i<RNum;i++) {
                        Data.push($('#CirMes-new tr').eq(i+1).find('td').eq(1).find('select').val());
                    }
                    var TypeId = $('#TypeId-new').attr('value');
                    
                    console.log(Data)
                    console.log(TypeId)
                    //发送信息
                    $.ajax({
                        type:"post",
                        url:uri,
                        async:true,
//                      dataType:'json',
                        data:{
                            data:Data,
                            TypeId:TypeId
                        },
                        success:function(data){
                            //clear old mes
                            $('.steps').html('');
                            //creat new mes
                            if(Data.length>0){
                                for (var i=0;i<Data.length;i++) {
                                    $('.steps').append("<li class='active' data-step='"+(i+1)+"'><span class='step'>"+(i+1)+"</span><span class='title'>"+Data[i]+"</span></li>");
                                }
                            }
                        },
                        error:function(s,e,t){
                            console.log(s,e,t);
                        }
                    });
                    $( this ).dialog( "close" );
                } 
            }
        ],
        width: "1200px",
    });
});