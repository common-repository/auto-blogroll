<?php
/*
  Copyright 2008 askie (email imaskie@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    email to imaskie@gmail.com
*/

//激活页面
function ab_install() 
{
	$wpv = get_bloginfo('version'); 
	if ($wpv < 2.1) 
  	{
	  	$c.= __("Wordpress 2.1 above Needed!","AutoBlogRoll");
    }
	/*if (!function_exists("file")) 
	{
		$c.= "file() ".__("function not find, can not check page PR!","AutoBlogRoll")."<br>";
	}*/
	if (!function_exists("fsockopen")) 
	{
		$c.= "fsockopen() ".__("function not find, can not check page PR!","AutoBlogRoll")."<br>";
	}
	$linkpage=get_option('ab_linkpagename');
	if ($linkpage=="" and ab_checkpage("links")) 
	{
		$linkpage="linksa";
		$c.= __("links page exist, recommend page name: ","AutoBlogRoll").$linkpage;
	}
	elseif ($linkpage=="" and ab_checkpage("links")==false) 
	{
		$linkpage="links";
		$c.= __("Recommend page name: ","AutoBlogRoll").$linkpage;
	}
	
	if ($linkpage<>"" and ab_checkpage($linkpage)) 
	{
		$c.= __("You have set the links page name !","AutoBlogRoll");
	}
	
	if (isset($c)) 
	{
		echo '<div class="updated"><p>'.$c.'</p></div>';
	}
	//获取已经存在的page
	$defaults = array(
		'depth' => 0, 'child_of' => 0,
		'selected' => 0, 'echo' => 1,
		'name' => 'page_id', 'show_option_none' => ''
	);
	extract( $defaults, EXTR_SKIP );
	$pages = get_pages($defaults);
	$currentId=get_option("ab_linkpageid");
	foreach ($pages as $p) 
	{
		if ($p->ID==$currentId) 
		{
			$useExistPage=" checked";
			break;
		}
	}
?>	
	<div class="wrap">
    <h3><a href="<?=$_SERVER["PHP_SELF"]?>?page=auto-blogroll/auto-blogroll.php&cm=manlinks"><? _e("ManageLinks","AutoBlogRoll");?></a> | <a href="<?=$_SERVER["PHP_SELF"]?>?page=auto-blogroll/auto-blogroll.php&cm=editlink&vid="><? _e("AddLink","AutoBlogRoll");?></a> | <a href="<?=$_SERVER["PHP_SELF"]?>?page=auto-blogroll/auto-blogroll.php&cm=setting"><? _e("GeneralSetting","AutoBlogRoll");?></a> | <a href="<?=$_SERVER["PHP_SELF"]?>?page=auto-blogroll/auto-blogroll.php&cm=install"><font color="Red"><? _e("Install","AutoBlogRoll");?></font></a> | <a href="<?=get_permalink(get_option("ab_linkpageid"))?>" target="_blank"><? _e("Preview","AutoBlogRoll");?></a></h3>
	<? ab_language("install"); ?>
    <form method="post" action="<?=$_SERVER["PHP_SELF"]?>?page=auto-blogroll/auto-blogroll.php&cm=doinstall">
	<table class="form-table">
		<tr>
       		<th nowrap><? _e("Links page name: ","AutoBlogRoll");?></th>
       		<td>
       		  <label><input type="radio" name="ab_whatepage" value="1"<?=isset($useExistPage)?"":" checked"?>><? _e("Create new page:","AutoBlogRoll");?></label> <input type="text" name="ab_linkpagename" value="<?=$linkpage?>"> 
				<? _e("(Use alphanumeric characters)","AutoBlogRoll");?><hr>
			  <label><input type="radio" name="ab_whatepage" value="2"<?=$useExistPage?>><? _e("Use exist page:","AutoBlogRoll");?></label> <?wp_dropdown_pages(array("selected"=>get_option("ab_linkpageid"),"name"=>"ab_pageid"));?>
			 </td>
		</tr>
		<tr>
       		<th nowrap><? _e("Function of the step:","AutoBlogRoll");?></th>
       		<td>
       		  <ul>
	       		  <li><? _e("Create your links exchange page","AutoBlogRoll");?></li>
	       		  <li><? _e("Import wordpress blogroll links","AutoBlogRoll");?></li>
       		  </ul>
			 </td>
		</tr>
		<tr>
       		<th nowrap><? _e("Manually Import wordpress blogroll links:","AutoBlogRoll");?></th>
       		<td>
       		<a href="<?=$_SERVER["PHP_SELF"]?>?page=auto-blogroll/auto-blogroll.php&cm=doinstall&subcm=inportblogroll"><? _e("(Click here)","AutoBlogRoll");?></a>  
			</td>
		</tr>
		<tr>
       		<th><? _e("Change the links page's content and title:","AutoBlogRoll");?></th>
       		<td>
       		<a href="<?=get_option("siteurl")?>/wp-admin/page.php?action=edit&post=<?=get_option("ab_linkpageid")?>"><? _e("(Click here)","AutoBlogRoll");?></a>  
			</td>
		</tr>
	</table>	    
	<p class="submit"><input type="submit" name="generate" value="<? _e("Install","AutoBlogRoll");?>" /></p>
    </form>
    </div>
<?php
}
function ab_language($cmd)
{
	?>
<form name="languageform" method="post" action="<?=$_SERVER["PHP_SELF"]?>?page=auto-blogroll/auto-blogroll.php&cm=changelanguage&backcmd=<?=$cmd?>">
    <table class="form-table">
		<tr>
       		<th nowrap><? _e("Plugin language:","AutoBlogRoll");?></th>
       		<td>
       		<input type="radio" name="ab_language" value="0" <?=get_option('ab_language')==0?" checked=\"checked\"":""?> onchange="document.languageform.submit();">English
			<input type="radio" name="ab_language" value="1" <?=get_option('ab_language')==1?" checked=\"checked\"":""?> onchange="document.languageform.submit();">简体中文
			<input type="radio" name="ab_language" value="2" <?=get_option('ab_language')==2?" checked=\"checked\"":""?> onchange="document.languageform.submit();">繁體中文
			</td>
		</tr>
	</table>
</form>		
	<?
}
function ab_setup() 
{
	require(ABSPATH . 'wp-config.php');
	
	global $wpdb;
	
	if ($_POST["ab_linkpagename"]<>"") 
	{
		update_option('ab_linkpagename',$_POST["ab_linkpagename"]);
	}
	
	if ($_POST["ab_whatepage"]=="2") 
	{
		update_option('ab_linkpageid',$_POST["ab_pageid"]);
		$seletedId=$_POST["ab_pageid"];
	}
	else 
	{
		$linkpage=get_option('ab_linkpagename');
		//检查links静态页面是否存在:
		$check = ab_checkpage($linkpage);
		
		//如果links静态页面不存在则创建:
		if (!$check) 
		{
			$content 	= "";
			$title 		= $linkpage;
			$status 	= "publish";
			$type 		= "page";
			
			$post_author 	= 1;
			$post_date 		= current_time('mysql');
			$post_date_gmt 	= current_time('mysql', 1);
			$post_content 	= $content;
			$post_title 	= $title;
			$post_status 	= $status;
			$post_type 		= $type;
			$post_data 		= compact('post_author', 'post_date', 'post_date_gmt', 'post_content', 'post_title', 'post_status', 'post_type');
			$maxid 			= wp_insert_post($post_data);
			
			//确认links静态页面已经存在:
			if ($maxid) 
			{
				update_option('ab_linkpageid',$maxid);
				//使用post_meta记录该页面是插件所创建
				add_post_meta($maxid,"ab_created","a");
				$msg .= $linkpage.__(" create successfully.","AutoBlogRoll")."<br />";
			} 
			else 
			{
				$msg .= $linkpage.__(" create failed.","AutoBlogRoll")."<br />";
			}
		} 
		else 
		{
			update_option('ab_linkpageid',$check);
			$msg .= $linkpage.__(" allready exsit.","AutoBlogRoll")."<br />";
			$maxid=$check;
		}
	}

	$msg .= __("install successfully.","AutoBlogRoll")."<br />";
	$msg .= __("Next step to <a href='./widgets.php'><font color=red>add AutoBlogRoll widget</font></a> to your sidebar.","AutoBlogRoll")."<br />";
	$msg .= "<a href=\"".get_permalink(isset($seletedId)?$seletedId:$maxid)."\" target=_blank>".__("Preview","AutoBlogRoll")."</a>";
	ab_active();
	return $msg;
}
function ab_active($force=true)
{
	//设置检测
	/*if ($force==false) 
	{
		$lastactivetime=get_option("ab_lastactivetime");
		if ($lastactivetime!="" and date("Y-m-d",$lastactivetime) >= date("Y-m-d")) 
		{
			return ;
		}
	}
	$linkkey=md5(time().rand(0,1000));
	update_option("ab_linkkey",$linkkey);
	require_once( ABSPATH . 'wp-includes/class-snoopy.php' );
	$http=new Snoopy();
	$http->agent = MAGPIE_USER_AGENT;
	$http->read_timeout = MAGPIE_FETCH_TIME_OUT;
	$http->use_gzip = MAGPIE_USE_GZIP;
	$data=array(
			"siteurl"			=>get_option("siteurl"),
			"email"				=>get_option("admin_email"),
			"blogname"			=>get_option("blogname"),	
			"blogdescription"	=>get_option("blogdescription"),
			"linkkey"			=>$linkkey,
			"approve"			=>get_option("ab_approved"),
			"limit_pr"			=>get_option("ab_pr"),
			"total"				=>get_option("ab_n"),
		);
	global $ab_hell;	
	if($http->submit("{$ab_hell}/auth/active/".$linkkey,$data))
	{
		if (substr($http->results,0,2)=="**" and substr($http->results,34,2)=="**") 
		{
			$blogkey=substr($http->results,2,32);
			update_option("ab_blogkey",$blogkey);
		}
	}
	update_option("ab_lastactivetime",time());*/
}
//检测文件和文件夹是否可写
function ab_writable($path) 
{
    if ($path{strlen($path)-1}=='/') // recursively return a temporary file path
    {
    	return ab_writable($path.uniqid(mt_rand()).'.tmp');
    }
    elseif (is_dir($path))
    {
    	return ab_writable($path.'/'.uniqid(mt_rand()).'.tmp');
    }
    // check tmp file for read/write capabilities
    $rm = file_exists($path);
    $f = @fopen($path, 'a');
    if ($f===false) return false;
    fclose($f);
    if (!$rm) unlink($path);
    return true;
}
//导入blogroll链接
function ab_inportblogroll()
{
	$categories = get_terms('link_category', "hide_empty=1");
	foreach ((array) $categories as $cat)
	{
		$cats[$cat->term_id]=$cat;
	}
	
	$args = array('category' => '', 'hide_invisible' => 0, 'orderby' => 'id', 'hide_empty' => 0);
	$links = get_bookmarks( $args );
	$DB=new ab_DB();
	$all=$DB->select();
	//根据PR和Alexa判断链接
	$Pr=new AB_PageRank();
	foreach ($links as $x) 
	{
		$data=array(
			"url"			=>$x->link_url,
			"title"			=>$x->link_name,
			"description"	=>$x->link_description,
			"logourl"		=>$x->link_image,
			"reurl"			=>$x->link_url,
			"approved"		=>$x->link_visible=="Y"?0:1,
			"cat"			=>$cats[$x->link_category]->name,
		);
		if (key_exists(md5(base64_encode($x->link_url)),$all)) 
		{
			foreach ($all[md5(base64_encode($x->link_url))] as $kk=>$zz) 
			{
				if (!key_exists($kk,$data)) 
				{
					$data[$kk]=$zz;
				}
			}
		}	
		$DB->update($data);	
	}
	return __("Import links from blogroll successfully!","AutoBlogRoll");
}
//显示当前发布版本信息
function ab_showversionstring()
{
	$v=get_option("ab_vesionstring");
	if ($v<>"") 
	{ 
		return '<div style="border: 1px dotted #FF6600; background-color: #FFEFDF; padding: 2px; margin-bottom: 5px; margin-top: -5px;">'.$v.'</div>';	
	}
}
//检查版本
function ab_versionCheck()
{
//	require_once( ABSPATH . 'wp-includes/class-snoopy.php' );
//	$http=new Snoopy();
//	$http->agent = MAGPIE_USER_AGENT;
//	$http->read_timeout = MAGPIE_FETCH_TIME_OUT;
//	$http->use_gzip = MAGPIE_USE_GZIP;
//	$http->fetch("http://www.pkphp.com/versioncheck.php");
//	$v=$http->results;
	
//	$v=@file("http://www.pkphp.com/versioncheck.php");
//	$v=@implode("\n",$v);

	if ($v=="") 
	{
		update_option("ab_vesionstring", $v);
		update_option("ab_vesionsupdatetime", date("Y-m-d"));
	}
}
function ab_cmt()
{
	if($_GET["start"]) update_option("ab_lastcmtid",$_GET["start"]);
	$lastcmtid=(int)get_option("ab_lastcmtid");
	$n=(int)$_GET["n"]==""?10:(int)$_GET["n"];
	global $wpdb;
	$sql = "SELECT max(comment_ID) FROM $wpdb->comments  ORDER BY `comment_ID` ASC";
	$maxid =$wpdb->get_var($sql);
	if ($lastcmtid>=$maxid) 
	{
		return ;
	}
	$sql = "SELECT comment_ID,comment_author,comment_author_email,comment_author_url FROM $wpdb->comments WHERE comment_author_email != '' AND comment_author_url != '' AND comment_author_url != 'http://' AND comment_approved = '1' AND comment_ID>'{$lastcmtid}' GROUP BY `comment_author_url` ORDER BY `comment_ID` ASC LIMIT 0,{$n}";
	$result=$wpdb->get_results($sql,ARRAY_A);
	$parseOpml=new ParseOpml();
	$siteurl=get_option("siteurl");
	$links=array();
	foreach ((array)$result as $c) 
	{
		if (strstr($c["comment_author_url"],$siteurl)==false) 
		{
			$links[]=array(
				'title'		=>$c["comment_author"],
				'email'		=>$c["comment_author_email"],
				'url'		=>$c["comment_author_url"],
			);
			$lastcmtid=$c["comment_ID"]>$lastcmtid?$c["comment_ID"]:$lastcmtid;
		}
	}
	update_option("ab_lastcmtid",$lastcmtid);
	$site["name"]=get_option("blogname");
	$parseOpml->outOpml($site,$links);
	exit();
}
//输出html代码时过滤特殊字符
function ab_specialchars( $text, $quotes = 0 ) 
{
	// Like htmlspecialchars except don't double-encode HTML entities
	$text = str_replace('&&', '&#038;&', $text);
	$text = str_replace('&&', '&#038;&', $text);
	$text = preg_replace('/&(?:$|([^#])(?![a-z1-4]{1,8};))/', '&#038;$1', $text);
	$text = str_replace('<', '&lt;', $text);
	$text = str_replace('>', '&gt;', $text);
	if ( 'double' === $quotes ) {
		$text = str_replace('"', '&quot;', $text);
	} elseif ( 'single' === $quotes ) {
		$text = str_replace("'", '&#039;', $text);
	} elseif ( $quotes ) {
		$text = str_replace('"', '&quot;', $text);
		$text = str_replace("'", '&#039;', $text);
	}
	return $text;
}
//去除网址最后的/
function ab_stripurlendslash($url)
{
	if (substr($url,-1)=="/") 
	{
		$url=substr($url,0,-1);
		if (substr($url,-1)=="/") 
		{
			$url=ab_stripurlendslash($url);
		}
	}
	return $url;
}
class ParseOpml {
    var $outline;
    var $head;
    var $index;
    var $vals;
    var $error;
    var $tags=array('TITLE',
                    'DATECREATED',
                    'DATEMODIFIED',
                    'OWNERNAME',
                    'OWNEREMAIL',
                    'EXPANSIONSTATE',
                    'VERTSCROLLSTATE',
                    'WINDOWTOP',
                    'WINDOWLEFT',
                    'WINDOWBOTTOM',
                    'WINDOWRIGHT');
	function ParseOpml($filename="")
	{
		$this->OPMLOutlineFromFile($filename);
	}
	function OPMLOutlineFromFile($filename="",$type="link") 
	{
        require_once( ABSPATH . 'wp-includes/class-snoopy.php' );
		$http=new Snoopy();
		$http->agent = MAGPIE_USER_AGENT;
		$http->read_timeout = MAGPIE_FETCH_TIME_OUT;
		$http->use_gzip = MAGPIE_USE_GZIP;
		$http->fetch($filename);
		$contents=$http->results;

        $p=xml_parser_create();
        xml_parse_into_struct($p,$contents,$vals,$index);
        xml_parser_free($p);

        $outline=array();
        if (key_exists('OUTLINE',$index)) 
        {
        	foreach((array)$index['OUTLINE'] as $foo)
	        {
	            if(in_array($vals[$foo]['type'],array('open','complete'))) 
	            {
	                if(empty($mindepth))
	                {
	                    $mindepth=$vals[$foo]['level'];
	                }
	                $tmp=array();
	                $tmp['text']=key_exists('TEXT',$vals[$foo]['attributes'])?$vals[$foo]['attributes']['TEXT']:"";
	                $tmp['type']=key_exists('TYPE',$vals[$foo]['attributes'])?$vals[$foo]['attributes']['TYPE']:"";
	                $tmp['xmlurl']=key_exists('XMLURL',$vals[$foo]['attributes'])?$vals[$foo]['attributes']['XMLURL']:"";
	                $tmp['htmlurl']=key_exists('HTMLURL',$vals[$foo]['attributes'])?$vals[$foo]['attributes']['HTMLURL']:"";
	                $tmp['updated']=key_exists('UPDATED',$vals[$foo]['attributes'])?$vals[$foo]['attributes']['UPDATED']:"";
	                $tmp['email']=key_exists('EMAIL',$vals[$foo]['attributes'])?$vals[$foo]['attributes']['EMAIL']:"";
	                $tmp['webmaster']=key_exists('WEBMASTER',$vals[$foo]['attributes'])?$vals[$foo]['attributes']['WEBMASTER']:"";
	                $tmp['logourl']=key_exists('LOGOURL',$vals[$foo]['attributes'])?$vals[$foo]['attributes']['LOGOURL']:"";
	                
	                if ($tmp['type']==$type) 
	                {
	                	$this->outline[]=$tmp;
	                }
	            }
	        }
        }
    }
	function outOpml($siteInfo,$linksData)
	{
		header('Content-Type: text/xml; charset=UFT-8', true);
		$str= '<?xml version="1.0"?'.">\n";
		$str.='<!-- generator="pkphp.com" -->
<opml version="1.0">
	<head>
		<title>Links for '.$siteInfo["name"].'</title>
		<dateCreated>'.gmdate("D, d M Y H:i:s").' GMT</dateCreated>
	</head>
	<body>';
		$str.='<outline type="category" title="">';
		foreach ((array) $linksData as $bookmark) 
		{
			$title = ab_specialchars($bookmark["title"]);
			$str.='<outline text="'.$title.'" type="link" xmlUrl="" htmlUrl="'.$bookmark["url"].'" updated="" webmaster="'.$bookmark["webmaster"].'" email="'.$bookmark["email"].'" logourl="'.$bookmark["logourl"].'" />';
		}
		$str.='</outline>';
		$str.='</body>
</opml>
';
		echo $str;
		exit();
	}
}
class ab_DB
{
	//配置数组
	var $setting=array(
				"linkfile" 	=> "links.php",		//数据库文件名
				); 
	//数据文件所存储字段名			
	var $keys=array("id","url","title","description","reurl","webmaster","email","logourl","pr","alexa","cat","order","approved","nolinknum","check","checktime");			
	//字符串字段,其余均为整数串
	var $strKeys=array("url","title","description","reurl","webmaster","email","logourl","cat");
	
	function ab_DB($setting=array())
	{
		//$this->setting["linkfile"]=str_replace("\\","/",dirname(__FILE__)."/".$this->setting["linkfile"]);
		$oldLinkfile = str_replace("\\","/",dirname(__FILE__)."/".$this->setting["linkfile"]);
		
		$upload_path=get_option( 'upload_path' );
		if (strstr($upload_path,':')==false and substr($upload_path,0,1)!='/') 
		{
			$upload_path=ABSPATH.$upload_path;
		}
		
		$newLinkfile = str_replace(array("\\",'//'),array("/",'/'),$upload_path."/".$this->setting["linkfile"]);
		
		if (!file_exists($newLinkfile) and file_exists($oldLinkfile)) 
		{
			if(copy($oldLinkfile,$newLinkfile))
			{
				unlink($oldLinkfile);
			}
		}
		$this->setting["linkfile"]=$newLinkfile;
	}
	
	function select($where=array(),$order="order ASC",$group="")
	{
		$data=array();
		@include($this->setting["linkfile"]);
		
		$newdata=array();
		foreach ($data as $key=>$var) 
		{
			foreach ($var as $k=>$v) 
			{
				if (in_array($k,$this->strKeys)) 
				{
					$var[$k]=stripcslashes(base64_decode($v));
				}
			}
			if (!is_array($where)) 
			{
				if ($where==$key) 
				{
					return $var;
				}
			}
			else 
			{
				$check=true;
				foreach ($where as $a=>$b) 
				{
					$check=($check and ($var[$a]==$b));
				}
				if ($check) 
				{
					$newdata[$key]=$var;
				}
			}
		}
		if ($group<>"") 
		{
			$newdata=$this->groupBy($newdata,$group);
		}
		if ($order<>"") 
		{
			$newdata=$this->orderBy($newdata,$order);
		}
		
		return $newdata;
	}
	//根据group输出数据
	function groupBy($data,$key)
	{
		$newdata=array();
		$group=array();
		foreach ($data as $k=>$v) 
		{
			if (in_array($v[$key],$group)==false) 
			{
				$newdata[$k]=$v;
				$group[]=$v[$key];
			}
		}
		return $newdata;
	}
	//根据order排序数据
	function orderBy($data,$order)
	{
		$order=explode(" ",$order);
		$key=$order[0];
		$sort=strtoupper($order[1])=="ASC"?"ASC":"DESC";
		
		$newdata=array();
		$order=array();
		foreach ($data as $k=>$v) 
		{
			$order[$k]=$v[$key];
		}
		if ($sort=="ASC") 
		{
			asort($order);
		}
		else 
		{
			arsort($order);
		}
		foreach ($order as $a=>$b) 
		{
			$newdata[$a]=$data[$a];
		}
		return $newdata;
	}
	/*
		更新数据
		$url string
	*/
	function delete($url,$urlkey="")
	{
		$data=$this->select();			
		$Vid=$urlkey<>""?$urlkey:md5(base64_encode($url));
		$newdata=array();
		foreach ($data as $key=>$var) 
		{
			if ($key==$Vid) 
			{
				continue;
			}
			$newdata[$key]=$var;
		}
		
		if ($this->WriteFile($newdata)) 
		{
			return true;
		}
		else 
		{
			return false;
		}
	}
	/*
		更新数据
		$data array
		$value string
	*/
	function update($UrlData=array(),$update=false)
	{
		if (!is_array($UrlData)) 
		{
			return false;
		}
		if (key_exists("url",$UrlData) and $UrlData["url"]<>"" and strlen(trim($UrlData["url"]))>10) 
		{
			$Vid=md5(base64_encode($UrlData["url"]));
		}
		else 
		{
			return false;
		}
		
		$data=$this->select();
		if ($update and key_exists($Vid,$data)==false) 
		{
			return false;
		}
		
		if (!key_exists($Vid,$data)) 
		{
			$UrlData["id"]=time();
			$data[$Vid]=$UrlData;
		}	
		else 
		{
			foreach ((array)$UrlData as $key=>$value) 
			{
				$data[$Vid][$key]=$value;
			}
		}

		if ($this->WriteFile($data)) 
		{
			return true;
		}
		else 
		{
			return false;
		}
	}
	//存入文件
	function writeFile($data=array())
	{
		$key		=$this->keys;
		$string		=$this->strKeys;
		
		$tp=array();
		foreach ($data as $Vid=>$UrlData) 
		{
			foreach ($UrlData as $k=>$v) 
			{
				if (in_array($k,$key)) 
				{
					if (in_array($k,$string)) 
					{
						$v=base64_encode($v);
					}
					else 
					{
						$v=(int)$v;
					}
					$tpdata[]="'$k'=>'$v'";
				}
			}
			$d.="\$data['{$Vid}']	=array(".implode(",",$tpdata).");\n";
			$tpdata=array();
		}
		
		$head="<?\n";		
		$foot="\n?>";
		
		$fp=fopen($this->setting["linkfile"],"w+") or die(dirname(__FILE__)._e("No permission to write, Please change folder to 0777 .","AutoBlogRoll"));
		flock($fp, LOCK_EX);
		if (fwrite($fp,$head.$d.$foot)) 
		{
			flock($fp, LOCK_UN);
			fclose($fp);
			return true;
		}
		else 
		{
			flock($fp, LOCK_UN);
			fclose($fp);
			return false;	
		}
	}
}
?>