<?php
require_once 'includes/functions.php';
session_destroy();
redirectTo('login.php');