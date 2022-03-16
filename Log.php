<?php
$conn = new mysqli("localhost", "username", "password", "my_aspid");
require_once './google/vendor/autoload.php';
  
// init configuration
$clientID = '1082057051595-fjiqgith9c071a1s6mtv2plhfvua8u2a.apps.googleusercontent.com';
$clientSecret = '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';
$redirectUri = 'http://aspid.altervista.org/boh/Log.php';
   
// create Client Request to access Google API
$client = new Google_Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope("email");
$client->addScope("profile");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if(isset($_POST['Logout']))
{
    if (isset($_COOKIE['Access'])) {
        echo "si";
        setcookie("Access", "", time() - 3600);
    }
    header("location: Log.php");
        exit;
}
if (isset($_GET['code'])) {
	$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
	$client->setAccessToken($token['access_token']);
	 
	// get profile info
	$google_oauth = new Google_Service_Oauth2($client);
	$google_account_info = $google_oauth->userinfo->get();
	$email =  $google_account_info->email;
	$name =  $google_account_info->name;
    $sql = "SELECT Password,Username FROM utenti Where Email = '" . $email."' ;";
  	$result = $conn->query($sql);
  	if ($result->num_rows > 0)
    {
    	$row = $result->fetch_assoc();
		echo "Benvenuto ".$name;
		$options = [
			'cost' => 12,
		];
		$Hash=password_hash($row["Username"].$row["Password"], PASSWORD_BCRYPT, $options);
		$sql = "UPDATE utenti
		SET Hash = ".$Hash."
		WHERE Username = ".$row["Username"]." and Password =".$row["Password"].";";
  		$result = $conn->query($sql);
    	setcookie("Access", $Hash, time()+60*60*24*30);
		 ?>
		 <html>
		 <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
			 <input type="submit" name="Logout" value="Log out" />
		 </form>
		 </html>
		 <?php
	}
	else{
	echo "Utente non presente Registrarsi";
	echo "<a href='Log.php'>Torna al login</a>";
	}
}else{
if(count($_COOKIE) > 0) 
{
  $Hash = $_COOKIE["Hash"];
  $sql = "SELECT Username,Password FROM utenti Where Hash = '" . $Hash."' ;";
  $result = $conn->query($sql);
  if ($result->num_rows > 0)
  {
	echo 'Password valida! Accesso effetuato';
    ?>
    <html>
    <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
        <input type="submit" name="Logout" value="Log out" />
    </form>
    </html>
    <?php
  }else {
	echo 'Errore nel cookie';
	}
}
else
{
    if ($_SERVER["REQUEST_METHOD"] == "POST") 
    {
        if(isset($_POST['UserLog']) && isset($_POST['PassLog']))
        {
            $user = $_POST['UserLog'];
            $pass = $_POST['PassLog'];
            $sql = "SELECT Password FROM utenti Where Username = '" . $user."' ;";
            $result = $conn->query($sql);
            if ($result->num_rows > 0)
            {
                $row = $result->fetch_assoc();
                if ($pass == $row["Password"]) 
                {
                      echo 'Password valida! Accesso effetuato';
                      $b = $_POST['acceptcookie'];
                      if($b == "Si")
                      {
						$options = [
							'cost' => 12,
						];
						$Hash=password_hash($user.$pass, PASSWORD_BCRYPT, $options);
						$sql = "UPDATE utenti
						SET Hash = ".$Hash."
						WHERE Username = ".$row["Username"]." and Password =".$row["Password"].";";
						$result = $conn->query($sql);
						setcookie("Access", $Hash, time()+60*60*24*30);
                      }
                        ?>
                        <html>
                        <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
                            <input type="submit" name="Logout" value="Log out" />
                        </form>
                        </html>
                        <?php
                }
                  else 
                {
                echo 'Password errata.';
                }
            }
            else 
            {
                echo 'Password errata.';
            }
        } 
        else if(isset($_POST['UserReg']) && isset($_POST['PassReg']))
        {
        $user = $_POST['UserReg'];
        $pass = $_POST['PassReg'];
        $sql = "INSERT INTO utenti (Username, Password)
        VALUES ('".$user."','".$pass."');";
        $result = $conn->query($sql);
        if($_POST['acceptcookie'] == "Si")
        {
			$options = [
				'cost' => 12,
			];
			$Hash=password_hash($user.$pass, PASSWORD_BCRYPT, $options);
			$sql = "UPDATE utenti
			SET Hash = ".$Hash."
			WHERE Username = ".$row["Username"]." and Password =".$row["Password"].";";
			  $result = $conn->query($sql);
			setcookie("Access", $Hash, time()+60*60*24*30);
        }
        echo "Utente creato";
        ?>
        <html>
        <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
            <input type="submit" name="Logout" value="Log out" />
        </form>
        </html>
        <?php
        }
    }
	else{
		?>
		<html>
		<body>
        <section class="forms-section">
  <h1 class="section-title">Login</h1>
  <div class="forms">
    <div class="form-wrapper is-active">
      <button type="button" class="switcher switcher-login">
        Login
        <span class="underline"></span>
      </button>
         <form class="form form-login" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
         <fieldset>
           <div class="input-block">
            <label for="login-email">User</label>
            <input id="login-email" type="text" name="UserLog" required>
          </div>
           <!--<input type="text" name="UserLog">-->
           <div class="input-block">
            <label for="login-password">Password</label>
            <input id="login-password" type="password" name="PassLog" required>
          </div>
          <?php
         echo "<a href='".$client->createAuthUrl()."'>Google Login</a>";
         ?>
          <br>
         <a href="Password.php" >Password dimenticata ?</a><br>
           <!--<input type="text" name="PassLog">-->
           Ricordami?
         <input type="radio" id="contactChoice1" name="acceptcookie" value="Si"required>
		 <label for="contactChoice1">Si</label>
         <input type="radio" id="contactChoice2" name="acceptcookie" value="No"required>
         <label for="contactChoice2">No</label>
         </fieldset>
           <button type="submit" class="btn-login">Login</button>
         </form>
         </div>
    <div class="form-wrapper">
      <button type="button" class="switcher switcher-signup">
        Sign Up
        <span class="underline"></span>
      </button>
         <form class="form form-signup" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
         <fieldset>
          <div class="input-block">
            <label for="signup-email">User</label>
            <input id="signup-email" type="text" name="UserReg" required>
          </div>
          <div class="input-block">
            <label for="signup-password">Password</label>
            <input id="signup-password" type="password" name="PassReg" required>
          </div>
         Ricordami?
         <input type="radio" id="contactChoice1" name="acceptcokie" value="Si" required>
		 <label for="contactChoice1">Si</label>
         <input type="radio" id="contactChoice2" name="acceptcokie" value="No" required>
         <label for="contactChoice2">No</label><br>
         <?php
         echo "<a href='".$client->createAuthUrl()."'>Google Login</a>";
         ?>
         </fieldset>
        <button type="submit" class="btn-signup">Registrati</button>
      </form>
      </div>
  </div>
</section>
        <?php
        }
        ?>
        </body>
        </html>
        <script>
        const switchers = [...document.querySelectorAll('.switcher')]

switchers.forEach(item => {
	item.addEventListener('click', function() {
		switchers.forEach(item => item.parentElement.classList.remove('is-active'))
		this.parentElement.classList.add('is-active')
	})
})

        </script>
        <style>
        *,
*::before,
*::after {
	box-sizing: border-box;
}

body {
	margin: 0;
	font-family: Roboto, -apple-system, 'Helvetica Neue', 'Segoe UI', Arial, sans-serif;
	background: #3b4465;
}

.forms-section {
	display: flex;
	flex-direction: column;
	justify-content: center;
	align-items: center;
}

.section-title {
	font-size: 32px;
	letter-spacing: 1px;
	color: #fff;
}

.forms {
	display: flex;
	align-items: flex-start;
	margin-top: 30px;
}

.form-wrapper {
	animation: hideLayer .3s ease-out forwards;
}

.form-wrapper.is-active {
	animation: showLayer .3s ease-in forwards;
}

@keyframes showLayer {
	50% {
		z-index: 1;
	}
	100% {
		z-index: 1;
	}
}

@keyframes hideLayer {
	0% {
		z-index: 1;
	}
	49.999% {
		z-index: 1;
	}
}

.switcher {
	position: relative;
	cursor: pointer;
	display: block;
	margin-right: auto;
	margin-left: auto;
	padding: 0;
	text-transform: uppercase;
	font-family: inherit;
	font-size: 16px;
	letter-spacing: .5px;
	color: #999;
	background-color: transparent;
	border: none;
	outline: none;
	transform: translateX(0);
	transition: all .3s ease-out;
}

.form-wrapper.is-active .switcher-login {
	color: #fff;
	transform: translateX(90px);
}

.form-wrapper.is-active .switcher-signup {
	color: #fff;
	transform: translateX(-90px);
}

.underline {
	position: absolute;
	bottom: -5px;
	left: 0;
	overflow: hidden;
	pointer-events: none;
	width: 100%;
	height: 2px;
}

.underline::before {
	content: '';
	position: absolute;
	top: 0;
	left: inherit;
	display: block;
	width: inherit;
	height: inherit;
	background-color: currentColor;
	transition: transform .2s ease-out;
}

.switcher-login .underline::before {
	transform: translateX(101%);
}

.switcher-signup .underline::before {
	transform: translateX(-101%);
}

.form-wrapper.is-active .underline::before {
	transform: translateX(0);
}

.form {
	overflow: hidden;
	min-width: 260px;
	margin-top: 50px;
	padding: 30px 25px;
  border-radius: 5px;
	transform-origin: top;
}

.form-login {
	animation: hideLogin .3s ease-out forwards;
}

.form-wrapper.is-active .form-login {
	animation: showLogin .3s ease-in forwards;
}

@keyframes showLogin {
	0% {
		background: #d7e7f1;
		transform: translate(40%, 10px);
	}
	50% {
		transform: translate(0, 0);
	}
	100% {
		background-color: #fff;
		transform: translate(35%, -20px);
	}
}

@keyframes hideLogin {
	0% {
		background-color: #fff;
		transform: translate(35%, -20px);
	}
	50% {
		transform: translate(0, 0);
	}
	100% {
		background: #d7e7f1;
		transform: translate(40%, 10px);
	}
}

.form-signup {
	animation: hideSignup .3s ease-out forwards;
}

.form-wrapper.is-active .form-signup {
	animation: showSignup .3s ease-in forwards;
}

@keyframes showSignup {
	0% {
		background: #d7e7f1;
		transform: translate(-40%, 10px) scaleY(.8);
	}
	50% {
		transform: translate(0, 0) scaleY(.8);
	}
	100% {
		background-color: #fff;
		transform: translate(-35%, -20px) scaleY(1);
	}
}

@keyframes hideSignup {
	0% {
		background-color: #fff;
		transform: translate(-35%, -20px) scaleY(1);
	}
	50% {
		transform: translate(0, 0) scaleY(.8);
	}
	100% {
		background: #d7e7f1;
		transform: translate(-40%, 10px) scaleY(.8);
	}
}

.form fieldset {
	position: relative;
	opacity: 0;
	margin: 0;
	padding: 0;
	border: 0;
	transition: all .3s ease-out;
}

.form-login fieldset {
	transform: translateX(-50%);
}

.form-signup fieldset {
	transform: translateX(50%);
}

.form-wrapper.is-active fieldset {
	opacity: 1;
	transform: translateX(0);
	transition: opacity .4s ease-in, transform .35s ease-in;
}

.form legend {
	position: absolute;
	overflow: hidden;
	width: 1px;
	height: 1px;
	clip: rect(0 0 0 0);
}

.input-block {
	margin-bottom: 20px;
}

.input-block label {
	font-size: 14px;
  color: #a1b4b4;
}

.input-block input {
	display: block;
	width: 100%;
	margin-top: 8px;
	padding-right: 15px;
	padding-left: 15px;
	font-size: 16px;
	line-height: 40px;
	color: #3b4465;
  background: #eef9fe;
  border: 1px solid #cddbef;
  border-radius: 2px;
}

.form [type='submit'] {
	opacity: 0;
	display: block;
	min-width: 120px;
	margin: 30px auto 10px;
	font-size: 18px;
	line-height: 40px;
	border-radius: 25px;
	border: none;
	transition: all .3s ease-out;
}

.form-wrapper.is-active .form [type='submit'] {
	opacity: 1;
	transform: translateX(0);
	transition: all .4s ease-in;
}

.btn-login {
	color: #fbfdff;
	background: #a7e245;
	transform: translateX(-30%);
}

.btn-signup {
	color: #a7e245;
	background: #fbfdff;
	box-shadow: inset 0 0 0 2px #a7e245;
	transform: translateX(30%);
}
        </style>
<?php
}
}
?>