<?php
require_once 'config.php';
require 'vendor/autoload.php';

//Fetching xkcd json object
$comicNo=rand(1,2540);
// $comicNo=200;
$url="https://xkcd.com/$comicNo/info.0.json";
$json = file_get_contents($url);
//$img=$json->img;
$json = json_decode($json);
//echo $json->img;

//$transcript=$json->transcript;
$image=$json->img;
$num=$json->num;

//SendGrid Mail Content and Config
$email = new \SendGrid\Mail\Mail();
$email->setFrom("**************", "Harsh Abichandani");
$email->setSubject("Your every 5 minute XKCD comic is here ! Enjoy !");
$email->addTo("***************", "XKCD comic Subscriber");
//$email->addContent("text/html", "$transcript");
//Comic Image inline of mail
$email->addContent(
    "text/html", "<strong>rtCamp XKCD Comic Assignment - PHP</strong> <br> <br> <img src='$image'>"
);

//Saving comic image in image directory to use as attachment in mail
$url = $image;
$img = "image/$num.png";
file_put_contents($img, file_get_contents($url));
$file_encoded = base64_encode(file_get_contents("image/$num.png"));
$email->addAttachment(
   $file_encoded,
   "image/",
   "img.png",
   "attachment"
);

//Data cleaning - Image deletion after sending mail
$file_pointer = $img;
if (!unlink($file_pointer)) {
    echo ("$file_pointer cannot be deleted due to an error");
}
else {
    echo ("$file_pointer has been deleted");
}

//SendGrid Send mail function call
$sendgrid = new \SendGrid(SENDGRID_API_KEY);
try {
    $response = $sendgrid->send($email);
    print $response->statusCode() . "\n";
    print_r($response->headers());
    print $response->body() . "\n";
} catch (Exception $e) {
    echo 'Caught exception: '. $e->getMessage() ."\n";
}
