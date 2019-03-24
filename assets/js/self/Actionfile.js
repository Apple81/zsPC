////附件上传
//$('#fileMesSave').on('click',function(){
//  Addfile()
//  $('#ads-mes').modal('hide')
//})
//$('#getfile').on('change',function(){
//	  var objUrl = getObjectURL(this.files[0]) ;
//  	if(objUrl){
//  		alert('已上传')
//  	}
//  	$("#ImgShow").attr("src", objUrl) ;
//  //建立一個可存取到該file的url
//	function getObjectURL(file) {
//	  var url = null ; 
//	  // 下面函数执行的效果是一样的，只是需要针对不同的浏览器执行不同的 js 函数而已
//	  if (window.createObjectURL!=undefined) { // basic
//	    url = window.createObjectURL(file) ;
//	  } 
//	  else if (window.URL!=undefined) { // mozilla(firefox)
//	    url = window.URL.createObjectURL(file) ;
//	  } 
//	  else if (window.webkitURL!=undefined) { // webkit or chrome
//	    url = window.webkitURL.createObjectURL(file) ;
//	  }
//	//alert(url)
//	  return url ;
//	}
//})
///*
// * 上传附件
// */
//function Addfile(){
// my_files = document.getElementById("getfile").files;
// 
//  if(!my_files.length)
//  {
//      alert('请选择文件后上传')
//      return
//  }
//  //获取数据
//  fData = new FormData();
////  fData.append("Fid",$('#formId').val())
//  fData.append('file',my_files[0])
//  console.log(fileUrl);
//  $.ajax({
//      type:"post",
//      url:fileUrl,
//      async:true,
//      dataType:'json',
//      data:fData,
//      processData:false,
//      contentType:false,
//      success:function(data){
////          console.log(data)
//          if(data['status'] == 'success'){
//              //显示图片
//              $('#ImgShow').attr('src',data['url'])
//          }
//          alert("保存成功")
////          alert(data['warm'])
//          //刷新表格数据
////          tabMesSimple.ajax.reload();
//          return
//      },
//      error:function(s,e,t){
//          alert('出现错误，请及时联系管理员')
//      }
//  });
//}