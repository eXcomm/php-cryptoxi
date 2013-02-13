<?PHP
// This is executed once whenever we refresh page.
function cleanup(){
    return true;
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
clean_session();

?>