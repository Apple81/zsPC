$(function(){
    /*
     * 左边树/菜单栏动作
     */
    var tagName = sessionStorage.getItem("tagName");
//  console.log(tagName);
    if(!tagName){
        var tagName = 'affairShow_Proj';
    }
    $('#'+tagName).parent('.TagSon').addClass('active');
    $('#'+tagName).parents('.TagFather').addClass('open');
    
    //  点击菜单后，JS写入缓存
    $('.TagSet').click(function(){
        $pageSta = $(this).attr("id");
        if($pageSta == 'SignOut') {
            $pageSta = 'affairShow_Proj';
            sessionStorage.setItem('projectId','');
        }
        sessionStorage.setItem("tagName", $pageSta); 
    });
    
    /*
     * control for link pro&action
     */
    var ProSelect = sessionStorage.getItem("projectId");
    var ProSelectName = sessionStorage.getItem("projectName");
    if(ProSelect===null||ProSelect.length==0) {
        $('.Require').addClass('NoAction');
//      for (var i=0;i<3;i++) {
//      	var text = $('.Require').find('.menu-text').eq(i).text();
//          $('.Require').find('.menu-text').eq(i).text('请先选定工程');
//      }
    }else{
        //工程名称显示
        $('#proName').text('【选中工程：'+ProSelectName+'】');
    }
    
    /*
     * draf & Pack => css
     */
    if (tagName == 'formShow_Draf' || tagName == 'formShow_Pack') {
        $('#tabList').removeClass('col-xs-5');
        $('#tabList').addClass('col-xs-7');
        
    }
    

});
function HardWroking(){
    alert('开发中')    
}
