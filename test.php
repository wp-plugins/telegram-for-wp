<?php
require_once("Notifygram.class.php");
	//This will send a test message.
        $nt = new Notifygram_Class();
        $nt->Notifygram($_POST['api_key'], $_POST['api_token'], $_POST['show_name']);
        $nt->notify($_POST['message']);
