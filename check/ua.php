<?php
header('HTTP/1.1 400 Bad Request');
header('Content-Type: text/plain');
echo "Empty User-Agent request-header fields are not allowed.\n";
echo "Robots need to use an identifying User-Agent.\n";
echo "A package/program/framework default User-Agent is not allowed.\n";
