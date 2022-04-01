<?php
//FORCE UPDTAE
function do_update($filepath,$update_path){

       
        $response_result = file_get_contents($update_path);
        
        //precheck result
        if($response_result ==false){
            die("Error: was not able to check update.<br>");
        }
        
        $contents="";
        $fs = fopen( $filepath, "a+" ) or die("error when opening the file");
        while (!feof($fs)) {
        $contents .= fgets($fs, 1024);
        #Debugging
        #echo fgets($fs, 1024) . "<br>";
        }
        fclose($fs);
        
        
        if($contents<>$response_result){
            $a = file_put_contents($filepath, $response_result);
            if($contents<>$response_result){
                die("Error: Update needed! Please update first.");
            }
        }else{
            return true;
        }


}

?>
