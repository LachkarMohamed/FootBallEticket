<?php
session_start();

session_unset();
session_destroy();

header('Location: /LACHKAR_MOHAMED_G1/GUI/index.html');
exit();
