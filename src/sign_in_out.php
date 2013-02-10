<?PHP
require_once 'anon_userxi.php';
$uxi = new UserXI();
if ($uxi->is_logged()) {
    $login_logout = '<li><a href="#" id="logout">Logout</a></li>';
    $avatar = $uxi->avatar_url();
    $register_avatar =  '<li class="dropdown">
              <a class="dropdown-toggle" href="#" data-toggle="dropdown">Avatar <strong class="caret"></strong></a>
              <div class="dropdown-menu" style="padding: 15px; padding-bottom: 15px;">
               <img src="'.$avatar.'">
              </div>
            </li>';
} else {
    $login_logout = '<li class="dropdown">
              <a class="dropdown-toggle" href="#" data-toggle="dropdown">Sign In <strong class="caret"></strong></a>
              <div class="dropdown-menu" style="padding: 15px; padding-bottom: 15px;">
                <form>
                  <input id="user" type="text" class="span2" placeholder="Login">
                  <input id="pass" type="password" class="span2" placeholder="Password">
                  <button id="login" class="btn">Login</button>
                </form>
              </div>
            </li>';
    $register_avatar = '<li><a href="#" id="register">Generate User</a></li>';
}



$form = $register_avatar .'<li class="divider-vertical"></li>'.$login_logout;
echo $form;
?>