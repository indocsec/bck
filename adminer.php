<?php
/** Adminer - Compact database management
* @link https://www.adminer.org/
* @author Jakub Vrana, https://www.vrana.cz/
* @copyright 2007 Jakub Vrana
* @license https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
* @version 5.4.1
*/namespace
Adminer;const
VERSION="5.4.1";error_reporting(24575);set_error_handler(function($Ec,$Gc){return!!preg_match('~^Undefined (array key|offset|index)~',$Gc);},E_WARNING|E_NOTICE);$cd=!preg_match('~^(unsafe_raw)?$~',ini_get("filter.default"));if($cd||ini_get("filter.default_flags")){foreach(array('_GET','_POST','_COOKIE','_SERVER')as$X){$xj=filter_input_array(constant("INPUT$X"),FILTER_UNSAFE_RAW);if($xj)$$X=$xj;}}if(function_exists("mb_internal_encoding"))mb_internal_encoding("8bit");function
connection($h=null){return($h?:Db::$instance);}function
adminer(){return
Adminer::$instance;}function
driver(){return
Driver::$instance;}function
connect(){$Hb=adminer()->credentials();$J=Driver::connect($Hb[0],$Hb[1],$Hb[2]);return(is_object($J)?$J:null);}function
idf_unescape($v){if(!preg_match('~^[`\'"[]~',$v))return$v;$Le=substr($v,-1);return
str_replace($Le.$Le,$Le,substr($v,1,-1));}function
q($Q){return
connection()->quote($Q);}function
escape_string($X){return
substr(q($X),1,-1);}function
idx($xa,$y,$l=null){return($xa&&array_key_exists($y,$xa)?$xa[$y]:$l);}function
number($X){return
preg_replace('~[^0-9]+~','',$X);}function
number_type(){return'((?<!o)int(?!er)|numeric|real|float|double|decimal|money)';}function
remove_slashes(array$eh,$cd=false){if(function_exists("get_magic_quotes_gpc")&&get_magic_quotes_gpc()){while(list($y,$X)=each($eh)){foreach($X
as$Ce=>$W){unset($eh[$y][$Ce]);if(is_array($W)){$eh[$y][stripslashes($Ce)]=$W;$eh[]=&$eh[$y][stripslashes($Ce)];}else$eh[$y][stripslashes($Ce)]=($cd?$W:stripslashes($W));}}}}function
bracket_escape($v,$Ea=false){static$gj=array(':'=>':1',']'=>':2','['=>':3','"'=>':4');return
strtr($v,($Ea?array_flip($gj):$gj));}function
min_version($Pj,$af="",$h=null){$h=connection($h);$Zh=$h->server_info;if($af&&preg_match('~([\d.]+)-MariaDB~',$Zh,$A)){$Zh=$A[1];$Pj=$af;}return$Pj&&version_compare($Zh,$Pj)>=0;}function
charset(Db$g){return(min_version("5.5.3",0,$g)?"utf8mb4":"utf8");}function
ini_bool($me){$X=ini_get($me);return(preg_match('~^(on|true|yes)$~i',$X)||(int)$X);}function
ini_bytes($me){$X=ini_get($me);switch(strtolower(substr($X,-1))){case'g':$X=(int)$X*1024;case'm':$X=(int)$X*1024;case'k':$X=(int)$X*1024;}return$X;}function
sid(){static$J;if($J===null)$J=(SID&&!($_COOKIE&&ini_bool("session.use_cookies")));return$J;}function
set_password($Oj,$N,$V,$F){$_SESSION["pwds"][$Oj][$N][$V]=($_COOKIE["adminer_key"]&&is_string($F)?array(encrypt_string($F,$_COOKIE["adminer_key"])):$F);}function
get_password(){$J=get_session("pwds");if(is_array($J))$J=($_COOKIE["adminer_key"]?decrypt_string($J[0],$_COOKIE["adminer_key"]):false);return$J;}function
get_val($H,$n=0,$vb=null){$vb=connection($vb);$I=$vb->query($H);if(!is_object($I))return
false;$K=$I->fetch_row();return($K?$K[$n]:false);}function
get_vals($H,$d=0){$J=array();$I=connection()->query($H);if(is_object($I)){while($K=$I->fetch_row())$J[]=$K[$d];}return$J;}function
get_key_vals($H,$h=null,$ci=true){$h=connection($h);$J=array();$I=$h->query($H);if(is_object($I)){while($K=$I->fetch_row()){if($ci)$J[$K[0]]=$K[1];else$J[]=$K[0];}}return$J;}function
get_rows($H,$h=null,$m="<p class='error'>"){$vb=connection($h);$J=array();$I=$vb->query($H);if(is_object($I)){while($K=$I->fetch_assoc())$J[]=$K;}elseif(!$I&&!$h&&$m&&(defined('Adminer\PAGE_HEADER')||$m=="-- "))echo$m.error()."\n";return$J;}function
unique_array($K,array$x){foreach($x
as$w){if(preg_match("~PRIMARY|UNIQUE~",$w["type"])){$J=array();foreach($w["columns"]as$y){if(!isset($K[$y]))continue
2;$J[$y]=$K[$y];}return$J;}}}function
escape_key($y){if(preg_match('(^([\w(]+)('.str_replace("_",".*",preg_quote(idf_escape("_"))).')([ \w)]+)$)',$y,$A))return$A[1].idf_escape(idf_unescape($A[2])).$A[3];return
idf_escape($y);}function
where(array$Z,array$o=array()){$J=array();foreach((array)$Z["where"]as$y=>$X){$y=bracket_escape($y,true);$d=escape_key($y);$n=idx($o,$y,array());$Zc=$n["type"];$J[]=$d.(JUSH=="sql"&&$Zc=="json"?" = CAST(".q($X)." AS JSON)":(JUSH=="pgsql"&&preg_match('~^json~',$Zc)?"::jsonb = ".q($X)."::jsonb":(JUSH=="sql"&&is_numeric($X)&&preg_match('~\.~',$X)?" LIKE ".q($X):(JUSH=="mssql"&&strpos($Zc,"datetime")===false?" LIKE ".q(preg_replace('~[_%[]~','[\0]',$X)):" = ".unconvert_field($n,q($X))))));if(JUSH=="sql"&&preg_match('~char|text~',$Zc)&&preg_match("~[^ -@]~",$X))$J[]="$d = ".q($X)." COLLATE ".charset(connection())."_bin";}foreach((array)$Z["null"]as$y)$J[]=escape_key($y)." IS NULL";return
implode(" AND ",$J);}function
where_check($X,array$o=array()){parse_str($X,$Ya);remove_slashes(array(&$Ya));return
where($Ya,$o);}function
where_link($t,$d,$Y,$bg="="){return"&where%5B$t%5D%5Bcol%5D=".urlencode($d)."&where%5B$t%5D%5Bop%5D=".urlencode(($Y!==null?$bg:"IS NULL"))."&where%5B$t%5D%5Bval%5D=".urlencode($Y);}function
convert_fields(array$e,array$o,array$M=array()){$J="";foreach($e
as$y=>$X){if($M&&!in_array(idf_escape($y),$M))continue;$ya=convert_field($o[$y]);if($ya)$J
.=", $ya AS ".idf_escape($y);}return$J;}function
cookie($B,$Y,$Te=2592000){header("Set-Cookie: $B=".urlencode($Y).($Te?"; expires=".gmdate("D, d M Y H:i:s",time()+$Te)." GMT":"")."; path=".preg_replace('~\?.*~','',$_SERVER["REQUEST_URI"]).(HTTPS?"; secure":"")."; HttpOnly; SameSite=lax",false);}function
get_settings($Db){parse_str($_COOKIE[$Db],$di);return$di;}function
get_setting($y,$Db="adminer_settings",$l=null){return
idx(get_settings($Db),$y,$l);}function
save_settings(array$di,$Db="adminer_settings"){$Y=http_build_query($di+get_settings($Db));cookie($Db,$Y);$_COOKIE[$Db]=$Y;}function
restart_session(){if(!ini_bool("session.use_cookies")&&(!function_exists('session_status')||session_status()==1))session_start();}function
stop_session($kd=false){$Gj=ini_bool("session.use_cookies");if(!$Gj||$kd){session_write_close();if($Gj&&@ini_set("session.use_cookies",'0')===false)session_start();}}function&get_session($y){return$_SESSION[$y][DRIVER][SERVER][$_GET["username"]];}function
set_session($y,$X){$_SESSION[$y][DRIVER][SERVER][$_GET["username"]]=$X;}function
auth_url($Oj,$N,$V,$k=null){$Cj=remove_from_uri(implode("|",array_keys(SqlDriver::$drivers))."|username|ext|".($k!==null?"db|":"").($Oj=='mssql'||$Oj=='pgsql'?"":"ns|").session_name());preg_match('~([^?]*)\??(.*)~',$Cj,$A);return"$A[1]?".(sid()?SID."&":"").($Oj!="server"||$N!=""?urlencode($Oj)."=".urlencode($N)."&":"").($_GET["ext"]?"ext=".urlencode($_GET["ext"])."&":"")."username=".urlencode($V).($k!=""?"&db=".urlencode($k):"").($A[2]?"&$A[2]":"");}function
is_ajax(){return($_SERVER["HTTP_X_REQUESTED_WITH"]=="XMLHttpRequest");}function
redirect($We,$pf=null){if($pf!==null){restart_session();$_SESSION["messages"][preg_replace('~^[^?]*~','',($We!==null?$We:$_SERVER["REQUEST_URI"]))][]=$pf;}if($We!==null){if($We=="")$We=".";header("Location: $We");exit;}}function
query_redirect($H,$We,$pf,$nh=true,$Lc=true,$Uc=false,$Ti=""){if($Lc){$si=microtime(true);$Uc=!connection()->query($H);$Ti=format_time($si);}$mi=($H?adminer()->messageQuery($H,$Ti,$Uc):"");if($Uc){adminer()->error
.=error().$mi.script("messagesPrint();")."<br>";return
false;}if($nh)redirect($We,$pf.$mi);return
true;}class
Queries{static$queries=array();static$start=0;}function
queries($H){if(!Queries::$start)Queries::$start=microtime(true);Queries::$queries[]=(preg_match('~;$~',$H)?"DELIMITER ;;\n$H;\nDELIMITER ":$H).";";return
connection()->query($H);}function
apply_queries($H,array$T,$Hc='Adminer\table'){foreach($T
as$R){if(!queries("$H ".$Hc($R)))return
false;}return
true;}function
queries_redirect($We,$pf,$nh){$ih=implode("\n",Queries::$queries);$Ti=format_time(Queries::$start);return
query_redirect($ih,$We,$pf,$nh,false,!$nh,$Ti);}function
format_time($si){return
lang(0,max(0,microtime(true)-$si));}function
relative_uri(){return
str_replace(":","%3a",preg_replace('~^[^?]*/([^?]*)~','\1',$_SERVER["REQUEST_URI"]));}function
remove_from_uri($yg=""){return
substr(preg_replace("~(?<=[?&])($yg".(SID?"":"|".session_name()).")=[^&]*&~",'',relative_uri()."&"),0,-1);}function
get_file($y,$Tb=false,$Zb=""){$bd=$_FILES[$y];if(!$bd)return
null;foreach($bd
as$y=>$X)$bd[$y]=(array)$X;$J='';foreach($bd["error"]as$y=>$m){if($m)return$m;$B=$bd["name"][$y];$bj=$bd["tmp_name"][$y];$_b=file_get_contents($Tb&&preg_match('~\.gz$~',$B)?"compress.zlib://$bj":$bj);if($Tb){$si=substr($_b,0,3);if(function_exists("iconv")&&preg_match("~^\xFE\xFF|^\xFF\xFE~",$si))$_b=iconv("utf-16","utf-8",$_b);elseif($si=="\xEF\xBB\xBF")$_b=substr($_b,3);}$J
.=$_b;if($Zb)$J
.=(preg_match("($Zb\\s*\$)",$_b)?"":$Zb)."\n\n";}return$J;}function
upload_error($m){$kf=($m==UPLOAD_ERR_INI_SIZE?ini_get("upload_max_filesize"):0);return($m?lang(1).($kf?" ".lang(2,$kf):""):lang(3));}function
repeat_pattern($Kg,$Re){return
str_repeat("$Kg{0,65535}",$Re/65535)."$Kg{0,".($Re%65535)."}";}function
is_utf8($X){return(preg_match('~~u',$X)&&!preg_match('~[\0-\x8\xB\xC\xE-\x1F]~',$X));}function
format_number($X){return
strtr(number_format($X,0,".",lang(4)),preg_split('~~u',lang(5),-1,PREG_SPLIT_NO_EMPTY));}function
friendly_url($X){return
preg_replace('~\W~i','-',$X);}function
table_status1($R,$Vc=false){$J=table_status($R,$Vc);return($J?reset($J):array("Name"=>$R));}function
column_foreign_keys($R){$J=array();foreach(adminer()->foreignKeys($R)as$q){foreach($q["source"]as$X)$J[$X][]=$q;}return$J;}function
fields_from_edit(){$J=array();foreach((array)$_POST["field_keys"]as$y=>$X){if($X!=""){$X=bracket_escape($X);$_POST["function"][$X]=$_POST["field_funs"][$y];$_POST["fields"][$X]=$_POST["field_vals"][$y];}}foreach((array)$_POST["fields"]as$y=>$X){$B=bracket_escape($y,true);$J[$B]=array("field"=>$B,"privileges"=>array("insert"=>1,"update"=>1,"where"=>1,"order"=>1),"null"=>1,"auto_increment"=>($y==driver()->primary),);}return$J;}function
dump_headers($Sd,$_f=false){$J=adminer()->dumpHeaders($Sd,$_f);$ug=$_POST["output"];if($ug!="text")header("Content-Disposition: attachment; filename=".adminer()->dumpFilename($Sd).".$J".($ug!="file"&&preg_match('~^[0-9a-z]+$~',$ug)?".$ug":""));session_write_close();if(!ob_get_level())ob_start(null,4096);ob_flush();flush();return$J;}function
dump_csv(array$K){foreach($K
as$y=>$X){if(preg_match('~["\n,;\t]|^0.|\.\d*0$~',$X)||$X==="")$K[$y]='"'.str_replace('"','""',$X).'"';}echo
implode(($_POST["format"]=="csv"?",":($_POST["format"]=="tsv"?"\t":";")),$K)."\r\n";}function
apply_sql_function($s,$d){return($s?($s=="unixepoch"?"DATETIME($d, '$s')":($s=="count distinct"?"COUNT(DISTINCT ":strtoupper("$s("))."$d)"):$d);}function
get_temp_dir(){$J=ini_get("upload_tmp_dir");if(!$J){if(function_exists('sys_get_temp_dir'))$J=sys_get_temp_dir();else{$p=@tempnam("","");if(!$p)return'';$J=dirname($p);unlink($p);}}return$J;}function
file_open_lock($p){if(is_link($p))return;$r=@fopen($p,"c+");if(!$r)return;@chmod($p,0660);if(!flock($r,LOCK_EX)){fclose($r);return;}return$r;}function
file_write_unlock($r,$Nb){rewind($r);fwrite($r,$Nb);ftruncate($r,strlen($Nb));file_unlock($r);}function
file_unlock($r){flock($r,LOCK_UN);fclose($r);}function
first(array$xa){return
reset($xa);}function
password_file($i){$p=get_temp_dir()."/adminer.key";if(!$i&&!file_exists($p))return'';$r=file_open_lock($p);if(!$r)return'';$J=stream_get_contents($r);if(!$J){$J=rand_string();file_write_unlock($r,$J);}else
file_unlock($r);return$J;}function
rand_string(){return
md5(uniqid(strval(mt_rand()),true));}function
select_value($X,$_,array$n,$Si){if(is_array($X)){$J="";foreach($X
as$Ce=>$W)$J
.="<tr>".($X!=array_values($X)?"<th>".h($Ce):"")."<td>".select_value($W,$_,$n,$Si);return"<table>$J</table>";}if(!$_)$_=adminer()->selectLink($X,$n);if($_===null){if(is_mail($X))$_="mailto:$X";if(is_url($X))$_=$X;}$J=adminer()->editVal($X,$n);if($J!==null){if(!is_utf8($J))$J="\0";elseif($Si!=""&&is_shortable($n))$J=shorten_utf8($J,max(0,+$Si));else$J=h($J);}return
adminer()->selectVal($J,$_,$n,$X);}function
is_blob(array$n){return
preg_match('~blob|bytea|raw|file~',$n["type"])&&!in_array($n["type"],idx(driver()->structuredTypes(),lang(6),array()));}function
is_mail($vc){$za='[-a-z0-9!#$%&\'*+/=?^_`{|}~]';$ic='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';$Kg="$za+(\\.$za+)*@($ic?\\.)+$ic";return
is_string($vc)&&preg_match("(^$Kg(,\\s*$Kg)*\$)i",$vc);}function
is_url($Q){$ic='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';return
preg_match("~^(https?)://($ic?\\.)+$ic(:\\d+)?(/.*)?(\\?.*)?(#.*)?\$~i",$Q);}function
is_shortable(array$n){return
preg_match('~char|text|json|lob|geometry|point|linestring|polygon|string|bytea|hstore~',$n["type"]);}function
host_port($N){return(preg_match('~^(\[(.+)]|([^:]+)):([^:]+)$~',$N,$A)?array($A[2].$A[3],$A[4]):array($N,''));}function
count_rows($R,array$Z,$we,array$yd){$H=" FROM ".table($R).($Z?" WHERE ".implode(" AND ",$Z):"");return($we&&(JUSH=="sql"||count($yd)==1)?"SELECT COUNT(DISTINCT ".implode(", ",$yd).")$H":"SELECT COUNT(*)".($we?" FROM (SELECT 1$H GROUP BY ".implode(", ",$yd).") x":$H));}function
slow_query($H){$k=adminer()->database();$Ui=adminer()->queryTimeout();$hi=driver()->slowQuery($H,$Ui);$h=null;if(!$hi&&support("kill")){$h=connect();if($h&&($k==""||$h->select_db($k))){$Fe=get_val(connection_id(),0,$h);echo
script("const timeout = setTimeout(() => { ajax('".js_escape(ME)."script=kill', function () {}, 'kill=$Fe&token=".get_token()."'); }, 1000 * $Ui);");}}ob_flush();flush();$J=@get_key_vals(($hi?:$H),$h,false);if($h){echo
script("clearTimeout(timeout);");ob_flush();flush();}return$J;}function
get_token(){$lh=rand(1,1e6);return($lh^$_SESSION["token"]).":$lh";}function
verify_token(){list($cj,$lh)=explode(":",$_POST["token"]);return($lh^$_SESSION["token"])==$cj;}function
lzw_decompress($Ka){$ec=256;$La=8;$ib=array();$yh=0;$zh=0;for($t=0;$t<strlen($Ka);$t++){$yh=($yh<<8)+ord($Ka[$t]);$zh+=8;if($zh>=$La){$zh-=$La;$ib[]=$yh>>$zh;$yh&=(1<<$zh)-1;$ec++;if($ec>>$La)$La++;}}$dc=range("\0","\xFF");$J="";$Yj="";foreach($ib
as$t=>$hb){$uc=$dc[$hb];if(!isset($uc))$uc=$Yj.$Yj[0];$J
.=$uc;if($t)$dc[]=$Yj.$uc[0];$Yj=$uc;}return$J;}function
script($ji,$fj="\n"){return"<script".nonce().">$ji</script>$fj";}function
script_src($Dj,$Wb=false){return"<script src='".h($Dj)."'".nonce().($Wb?" defer":"")."></script>\n";}function
nonce(){return' nonce="'.get_nonce().'"';}function
input_hidden($B,$Y=""){return"<input type='hidden' name='".h($B)."' value='".h($Y)."'>\n";}function
input_token(){return
input_hidden("token",get_token());}function
target_blank(){return' target="_blank" rel="noreferrer noopener"';}function
h($Q){return
str_replace("\0","&#0;",htmlspecialchars($Q,ENT_QUOTES,'utf-8'));}function
nl_br($Q){return
str_replace("\n","<br>",$Q);}function
checkbox($B,$Y,$bb,$He="",$ag="",$fb="",$Je=""){$J="<input type='checkbox' name='$B' value='".h($Y)."'".($bb?" checked":"").($Je?" aria-labelledby='$Je'":"").">".($ag?script("qsl('input').onclick = function () { $ag };",""):"");return($He!=""||$fb?"<label".($fb?" class='$fb'":"").">$J".h($He)."</label>":$J);}function
optionlist($fg,$Rh=null,$Hj=false){$J="";foreach($fg
as$Ce=>$W){$gg=array($Ce=>$W);if(is_array($W)){$J
.='<optgroup label="'.h($Ce).'">';$gg=$W;}foreach($gg
as$y=>$X)$J
.='<option'.($Hj||is_string($y)?' value="'.h($y).'"':'').($Rh!==null&&($Hj||is_string($y)?(string)$y:$X)===$Rh?' selected':'').'>'.h($X);if(is_array($W))$J
.='</optgroup>';}return$J;}function
html_select($B,array$fg,$Y="",$Zf="",$Je=""){static$He=0;$Ie="";if(!$Je&&substr($fg[""],0,1)=="("){$He++;$Je="label-$He";$Ie="<option value='' id='$Je'>".h($fg[""]);unset($fg[""]);}return"<select name='".h($B)."'".($Je?" aria-labelledby='$Je'":"").">".$Ie.optionlist($fg,$Y)."</select>".($Zf?script("qsl('select').onchange = function () { $Zf };",""):"");}function
html_radios($B,array$fg,$Y="",$Vh=""){$J="";foreach($fg
as$y=>$X)$J
.="<label><input type='radio' name='".h($B)."' value='".h($y)."'".($y==$Y?" checked":"").">".h($X)."</label>$Vh";return$J;}function
confirm($pf="",$Sh="qsl('input')"){return
script("$Sh.onclick = () => confirm('".($pf?js_escape($pf):lang(7))."');","");}function
print_fieldset($u,$Qe,$Sj=false){echo"<fieldset><legend>","<a href='#fieldset-$u'>$Qe</a>",script("qsl('a').onclick = partial(toggle, 'fieldset-$u');",""),"</legend>","<div id='fieldset-$u'".($Sj?"":" class='hidden'").">\n";}function
bold($Na,$fb=""){return($Na?" class='active $fb'":($fb?" class='$fb'":""));}function
js_escape($Q){return
addcslashes($Q,"\r\n'\\/");}function
pagination($D,$Kb){return" ".($D==$Kb?$D+1:'<a href="'.h(remove_from_uri("page").($D?"&page=$D".($_GET["next"]?"&next=".urlencode($_GET["next"]):""):"")).'">'.($D+1)."</a>");}function
hidden_fields(array$eh,array$Wd=array(),$Wg=''){$J=false;foreach($eh
as$y=>$X){if(!in_array($y,$Wd)){if(is_array($X))hidden_fields($X,array(),$y);else{$J=true;echo
input_hidden(($Wg?$Wg."[$y]":$y),$X);}}}return$J;}function
hidden_fields_get(){echo(sid()?input_hidden(session_name(),session_id()):''),(SERVER!==null?input_hidden(DRIVER,SERVER):""),input_hidden("username",$_GET["username"]);}function
file_input($oe){$ff="max_file_uploads";$gf=ini_get($ff);$Aj="upload_max_filesize";$Bj=ini_get($Aj);return(ini_bool("file_uploads")?$oe.script("qsl('input[type=\"file\"]').onchange = partialArg(fileChange, "."$gf, '".lang(8,"$ff = $gf")."', ".ini_bytes("upload_max_filesize").", '".lang(8,"$Aj = $Bj")."')"):lang(9));}function
enum_input($U,$_a,array$n,$Y,$yc=""){preg_match_all("~'((?:[^']|'')*)'~",$n["length"],$df);$Wg=($n["type"]=="enum"?"val-":"");$bb=(is_array($Y)?in_array("null",$Y):$Y===null);$J=($n["null"]&&$Wg?"<label><input type='$U'$_a value='null'".($bb?" checked":"")."><i>$yc</i></label>":"");foreach($df[1]as$X){$X=stripcslashes(str_replace("''","'",$X));$bb=(is_array($Y)?in_array($Wg.$X,$Y):$Y===$X);$J
.=" <label><input type='$U'$_a value='".h($Wg.$X)."'".($bb?' checked':'').'>'.h(adminer()->editVal($X,$n)).'</label>';}return$J;}function
input(array$n,$Y,$s,$Da=false){$B=h(bracket_escape($n["field"]));echo"<td class='function'>";if(is_array($Y)&&!$s){$Y=json_encode($Y,128|64|256);$s="json";}$xh=(JUSH=="mssql"&&$n["auto_increment"]);if($xh&&!$_POST["save"])$s=null;$td=(isset($_GET["select"])||$xh?array("orig"=>lang(10)):array())+adminer()->editFunctions($n);$Dc=driver()->enumLength($n);if($Dc){$n["type"]="enum";$n["length"]=$Dc;}$fc=stripos($n["default"],"GENERATED ALWAYS AS ")===0?" disabled=''":"";$_a=" name='fields[$B]".($n["type"]=="enum"||$n["type"]=="set"?"[]":"")."'$fc".($Da?" autofocus":"");echo
driver()->unconvertFunction($n)." ";$R=$_GET["edit"]?:$_GET["select"];if($n["type"]=="enum")echo
h($td[""])."<td>".adminer()->editInput($R,$n,$_a,$Y);else{$Fd=(in_array($s,$td)||isset($td[$s]));echo(count($td)>1?"<select name='function[$B]'$fc>".optionlist($td,$s===null||$Fd?$s:"")."</select>".on_help("event.target.value.replace(/^SQL\$/, '')",1).script("qsl('select').onchange = functionChange;",""):h(reset($td))).'<td>';$oe=adminer()->editInput($R,$n,$_a,$Y);if($oe!="")echo$oe;elseif(preg_match('~bool~',$n["type"]))echo"<input type='hidden'$_a value='0'>"."<input type='checkbox'".(preg_match('~^(1|t|true|y|yes|on)$~i',$Y)?" checked='checked'":"")."$_a value='1'>";elseif($n["type"]=="set")echo
enum_input("checkbox",$_a,$n,(is_string($Y)?explode(",",$Y):$Y));elseif(is_blob($n)&&ini_bool("file_uploads"))echo"<input type='file' name='fields-$B'>";elseif($s=="json"||preg_match('~^jsonb?$~',$n["type"]))echo"<textarea$_a cols='50' rows='12' class='jush-js'>".h($Y).'</textarea>';elseif(($Qi=preg_match('~text|lob|memo~i',$n["type"]))||preg_match("~\n~",$Y)){if($Qi&&JUSH!="sqlite")$_a
.=" cols='50' rows='12'";else{$L=min(12,substr_count($Y,"\n")+1);$_a
.=" cols='30' rows='$L'";}echo"<textarea$_a>".h($Y).'</textarea>';}else{$rj=driver()->types();$mf=(!preg_match('~int~',$n["type"])&&preg_match('~^(\d+)(,(\d+))?$~',$n["length"],$A)?((preg_match("~binary~",$n["type"])?2:1)*$A[1]+($A[3]?1:0)+($A[2]&&!$n["unsigned"]?1:0)):($rj[$n["type"]]?$rj[$n["type"]]+($n["unsigned"]?0:1):0));if(JUSH=='sql'&&min_version(5.6)&&preg_match('~time~',$n["type"]))$mf+=7;echo"<input".((!$Fd||$s==="")&&preg_match('~(?<!o)int(?!er)~',$n["type"])&&!preg_match('~\[\]~',$n["full_type"])?" type='number'":"")." value='".h($Y)."'".($mf?" data-maxlength='$mf'":"").(preg_match('~char|binary~',$n["type"])&&$mf>20?" size='".($mf>99?60:40)."'":"")."$_a>";}echo
adminer()->editHint($R,$n,$Y);$dd=0;foreach($td
as$y=>$X){if($y===""||!$X)break;$dd++;}if($dd&&count($td)>1)echo
script("qsl('td').oninput = partial(skipOriginal, $dd);");}}function
process_input(array$n){if(stripos($n["default"],"GENERATED ALWAYS AS ")===0)return;$v=bracket_escape($n["field"]);$s=idx($_POST["function"],$v);$Y=idx($_POST["fields"],$v);if($n["type"]=="enum"||driver()->enumLength($n)){$Y=$Y[0];if($Y=="orig")return
false;if($Y=="null")return"NULL";$Y=substr($Y,4);}if($n["auto_increment"]&&$Y=="")return
null;if($s=="orig")return(preg_match('~^CURRENT_TIMESTAMP~i',$n["on_update"])?idf_escape($n["field"]):false);if($s=="NULL")return"NULL";if($n["type"]=="set")$Y=implode(",",(array)$Y);if($s=="json"){$s="";$Y=json_decode($Y,true);if(!is_array($Y))return
false;return$Y;}if(is_blob($n)&&ini_bool("file_uploads")){$bd=get_file("fields-$v");if(!is_string($bd))return
false;return
driver()->quoteBinary($bd);}return
adminer()->processInput($n,$Y,$s);}function
search_tables(){$_GET["where"][0]["val"]=$_POST["query"];$Uh="<ul>\n";foreach(table_status('',true)as$R=>$S){$B=adminer()->tableName($S);if(isset($S["Engine"])&&$B!=""&&(!$_POST["tables"]||in_array($R,$_POST["tables"]))){$I=connection()->query("SELECT".limit("1 FROM ".table($R)," WHERE ".implode(" AND ",adminer()->selectSearchProcess(fields($R),array())),1));if(!$I||$I->fetch_row()){$ah="<a href='".h(ME."select=".urlencode($R)."&where[0][op]=".urlencode($_GET["where"][0]["op"])."&where[0][val]=".urlencode($_GET["where"][0]["val"]))."'>$B</a>";echo"$Uh<li>".($I?$ah:"<p class='error'>$ah: ".error())."\n";$Uh="";}}}echo($Uh?"<p class='message'>".lang(11):"</ul>")."\n";}function
on_help($ob,$fi=0){return
script("mixin(qsl('select, input'), {onmouseover: function (event) { helpMouseover.call(this, event, $ob, $fi) }, onmouseout: helpMouseout});","");}function
edit_form($R,array$o,$K,$_j,$m=''){$Di=adminer()->tableName(table_status1($R,true));page_header(($_j?lang(12):lang(13)),$m,array("select"=>array($R,$Di)),$Di);adminer()->editRowPrint($R,$o,$K,$_j);if($K===false){echo"<p class='error'>".lang(14)."\n";return;}echo"<form action='' method='post' enctype='multipart/form-data' id='form'>\n";if(!$o)echo"<p class='error'>".lang(15)."\n";else{echo"<table class='layout'>".script("qsl('table').onkeydown = editingKeydown;");$Da=!$_POST;foreach($o
as$B=>$n){echo"<tr><th>".adminer()->fieldName($n);$l=idx($_GET["set"],bracket_escape($B));if($l===null){$l=$n["default"];if($n["type"]=="bit"&&preg_match("~^b'([01]*)'\$~",$l,$uh))$l=$uh[1];if(JUSH=="sql"&&preg_match('~binary~',$n["type"]))$l=bin2hex($l);}$Y=($K!==null?($K[$B]!=""&&JUSH=="sql"&&preg_match("~enum|set~",$n["type"])&&is_array($K[$B])?implode(",",$K[$B]):(is_bool($K[$B])?+$K[$B]:$K[$B])):(!$_j&&$n["auto_increment"]?"":(isset($_GET["select"])?false:$l)));if(!$_POST["save"]&&is_string($Y))$Y=adminer()->editVal($Y,$n);$s=($_POST["save"]?idx($_POST["function"],$B,""):($_j&&preg_match('~^CURRENT_TIMESTAMP~i',$n["on_update"])?"now":($Y===false?null:($Y!==null?'':'NULL'))));if(!$_POST&&!$_j&&$Y==$n["default"]&&preg_match('~^[\w.]+\(~',$Y))$s="SQL";if(preg_match("~time~",$n["type"])&&preg_match('~^CURRENT_TIMESTAMP~i',$Y)){$Y="";$s="now";}if($n["type"]=="uuid"&&$Y=="uuid()"){$Y="";$s="uuid";}if($Da!==false)$Da=($n["auto_increment"]||$s=="now"||$s=="uuid"?null:true);input($n,$Y,$s,$Da);if($Da)$Da=false;echo"\n";}if(!support("table")&&!fields($R))echo"<tr>"."<th><input name='field_keys[]'>".script("qsl('input').oninput = fieldChange;")."<td class='function'>".html_select("field_funs[]",adminer()->editFunctions(array("null"=>isset($_GET["select"]))))."<td><input name='field_vals[]'>"."\n";echo"</table>\n";}echo"<p>\n";if($o){echo"<input type='submit' value='".lang(16)."'>\n";if(!isset($_GET["select"]))echo"<input type='submit' name='insert' value='".($_j?lang(17):lang(18))."' title='Ctrl+Shift+Enter'>\n",($_j?script("qsl('input').onclick = function () { return !ajaxForm(this.form, '".lang(19)."…', this); };"):"");}echo($_j?"<input type='submit' name='delete' value='".lang(20)."'>".confirm()."\n":"");if(isset($_GET["select"]))hidden_fields(array("check"=>(array)$_POST["check"],"clone"=>$_POST["clone"],"all"=>$_POST["all"]));echo
input_hidden("referer",(isset($_POST["referer"])?$_POST["referer"]:$_SERVER["HTTP_REFERER"])),input_hidden("save",1),input_token(),"</form>\n";}function
shorten_utf8($Q,$Re=80,$yi=""){if(!preg_match("(^(".repeat_pattern("[\t\r\n -\x{10FFFF}]",$Re).")($)?)u",$Q,$A))preg_match("(^(".repeat_pattern("[\t\r\n -~]",$Re).")($)?)",$Q,$A);return
h($A[1]).$yi.(isset($A[2])?"":"<i>…</i>");}function
icon($Rd,$B,$Qd,$Wi){return"<button type='submit' name='$B' title='".h($Wi)."' class='icon icon-$Rd'><span>$Qd</span></button>";}if(isset($_GET["file"])){if(substr(VERSION,-4)!='-dev'){if($_SERVER["HTTP_IF_MODIFIED_SINCE"]){header("HTTP/1.1 304 Not Modified");exit;}header("Expires: ".gmdate("D, d M Y H:i:s",time()+365*24*60*60)." GMT");header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");header("Cache-Control: immutable");}@ini_set("zlib.output_compression",'1');if($_GET["file"]=="default.css"){header("Content-Type: text/css; charset=utf-8");echo
lzw_decompress("h:M±h´ħ̐±ܔ͈\"PѩҭcQCa¤鉲ó鈞d<̦󡼤:;NBqR;1Lf³9Ȟu7&)¤l;3͑񈀊/CQXʲ2MƑa䩰)°쥺LuÝh歹Ս23lȎi7³mڷ4њ<-Ҍ´¹!U,Févt2S,¬䡴҇FꖘúaNq㩓-֎ǜh꺮5û9ș¨;jµ-޷_9krùٓ;.КtTq˯¦0³­ֲ®{�ùý\r爮싍GS Zh²;¼i^ÀuxøWΒC@Ķ¤©kҽ¡Т©ˢ켯Aؠ0¤+(ڐÁ°l\ꠃx躜r耢8\0新!\0FƜnB͎㨒3 \r\\º۪Ȅa¼'I⼪(i\n\r©¸ú4Oüg@4ÁCº@@!đB°݉°¸c¤ʂ¯ı,\r1Eh舐&2PZ¦ퟒ�GûH9G\"v§ꌒ¢££¤4rƱ͏DВ¤\npJ뭁|/.¯c꓄u·£¤ö:,ʽ°¢RŝU5¥mVÁk͌LQ@-\\ª¦˓@9Á㜥ړrÁαMPDらa\r(YY\\〘õpê:£p÷lLC ű踎͊O,\rƲ]7?m06仰ܖTш͡ҥC;_˗ѹȴd>¨²bnퟖ�n¼ܣ3÷X¾ö8\r훋-)۩>V[Y㹦L3¯#̎X|ՉX \\ù`ˈC§瘥#љHɌ2ʲ.# öZ`¼¾㳮·¹ªÒ£º\0uh־¥M²͟\niZeO/CӒ_`3ݲ𱃾=Ы3£R/;䯤ۜ\0ú㞚µmùú򾤷/«֕AΘ¿°ñ.½sጣý :\$Ɇ¢¸ª¾£w8󟾾«HԪ­\"¨¼¹Գ7gSõ䱇∆L鎯瑲_¤O'Wض]c=ý5¾1X~7;iþ´\r�n¨JS1Z¦ø£؆ߓͣ吂tüAԖ퐸6fФù;Y]©õzIÀp¡ѻ§𣉳®Y˝}@¡\$.+1¶'>Zãpd੒GL桄#k��zY҄Auϕvݎ]s9ђ؟AqΌÁ:ƅ\nKhB¼;­֚XbAHq,❃Iɠ窹S[ˌ¶1ƖӔr񔻶pނÛ)#鐉;4̈񒯪ռ³L Á;lfª\n¶s\$K`нƴՔ£¾7jx`d%j] ¸4Y¤HbY ؊`¤GG .ŜK򦌊I©)2MfָݘRC¸̱V,©ۑ~g\0薂৶ݺõ[j�½:AlIq©u3\"ꦁq¤漸<9s'㑝JʼМ0`p ³jfOƢЉú¬¨q¬¢\$邩²ñJ¹>RH(ǔq\n#re(y󖇊µ0¡Q҈£򈶆P曃:·G伞 ݴ©ҞӰÐZµ\\´訜n֩~¦´°9R%דj·{7䰞_ǳ	z|8ňꙉ\"@ܣ9DVLŜ$H5ԘWJ@z®a¿J Ğ	)®2\nQvÀԝ뇎āj (A¸Ӛ°BB05´6b˰][諪Awvkg􆴶ºիk[jmzc¶}荹DZi휤5e«ʷ°º	A CY%.Wb*뮼.­ٳq/%}B̘­皖337ʻaº򞷗[጗Qʝ޲ü_Ȕ2`ǱIѩ,÷曣Mf&(s-䘫AİتDw؄TNÀɻŪX\$鸪+;аˆڕ93µJkS;·§ÁqR{>l;B1AȐI♢) (6±­r÷\rݜrڇڟ욛R^SOy/ލ#Ə9{kસv\"úKC⊃¨rEo\0ø̜\,љ|fa͚³hI©/o̙4ċk^pHȞͰhǡVÁvox@ø`�&(ù­ü;~Ǎz̶׸¯*°Ɯ5®ܝ±E Á°颮Ә¤´3öņgrDь󩴧{»佥³©L&ú>脻¢ؚZ췡\0ú°̊@אӛffŌRVh֝²盉ۈ½ⰲӷ) =x^,k2��jࢫl0u랜"¬fp¨¸1񒉿z[]¤wpN6dIªz뵿宮7X{;Áȳ؋-I	⻼7pjÝ¢R#ª,ù_-м󾳀\\檛WqޱJ֘uh£ІbLÁKԥ繖ľ©¦Þѕ®µªüV{K}S ʝޝMþ·̀¼¦.M¶\\ªix¸bÁ¡1+£α?<ų꾈ýӜ$÷\\вۜ$ضtԏ̈㜤s¼¼©xľx󧈃ᮓkVĉ=z6½¡ʧæ䎡¢ָhܼ¸º±ý¯R¤噣8g¢䊷:_³�ҒIRKÝ¨.½nkVU+dwj§%³`#,{醳˗ퟩ�Yý׵(oվɰ.¨c0g℘Ok7®苤Όlҍhx;Ϝ؏ ݃Lû´\$09*9 ܨNrüMՂ.>\0زP9ȧ	\0\$\\F󪲤'εL庋bú𖴏2À􅢰9Àퟀ�nb쭎¤󅠣ĜɃ 겐Yꨠt͠؅\n𵂮©ʅ⮜$op lX\n@`\r	ȜrЈ Έ ¦  	 ʆઆ𚈠Ή@ڋ@ڜn  	\0j@Q@1\rÀ@ ¢	\$p	 V\0򅠠\n\0¨\n Мn@¨' 쌀¤\n\0`\rÀڈ ¬	Ҝrऌ ´\0Џr°挀򉜰`	இ {	,\"¨ȞP0¥\n¬4±\n0·¤.0Ìpˌ𓜲pۜr𣎰뎰󏰻񙐱񙑑0ߒ%ђѱQ8\n ԏ\0􈫊ȼ\0^ҏ\0`چ@´ȏ>\nѯ1w±,Y	h*=¡P¦:іV¸.q£Ō͜r՜rp鎐񐱁сQ	ёї1ג `ѝ񯓱7±랱򜲠^À䏜"y`\nÀ # \0ꉠp\n򜮀` r Q𦢧1ҳ\n°¯#°µ#𼌱¥\$q«\$ѱ%0奱½%й&Ǧq͑ &񛧱ڜrR}16	 b\r`µ`ܜrÀ	ވÀ̌dઆ¨	j\n¯``À\n`dcсP,򱙒י\$¿rIҏ 	Q	򙏳2b1ɦϰ1ӑљ ӏ fÀϓ\0ª\0¤ Άf\0j\n f`≠®\n`´@\$n=`\0ȎҶ nIМ$ÿP(¤'˰􄌿Ġ·gɶ--҃7R皠 	4ࠅ��˦±ѝ2t\r��n 	H*@	`\n ¤ 艠򆬕2¿,z\r쾈 脜rF촨ö؄ 뭐õ䄬´z~¡\0]Ğ\\¥׉\\¥£}ItC\nÁT}ªؗIEJ\rx׉û¾ٍpIH��fht믮bxYE쩝K´ªoj\n𭅌Àއtr׮À~d»H2U4©GܜAꂧ4þuPtރՖ½谐 򐠍L/¿P׉\"G!RtO-̎µ<#õAPuI뒨\$c¹ÄƊ §¢-Çⴏ`Pv§^W@tH;Q°µRę՜$´©gK膼\rR*\$4®' 󍨐Ȋ[�Iª󎭕mсƨ:+þ¼5@/­l¾I¾ª�^\0ODøª¬؜rR'r蔐­[ꖷĄª®«MC덃Z4慠B\"栶´euN�¬靏𴺜rª`܀hö*\r¶.V%ڡMBlPFϜ"د&կ@\Cޯ©:mMgn򎮶ʩ8I2\rp��÷﫚 mT©ueõզv>f´И֠DU[ZTϖЃേT𜲖¹Uvkõ^ז¦øL딙b/¾K¶Sev2÷ubvǏVD𖉭՜$򥖘?ud硗|,\rø+nUeךƄʖþö뭾X¯ºûԶBGd¶\$i¶獶!t#L쳯·UIOu?ZweRϘ 룷ª. `ȡiø񜲢§%©b∅¦H®\"\"\"h�\$b@Ẫ䆜0f\"鲗¨®*悋|\$\$¬B֗ \"@r¯(\r`ʏ ¸ǂ(0&.`Ҏk9B\n&#(Ī℀䂯څ«dü^÷º®ü £@²`ҌI-{0£✮B{4sG{§ø;z®©b÷{ ѻbׯ){BxKÀŇ5=cڪ«y宦슣Prŉ/ܠ\0ڋ▜r¥׉퉈=¸£N\\ئ=Ë菽XV파x¹µإˋx²©døՊی*H'¦δ¸»{Xƽ؊=\0¼\0¾¹囉«JڴٹOإ¹؉螜røý ʄXý§ŇĽ}׺°¾ ù)y'٧Ñى̨ù[l(5`f\\Á`¿ùe.lY(¹=zה!Y%h¾O¹+ù`ٙ\"e 拧ėºK򹥾¿¯£¸ÿ ߚ٣S¹EIYû.H֊tG·`¾H¼J5»͵~ ¸6C¥hø§ùXDz\nx¡yshFK¡c¡zj¢ZY8(¹þ%ِ|yI«£ߑ؃چ饡úY¡X»¡u¢ڠ´ک]¦ڣ¡ڍ¥ú;ȧù򾇡Q T©øüú¨ [~W龙c݂z©úµz¥º½¢ú\r¬:  \0貙û¢x)ʡªúɡ¹K¦ú+§z!£ӀC+°´ٮ⃯:ݎ§ª¤ú©¢Zgû~z4f¥¯	¥:÷£sºӪ꫚õxʂ%»=³Gۉf3?ú㎸¿µ+Y´úq¶@̻Gúᙹ¶»oµّ´۰\rª~Á{W¶[·¹鮹躜0Ɯ\»·;e¹ۡ¶YI\"·¸zdk©Zö|[uuτ+׹9q¼¹nR ˮ¥B»ؗz|\rᤄýk¤^»[1ªۥ.pA­2<۽¼ء蜤黖5)³m¸!»јXýºYø¨5vT\\®QÀ%:À¢>Àɛۻ¸e|/·yÁń§ŗ§xנ|g®ӄC݆\\ü¼<¼9z\\®#𮆖;8¡莍X7ø׊Μ"8&d5¬P4Gj?ʜ0ܿ\"=­ùHER");}elseif($_GET["file"]=="dark.css"){header("Content-Type: text/css; charset=utf-8");echo
lzw_decompress("h:M±h´ħƃȨ0ÁLЁह1¢S!¤ۉF!°朢-6NĢdGgӈ°º;Nr£)öc7\r稈آ81s9¼¤ܫ\r磩ʭ8OVA¡£1c34Of*ª- P¨1©r41ٮ6̤2ց®ۯ½܌#3BǦ#	֧9Φꘌfc\rǆIЂb6EC&¬ЬbuĪm7aV㕂Ás²#m!��岹޶\\3\rL:SA¤k5ݮǟ·׬ýʒaF¸3阒e6fS¦빾󈸲!ǌú -΋,̳L‘ºJ¶˲¢*J 䬵£¤»	¸𓗂¹Áb©c蠹­깹¤怏Ԑ迃Hܸ£ \\·È궾«`𖅎¸޻A༄T'¨p&q´qE괅\rl­è¼5#pώȒ ѣIݥꦗBI؞ܲ¨>ʫ29<«嗃2¯¶7j¬8jҬc(nԄ翨a\0ŏ@5*3:δ涌£氌㭂́ÀlLPƴ@ʉ°Ꜥ¡H¥4 n31¶汍t򰮡͙9闏!¨r¼ڔ؜ە興£ùQ°¹6膱¬«<ø7°\r-xC\n ܣ®@Ҹܔԑ:\$iܘ¶m«ª˴틩d¬²{\n6\rxhˋ⣞'4Vø@a͇<´#h0¦S歅c¸ֹ+p«a2ԣyh®BO\$Á繶wiXɔùVY9*r÷Htm	@b֑|@ü/l\$z¦­ +ԥp2lɄ.õغՖ۬ķﻘǦ{À˭X¨C<l9𭶸9ﭬ򙤃¯À­7RüÀ0\\괎÷Pș)AȯÀxĚq͏#¸¥Ȧ[;»ª6~Pۜra¸ʚTGT0謗u¸ޟ¾³ޜn3𜜠\\ʎJ©udªCGÀ§©PZ÷>³Áûd8֒¨話½􃿖·dLퟔ�.(ti­>«,􃃖Ò+9iޞC\$䝘#\"΁ChVb\nЊ6𔲃ewᜮf¡À6m	!1'cÁ他تeLRn\r쾇\$􃲓\$ᘰÀꙡ'«l6&ø~Ad\$느\$s ¦ȃB4򉝩jª.ÁRC̔Qj\"7\n㘳!²6=΂Ȁ}");}elseif($_GET["file"]=="functions.js"){header("Content-Type: text/javascript; charset=utf-8");echo
lzw_decompress("':̢Фi1㳱ԝ	4̀£̑6a&󐰇:OAI줥:NFᄼݡCyꭲ˅\"㉔ʲ<̱ي/C#ٶ:DbqSeJ˦Cܺ\n\n¡ǱS\rZH\$RAܞS+XKvtdܧ:£�EvXŞ³jɭҩejײM§©亁B«ǈ&ʮL§C°3呰ՌƩ-x蜮ә섓ȂyNa䐮:煛¼䨳͐( cLŜ/õ£(ƈ5{޴Qy4øg-ý¢ꩴڃfЎ(իbUýϫ·܎&㺎ä��b¾¢ؠ.­ہ\rΐܼ»σĺ¼͜n ©ChҼ\r)`蘥`淥CʒȢZùµ㘊<QűX÷¼@·0dp9EQüf¾°ӆ؜r䡍拨h��Ünp'#Č¤£H̔(i*r¸榼#¢淋Ȉ~# ȓA:N6㰊©lլ§\r􁊐γ£!@Ҳ>Cr¾¡¬h°N᝞¦(a0M3Ͳ׶ԕ愣E2'!<·£3R<𛂏㘒攃Hη#n䫱a\$!蜲Ј0¤.°wd¡r:Yö¨酲慡]<¹j⥳@ߜ\װl§_\rÁZ¸ғ¬TͩZɳ򎳏\"²~9À©³jㅉPؖ)QYbݕD동c¿`zᣞµѨ̛'룴BOh¢*2ÿ<ŒOꦧ-Z£գ 踎aОú+r2bø\\ᾰ©ᾌ¥ùש¸Áޮٞp!#`卫Zö¸6¶12׃@鲫yȆ9\r줂3烰ޅ輣!p9஑o6s¿𣆘3홠bA¨ʶ񹦽ÀZ£#6ûʥ?s¨Ȝ"ω|؂§)þbJc\r»½Nޟsɛih8ϐ¹束躞;躈垌õuI5û@豍ªA萗aH^\$H׶ㅖ@ÛL~¨ùb9'§ø¿±S?PЭ¯򘰍C𜮘R򎭌4ޓȓ:Àõ܇Ը򄌴µh(k\njIȶ\"EY#¹Wrª\rG8£@tСXԓ⌂S\nc0ɫC I\rʰ<u`A!󩎐Բփ¢\0=¾ 斡䐈1ӢK!¹!埐pĝIsѬ6⤞éɎi1+°ȏ┫꼕¸^	ᜮɲ0´Fԉ_\$멦\0 ¤C8E^¬į3W!א)u*䟑Ԩ&\$ꔲY\n©]Ek񄚖¨\$xTse!RY» R`=L򸣄ޫ\nl_.!²V!r\nHЫ²\$א`{1	|± °i<jRrPTG|w©4b´\r¡ǆ4d¤,§E¡ȶ©䏼è[Nq@Oi׾'ѩ\r¥󗻦]#潐0»ASIJdс/QÁ´⸵t\r¥UGğG<鍼y-Iɺ򄤝М" P B\0ý텀ȞÁq`Aa̡J堒䊮)JB.¦TܱL¡÷ Cpp\0(7cYYa¨M鱕em4Ӗc¢¸r£«S)o񍠂p权!I¼¾Sb0m챎(dEHø¸߳Xª£/¬P©踙yƘ鸵Ȓ\$+֖»²gd茀öΎyݐ܏³Jט렢lE¢ur̬dCX}e¬셑¥õ«m]в ̽Ȩ-z¦Z庻Iö\) ,\n¤>򩷞¤朲VS\njx*w`ⴷSFi̓d¯¼,»ᐖZFM}Њ À\\Z¾P읠¹zؚûE]�ɟO룭ԁ]À ¬Á%þ\"w4¥\n\$øɺV¢SQDۺݶ«䇋wMԮS0B-sƪ)㾚�|˞R8kMsd¹ka)h%\"PͰnn÷/Á#;֧\rdȐ¸8ކ<3\$©,咐);<4`Λ¢<2\nʵ钇@w-®ፗAϜ0¹ºª¹Lr옙Cࡏ>º昴ºLõ첂yto;2ݑª±trm躧A�¡÷ANºݜ\\"kº5oV뉃=7r1ݰ䁶\\+9ª※°瞨if¬=·rҏºuڊût؝yӞЙùCö¶ºÁ³ҵ݇ܧi¥vf݂ù+¥Ø|ʬ;¸ ]~ӊ|\re÷¥쿓݂څ'�¦䔯²°	½\0+Wcoµw6wd Su¼j¨3@򰡣÷\n .wm[8x<²ˣM¬\n9ý²ý'aùގ1>Ȅ£[¶ﵺdx¯༜"Yc¸ނ!i¹¥ꕷÀ}��¹kººܘ]­¶¸ԒÀ{󉗚R¥=f W~杉(bea®'ubףּ>)\$°P÷᭚6þR*IGu#ƕUKµAXtѨӠ_ܢ ¾£p¸ &Uˋى퉝ýÁYG6P]Ar!b¡ *ЙJoµӯ哿󁯁򶽽*À ء难_ªÀٴB³_~RBiKùþ`牦Jە\0­􌮎\0М$̅þ僂K SЎ򢪚¤І ̻0pvMJ bN`Lÿ歃eº/`RO.0P串`ꉥüƂ¸d GxǢP-(@ɸӀ洨H%<&À̚قÀ腰¬°%\0®pЇЄø꣉¯	ȯ\"ö¢J³¢\ns_À̜rৎ`!k䰘	萺Ķ�p\$ú'ퟜ�RUeZÿ¨d\$윮LᎆBº↳.ޤnҴm>vj䕭)	Mº\r\0®ʊHќ"5*!eºZJº蒫ㆦ(dc±¼(xܑjg\0\\õµÀ¶ Z@º઼`^r)<(ȩ̜몳ʐ쀙k­̭l3Qyс@ɘѐfάPn璼¨Д 򯍎·mRձ³�mvúN֍|úШZ²ȆڨYpø\"4Ǩ栲&lҐ`Ā£Xx bbdв0Fr5°<»C挲z¨¯6䨥!¤\rdz؋;Ĵ³²\nٍ HƋQ\$QEnn¢n\rÀ©#T\$°²ˈ(ȟѩ|c¤,¼-ú#蚜r ܡJµ{dѝE\n\$²ƂriTԲ+ŲPEDBe}&%Rf²¥\nü^􈃒ȚڠRVŁ,ѻ«缎윰O1锪c^\r%\r 쫠Ү\0y1蔮°\r´ĂK1捳H®\r\"û0\0NkXPr¸¯{3 콉\nSȤڗx.Z񒔱wS;53 .¢s4sO3FºٲS~YFpZs¡'΀ّOqR4\n­6q6@Dhٶ͕7vE¢l\"Ş;-娂&Ϣ*²*򮡠䜲!#縧G\"͆wÁ\"úՠȏ2!\"R(vÀX漜"D̶À¦)@ᆓ,¸zm򁆍wT@ÀԠ Мn֓𺐫hдIDԐ\$m>朲&`>´4ȒA#*룒<w\$T{\$´4@dӴRem6¯-#Dd¾%E¥DT\\ \$)@܋´WC¬(t®\"Mܣ@úTF\r,g¦\rP8þ´֣Jü°c öĹƂꠊ\"LªZԤ\r+P4ý=¥¤S♔õA)0\"¦CDhǍ\n%F԰֓ü|fLNlFtDmH¯ªþ°5彈͜nļ4ü³õ\$ྋ񶜲bZਜr\"pEQ%¤wJ´ÿV0ԅM%嬜"hPFၣ®򯇒6 h6]5¥\$fS÷CLiRT?R¨þC񵣈U§Z¤晢Fþ/殪Zܜ"\"^ι´6RG ²̮⺜\$ªѥ\\&O֨v^ ϋUºѮΒam³(\rﺌ¯¾ü\$_ª楱+KTtض.ٖ36\n룵:´@6 újPÁQõF/S®k\"<4AgAСU\$'놈ӡf໑O\"׫~²S;ŀ½󮯋: k¼9­ü²󄎥]`nú¼ҭ7¨;V˝⸗À©2H¢U®YlB�ö⯎֔´°¶ö	§ý⮰®։l¾m\0񴂲)¥XÁ\0ʂQ߱FSq4ÿnFx+pԲ¦EƓovúGW7oׄw׋RW׈\r4`|cq,ױ9·u ϵ÷cq䒜"LC tÀh⩧\rʏÀ\\øW@ɧ|D#S\r%5l桥++垇k^ʙ`/7¸(z*񘋀ퟝ�Eݻ¦S(W׭Xė0V£0ˑ¥	~릂닕2Q­ꂲu mC¬됄£tr(\0Q!K;xNýWÀúÿ§øȁ?b< @ŗ`֘,º`0eºƂN'²¤&~øtӵ\"| ¬i 񂥠 7¾Rø ¸lSu°8AûdF%(Ժ 亯󿳀A-oQź@|~©KÀʞ@x󟍢~D¦@س¸TNŚC	W҂ix<\0P|Ħ\n\0\n`¨¥ ¹\"&?st|ïwਭd굀N£^8À[t©9ªB\$అ§©ퟖ�'\">U~ÿ98 铲ÔFĦ °¹uȅ°/)9À\0ᘫAùz\"FWAx¤\$'©jG´(\"ٌ ±s%THe,	M7¼ ǅء ˓ƃ·&wYԏ3°ظ /\rϖù¯ٻ\"ùݜp{%4b󌠭¤Ե~n卅3	Ό °9峘ֿd䕏Zŏ9栗@¨l»f¯õؑbP¤*Go兠8¨¯ùA悼Àz	@¦	ݒb¡Zn_ͨº'ѢF\$f¬§`ö󺆈dDdH%4\rs΁jLRȧ޹fڹg IϘ,R\\·øʖ>\nH[´\"°À\rӁL̬%놌l8gzL缰ko\$ǫ­᠒ËPԶ値ϧV:V؍ü%±蕀ø6Ǽ\r๔«®LE´NԀS#ö.¶[x4¾a猭´LL® ª\n@£\0۫tٲ圮^F­º¥º5`͝ R7ȬL uµ(dº¡¹ Ԝr䂦/uCf״ÿcҞ B﬎_´nLԜ0© \$»Ʀ¶¸~ÀUkﶥe􋥦˲\0ZaZXأ¦|Cq¨/<}س¡Ńº²º¶ Zº*­w\nO㇅z`¼5®18¶cøû®¯­®暉ÀQ2YsǜK朮£\\\"­ ðc򖪵B¶钱<3+õņµ*ؓ雵4ӭ쭛:RhITdevΉµH䨒-Zw\\ƥn赶\n̗ө\$Յow¬+© ºù˲ɂ¶&Jq+û}҄฼Ӫ«dŎ?敥BBeǯM¶Nm=τ󕷂¢\$HRfªwb|²x dû2掩S೘gɀ߾Γv §|﫲x½\0{ԃR=Fÿώ΢®ϣr½8	ퟡ�vȸ*ʳ£{2Sݫ;S¦ӨƫyL\$\"_۫©B縇¬ݜ"E¸%ºŚº\nøЂp¾p''«p󷘕Ҫ\"8бI\\ @ ʾ Lnퟺ�RߣM䞄µþqLNƮ\n\\̎\$`~@`\0u牾^@լ-{5񔬀bruÁo[Á²¾¨ս鯱y.ש {鶱°RpМ$¸+13ۚúڐú+¨O!D)® ܮu<¯,«ᱟ=Jdƫ}µd#©0ɞcӂ3U3»EY¹û¢\rû¦tj5ҥ7»e©wׄǡúµ¢^q߂¿9Ɵ<\$}k퍲RI-ø°¸+'_Ne?SیR�*X4鮼c}¬蜢@vi>;5>Dn \r䫩bN镵P@Y䇼񖨶iõ#PB2A½-흰d0+ퟗ�Kûø¿�n飼ddøOÀ¯塆cüi<ú0\0\\ù끑gù檡NTi'  ·��mjᐜŷ»¸uΊ+ªV~À²ù 'ol`ù³¿󜢬ü̚£דFÀ喉ý⻃©¸¤þT aώEۃQư´ p+?ø\nƾ'l½¤* tɆKάp°(YC\n-q̔0圢*ɕÁ,#üⷷº\"%¨+qĎ¸ꂱ°=婮@x7:ťGcYIН0*kÀۈ\\·¯𑐟{¤ ŇǣÁý\r终³[p¨ >7ӣh뮍΂Ԯµ£¦S|&J򍇾8´ÀmOhþĭ	ՑqJ&aݢ¨'.b珰ج\$ö­܌D@°CHB	Ȧ❡|\$Ԭ-6°²+̫  pºଡAC\rɓ쯎0´񐂮¢MéZnE͢j*>û!Ңu%¤©gذ£
