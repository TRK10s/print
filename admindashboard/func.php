<?php
function is_Admin(){
    if ($_SESSION['userRole'] === 'Admin') {
        return true;
    }
    return false;
}

function is_Staff(){
    if ($_SESSION['userRole'] === 'Staff') {
        return true;
    }
    return false;
}

function is_Student(){
    if ($_SESSION['userRole'] === 'Student') {
        return true;
    }
    return false;
}
?>
