<?php
/*
Plugin Name: Auto Blogroll
Plugin URI:  http://fairyfish.net/2008/08/07/auto-blogroll/
Description: Auto manage your blogroll links. Once activated, click on Settings > <a href='./options-general.php?page=auto-blogroll/auto-blogroll.php'>AutoBlogRoll</a> to config!
Version: 2.5
Author: askie
Author URI: http://www.pkphp.com/

  Copyright 2008 Askie (email imaskie@gmail.com)

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

    
INSTRUCTIONS
------------
1. Upload and activate, set permission of plug-in directory as "0777".
2. go to setting-> AutoBlogroll-> install,  activate  plug-in.
3. go to setting-> AutoBlogroll-> GeneralSetting, set basic parameters
4. go to widget page of templates and insert AutoBlogroll widget to your sidebar
5. After that, setting-> AutoBlogroll-> Manage link
6. setting-> AutoBlogroll-> GeneralSetting to chose plugin's language face.
    
*/
$ab_version=2.2;
//调用语言包
$locale = get_locale();
if (get_option('ab_language')==1 or $locale=="zh_CN") 
{
	load_textdomain('AutoBlogRoll', dirname(__FILE__) . "/auto-blogroll-zh_CN.mo");
}
elseif (get_option('ab_language')==2)
{
	load_textdomain('AutoBlogRoll', dirname(__FILE__) . "/auto-blogroll-zh_TW.mo");
}
else 
{
	load_textdomain('AutoBlogRoll', dirname(__FILE__) . "/auto-blogroll-$locale.mo");
}
//一般设定
function ab_generalsetting()
{
	if ($_POST['flag']=="ab_general") 
     {
		foreach ($_POST as $key=>$value) 
		{
			if ($key=="ab_pr") 
			{
				$value=$value>11?10:$value;
			}
			if ($key=="ab_intitle") 
			{
				if ($value<>get_option("ab_intitle")) 
				{
					$DB=new ab_DB();
					$inlink=$DB->select(array("url"=>get_permalink(get_option("ab_linkpageid"))));
					$inlink=array_pop($inlink);
					$DB->delete($inlink["url"]);
				}
			}
			if (strstr($key,"ab_")==$key) 
			{
				if ($key=="ab_language" and get_option("ab_language")<>$value) 
				{
					$changelanguage=true;
				}
				update_option($key, $value);
			}
		}
		ab_active();
		if ($changelanguage==true) 
		{
			$curl="{$_SERVER["PHP_SELF"]}?page=auto-blogroll/auto-blogroll.php&cm=setting";
			echo '<div class="updated"><p>'.__("Saved successfully!","AutoBlogRoll").$msg.__(" refresh page now!","AutoBlogRoll").'<a href="'.$curl.'"> Click here</a>'.'<script language="javascript">
setTimeout("window.open(\''.$curl.'\',\'_self\');",1000);
</script></p></div>';
		}
		else 
		{
			echo '<div class="updated"><p>'.__("Saved successfully!","AutoBlogRoll").'</p></div>';
		}
     }
     $ab_n=get_option("ab_pr");
	 $ab_n=$ab_n>11?10:$ab_n;	
?>	
<div class="wrap">
<h3><a href="<?=$_SERVER["PHP_SELF"]?>?page=auto-blogroll/auto-blogroll.php"><? _e("ManageLinks","AutoBlogRoll");?></a> | <a href="<?=$_SERVER["PHP_SELF"]?>?page=auto-blogroll/auto-blogroll.php&cm=editlink&vid="><? _e("AddLink","AutoBlogRoll");?></a> | <a href="<?=$_SERVER["PHP_SELF"]?>?page=auto-blogroll/auto-blogroll.php&cm=setting"><font color="Red"><? _e("GeneralSetting","AutoBlogRoll");?></font></a> | <a href="<?=$_SERVER["PHP_SELF"]?>?page=auto-blogroll/auto-blogroll.php&cm=install"><? _e("Install","AutoBlogRoll");?></a> | <a href="<?=get_permalink(get_option("ab_linkpageid"))?>" target="_blank"><? _e("Preview","AutoBlogRoll");?></a></h3>
<table width="100%" border="0" cellpadding="3">
<tr>
<td valign="top">
<? ab_language("setting"); ?>
		<form name="updateoption" method="post" action="<?=$_SERVER["PHP_SELF"]?>?page=auto-blogroll/auto-blogroll.php&cm=setting">
		<input type="hidden" name="flag" value="ab_general">		
		<table class="form-table">
			<tr>
           		<th nowrap><? _e("Total of links:","AutoBlogRoll");?></th>
           		<td>
           		  <input type="text" name="ab_n" value="<?=get_option('ab_n')==""?"10":get_option('ab_n')?>"> 
				 </td>
			</tr>
			<tr>
           		<th nowrap><? _e("Exchange page same as site URL:","AutoBlogRoll");?></th>
           		<td>
           		<input type="radio" name="ab_urlisreurl" value="0" <?=get_option('ab_urlisreurl')==0?" checked=\"checked\"":""?>>Yes
				<input type="radio" name="ab_urlisreurl" value="1" <?=get_option('ab_urlisreurl')==1?" checked=\"checked\"":""?>>No
  				</td>
			</tr>
			<tr>
           		<th nowrap><? _e("PR needed:","AutoBlogRoll");?></th>
           		<td>
	           		<input type="radio" name="ab_usepr" value="1" <?=get_option('ab_usepr')==1?" checked=\"checked\"":""?>><? _e("Check according to PR","AutoBlogRoll");?>
					<input type="radio" name="ab_usepr" value="0" <?=get_option('ab_usepr')==0?" checked=\"checked\"":""?>><? _e("Not according to PR","AutoBlogRoll");?><br>

				 	<input type="text" name="ab_pr" value="<?=get_option('ab_pr')==""?"0":get_option('ab_pr')?>"><? _e("Minimal of PR","AutoBlogRoll");?>
				 </td>
			</tr>
			
			<tr>
           		<th nowrap><? _e("Alexa needed (coding...):","AutoBlogRoll");?></th>
           		<td>
           		<input disabled type="radio" name="ab_usealexa" value="1" <?=get_option('ab_usealexa')==1?" checked=\"checked\"":""?>><? _e("Check according to Alexa","AutoBlogRoll");?>
				<input disabled type="radio" name="ab_usealexa" value="0" <?=get_option('ab_usealexa')==0?" checked=\"checked\"":""?>><? _e("Not according to Alexa","AutoBlogRoll");?><br>
  
           		<input disabled type="text" name="ab_alexa" value="<?=get_option('ab_alexa')==""?"100000":get_option('ab_alexa')?>"><? _e("Maximal of Alexa","AutoBlogRoll");?> 
				 </td>
			</tr>
			<tr>
           		<th nowrap><? _e("Links need Approved to show:","AutoBlogRoll");?></th>
           		<td>
           		<input type="radio" name="ab_approved" value="1" <?=get_option('ab_approved')==1?" checked=\"checked\"":""?>>Yes
				<input type="radio" name="ab_approved" value="0" <?=get_option('ab_approved')==0?" checked=\"checked\"":""?>>No
  				</td>
			</tr>
			<tr>
           		<th nowrap><? _e("Check you link form exchange page daily :","AutoBlogRoll");?></th>
           		<td>
           		<input type="radio" name="ab_checkdaily" value="1" <?=get_option('ab_checkdaily')==1?" checked=\"checked\"":""?>>Yes
				<input type="radio" name="ab_checkdaily" value="0" <?=get_option('ab_checkdaily')==0?" checked=\"checked\"":""?>>No
  				</td>
			</tr>
			<tr>
           		<th nowrap><? _e("Only display on Home :","AutoBlogRoll");?></th>
           		<td>
           		<input type="radio" name="ab_onlydisplayonhome" value="1" <?=get_option('ab_onlydisplayonhome')==1?" checked=\"checked\"":""?>>Yes
				<input type="radio" name="ab_onlydisplayonhome" value="0" <?=get_option('ab_onlydisplayonhome')==0?" checked=\"checked\"":""?>>No
  				</td>
			</tr>
			<tr>
           		<th nowrap><? _e("Invalid checking your URL count :","AutoBlogRoll");?></th>
           		<td>
           		<input type="text" name="ab_nolinknum" value="<?=get_option('ab_nolinknum')==""?"5":get_option('ab_nolinknum')?>"> 
  				<br><? _e("If the failure times in checking your link form the exchange page be over this number , he's link will no displaying on you exchange page.","AutoBlogRoll");?>
			</td>
			</tr>
			<tr>
           		<th nowrap><? _e("Title of exchange link page:","AutoBlogRoll");?></th>
           		<td>
           		<input type="text" style="width:98%;" name="ab_intitle" value="<?=get_option('ab_intitle')==""?_e("Apply to exchange link","AutoBlogRoll"):get_option('ab_intitle')?>"> 
			</td>
			</tr>
			<tr>
           		<th nowrap><? _e("My blog's logo file URL:","AutoBlogRoll");?></th>
           		<td>
           		<input type="text" name="ab_logourl" value="<?=get_option('ab_logourl')==""?"http://":get_option('ab_logourl')?>"> 
			</td>
			</tr>
			<tr>
           		<th nowrap><? _e("Support askie:","AutoBlogRoll");?></th>
           		<td>
           		<input type="radio" name="ab_helpaskie" value="1" <?=get_option('ab_helpaskie')==1?" checked=\"checked\"":""?>>Yes(Give askie a link!)
				<input type="radio" name="ab_helpaskie" value="0" <?=get_option('ab_helpaskie')==0?" checked=\"checked\"":""?>>No
  				</td>
			</tr>
			<tr>
           		<th><? _e("Synchronous working with wordpress database:","AutoBlogRoll");?></th>
           		<td>
           		<? _e("import","AutoBlogRoll");?>: <input type="radio" name="ab_backup2wp" value="1" <?=get_option('ab_backup2wp')==1?" checked=\"checked\"":""?>>Yes
				<input type="radio" name="ab_backup2wp" value="0" <?=get_option('ab_backup2wp')==0?" checked=\"checked\"":""?>>No<br>

				<? _e("delete","AutoBlogRoll");?> : <input type="radio" name="ab_delete2wp" value="1" <?=get_option('ab_delete2wp')==1?" checked=\"checked\"":""?>>Yes
				<input type="radio" name="ab_delete2wp" value="0" <?=get_option('ab_delete2wp')==0?" checked=\"checked\"":""?>>No
  				</td>
			</tr>	
			<tr>
           		<th nowrap><? _e("Exchange term:","AutoBlogRoll");?></th>
           		<td>
           		<?
					$term=stripslashes(get_option("ab_term"));
				?>
           		<textarea name="ab_term" style="width:400px;height:300px;"><?=$term?></textarea><br>
				[limitpr],[limitalexa],[myblogname],[myblogurl],[myblogdescription],[mybloglogourl]
				<div>
				<?
					$ckeys=array("[limitpr]","[limitalexa]","[myblogname]","[myblogurl]","[myblogdescription]","[mybloglogourl]");
					$cvalue=array(get_option("ab_pr"),get_option("ab_alexa"),get_option("blogname"),get_option("siteurl"),get_option("blogdescription"),get_option("ab_logourl"));
					$term=str_replace($ckeys,$cvalue,$term);
					echo $term;
				?>
				</div>

           		</td>
			</tr>		
		</table>
	<p><div class="submit"><input type="submit" name="update_rp" value="<?php _e('Save!', 'update_rp') ?>"  style="font-weight:bold;" /></div></p>
	</form> 		
</td>
<td valign="top" width="250">
<?ab_sidebar();?>
</td>
</tr>
</table>	
</div>
<?php }
//管理链接
function ab_managelistlinks()
{	
	if (date("Y-m-d")<>get_option("ab_vesionsupdatetime")) 
	{
		ab_versionCheck();
	}
	if ($_GET['cm']=="setting") 
	{
		ab_generalsetting();
		return ;
	}
	if ($_GET['cm']=="changelanguage") 
	{
		update_option("ab_language",$_POST["ab_language"]);
		echo '<div class="updated"><p>'.__("Language change successfully!","AutoBlogRoll").'</p></div>
		<script language="javascript">
setTimeout("window.open(\''.$_SERVER["PHP_SELF"].'?page=auto-blogroll/auto-blogroll.php&cm='.$_GET["backcmd"].'\',\'_self\');",1000);
</script>';
		return ;
	}
	if ($_GET['cm']=="editlink") 
	{
		ab_editlink();
		return ;
	}
	if ($_GET['cm']=="doinstall") 
    {
	  	if ($_GET["subcm"]=="inportblogroll") 
	  	{
	  		echo '<div class="updated"><p>'.ab_inportblogroll().'</p></div>';
	  	}
	  	else 
	  	{
	  		$gb = ab_setup();
			$gb.="<br>".ab_inportblogroll();
			echo '<div class="updated"><p>'.$gb.'</p></div>';
	  	}
	}
	if ($_GET['cm']=="install" or get_option("ab_linkpageid")=="") 
	{
		ab_install();
		return ;
	}
	//managelinks
	if ($_POST['flag']=="ab_doeditlink")
	{
		echo '<div class="updated"><p>'.ab_doeditlink().'</p></div>';
	}
	if ($_GET['subcm']=="save")
	{
		echo '<div class="updated"><p>'.ab_dolinksmanage().'</p></div>
		<script language="javascript">
setTimeout("window.open(\''.$_SERVER["PHP_SELF"].'?page=auto-blogroll/auto-blogroll.php\',\'_self\');",1);
</script>';
		return ;
	}
	if ($_GET['subcm']=="delete")
	{
		echo '<div class="updated"><p>'.ab_dolinksdelete().'</p></div>';
	}
	if ($_GET['subcm']=="checklinks")
	{
		ab_CheckSomeLinks();
		echo '<div class="updated"><p>'.__("Checked successfully!","AutoBlogRoll").'</p></div>';
	}
?>	
<div class="wrap">
<script>
function checkboxselect(itemname,checkstatus)
 {
	 if(!itemname) return;
	 
	 if(!itemname.length)
	 {
	 	itemname.checked=checkstatus;
	 }
	 else
	 {
	 	for(var i=0; i<itemname.length; ++i)
		 {
		 itemname[i].checked=checkstatus;
		 }
	 }
	 
	 var sel = document.getElementsByName("all_sel");
	 for(var i=0; i<sel.length; ++i)
	 {
	 	sel[i].checked = checkstatus;
	 }
 }
 function Allcheckboxselect(itemname)
 {
     var n=0;
	 var sel = document.getElementsByName("all_sel");
		
	 if(!itemname) return;
	 if(!itemname.length)
	 {
	 	sel.checked=itemname.checked;
	 }
	 else
	 {
	 	for(var i=0;i<itemname.length;++i)
		 {
		 	if(itemname[i].checked==true)
			{
				n=n+1;
			}
		 }
	 }

	 if(n==itemname.length)
		 for(var i=0; i< sel.length; ++i)
		 {
			sel[i].checked = true;
		 }
	 else
		for(var i=0; i< sel.length; ++i)
		 {
			sel[i].checked = false;
		 }
 }
function DeleteLinks()
{
	var ff= document.getElementById('managelinksform');
	ff.action="<?=$_SERVER["PHP_SELF"]?>?page=auto-blogroll/auto-blogroll.php&subcm=delete";
	ff.submit();
	return true;
}
function CheckingLinks()
{
	var ff= document.getElementById('managelinksform');
	ff.action="<?=$_SERVER["PHP_SELF"]?>?page=auto-blogroll/auto-blogroll.php&subcm=checklinks";
	ff.submit();
	return true;
}
</script>

<style>
/* tables */
table.tablesorter {
	font-family:arial;
	background-color: #CDCDCD;
	margin:10px 0pt 15px;
	font-size: 12px;
	width: 100%;
	text-align: left;
}
table.tablesorter thead tr th, table.tablesorter tfoot tr th {
	background-color: #e6EEEE;
	border: 1px solid #FFF;
	font-size: 12px;
	padding: 4px;
}
table.tablesorter thead tr td, table.tablesorter tfoot tr td {
	background-color: #e6EEEE;
	border: 1px solid #FFF;
	padding: 4px;
}
table.tablesorter thead tr .header {
	background-image: url(<?=ab_getpluginUrl()?>tablesorter/bg.gif);
	background-repeat: no-repeat;
	background-position: center right;
	cursor: pointer;
}
table.tablesorter tbody td {
	color: #3D3D3D;
	padding: 4px;
	background-color: #FFF;
	vertical-align: top;
}
table.tablesorter tbody tr.odd td {
	background-color:#F0F0F6;
}
table.tablesorter thead tr .headerSortUp {
	background-image: url(<?=ab_getpluginUrl()?>tablesorter/asc.gif);
}
table.tablesorter thead tr .headerSortDown {
	background-image: url(<?=ab_getpluginUrl()?>tablesorter/desc.gif);
}
table.tablesorter thead tr .headerSortDown, table.tablesorter thead tr .headerSortUp {
background-color: #8dbdd8;
}
</style>
<script type="text/javascript" src="<?=ab_getpluginUrl()?>tablesorter/jquery.tablesorter.min.js"></script>
<script>
jQuery(document).ready(function() 
    { 
        jQuery("#mylinkslist").tablesorter({ 
        headers: { 
		            0: { sorter: false }
        		 }
    	}); 
    } 
); 
</script>
<h3><a href="<?=$_SERVER["PHP_SELF"]?>?page=auto-blogroll/auto-blogroll.php"><font color="Red"><? _e("ManageLinks","AutoBlogRoll");?></font></a> | <a href="<?=$_SERVER["PHP_SELF"]?>?page=auto-blogroll/auto-blogroll.php&cm=editlink&vid="><? _e("AddLink","AutoBlogRoll");?></a> | <a href="<?=$_SERVER["PHP_SELF"]?>?page=auto-blogroll/auto-blogroll.php&cm=setting"><? _e("GeneralSetting","AutoBlogRoll");?></a> | <a href="<?=$_SERVER["PHP_SELF"]?>?page=auto-blogroll/auto-blogroll.php&cm=install"><? _e("Install","AutoBlogRoll");?></a> | <a href="<?=get_permalink(get_option("ab_linkpageid"))?>" target="_blank"><? _e("Preview","AutoBlogRoll");?></a></h3>
<table width="100%" border="0" cellpadding="3">
<tr>
<td valign="top">
		<form id="managelinksform" name="managelinksform" method="post" action="<?=$_SERVER["PHP_SELF"]?>?page=auto-blogroll/auto-blogroll.php&subcm=save">
		<input type="hidden" name="flag" value="ab_dolinksmanage">		
<table id="mylinkslist" class="widefat">
<thead>
	<tr>
		<th scope="col" class="check-column"><input type="checkbox" name=all_sel value="" onClick="checkboxselect(document.getElementsByName('vid[]'), checked);"></th>
		<th scope="col"><? _e("date","AutoBlogRoll");?></th>
		<th scope="col"><? _e("link","AutoBlogRoll");?></th>
		<th scope="col"><? _e("logo","AutoBlogRoll");?></th>
		<th scope="col"><? _e("categorize","AutoBlogRoll");?></th>
		<th scope="col" class="num"><? _e("PR","AutoBlogRoll");?></th>
		<th scope="col"><? _e("display","AutoBlogRoll");?></th>
		<th scope="col"><? _e("check","AutoBlogRoll");?></th>
		<th scope="col"><? _e("invalidation","AutoBlogRoll");?></th>
		<th scope="col"><? _e("operate","AutoBlogRoll");?></th>
		<th scope="col"><? _e("sort","AutoBlogRoll");?></th>
	</tr>
</thead>
<tbody>
<?
$DB=new ab_DB();
$data=$DB->select();
foreach ($data as $key=>$link) 
{
$highlight=(int)$link["nolinknum"]>=get_option("ab_nolinknum")?'style="background-color: #FFECB3;"':"";
?>
<tr class="alternate author-self status-publish" <?=$highlight?> valign="top">

	<td valign="middle"><input name="vid[]" value="<?=$key?>" type="checkbox" onClick="Allcheckboxselect(document.getElementsByName('vid[]'));"></th>
	<td><abbr title="<?=date("Y-m-d H:i:s",$link["id"])?>"><?=date("Y-m-d",$link["id"])?></abbr></td>
	<td><strong><a class="row-title" href="<?=$link["url"]?>" title="Last Check:<?=date("Y-m-d H:i:s",$link["checktime"])?>" target="_blank"><?=$link["title"]?></a></strong></td>
	<td><? if(!($link["logourl"]=="http://" or $link["logourl"]=="")) echo '<img src="'.$link["logourl"].'" width="88" height="31">';?></td>
	<td><input type="text" name="cat[<?=$key?>]" value="<?=$link["cat"]?>"  style="width:36px;"></td>
	<td class="num"><?=$link["pr"]?></td>
	<td><input type="checkbox" value="0" name="approved[<?=$key?>]" <?=$link["approved"]==0?"checked":""?>></td>
	<td><input type="checkbox" value="1" name="check[<?=$key?>]" <?=$link["check"]==1?"checked":""?>></td>
	<td class="num"><a href="<?=$_SERVER["PHP_SELF"]?>?page=auto-blogroll/auto-blogroll.php&cmd=listcheckrecord&url=<?=$link["url"]?>" target="_blank"><?=(int)$link["nolinknum"]?></a></td>
	<td><a href="<?=$_SERVER["PHP_SELF"]?>?page=auto-blogroll/auto-blogroll.php&cm=editlink&vid=<?=$key?>"><? _e("edit","AutoBlogRoll");?></a></td>
	<td><input type="text" name="order[<?=$key?>]" value="<?=$link["order"]?>" style="width:22px;"></td>

</tr>
<?}?>
</tbody>		
</table>
<?=str_replace("[siteurl]",get_option("siteurl"),__("<ul><li>display=whether to displaying the link</li><li>check=whether checking your link form the site</li><li>invalidation=invalidation times of checking your link</li><li>add <a href=\"[siteurl]/wp-admin/widgets.php\">widget</a> to you sidebar！</li></ul>","AutoBlogRoll"));?>
	<p><div class="submit">
	<input type="submit" name="update_rp" value="<? _e("save","AutoBlogRoll");?>"  style="font-weight:bold;" />
	<input name="bt_check" type="button" value="<? _e("check","AutoBlogRoll");?>"  onClick="CheckingLinks();">
	<font color="White">---------------------------------------------------</font>
	<input name="bt_delete" type="button" value="<? _e("delete","AutoBlogRoll");?>"  onClick="if(confirm('delete the links?')) DeleteLinks();">
	</div>
	</p>
	</form> 		
</td>
<td valign="top" width="250">
<?ab_sidebar();?>
</td>
</tr>
</table>	
</div>
<?php }
//编辑链接
function ab_editlink()
{	
	$DB=new ab_DB();
	$link=$DB->select($_GET["vid"]);
?>	
<div class="wrap">
<h3><a href="<?=$_SERVER["PHP_SELF"]?>?page=auto-blogroll/auto-blogroll.php"><? _e("ManageLinks","AutoBlogRoll");?></a> | <a href="<?=$_SERVER["PHP_SELF"]?>?page=auto-blogroll/auto-blogroll.php&cm=editlink&vid="><font color="Red"><? _e("AddLink","AutoBlogRoll");?></font></a> | <a href="<?=$_SERVER["PHP_SELF"]?>?page=auto-blogroll/auto-blogroll.php&cm=setting"><? _e("GeneralSetting","AutoBlogRoll");?></a> | <a href="<?=$_SERVER["PHP_SELF"]?>?page=auto-blogroll/auto-blogroll.php&cm=install"><? _e("Install","AutoBlogRoll");?></a> | <a href="<?=get_permalink(get_option("ab_linkpageid"))?>" target="_blank"><? _e("Preview","AutoBlogRoll");?></a></h3>
<table width="100%" border="0" cellpadding="3">
<tr>
<td valign="top">
		<form name="editlinsform" method="post" action="<?=$_SERVER["PHP_SELF"]?>?page=auto-blogroll/auto-blogroll.php">
		<input type="hidden" name="flag" value="ab_doeditlink">		
		<input type="hidden" name="vid" value="<?=$_GET["vid"]?>">
		<table class="form-table">
			<tr>
           		<th nowrap><? _e("Webmaster","AutoBlogRoll");?></a>：</th>
           		<td>
           		  <input type="text" name="webmaster" value="<?=$link["webmaster"]?>" style="width:400px;"> 
				 </td>
			</tr>
			<tr>
           		<th nowrap><? _e("Email","AutoBlogRoll");?></a>：</th>
           		<td>
           		  <input type="text" name="email" value="<?=$link["email"]?>" style="width:400px;"> 
				 </td>
			</tr>
			<tr>
           		<th nowrap><? _e("Site name","AutoBlogRoll");?></a>：</th>
           		<td>
           		  <input type="text" name="title" value="<?=$link["title"]?>" style="width:400px;"> 
				 </td>
			</tr>
			<tr>
           		<th nowrap><? _e("Site URL","AutoBlogRoll");?></a>：</th>
           		<td>
           		  <input type="text" name="url" value="<?=$link["url"]?>" style="width:400px;"> 
				 </td>
			</tr>
			<tr>
           		<th nowrap><? _e("Exchange link page","AutoBlogRoll");?></a>：</th>
           		<td>
           		  <input type="text" name="reurl" value="<?=$link["reurl"]?>" style="width:400px;"> 
				 </td>
			</tr>
			<tr>
           		<th nowrap><? _e("Logo URL","AutoBlogRoll");?></a>：</th>
           		<td>
           		  <input type="text" name="logourl" value="<?=$link["logourl"]?>" style="width:400px;"> 
				 </td>
			</tr>
			<tr>
           		<th nowrap><? _e("Description","AutoBlogRoll");?></a>：</th>
           		<td>
           		  <input type="text" name="description" value="<?=$link["description"]?>" style="width:400px;"> 
				 </td>
			</tr>		
		</table>
	<p><div class="submit"><input type="submit" name="update_rp" value="<?php _e('Save!', 'update_rp') ?>"  style="font-weight:bold;" /></div></p>
	</form>		
</td>
<td valign="top" width="250">
<?ab_sidebar();?>
</td>
</tr>
</table>	
</div>
<?php }
//侧边栏
function ab_sidebar()
{
	?>
	<b>About：</b><br>
	<p align="left">
	<?=ab_showversionstring()?>
	<ul>
	<li><a href="http://fairyfish.net/2008/06/27/wordpress-seo-plugin-for-chine/" target="_blank">WordPress中文SEO插件</a><br></li>
	<li>Askie's home page <a href="http://www.pkphp.com/" target="_blank">PKPHP.com</a>!</li>
	</ul>	
	</p>
	<hr>
	<b>Support<a href="http://www.pkphp.com/" target="_blank">askie</a>：</b><br>
	<p align="center" style="vertical-align: middle;">
    <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
		<input type="hidden" name="cmd" value="_s-xclick">
		<input type="image" src="https://www.paypal.com/zh_XC/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
		<img alt="" border="0" src="https://www.paypal.com/zh_XC/i/scr/pixel.gif" width="1" height="1">
		<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----">
	</form>
	</p>
	<?
}
//获取插件URL
function ab_getpluginUrl()
{
	$path = dirname(__FILE__);
	$path = str_replace("\\","/",$path);
	$path = trailingslashit(get_bloginfo('wpurl')) . trailingslashit(substr($path,strpos($path,"wp-content/")));
	return $path;
}
function ab_addpages() 
{
    add_options_page('AutoBlogroll', 'AutoBlogroll', 8, __FILE__, 'ab_managelistlinks');
}
function ab_viladUrl($url)
{
	if (preg_match("/(http:\/\/+[\w\-]+\.[\w\-]+)/i",$url))
    {
        return true;
    }
    else 
    {
    	return false;
    }
}
function ab_msg($str)
{
?>
<div style="border:1px dashed #F60;color:#000;background-color: #FFF4CA;margin-bottom: 0px;padding-top: 0px;padding-right: 0px;padding-bottom: 0px;padding-left: 15px;padding-right: 15px;">
<p>
<?
echo $str;
echo "</p></div>";
}
//输出资助链接表单
function ab_addlinkform()
{
	echo ab_listAllLinks();
	$DB=new ab_DB();
	$allLinks=$DB->select();
	if (count($allLinks)>=get_option("ab_n")) 
	{
		ab_msg(__("The total number of links to the ceiling!","AutoBlogRoll"));
		return ;
	}
	$term=stripslashes(get_option("ab_term"));
	$ckeys=array("[pr]","[alexa]","[limitpr]","[limitalexa]","[myblogname]","[myblogurl]","[myblogdescription]","[mybloglogourl]");
	$cvalue=array(get_option("ab_usepr"),get_option("ab_usealexa"),get_option("ab_pr"),get_option("ab_alexa"),get_option("blogname"),get_option("siteurl"),get_option("blogdescription"),get_option("ab_logourl"));
	$term=str_replace($ckeys,$cvalue,$term);
?>
<div style="background-color: #EEEEEE;
	border-top-width: 1px;
	border-bottom-width: 1px;
	border-top-style: solid;
	border-right-style: none;
	border-bottom-style: solid;
	border-left-style: none;
	border-top-color: #666666;
	border-bottom-color: #666666;
	padding-right: 5px;
	padding-left: 5px;
	padding-top: 3px;
	padding-bottom: 3px;
	text-align: left;">
<form action="#addlinkform" method="post" name="addlinkform">
<a name="addlinkform" id="addlinkform"></a>
<?=ab_addlink();?>
<?=$term;?>
<table border="0">
<tbody>
<tr>
	<td><b><? _e("Webmaster","AutoBlogRoll");?>:</b></td>
	<td><input name="webmaster" maxlength="50" value="<?=$_POST["webmaster"]?>" size="25" type="text"> (<font color="Red">*</font>)</td>
</tr>
<tr>
	<td><b><? _e("Email","AutoBlogRoll");?>:</b></td>
	<td><input name="email" maxlength="50" value="<?=$_POST["email"]?>" size="25" type="text"> (<font color="Red">*</font>)</td>
</tr>
<tr>
	<td><b><? _e("Site name","AutoBlogRoll");?>:</b></td>
	<td><input name="title" maxlength="50" value="<?=$_POST["title"]?>" size="25" type="text"> (<font color="Red">*</font>)</td>
</tr>
<tr>
	<td><b><? _e("Site URL","AutoBlogRoll");?>:</b></td>
	<td><input name="url" maxlength="100" value="<?=$_POST["url"]==""?"http://":$_POST["url"]?>" size="25" type="text">
(<font color="Red">*</font>)<? if ((get_option("ab_usepr")==1 or get_option("ab_usealexa")==1) and get_option("ab_urlisreurl")<>1) 
{
	if (get_option("ab_usepr")==1) $x[]="<font color=red>PR≥".get_option("ab_pr")."</font>";
	if (get_option("ab_usealexa")==1) $x[]="<font color=red>Alexa≤".get_option("ab_alexa")."</font>";
	echo "".implode("、",$x);
} ?></td>
</tr>
<?if (get_option('ab_urlisreurl')==1) 
{
?>
<tr>
	<td valign="top"><b><? _e("Exchange link page","AutoBlogRoll");?>:</b></td>
	<td><input name="reurl" maxlength="100" value="<?=$_POST["reurl"]==""?"http://":$_POST["reurl"]?>" size="25" type="text"> (<font color="Red">*</font>)<? if (get_option("ab_usepr")==1) 
{
	if (get_option("ab_usepr")==1) $v="<font color=red>PR≥".get_option("ab_pr")."</font>";
	echo "<br /><font color=red>".__("host be same as siteURL","AutoBlogRoll")."</font> & ".$v;
} ?>
	</td>
</tr>
<?
}?>
<tr>
	<td><b><? _e("Logo URL","AutoBlogRoll");?>:</b></td>
	<td><input name="logourl" maxlength="100" value="<?=$_POST["logourl"]==""?"http://":$_POST["logourl"]?>" size="25" type="text"></td>
</tr>
<tr>
	<td><b><? _e("Description","AutoBlogRoll");?>:</b></td>
	<td><input name="description" maxlength="200" value="<?=$_POST["description"]?>" size="25" type="text"></td>
</tr>
</tbody></table>

<p>
<input value=" add " type="submit">
</p>
</form>

</div>
<?php
}
//添加链接
function ab_addlink()
{
	global $id;
	$DB=new ab_DB();
	$allLinks=$DB->select();
	if (count($allLinks)>=get_option("ab_n")) 
	{
		ab_msg(__("The total number of links to the ceiling!","AutoBlogRoll"));
		return ;
	}
	
	if (!isset($_POST["url"])) return ;
	if (get_option("ab_urlisreurl")<>1) 
	{
		$_POST["reurl"]=$_POST["url"];
	}	
	$forbidkeys=array("webmaster"=>__("Webmaster","AutoBlogRoll"),"email"=>__("Email","AutoBlogRoll"),"title"=>__("Site name","AutoBlogRoll"),"url"=>__("Site URL","AutoBlogRoll"),"reurl"=>__("Exchange link page","AutoBlogRoll"));
	foreach ($_POST as $key=>$value) 
	{
		$value=trim($value);
		if (key_exists($key,$forbidkeys)) 
		{
			if ($value=="") 
			{
				$error[]=$forbidkeys[$key];
			}
		}
		if (is_array($error)) 
		{
			ab_msg(implode(",",$error).__(" must be added","AutoBlogRoll"));
			return ;
		}
	}
	//判断数据合法性
	if (!(ab_viladUrl($_POST["url"]) and ab_viladUrl($_POST["reurl"]))) 
	{
		ab_msg(__("Site URL ro Exchange link page is invalid!","AutoBlogRoll"));
		return ;
	}
	//判断邮件合法性
	if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$_POST["email"]))
    {
        ab_msg(__("Email is invalid!","AutoBlogRoll"));
		return ;
    }
	//判断网站网址与交换链接网址是否同域名
	$yurl=parse_url($_POST["url"]);
	$yrurl=parse_url($_POST["reurl"]);
	if ($yurl["host"]<>$yrurl["host"]) 
	{
		ab_msg(__("Site URL ro Exchange link page is not one same host!","AutoBlogRoll"));
		return ;
	}
	//判断网址是否已经交换链接
	if (count($DB->select(array("url"=>$_POST["url"])))>0) 
	{
		ab_msg(__("Link allready exsit!","AutoBlogRoll"));
		return ;
	}
	//检测对方网页是否已经放置本站链接
	$linkcheck=ab_CheckLinkRormUrl($_POST["reurl"]);
	if ($linkcheck==="a") 
	{
		ab_msg(__("Your site have set nofollow or noindex!","AutoBlogRoll"));
		return ;
	}
	elseif (($linkcheck==="b")) 
	{
		ab_msg(__("Can not find my site URL in your page!","AutoBlogRoll"));
		return ;
	}
	
	$data=array(
			"url"			=>$_POST["url"],
			"title"			=>$_POST["title"],
			"description"	=>$_POST["description"],
			"logourl"		=>$_POST["logourl"],
			"reurl"			=>$_POST["reurl"],
			"webmaster"		=>$_POST["webmaster"],
			"email"			=>$_POST["email"],
			"approved"		=>get_option("ab_approved"),
			"check"			=>get_option("ab_checkdaily"),
			"order"			=>0,
				);
	//根据PR和Alexa判断链接
	$Pr=new AB_PageRank();
	$xpr=$Pr->printrank($_POST["reurl"]);
	$data["pr"]=$xpr;
	if (get_option("ab_usepr")) 
	{
		if ($xpr<get_option("ab_pr")) 
		{
			ab_msg(__("Your page PR so little!","AutoBlogRoll"));
			return ;
		}
	}
/*	$Alexa=new AB_Alexa();
	$xalexa=$Alexa->threeMothAlexa($_POST["reurl"]);
	$xalexa=str_replace("--","10000000000",str_replace(",","",$xalexa));
	$data["alexa"]=$xalexa;
	if (get_option("ab_usealexa")) 
	{
		if ($xalexa>get_option("ab_alexa")) 
		{
			ab_msg("对不起，你的网站Alexa({$xalexa})大于".get_option("ab_alexa")."，无法交换链接！");
			return ;
		}
	}*/
				
	$DB=new ab_DB();
	if ($DB->update($data)) 
	{
		ab_add2wp($data);
		$_POST=array();
	}
	if ($data["approved"]==1) 
	{
		$msg=__("Your link need be appoved to display!","AutoBlogRoll");
	}
	ab_msg(__("Exchang successfully!","AutoBlogRoll").$msg.__(" refresh page now!","AutoBlogRoll").'<a href="'.get_permalink($id).'"> Click here</a>......	
<script language="javascript">
setTimeout("window.open(\''.get_permalink($id).'\',\'_self\');",1000);
</script>');	
	return ;			
}
function ab_apply()
{
	global $id;
	
	$DB=new ab_DB();
	$allLinks=$DB->select();
	if (count($allLinks)>=get_option("ab_n")) 
	{
		echo (__("The total number of links to the ceiling!","AutoBlogRoll")."[autobloglink.com]");
		exit();
	}
	
	if (!isset($_POST["url"])) return ;
	if (get_option("ab_urlisreurl")<>1) 
	{
		$_POST["reurl"]=$_POST["url"];
	}	
	$forbidkeys=array("webmaster"=>__("Webmaster","AutoBlogRoll"),"email"=>__("Email","AutoBlogRoll"),"title"=>__("Site name","AutoBlogRoll"),"url"=>__("Site URL","AutoBlogRoll"),"reurl"=>__("Exchange link page","AutoBlogRoll"));
	foreach ($_POST as $key=>$value) 
	{
		$value=trim($value);
		if (key_exists($key,$forbidkeys)) 
		{
			if ($value=="") 
			{
				$error[]=$forbidkeys[$key];
			}
		}
		if (is_array($error)) 
		{
			ab_msg(implode(",",$error).__(" must be added","AutoBlogRoll")."[autobloglink.com]");
			return ;
		}
	}
	//判断数据合法性
	if (!(ab_viladUrl($_POST["url"]) and ab_viladUrl($_POST["reurl"]))) 
	{
		echo (__("Site URL ro Exchange link page is invalid!","AutoBlogRoll")."[autobloglink.com]");
		exit();
	}
	//判断邮件合法性
	if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$_POST["email"]))
    {
        echo (__("Email is invalid!","AutoBlogRoll")."[autobloglink.com]");
		exit();
    }
	//判断网站网址与交换链接网址是否同域名
	$yurl=parse_url($_POST["url"]);
	$yrurl=parse_url($_POST["reurl"]);
	if ($yurl["host"]<>$yrurl["host"]) 
	{
		echo (__("Site URL ro Exchange link page is not one same host!","AutoBlogRoll")."[autobloglink.com]");
		exit();
	}
	//判断网址是否已经交换链接
	if (count($DB->select(array("url"=>$_POST["url"])))>0) 
	{
		echo ("[ok]".__("Link allready exsit!","AutoBlogRoll")."[autobloglink.com]");
		exit();
	}
	//检测对方网页是否已经放置本站链接
	$linkcheck=ab_CheckLinkRormUrl($_POST["reurl"]);
	if ($linkcheck==="a") 
	{
		echo (__("Your site have set nofollow or noindex!","AutoBlogRoll")."[autobloglink.com]");
		exit();
	}
	elseif (($linkcheck==="b")) 
	{
		echo (__("Can not find my site URL in your page!","AutoBlogRoll")."[autobloglink.com]");
		exit();
	}
	
	$data=array(
			"url"			=>$_POST["url"],
			"title"			=>$_POST["title"],
			"description"	=>$_POST["description"],
			"logourl"		=>$_POST["logourl"],
			"reurl"			=>$_POST["reurl"],
			"webmaster"		=>$_POST["webmaster"],
			"email"			=>$_POST["email"],
			"approved"		=>get_option("ab_approved"),
			"check"			=>get_option("ab_checkdaily"),
			"order"			=>0,
				);
	//根据PR和Alexa判断链接
	$Pr=new AB_PageRank();
	$xpr=$Pr->printrank($_POST["reurl"]);
	$data["pr"]=$xpr;
	if (get_option("ab_usepr")) 
	{
		if ($xpr<get_option("ab_pr")) 
		{
			echo (__("Your page PR so little!","AutoBlogRoll")."[autobloglink.com]");
			exit();
		}
	}			
	$DB=new ab_DB();
	if ($DB->update($data)) 
	{
		ab_add2wp($data);
		$_POST=array();
	}
	if ($data["approved"]==1) 
	{
		echo "[ok]".__("Your link need be appoved to display!","AutoBlogRoll")."[autobloglink.com]";
		exit();
	}
	echo ("[ok]".__("Exchang successfully!","AutoBlogRoll")."[autobloglink.com]");	
	
	//删除首页缓存
	ab_deleteIndexhtml();
	
	exit();			
}
//管理员编辑链接
function ab_doeditlink()
{
	$DB=new ab_DB();
	
	$data=$DB->select($_POST["vid"]);
	//删除原来的记录
	$DB->delete($data["url"]);
	
	//更新数据
	$data["url"]		=$_POST["url"];
	$data["title"]		=$_POST["title"];
	$data["description"]=$_POST["description"];
	$data["logourl"]	=$_POST["logourl"];
	$data["reurl"]		=$_POST["reurl"];
	$data["webmaster"]	=$_POST["webmaster"];
	$data["email"]		=$_POST["email"];
	
	//PR和Alexa
	$Pr=new AB_PageRank();
	$xpr=$Pr->printrank($_POST["reurl"]);
	$data["pr"]=$xpr;

	/*$Alexa=new AB_Alexa();
	$xalexa=$Alexa->threeMothAlexa($_POST["reurl"]);
	$xalexa=str_replace("--","10000000000",str_replace(",","",$xalexa));
	$data["alexa"]=$xalexa;*/
	
	$DB->update($data);
	ab_add2wp($data);
	
	//删除首页缓存
	ab_deleteIndexhtml();
	
	return $msg.__("Edit successfully!","AutoBlogRoll");
}
//备份增加的链接到wp数据库
function ab_add2wp($LinkData)
{
	if (get_option('ab_backup2wp')<>1) 
	{
		return ;
	}
	global $wpdb;
	
	$url=$LinkData["url"];
	//检查link
	$check = $wpdb->get_var("SELECT link_id FROM $wpdb->links WHERE link_url='{$url}'");
	$link = array( 'link_url' => $url, 'link_name' => $wpdb->escape($LinkData["title"]), 'link_category' => array(get_option('default_link_category')), 'link_description' => $wpdb->escape($LinkData["description"]), 'link_owner' => 1, 'link_image' => $LinkData["logourl"], 'link_visible' => $LinkData["approved"]==0?"Y":"N");
	
	if (!function_exists(wp_update_link)) 
	{
		require_once("wp-admin/includes/bookmark.php");
	}
	if ($check) 
	{
		$link["link_id"]=$check;
		return wp_update_link( $link );
	}
	else 
	{
		return wp_insert_link($link);
	}
}
//对多个链接进行排序、分类操作
function ab_dolinksmanage()
{
	$DB=new ab_DB();
	$links=$DB->select();
	foreach ($links as $key=>$link) 
	{
		$link["cat"]		=$_POST["cat"][$key];
		$link["order"]		=$_POST["order"][$key];
		$link["approved"]	=key_exists($key,(array)$_POST["approved"])	?0:1;
		$link["check"]		=key_exists($key,(array)$_POST["check"])	?1:0;
		$link["nolinknum"]	=key_exists($key,(array)$_POST["approved"])	?0:$link["nolinknum"];
		$DB->update($link);
		ab_add2wp($link);
	}
	return __("Saved successfully! Refreshing now ...","AutoBlogRoll");
}
//删除多个链接
function ab_dolinksdelete()
{
	global $wpdb;
	$DB=new ab_DB();
	if (is_array($_POST["vid"])) 
	{
		foreach ($_POST["vid"] as $key) 
		{
			if (get_option('ab_delete2wp')==1) 
			{
				$link=$DB->select($key);
				$url=$link["url"];
				$id = $wpdb->get_var("SELECT link_id FROM $wpdb->links WHERE link_url='{$url}'");
				wp_delete_link($id);
			}
			
			$DB->delete("",$key);
		}
	}
	
	return __("Deleted successfully!","AutoBlogRoll");
}
//审核链接
function ab_dolinksapprove($approve=0)
{
	$DB=new ab_DB();
	$links=$DB->select();
	if (is_array($_POST["vid"])) 
	{
		foreach ($_POST["vid"] as $key) 
		{
			$links[$key]["approved"]=$approve;
			$DB->update($links[$key]);
		}
	}
	return __("Saved successfully!","AutoBlogRoll");
}
//输出所有链接
function ab_listAllLinks()
{
	$DB=new ab_DB();
	return ab_listlinks($DB->select(array("approved"=>0)));
}
//根据目录输出链接
function ab_blogroll($cat="")
{
	$DB=new ab_DB();
	$cat=(array)$cat;
	if (!in_array("",$cat)) 
	{
		$cat[]="";
	}
	$links=array();
	foreach ($cat as $catname) 
	{
		$catlinks=(array)$DB->select(array("approved"=>0,"cat"=>$catname));
		foreach ($catlinks as $key=>$link) 
		{
			$links[$key]=$link;
		}
	}
	return ab_listlinks($links);
}
//输出链接模板
function ab_listlinks($data)
{
	$z=array();
	foreach ($data as $site) 
	{
		$z[]="<li><a href='{$site["url"]}' title='{$site["description"]}' target='_blank'>{$site["title"]}</a></li>";
	}
	return "<ul>".implode("\r",(array)$z)."</ul>";
}
//从网页检测本站链接是否存在
function ab_CheckLinkRormUrl($url)
{
	$siteurl=get_option("siteurl");
	//检测Url时候在最后添加了/
	$slash=substr($siteurl,-1,1)=="/"?"/":"";
	//如果是本网站链接则停止检测
	$sitehost=parse_url($siteurl);
	$checkhost=parse_url($url);
	if ($sitehost["host"]==$checkhost["host"]) 
	{
		return true;
	}
	
	require_once( ABSPATH . 'wp-includes/class-snoopy.php' );
	$http = new Snoopy();
	$http->agent = MAGPIE_USER_AGENT;
	$http->read_timeout = MAGPIE_FETCH_TIME_OUT;
	$http->use_gzip = MAGPIE_USE_GZIP;
	@$http->fetch($url);
	$file=$http->results;
	
	$urls=ab_GetUrlFromHtml($file);
	$webNofollowCheck=ab_checkHtmlNofollow($file);
	//判断网页nofollow，noindex设置
	if ($webNofollowCheck==1) 
	{
		return "a";
	}
	//判断链接
	foreach ($urls as $url) 
	{
		if ($slash=="") 
		{
			if (substr($url,-1,1)=="/") 
			{
				$url=substr($url,0,-1);
			}
		}
		if ($url==$siteurl) 
		{
			return true;
		}
	}
	return "b";
}
//获取文本中获取链接地址
function ab_GetUrlFromHtml($html)
{
	preg_match_all("'<a[^>]*?>'si",$html,$n);
	$url=array();
	foreach ($n as $m)
	{
		foreach ($m as $x)
		{
			preg_match('#href[[:space:]]*=[[:space:]]*[\'|"]?([[:alnum:]:@/._-]+[?]?[^\'|"]*)"?#ie',$x,$y);
			if (!preg_match('/(noindex|nofollow)(.*)>/siU',$x,$z)) 
			{
				$url[]=$y[1];
			}
		}
	}
	return $url;
}
//检测网页是否设置nofollow
function ab_checkHtmlNofollow($html)
{
	 return preg_match('/<meta([^>]+)(noindex|nofollow)(.*)>/siU',$html,$meta);
}
//检测链接有效性
function ab_CheckSomeLinks()
{
	$DB=new ab_DB();
	$sites=$DB->select();
	foreach ((array)$_POST["vid"] as $key) 
	{
		ab_CheckLink($sites[$key]["url"],true);
	}
}
//例外链接
function ab_pravitelink()
{
	$DB=new ab_DB();
	$sites=$DB->select();
	if (get_option("ab_helpaskie")==1) 
	{
		$pravitelink[]=array(
				"url"		=>"http://www.pkphp.com",
				"reurl"		=>"http://www.pkphp.com",
				"title"		=>"PK with PHP!",
				"approved"	=>"0",
				"check"		=>"0",
				"nolinknum"	=>"0",
			);
	}
	if (get_option("ab_linkpageid")<>"") 
	{
		$pravitelink[]=array(
				"url"		=>get_permalink(get_option("ab_linkpageid")),
				"reurl"		=>get_permalink(get_option("ab_linkpageid")),
				"title"		=>get_option('ab_intitle')==""?__("Apply to exchange link","AutoBlogRoll"): get_option('ab_intitle'),
				"approved"	=>"0",
				"check"		=>"0",
				"nolinknum"	=>"0",
				"order"		=>"99",
		);
	}

	foreach ((array)$pravitelink as $site) 
	{
		if (!(key_exists(md5(base64_encode($site["url"])),$sites) or key_exists(md5(base64_encode($site["url"]."/")),$sites))) 
		{
			$DB->update($site);
			continue;
		}
	}
}
$ab_hell=base64_decode("aHR0cDovL3d3dy5hdXRvYmxvZ2xpbmsuY29t");
//检测链接有效性
function ab_CheckAllLink()
{
	$DB=new ab_DB();
	$sites=$DB->select();
	foreach ($sites as $site) 
	{
		$r=ab_CheckLink($site["url"]);
		if ($r=="done") 
		{
			break;
		}
	}
}
//根据网址检测链接有效性
function ab_CheckLink($url,$force=false)
{
	$DB=new ab_DB();
	$sites=$DB->select(array("url"=>$url));
	foreach ($sites as $site) 
	{
		if ($force==false) 
		{
			if ($site["check"]<>1) 
			{
				continue;
			}
			if ($site["checktime"] != "" and date("Y-m-d",$site["checktime"]) >= date("Y-m-d")) 
			{
				continue;
			}
		}
		
		if (strstr($site["url"],get_option("siteurl")) or strstr($site["url"],"http://www.pkphp.com")) 
		{
			continue;
		}
		$a=ab_CheckLinkRormUrl($site["reurl"]);
		if ($a===true) 
		{
			$site["nolinknum"]=0;
		}
		else 
		{
			$site["nolinknum"]=$site["nolinknum"]+1;
			if ((int)$site["nolinknum"]>=(int)get_option("ab_nolinknum")) 
			{
				$site["approved"]=1;
			}
		}
		//每天保存检测记录
		ab_saveCheckRecord($site["url"],($a===true?1:0));
		//保存记录
		$site["checktime"]=time();
		$DB->update($site,true);
		return "done";
	}
	
	//删除首页缓存
	ab_deleteIndexhtml();
	
	return "none";
}
//存储链接记录
function ab_saveCheckRecord($siteurl,$result)
{
	$record=get_option('ab_checkrecord',array());
	$date=date("Y-m-d");
	$record[$siteurl][$date]=$result;
	return update_option('ab_checkrecord',$record);
}
//输出检测记录
function ab_listCheckRecord()
{
	$record=get_option('ab_checkrecord',array());
	$x=$record[$_GET['url']];
	if (is_array($x)) 
	{
		foreach ($x as $date=>$result) 
		{
			if ($result==0) 
			{
				echo '<font color=red>'.'<li>'.$date.':'.$result.'</li>'.'</font>';
			}
			else 
			{
				echo '<li>'.$date.':'.$result.'</li>';
			}	
		}
	}
	exit();
}
//从服务器端读入链接
function ab_importLinksFromServer()
{
	/*if (get_option("ab_importlink")=="Y") 
	{
		global $ab_hell;
		$parseOpml=new ParseOpml("{$ab_hell}/blog/outlinks/key/".get_option("ab_blogkey"));
		$DB=new ab_DB();
		$allLinks=$DB->select();
		$pr=new AB_PageRank();
		$flag=false;
		foreach ((array)$parseOpml->outline as $link) 
		{
			$data=array(
				"title"			=>$link["text"],
				"url"			=>$link["htmlurl"],
				"description"	=>$link["description"],
				"reurl"			=>$link["htmlurl"],
				"webmaster"		=>$link["webmaster"],
				"email"			=>$link["email"],
				"logourl"		=>$link["logourl"],
				"approved"		=>get_option("ab_approved"),
				"check"			=>get_option("ab_checkdaily"),
				"pr"			=>$pr->printrank($link["htmlurl"]));
				
			$flag=$DB->update($data);
			if ($flag) 
			{
				ab_add2wp($data);
			}
		}
		update_option("ab_importlink","");
	}*/
}
//输出链接opml
function ab_exportLinksOpml()
{
	$parseOpml=new ParseOpml();
	$DB=new ab_DB();
	$cat=(array)get_option('ab_widget_cat');
	$links=array();
	$siteurl=get_option("siteurl");
	foreach ($cat as $catname) 
	{
		$catlinks=(array)$DB->select(array("approved"=>0,"cat"=>$catname));
		foreach ($catlinks as $key=>$link) 
		{
			if (strstr($link["url"],$siteurl)==false) 
			{
				$links[$key]=$link;
			}
		}
	}
	$site["name"]=get_option("blogname");
	$parseOpml->outOpml($site,$links);
	exit();
}
//初始化
function ab_init()
{
	//加载库文件
	include_once('ab_function.php');
	include_once('ab_PageRank.php');
	include_once('ab_Alexa.php');  
	
	$ab_config=array(
		"ab_n"		=>20,
		"ab_pr"		=>0,
		"ab_usepr"	=>1,
		"ab_alexa"	=>1000000,
		"ab_nolinknum"	=>5,
		"ab_helpaskie"	=>1,
		"ab_backup2wp"	=>1,
		"ab_delete2wp"	=>1,
		"ab_checkdaily"	=>1,
		"ab_linkkey"	=>"",
		"ab_term"		=>"<b>交换链接条款：</b>
<ul>
<li><b>本网站名：</b>[myblogname]</li>
<li><b>本站网址：</b>[myblogurl]</li>
<li><b>本站简介：</b>[myblogdescription]</li>
<li><b>本站图标：</b>[mybloglogourl]</li>

<li>交换链接前请先添加本站的链接，否则无法提交表单。</li>
<li>要求交换链接的页面PR大于等于[limitpr]</li>
<li>要求交换链接的页面Alexa小于等于[limitalexa]</li>
<li>以下表单后带<font color=Red>*</font>为必填写项目。</li>
</ul>",
	);
	foreach ($ab_config as $key=>$var) 
	{
		if (get_option($key)=="") 
		{
			update_option($key,$var);
		}
	}
	//激活
	if ($_GET["ab_active"])
	{
		echo get_option("ab_linkkey");
		exit();
	}
	if ($_GET["ab_cmt"]<>"" and $_GET["ab_cmt"]==get_option("ab_blogkey"))
	{
		ab_cmt();
		exit();
	}
	if ($_GET["applylink"]<>"" and $_GET["applylink"]==get_option("ab_blogkey"))
	{
		ab_apply();
		exit();
	}
	//通知从服务器更新申请到的链接
	if ($_GET["ab_importlink"]<>"")
	{
		update_option("ab_importlink","Y");
		ab_importLinksFromServer();
		exit();
	}
	//输出检测记录
	if ($_GET["cmd"]=="listcheckrecord")
	{
		ab_listCheckRecord();
		exit();
	}
	//判断是否需要导入链接
	if (get_option("ab_importlink")=="Y")
	{
		ab_importLinksFromServer();
		exit();
	}
	//输出链接opml
	if ($_GET["exportlinksopml"])
	{
		if($_GET["exportlinksopml"]==get_option("ab_blogkey")) ab_exportLinksOpml();
		exit();
	}
	if (get_option("ab_blogkey")=="") 
	{
		ab_active();
	}
	ab_importLinksFromServer();
	ab_pravitelink();
	
	//每小时检测链接
	$lastchecktime=get_option("ab_lastchecktime");
	if (date("Y-m-d-H",$lastchecktime) != date("Y-m-d-H")) 
	{
		ab_CheckAllLink();
		update_option("ab_lastchecktime",time());
	}
}
add_action('admin_menu', 'ab_addpages');
add_action('init', 'ab_init');
//检测页面是否存在
function ab_checkpage($page)
{
	global $wpdb;
	
	//检查links静态页面是否存在:
	$check = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_status='publish' AND post_name='{$page}'");
	
	//如果links静态页面不存在则创建:
	if ($check) 
	{
		return $check;
	}
	else 
	{
		return false;
	}
}
//给创链接页面添加内容
function ab_outLinksandform($content)
{
	global $id;
	
	//检查links静态页面id:
	$linkspageid = get_option('ab_linkpageid');
	if ($linkspageid==$id) 
	{
		$content=$content.ab_addlinkform();
		return $content;
	}
	else 
	{
		return $content;
	}
}
//输出链接和申请表单
add_filter("the_content","ab_outLinksandform");
//增加新的定时间隔设置
//function ab_reccurences() {
//	return array(
//		'weekly' 		=> array('interval' => 604800, 'display' => 'Once Weekly'),
//		'fortnightly' 	=> array('interval' => 1209600, 'display' => 'Once Fortnightly'),
//		'threesecondly' => array('interval' => 3, 'display' => 'Once ThreeSecondly'),
//	);
//}
//add_filter('cron_schedules', 'ab_reccurences');
//每天定期检测网页链接有效性
if (get_option("ab_checkdaily")==1) 
{
	if (!wp_next_scheduled('ab_CheckAllLink_hook')) 
	{
		wp_schedule_event( time(), 'hourly', 'ab_CheckAllLink_hook' );
	}
}
add_action( 'ab_CheckAllLink_hook', 'ab_CheckAllLink' );
//widget
function widget_sidebar_autoblogroll() 
{
	function widget_autoblogroll($args) 
	{
		if ((int)get_option("ab_onlydisplayonhome")==0) 
	    {
	    	widget_autoblogroll_do($args);
	    	return ;
	    }
		if ((int)get_option("ab_onlydisplayonhome")==1 and is_home()) 
	    {
			widget_autoblogroll_do($args);
	    	return ;
	    }
	}
	function widget_autoblogroll_do($args) 
	{
		extract($args);

		$title = get_option('ab_widget_title','Links');
		$cat = get_option('ab_widget_cat');
	    
		echo $before_widget;
		echo $before_title . $title . $after_title;
		$output = ab_blogroll($cat);
		echo $output;
		echo $after_widget;
	}
	register_sidebar_widget('AutoBlogRoll', 'widget_autoblogroll');
	
	//把目录换成checkbox代码
	function widget_autoblogroll_cat2checkbox()
	{
		$oldcat = (array)get_option('ab_widget_cat');
		
		$DB=new ab_DB();
		$cats=$DB->select(array(),"","cat");
		foreach ($cats as $cat) 
		{
			$checked=in_array($cat["cat"],$oldcat)?" checked":"";
			$code[]="<label><input type='checkbox' name='ab_widget_cat[]' id='ab_widget_cat[]' value='{$cat["cat"]}'{$checked}>{$cat["cat"]}</label>";
		}
		return @implode("\n",$code);
	}
	//widget 选项
	function widget_autoblogroll_options() 
	{
		//如果提交更新
		if ( $_POST["ab_submit"] ) 
		{ 	
			foreach ($_POST as $key=>$var) 
			{
				if (strstr($key,"ab_widget_")==$key) 
				{
					update_option($key,$var);
				}
			}
		}
		$title = attribute_escape(get_option('ab_widget_title','Links'));
	?>
		<p><label>
		<?php _e('Title:'); ?> <input class="widefat" id="ab_widget_title" name="ab_widget_title" type="text" value="<?php echo $title; ?>" />
		</label></p>
		<p><? _e("Chose categorize :","AutoBlogRoll")?>
		<?=widget_autoblogroll_cat2checkbox()?>
		</p>
		<input type="hidden" id="ab_submit" name="ab_submit" value="1" />
	<?php
	}
	register_widget_control('AutoBlogRoll', 'widget_autoblogroll_options', 200, 90);
	register_sidebar_widget('AutoBlogRoll', 'widget_autoblogroll');
}
add_action('plugins_loaded', 'widget_sidebar_autoblogroll');
//卸载
register_deactivation_hook(__FILE__,'ab_deactivation');
function ab_deactivation()
{
	global $wpdb;
	$remove_options_sql = "DELETE FROM $wpdb->options WHERE $wpdb->options.option_name like 'ab_%'";
	$wpdb->query($remove_options_sql);
	//删除插件创建的链接页面
	$ab=get_post_meta(get_option("ab_linkpageid"),"ab_created",true);
	if ($ab=="a") 
	{
		$post_id_del=get_option("ab_linkpageid");
		if ( !current_user_can('delete_page', $post_id_del) )
		{
			wp_die( __('You are not allowed to delete this page.') );
		}

		if ( !wp_delete_post($post_id_del) )
		{
			wp_die( __('Error in deleting...') );
		}
		delete_post_meta_by_key("ab_linkpageid");
	}
	//删除定时程序
	wp_clear_scheduled_hook('ab_CheckAllLink_hook');
}
//刪除首页缓存
function ab_deleteIndexhtml()
{
	//echo ABSPATH;
}
?>