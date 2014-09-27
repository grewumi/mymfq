<?php
class virtualapi extends spController{
  public function loginAlimama(){
    import("function_login_taobao.php");

    if(!empty($_POST['username']) && !empty($_POST['password']))
    {
        header("Content-type: text/html; charset=gbk");
        $user = trim($_POST['username']);
        $pass = trim($_POST['password']);

        loginTaobao($user, $pass);
       
        
        echo getCommissionRate('27424572020');
     }
    $this->display("admin/loginAlimama.html");
  } 
}
?>

