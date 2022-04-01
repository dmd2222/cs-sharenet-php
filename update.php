<?php

class updater{

    static public function do_file_update($filepath,$update_path){

        //check vars
        if(is_string($filepath)<>true)die("Not a valid file path.");
        if(is_string($update_path)<>true)die("Not a valid update path.");
       
        $response_result = file_get_contents($update_path);

        //check response
        if(is_string($response_result)<>true)die("Not a valid response result.");
        
        //precheck result
        if($response_result ==false){
            die("Error: was not able to check update.(For file: " .  $filepath . ")(Update path: " . $update_path . ")<br>");
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

}


?>
