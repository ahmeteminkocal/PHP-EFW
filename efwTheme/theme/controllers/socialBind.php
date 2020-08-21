<?php
ob_start();

define('OAUTH2_CLIENT_ID', '');
define('OAUTH2_CLIENT_SECRET', '');

$authorizeURL = 'https://discordapp.com/api/oauth2/authorize';
$tokenURL = 'https://discordapp.com/api/oauth2/token';
$apiURLBase = 'https://discordapp.com/api/users/@me';

// Start the login process by sending the user to Discord's authorization page
if(get('action') == 'login') {

    $params = array(
        'client_id' => OAUTH2_CLIENT_ID,
        'redirect_uri' => 'https://'.\efwTheme\engine::getCurrentDomain(false).'/account/profile/bind/discord',
        'response_type' => 'code',
        'scope' => 'identify guilds guilds.join gdm.join'
    );

    // Redirect the user to Discord's authorization page
    header('Location: https://discordapp.com/api/oauth2/authorize' . '?' . http_build_query($params));
    die();
}


// When Discord redirects the user back here, there will be a "code" and "state" parameter in the query string
if(get('code')) {

    // Exchange the auth code for a token
    $token = apiRequest($tokenURL, array(
        "grant_type" => "authorization_code",
        'client_id' => OAUTH2_CLIENT_ID,
        'client_secret' => OAUTH2_CLIENT_SECRET,
        'redirect_uri' => 'https://'.\efwTheme\engine::getCurrentDomain(false).'/account/profile/bind/discord',
        'code' => get('code')
    ));
    $logout_token = $token->access_token;
    $_SESSION['access_token'] = $token->access_token;



}

if(session('access_token')) {
    $user = apiRequest($apiURLBase);
    \efwEngine\app\discord::regUser($user->id, $user->username, $user->discriminator, $user->locale, $user->avatar, $user->public_flags, session("access_token"));
    \efwEngine\app\system::redirect("/account/profile/settings/");
} else {
    echo '<h3>Giriş Yapılmamış</h3>';
    echo '<p><a href="?action=login">Giriş yap</a></p>';
}


if(get('action') == 'logout') {
    // This must to logout you, but it didn't worked(

    $params = array(
        'access_token' => $logout_token
    );

    // Redirect the user to Discord's revoke page
    header('Location: https://discordapp.com/api/oauth2/token/revoke' . '?' . http_build_query($params));
    die();
}

function apiRequest($url, $post=FALSE, $headers=array(), $auth = "Bearer", $method = "") {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

    $response = curl_exec($ch);


    if($post)
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
    if($method != ""){
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    }
    $headers[] = 'Accept: application/json';

    if(session('access_token'))
        $headers[] = 'Authorization: '.$auth.' ' . session('access_token');

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    return json_decode($response);
}

function get($key, $default=NULL) {
    return array_key_exists($key, $_GET) ? $_GET[$key] : $default;
}

function session($key, $default=NULL) {
    return array_key_exists($key, $_SESSION) ? $_SESSION[$key] : $default;
}

?>