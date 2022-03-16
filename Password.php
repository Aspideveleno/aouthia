<style>
    body { background: #9cb8b3; }

h1 {
    font: 600 1.5em/1 'Raleway', sans-serif;
    color: rgba(0,0,0,.5);
    text-align: center;
    text-transform: uppercase;
    letter-spacing: .5em;
    position: absolute;
    top: 25%;
    width: 100%;
}

span, span:after {
    font-weight: 900;
    color: #efedce;
    white-space: nowrap;
    display: inline-block;
    position: relative;
    letter-spacing: .1em;
    padding: .2em 0 .25em 0;
}

span {
    font-size: 4em;
    z-index: 100;
    text-shadow: .04em .04em 0 #9cb8b3;
}

span:after {
    content: attr(data-shadow-text);
    color: rgba(0,0,0,.35);
    text-shadow: none;
    position: absolute;
    left: .0875em;
    top: .0875em;
    z-index: -1;
    -webkit-mask-image: url(//f.cl.ly/items/1t1C0W3y040g1J172r3h/mask.png);
}
</style>
<?php
$conn = new mysqli("localhost", "username", "password", "my_aspid");
require_once 'php-jwt-main/src/BeforeValidException.php';
    require_once 'php-jwt-main/src/ExpiredException.php';
    require_once 'php-jwt-main/src/SignatureInvalidException.php';
    require_once 'php-jwt-main/src/JWT.php';
    require_once 'php-jwt-main/src/Key.php';

    use Firebase\JWT\JWT;  
    use Firebase\JWT\Key; 
    use Firebase\JWT\ExpiredException;
    use Firebase\JWT\SignatureInvalidException;
    use Firebase\JWT\BeforeValidException;
if(isset($_POST['Mail']))
{
    $sql = "SELECT Password,Username FROM utenti Where Email = '" . $_POST['Mail']."' ;";
    $result = $conn->query($sql);
    if ($result->num_rows > 0)
    {
        $row = $result->fetch_assoc();
        $d=strtotime("tomorrow");
        $key = "Chiave";
        $payload = array(
            "iss" => $row["Username"],
            "aud" => $row["Password"],
            "iat" => $t=time(),
            "exp" => $t=time() + (10 * 60)
        );
        $jwt = JWT::encode($payload, $key, 'HS256');
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
        /*
         NOTE: This will now be an object instead of an associative array. To get
         an associative array, you will need to cast it as such:
        */
        $decoded_array = (array) $decoded;
        //exit;
        $to = $_POST['Mail'];
        $subject = 'Recupero Password';
        $message = 'Recupera password link:http://aspid.altervista.org/boh/Rec_Pass.php?TKN='.$jwt;
        $headers = 'From: Login.Altervista' . "\r\n" .
            'Reply-To: boh' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

        $success = mail($to, $subject, $message, $headers);
        if (!$success) {
            $errorMessage = error_get_last()['message'];
        }
        echo '<span data-shadow-text="Mail mandata a :'.$_POST['Mail'].'">Mail mandata a :'.$_POST['Mail'].'</span>';
    }
    else{
        echo'<span data-shadow-text="Utente non registrato">Utente non registrato</span>';
    }
}
else
{
?>
	<html>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
        Inserire email:<input type="text" name="Mail" required>
        <input type="submit">
    </form>
    </html>
<?php

}
?>