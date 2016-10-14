<?php
error_reporting(0);


$from = $argv[1];
$to = $argv[2];
$subject = $argv[3];
$hmf = $argv[4];

function checkRemoteFile($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    // don't download content
    curl_setopt($ch, CURLOPT_NOBODY, 1);
    curl_setopt($ch, CURLOPT_FAILONERROR, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if(curl_exec($ch)!==FALSE)
    {
        return true;
    }
    else
    {
        return false;
    }
}


foreach(range('a','z') as $letter){
$alpha[] = $letter;
}

shuffle($alpha);
//                              echo $alpha."\r\n";

$i = 0;

//print_r($alpha);

while($i <= 12){
$boundry .= $alpha[$i];
//                              echo $boundry."\r\n";
$i++;
}
$boundry = $boundry.$boundry.$boundry;


if(!filter_var($from, FILTER_VALIDATE_EMAIL)){
//if(!preg_match("/^[A-z0-9._%+-]+@[A-z0-9.-]+\.[A-z]{2,63}$/g",$from,$matches)){

        echo "Usage: \r\n";
        echo " mail.php [from_address] [to_address] [subject] [html_message_file]\r\n";
        echo "\r\n";
        exit(0);
        };

if (file_exists($hmf)) {
//                                          echo "The file $hmf exists\r\n";
        $html = file_get_contents($hmf);
        //do more stuff with the file.
        $src = simplexml_import_dom(DOMDocument::loadHTML($html))->xpath("//img/@src");
        /*
        $input_file = fopen($src[0][0][0],rb);
        while(!feof($input_file))
{
    $plain = fread($input_file, 57 * 143);
    $encoded = base64_encode($plain);
    $encoded = chunk_split($encoded, 76, "\r\n");
    $enc[0] .= $encoded;
}
*/


        $ii = 0;
        foreach($src as $s){
        if(checkRemoteFile($s[0][0])){
                $encoded = base64_encode(file_get_contents($s[0][0]));
                    $enc[$ii] = $encoded;

//                      fclose($fhandle);
                                $image_prefix = "data:".mime_content_type($s[0][0]).";base64,";
//                              echo "replacing: ".$s[0][0]." - with: ".$image_prefix.substr($enc[$ii],7).".....\r\n\r\n";
                                $html = str_replace($s[0][0], $image_prefix.$enc[$ii], $html);
                        }
                        $ii = $ii++;
                }
//      else echo $s[0][0]." is not a vlaid file.";
        //}

//echo "\r\n\r\n";




} else {
    echo "The file $hmf does not exist";
        exit(1);
}


$message = "From: ".$from."
To: ".$to."
Subject: ".$subject."
Mime-Version: 1.0
Content-Type: multipart/related; boundary=\"".$boundry."\"; type=\"text/html\"

--".$boundry."
Content-Type: text/html; charset=\"US-ASCII\"

";

$message .= $html;

$message .= "

--".$boundry."--

";
echo $message;
?>
