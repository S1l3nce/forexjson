<?php


// Return javascript code
header("Content-Type: text/javascript");



// Function that returns a forex array
function get_forex() {
    $currencies = array("AED", "ANG", "ARS", "AUD", "BDT", "BGN", "BHD", "BND",
      "BOB", "BRL", "BWP", "CAD", "CHF", "CLP", "CNY", "COP", "CRC", "CZK", "DKK",
      "DOP", "DZD", "EEK", "EGP", "FJD", "GBP", "HKD", "HNL", "HRK", "HUF",
      "IDR", "ILS", "INR", "JMD", "JOD", "JPY", "KES", "KRW", "KWD", "KYD", "KZT",
      "LBP", "LKR", "LTL", "LVL", "MAD", "MDL", "MKD", "MUR", "MVR", "MXN", "MYR",
      "NAD", "NGN", "NIO", "NOK", "NPR", "NZD", "OMR", "PEN", "PGK", "PHP", "PKR",
      "PLN", "PYG", "QAR", "RON", "RSD", "RUB", "SAR", "SCR", "SEK", "SGD", "SKK",
      "SLL", "SVC", "THB", "TND", "TRY", "TTD", "TWD", "TZS", "UAH", "UGX",
      "UYU", "UZS", "VEF", "VND", "XOF", "YER", "ZAR", "ZMK", "ZMW");
    $from = array("USD", "EUR");
    

    $data = array("forex" => array(),
                  "last_updated" => time(),
                  "last_update_timestamp" => date("Y-m-d H:i:s"));
    

    for($i=0;$i<count($currencies);$i++) {
    // for($i=0;$i<1;$i++) { // use it when testing
        for($j=0;$j<count($from);$j++) {
    
    
            $f = $from[$j];
            $c = $currencies[$i];
    
            $web = file_get_contents("https://www.google.com/finance/converter?a=1&from=".$f."&to=".$c);
            $web = explode("\n",$web);
            $web = $web[212];
    
            $number = substr($web, 58);
            $number = substr($number,0,strlen($number)-11);
    
            $data["forex"][$f.$c] = $number;
    
        }
    }


    return $data;
}





$json = false;
$file = ".data.json"; // the file to store the tmp json file

// Check if file exits and if last update was less than an hour
// to stop the script and return the result since it hasn't changed
if(file_exists($file)) {
    $fopen = fopen($file,"r");
    $content = json_decode(fread($fopen, filesize($file)));
    fclose($fopen);
    if(time() - $content->last_updated < 3600) {
        $json = json_encode($content);
    }
}
if(!$json) {
    $json = json_encode(get_forex());
    $fopen = fopen($file, "w+");
    fwrite($fopen, $json);
    fclose($fopen);
}

if(isset($_GET["callback"])) echo $_GET["callback"]."(";
echo $json;
if(isset($_GET["callback"])) echo ")";

?>
