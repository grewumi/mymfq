function huoqu(){
	$("input#huoqu").click(function(){
		var iid = $("#iid").val();
		$.get("/iteminfo.html",{
		    iid:iid
		},function(data){
			var dataObj=eval("("+data+")"); //转换为json对象
			//alert(data);
		    $("input#title").val(dataObj.title);
		    $("input#oprice").val(dataObj.oprice);
		    $("input#link").val(dataObj.link);
		    $("input#pic").val(dataObj.pic);
		    $("input#ww").val(dataObj.nick);
		    if(dataObj.carriage){
		    	$(":radio[name='carriage'][value='1']").attr("checked","checked");
		    }else{
		    	$(":radio[name='carriage'][value='0']").attr("checked","checked");
		    }
			//alert(dataObj.shopshow);
			if(dataObj.shopshow)// 是否天猫
				$(":radio[name='shopshow'][value='1']").attr("checked","checked");
			else
				$(":radio[name='shopshow'][value='0']").attr("checked","checked");
				
			if(dataObj.shopv)// 是否VIP
				$(":radio[name='shopv'][value='1']").attr("checked","checked");
			else
				$(":radio[name='shopv'][value='0']").attr("checked","checked");
		});
	});
}
function userhuoqu(){
	$("input#yijianhuoqu").click(function(){
		var iid = $("#iid").val();
		$.get("/iteminfo.html",{
		    iid:iid
		},function(data){
			var dataObj=eval("("+data+")"); //转换为json对象
			//alert(data);
		    $("input#title").val(dataObj.title);
		    $("input#oprice").val(dataObj.oprice);
		    $("input#link").val(dataObj.link);
		    $("input#pic").val(dataObj.pic);
		    $("input#ww").val(dataObj.nick);
		    if(dataObj.carriage){
		    	//alert('baoyou');
		    	$("input#postage").checked = true;
		    }else{
		    	//alert('bubaoyou');
		    	$("input#nopostage").checked = true;
		    }
		});
	});
}

function setrank(){
	 $(".set500").click(function(){
		 $("#rank").val(500);
	 });
	  $(".set499").click(function(){
		 $("#rank").val(499);
	 });
}