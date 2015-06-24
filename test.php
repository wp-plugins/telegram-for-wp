<?php
require_once("Notifygram.class.php");
	//This will send a test message.
        $nt = new Notifygram();
        $nt->Notifygram($_POST['api_key'], $_POST['api_token']);
        $nt->notify($_POST['message']);
