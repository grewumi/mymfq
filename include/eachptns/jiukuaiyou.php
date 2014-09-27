<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
global $contentptn,$singleptn;

$contentptn = '/class="jiu-main"(.+?)class="page"/is';

$singleptn = '/<li(.+?)class="price-current"(.+?)<\/em>(\d+\.?\d+)<\/span>(.+?)class="btn(.+?)href="(.+?)[?,&,]id=(\d+)(.*?)"(.+?)<\/li>/is';

?>