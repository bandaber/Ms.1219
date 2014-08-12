<?php
function nettoyage($content) {
	$content = str_replace("'", '&rsquo;',$content);
	//$content = str_replace("&rsquo; ", '&rsquo;',$content);
   // $content = str_replace('&laquo;&nbsp;', '&laquo;',$content);
    //$content = str_replace('&nbsp;&raquo;', '&raquo;',$content);
    /*$content = str_replace('«&nbsp;', '&laquo;',$content);
    $content = str_replace('&nbsp;»', '&raquo;',$content);*/
    
    $content = str_replace('"', ' &quot;',$content);
    /*$content = str_replace(' &quot;', ' &laquo;',$content);
    $content = str_replace('&quot; ', '&raquo; ',$content);*/
   /* $content = str_replace('« ', '&laquo;',$content);
    $content = str_replace(' »', '&raquo;',$content);*/
    $content = str_replace(' :', '&nbsp;:',$content);
    $content = str_replace(' ;', '&nbsp;;',$content);
    $content = str_replace(' ?', '&nbsp;?',$content);
    $content = str_replace(' !', '&nbsp;!',$content);
    $content = str_replace('- ', '&mdash; ',$content);
    $content = str_replace(' – ', '&nbsp;&mdash; ',$content);
    return $content;
}

?>