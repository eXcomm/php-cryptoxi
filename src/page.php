<?PHP
// This is executed once whenever we refresh page.
include_once 'cryptoxi.class.php';
function cleanup(){
    $c = new CryptoXI();
    $c->clean_up();
}
function start_session(){
    if(session_id() == '') {
        session_start();
    }
}
function clean_session(){
    start_session();
    $_SESSION['read'] = 0;

}
cleanup();
clean_session();

?>