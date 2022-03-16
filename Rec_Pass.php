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
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }
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
if(isset($_POST["newpass"]) && isset($_POST["User"]) && isset($_POST["Pass"]))
{
    $sql = 'SELECT * FROM utenti Where Username ="' . $_POST['User'].'" and Password = "' . $_POST['Pass'].'" ;';
    $result = $conn->query($sql);
    if ($result->num_rows > 0)
    {
        $row = $result->fetch_assoc();
        if($row["Username"] == $_POST["User"] && $row["Password"] == $_POST["Pass"])
        {
            $sql = 'UPDATE utenti
					SET Password = "'.$_POST["newpass"].'"
					WHERE Username = "'.$row["Username"].'" and Password ="'.$row["Password"].'";';
            $result = $conn->query($sql);
            echo '<span data-shadow-text="Password cambiata">Password cambiata</span>';
            echo "<a href=Log.php'>Torna al login</a>";
        }
    }
}
else{
	if(isset($_GET["TKN"]))
    {
        $JWT = $_GET["TKN"];
        $decoded = JWT::decode($JWT, new Key("Chiave", 'HS256'));
        $decoded_array = (array) $decoded;
        if($decoded_array["exp"] > $t=time())
        {
        ?>
		<html>
		<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
            <input type="text" name="newpass" placeholder="Inserire la nuova password" required/>
		    <input type="submit" name="Logout" value="Invio" />
            <input type="hidden" name="User" value=<?php echo $decoded_array["iss"];?> />
            <input type="hidden" name="Pass" value=<?php echo $decoded_array["aud"];?> />
		</form>
        <style>
            @import  url(https://fonts.googleapis.com/css?family=Montserrat);

body{
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100vh;
  font-family: Montserrat;
  background: #313E50;
}

.text-input{
  
  position: relative;
  margin-top: 50px;
  
  input[type="text"]{
    display: inline-block;
    width: 500px;
    height: 40px;
    box-sizing: border-box;
    outline: none;
    border: 1px solid lightgray;
    border-radius: 3px;
    padding: 10px 10px 10px 100px;
    transition: all 0.1s ease-out;
  }
  
  input[type="text"] + label{
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    height: 40px;
    line-height: 40px;
    color: white;
    border-radius: 3px 0 0 3px;
    padding: 0 20px;
    background: #E03616;
    transform: translateZ(0) translateX(0);
    transition: all 0.3s ease-in;
    transition-delay: 0.2s;
  }
  
  input[type="text"]:focus + label{
    transform: translateY(-120%) translateX(0%);
    border-radius: 3px;
    transition: all 0.1s ease-out;
  }
  
  input[type="text"]:focus{
    padding: 10px;
    transition: all 0.3s ease-out;
    transition-delay: 0.2s;
  }
}
</style>
		</html>
		<?php
        }
        else{
            echo "Tempo scaduto";
	        echo "<a href='Log'>Torna al login</a>";
        }
    }
    else
    {
    	header("location: Log.php");
    }
}
?>