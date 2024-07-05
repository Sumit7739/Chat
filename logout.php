<?php
session_start();
session_destroy();
header("Location: index.html");

if (isset($_SESSION['user_id'])) {
    session_destroy();
    header("Location: index.html");
} else {
    header("Location: index.html");
}
