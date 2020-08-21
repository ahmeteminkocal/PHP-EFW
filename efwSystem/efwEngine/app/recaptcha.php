<?php


namespace efwEngine\app;


class recaptcha
{
    private static string $secret = "";
    private static string $secretV2 = "";
    private static string $site = "";
    private static string $siteV2 = '';
    public static function checkCaptcha($response = "", $version = 2){
        switch ($version){
            case 3:
                $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
                $recaptcha_secret = self::$secret;
                $recaptcha_response = $response;

                // Make and decode POST request:
                $recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
                $recaptcha = json_decode($recaptcha);
                // Take action based on the score returned:
                if ($recaptcha->score >= 0.5) {
                    // Verified - send email
                    return true;
                }
                break;
            case 2:
                if($response == "") {
                    if(isset($_POST["g-recaptcha-response"]))
                    $grecaptcharesponse = $_POST["g-recaptcha-response"];
                    if(isset($_GET["g-recaptcha-response"]))
                    $grecaptcharesponse1 = $_GET["g-recaptcha-response"];
                    $response = $grecaptcharesponse ?? $grecaptcharesponse1;
                }


                $ch = curl_init();

                curl_setopt_array($ch, [
                    CURLOPT_URL => 'https://www.google.com/recaptcha/api/siteverify',
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => [
                        'secret' => self::$secretV2,
                        'response' => $response,
                        'remoteip' => system::getUserIP()
                    ],
                    CURLOPT_RETURNTRANSFER => true
                ]);

                $output = curl_exec($ch);
                $recaptcha = json_decode($output);

            //    var_dump($recaptcha);
                // Take action based on the score returned:
                return $recaptcha->success;
                break;
        }


// Not verified - show form error
        return false;
    }
    public static function recaptchaJS($action, $version = 2){
        switch ($version){
            case 3:
                echo '    <style>
        .grecaptcha-badge {
            bottom:100px !important;
        }
    </style>
    <script src="https://www.google.com/recaptcha/api.js?render='.self::$site.'"></script>
    <script>
        grecaptcha.ready(function () {
            grecaptcha.execute(\''.self::$site.'\', { action: \''.$action.'\' }).then(function (token) {
                var recaptchaResponse = document.getElementById(\'recaptchaResponse\');
                recaptchaResponse.value = token;
            });
        });
    </script>';
                break;
            case 2:
                echo '
    <script src="https://www.google.com/recaptcha/api.js"></script>
    <script>
        grecaptcha.ready(function () {
            grecaptcha.execute(\''.self::$siteV2.'\', { action: \''.$action.'\' }).then(function (token) {

            });
        });
    </script>';
                break;
        }

    }
    public static function recaptchaInput($version = 2, $return = false){
        switch ($version){
            case 3:
                echo '                        <input type="hidden" name="recaptcha_response" id="recaptchaResponse">';

                break;
            case 2:
                if($return) {

                    return '       <input type="hidden" name="recaptcha_response" id="recaptchaResponse">     <div class="g-recaptcha" data-sitekey="' . self::$siteV2 . '"></div>';
                }else{
                    echo '       <input type="hidden" name="recaptcha_response" id="recaptchaResponse">     <div class="g-recaptcha" data-sitekey="' . self::$siteV2 . '"></div>';

                }
                break;
        }

    }
}