<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function getcaijicontent($contents,$contentptn,$singleptn){
    // ƥ����Ʒ����
    preg_match_all($contentptn,$contents,$arr1,PREG_SET_ORDER);
    
    // ƥ�䵥����Ʒ����
    preg_match_all($singleptn,$arr1[0][0],$arr2,PREG_SET_ORDER);
    
    if($arr2)
        return $arr2;
    else
        return null;
}
?>