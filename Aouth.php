<?php
session_start();
$conn = new mysqli("localhost", "username", "password", "my_aspid");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
if(isset($_GET["code"]) && isset($_GET["state"]))
{
        $state =$_GET["state"];
            if($state == "1234")
            {
            echo "state giusto";
            $code = $_GET["code"];
            $url = 'https://id.paleo.bg.it/oauth/token';
        $data = array('grant_type' => 'authorization_code', 'code' => $code, 'redirect_uri' => 'http://aspid.altervista.org/Aoth/ciao.php', 'client_id' => 'aeed8e5c834265de6493b1c4f280b9ed', 'client_secret' => 'edf5c613e49a9c47f13e4d290f6d2db54145693de871384c5e7e53f288c693b062800c94883a679148d05bce12d847bb958047c3237e44c16dcb33ccb9fc488d');
        $curl = curl_init();

        curl_setopt_array($curl, [
        CURLOPT_URL => "https://id.paleo.bg.it/oauth/token",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "{\n  \"grant_type\": \"authorization_code\",\n  \"code\": \"".$code."\",\n  \"redirect_uri\": \"http://aspid.altervista.org/Aoth/ciao.php\",\n  \"client_id\": \"aeed8e5c834265de6493b1c4f280b9ed\",\n  \"client_secret\": \"edf5c613e49a9c47f13e4d290f6d2db54145693de871384c5e7e53f288c693b062800c94883a679148d05bce12d847bb958047c3237e44c16dcb33ccb9fc488d\"\n}",
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json"
        ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
        echo "cURL Error #:" . $err;
        } else {
        echo $response;
        }
        }
}
else
{
if(count($_SESSION) > 0) 
{
  $pass = $_SESSION["Pass"];
  $user = $_SESSION["User"];
  $sql = "SELECT Username FROM utenti where Username = '". $user . "' and Password = '" . $pass ."'";
  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
    echo 'Sessione valida! Accesso effetuato';
    } 
    else {
    echo "Sessione errata";
    }
}
else
{
    if ($_SERVER["REQUEST_METHOD"] == "POST") 
    {
        if(isset($_POST['UserLog']) && isset($_POST['PassLog']))
        {
            $pass = $_POST["PassLog"];
            $user = $_POST["UserLog"];
            $sql = "SELECT Username FROM utenti where Username = '". $user . "' and Password = '" . $pass ."'";
            $result = $conn->query($sql);
            //var_dump($result);
            if ($result->num_rows > 0) 
            {
                echo 'Password valida! Accesso effetuato';
            }
                else 
                {
                echo "Email o password errata";
                }
        }
        else
        {
            $pass = $_POST["Pass"];
            $user = $_POST["User"];
            $sql = "INSERT INTO utenti (Username, Password) VALUES ('". $user ."','". $pass ."');";
            echo "Utente creato";
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
           <!--<input type="text" name="PassLog">-->
           <a href=" https://id.paleo.bg.it/oauth/authorize?client_id=aeed8e5c834265de6493b1c4f280b9ed&response_type=code&state=1234&redirect_uri=http://aspid.altervista.org/Aoth/Login.php">accedi a palocapa</a>
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
            <input id="signup-email" type="text" name="User" required>
          </div>
          <div class="input-block">
            <label for="signup-password">Password</label>
            <input id="signup-password" type="password" name="Pass" required>
          </div>
          <a href=" https://id.paleo.bg.it/oauth/authorize?client_id=aeed8e5c834265de6493b1c4f280b9ed&response_type=code&state=1234&redirect_uri=http://aspid.altervista.org/Aoth/Login.php">accedi a palocapa</a>
         </fieldset>
        <button type="submit" class="btn-signup">Registrati</button>
      </form>
      </div>
  </div>
</section>
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
}
?>
