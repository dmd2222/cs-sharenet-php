<?php
/*
The MIT License (MIT)
Copyright (c) 2015 Jan Knipper <j.knipper@part.berlin>
Copyright (c) 2021 CS-Digital UG <info  @cs  -  digital-   ug . ~~ de >
Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:
The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
SOURCE: https://github.com/dmd2222/php-ban-ip
   */
    if (!empty($_POST['csd_identifier']) || !empty($_GET['csd_identifier'])) {
        var_dump("csd_identifier:","found-csd-identifier",(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
    }
/*
-----------  CONFIG ----------- 
*/

require_once("php-ban-ip/banip.php");
require_once("updater_class.php");

$__port = 80; // the port of your server
$__max_relay = 10000; // how many server addresses the relay will store
$__save_messages = true; // true to store messages on the server --- you can't relay without saving the latest messages
$__callback = true;

// -------------------------

$__use_url=true;//use url or ip?





//URL or ip?
if($__use_url==true){//use url
    #$actual_link = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
    #$actual_link="https://idenlink.de/api_online/Sharenet/1/relay_one.php";
    $actual_link = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
    $pfile = strlen(Explode('/', $_SERVER["SCRIPT_NAME"])[count(Explode('/', $_SERVER["SCRIPT_NAME"])) - 1]);
    $rest = substr( $actual_link, 0, -intval($pfile));
    $actual_link  = $rest . "relay_one.php"; 

    $__host=$actual_link; 
}else{
    $__host = $_SERVER['SERVER_ADDR'].':'.$__port;
}

$sharenetHost = $__host;




//FORCE UPDTAE
if(updater::do_file_update("relay_one.php","https://raw.githubusercontent.com/dmd2222/cs-sharenet-php/main/relay_one.php")==false){
    die("updateerror.");
}

//Make less changes before update

//delete old files
if(countdown_trigger("data","1",86400,"1")==true)
{
    del_files_one_type_older_than_every_timespan("data","snm",86400);
}





if(!file_exists("data/config/serv.lst")) {
    @mkdir("data");
    @mkdir("data/config");
    @file_put_contents("data/config/serv.lst","https://idenlink.de/api_online/Sharenet/relay_one.php"); //first seed
    @touch("data/config/user.lst");
}
if(isset($_GET['message'], $_GET['from'], $_GET['date'], $_GET['author'])) {
    if(strlen($_GET['message'])>200) {
        echo "error:too long";
        exit;
    }
    if(!is_numeric($_GET['date'])){ echo "error:date";exit; }
    // Author checking
    $author = explode("@", $_GET['author']);
    if(isset($author[0], $author[1])) {
        $domain = explode(":", $author[1]);
        if(isset($domain[1])) {
            $port = $domain[1];
        }
        else
        {
            $port = 80;
        }
        $fdomain = explode(":", $_GET['from']);
        if(isset($fdomain[1])) {
            $fport = $fdomain[1];
        }
        else
        {
            $fport = 80;
        }
    
        $hash = sha1(htmlspecialchars($_GET['message'].$_GET['author']).md5(htmlspecialchars(substr($_GET['message'], 0, 200))).$_GET['date']);
             //use url
             if($__use_url==true){
                //use url
                $temp_string=$_GET['from']."?hash=". $hash ;//. "&from=" . $__host ;
                $from_serv_response = @file_get_contents($temp_string);
            }else{
                //use ip
                $temp_string="http://".$fdomain[0].":".$fport."/relay.php?hash=".$hash;
                $from_serv_response = @file_get_contents($temp_string);
            }
           


        
        if(!preg_match("#ok#",$from_serv_response)) { 
            echo "error:unknown source: from_serv_response: " . $from_serv_response . "<br>";
            echo "(pos1)temp_string: ".$temp_string; 
            exit;
         }
        if(@fsockopen($domain[0], $port) || $__use_url==true) {
            if(!file_exists("data/".$hash.".snm")) {

                    //use url
                    if($__use_url==true){
                        //use url
                        $temp_string=$_GET['from']."?user=".urlencode($author[0])."&hash=".$hash;
                        $main_serv_response = @file_get_contents($temp_string);
                    }else{
                        //use ip
                        $temp_string="http://".$domain[0].":".$port."/relay.php?user=".urlencode($author[0])."&hash=".$hash;
                        $main_serv_response = @file_get_contents($temp_string);
                    }

                if(preg_match("#ok#",$main_serv_response)) {
                
                    $file['hash'] = $hash;
                    $file['date'] = htmlspecialchars($_GET['date']);
                    $file['author'] = htmlspecialchars($_GET['author']);
                    $file['from'] = htmlspecialchars($_GET['from']);
                    $file['message'] = htmlspecialchars(substr($_GET['message'], 0, 200));
                    $file = json_encode($file);
                    if($__save_messages) {
                        $cnt = fopen("data/".$hash.".snm", "w+");
                        fputs($cnt, $file);
                        fclose($cnt);
                    }
                    if($__callback) {
                         callback($file);
                       
                    }

                    $servs = file_get_contents("data/config/serv.lst");
                    $serv_list = preg_split('/\r?\n/', $servs);
                    if(!in_array(htmlspecialchars($_GET['from']), $serv_list) AND count($serv_list)<$__max_relay) {
                        $slist = fopen("data/config/serv.lst", "a");
                        fputs($slist, "".PHP_EOL.htmlspecialchars($_GET['from']));
                        fclose($slist);
                    }
                    if(!in_array(htmlspecialchars($author[1]), $serv_list) AND count($serv_list)<$__max_relay) {
                        $slist = fopen("data/config/serv.lst", "a");
                        fputs($slist, "".PHP_EOL.htmlspecialchars($author[1]));
                        fclose($slist);
                    }
                    foreach($serv_list as $line) {
                        $host = explode(":", $line);
                        if(isset($host[1])) {
                            $hport = $host[1];
                        }
                        else
                        {
                            $hport = 80;
                        }
                        
                                //redirect to both
                                //use url
                                $temp_string=$line ."?message=".urlencode($_GET['message'])."&author=".urlencode($_GET['author'])."&date=".urlencode($_GET['date'])."&from=".$__host;
                                $fgc = @file_get_contents($temp_string);
                   
                                //use ip
                                $temp_string="http://".$host[0].":".$hport."/relay.php?message=".urlencode($_GET['message'])."&author=".urlencode($_GET['author'])."&date=".urlencode($_GET['date'])."&from=".$__host;
                                $fgc = @file_get_contents($temp_string);
                            
                    }
                    echo "ok";
                }
                else
                {
                    echo "error:origin error";
                }
            }
            else
            {
                echo "error:already stored";
            }
        }
        else
        {
            echo "error:domain";
        }
    }
}

// Checking
if(isset($_GET['hash'], $_GET['user'])) {
    $users = file_get_contents("data/config/user.lst");
    $user_list = preg_split('/\r?\n/', $users);
    if(in_array($_GET['user'], $user_list) AND file_exists("data/".str_replace(".","",$_GET['hash']).".snm")) {
        echo "ok";
    }
}

if(isset($_GET['hash'])) {
    if(file_exists("data/".str_replace(".","",$_GET['hash']).".snm")) {
        echo "ok";
    }else{
        echo "not found";
    }
}




/*
ShareNetLib ----------------------
Sample functions that can be used with a ShareNet relay.
License : http://creativecommons.org/licenses/by-nc-nd/3.0/
https://sourceforge.net/projects/sharenet/
----------------------------------

sharenetGetUserList()
sharenetGetServerList()
sharenetIssetUser()
sharenetAddUser($name)
sharenetAddServer($address)
sharenetSend($user, $message)
sharenetOpenMessage($hash)
*/




function callback($data){
    //do something callback
    $cnt = fopen("data/callback.snm", "w+");
    fputs($cnt, $data);
    fclose($cnt);
}


function sharenetGetUserList() {
	$users = file_get_contents("data/config/user.lst");
    $user_list = preg_split('/\r?\n/', $users);
    return $user_list;
}


function sharenetGetServerList() {
	$users = file_get_contents("data/config/serv.lst");
    $user_list = preg_split('/\r?\n/', $users);
    return $user_list;
}

function sharenetIssetUser($u) {
	$ul = sharenetGetUserList();
	if(in_array($u, $ul)) {
		return true;
	}
	else
	{
		return false;
	}
}

function sharenetAddUser($u) {
	file_put_contents("data/config/user.lst", file_get_contents("data/config/user.lst").PHP_EOL.$u);
}

function sharenetAddServer($u) {
	file_put_contents("data/config/serv.lst", file_get_contents("data/config/serv.lst").PHP_EOL.$u);
}

function sharenetSend($user, $message) {

 

   	if(sharenetIssetUser($user)) {
	    $host = $GLOBALS['sharenetHost'];
        $__use_url = $GLOBALS['__use_url'];
	    $date = time();
	    $author = $user."@".$host;
	    if(strlen($message)<201) {
          
	        $hash = sha1(htmlspecialchars($message.$author).md5(htmlspecialchars(substr($message, 0, 200))).$date);
	        $file = array("hash"=>$hash, "date"=>$date, "from"=>$host, "author"=>$author, "message"=>$message);
	        file_put_contents("data/".$hash.".snm", json_encode($file));
            //check if written
            if(file_get_contents("data/".$hash.".snm")<>json_encode($file)){
                die("Error: " . "data/".$hash.".snm" . "not written correctly.");
            }
	        $s = file_get_contents("data/config/serv.lst");
	        $r_list = preg_split('/\r?\n/', $s);
	        foreach($r_list as $relay) {
                //use url
                if($__use_url==true){
                        //use url
                        $temp_string=$relay."?message=".urlencode($message)."&author=".$author."&date=".$date."&from=".$host;
                        $get = file_get_contents($temp_string);
                }else{
                        //use ip
                        $temp_string="http://".$relay."/relay.php?message=".urlencode($message)."&author=".$author."&date=".$date."&from=".$host;
                        $get = file_get_contents($temp_string);
                }
                
	            
	            if(preg_match("#ok#", $get)) {
	           
                    return "ok: ".$relay . " temp_string: " . $temp_string . "<br>";
	            }
	            else
	            {
	                echo htmlspecialchars($get).">> not successfull>>".$relay . "<br>";
                    echo "temp_string: " . $temp_string . "<br>";
                    echo "hash: " . $hash . "<br>";
	            }
	        }
	    }
	}
}

function sharenetOpenMessage($hash) {
	return json_decode(file_get_contents("data/".$hash.".snm"));
}



//for deleting files
function del_files_one_type_older_than_every_timespan($dir ,$filetype_to_delete , $seconds_older_delete){
    /*** if file is 24 hours (86400 seconds) old then delete it ***/
    //8035200 sek ~3 monate
    //5356800 sek ~ 2 Monate
    //3456000 sek ~ 40 tage
    //2678400 sek ~ 1 Monat
    //1209600 sek ~ 14 Tage
    //864000 sek ~ 10 tage
    $debug_mode=false;

    // cycle through all files in the directory 
    if($debug_mode)echo(" dir: " . $dir . ", filetype_to_delete: " . $filetype_to_delete . ", seconds_older_delete: " . $seconds_older_delete);//DEBUGGING
    foreach (glob($dir."*." . $filetype_to_delete) as $file) {
        
        $time_result=time() - filemtime($file);

        if($debug_mode)echo(" time_result: " . $time_result); //DEBUGGING

        if($time_result > $seconds_older_delete){
            // Use unlink() function to delete a file  
                if(!unlink($file)){
                    //was not possible to delete
                    die("Error: del_files_one_type_older_than_every_timespan: deleting file");
                }
            }
    }
}


function countdown_trigger($dir,$trigegr_no,$next_time_difference_in_seconds_int, $trigger_value=''){
//check vars
is_int(intval($trigegr_no));
is_int(intval($next_time_difference_in_seconds_int));
$file_path_name = $dir . "countdown_trigger". $trigegr_no . ".json";
//check file exist and create if not
if(!is_file($file_path_name)){     
    file_put_contents($file_path_name, $trigger_value);     
    chmod($file_path_name,0600);
}
}


?>
