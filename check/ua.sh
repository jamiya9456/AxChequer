#!/bin/sh
echo 'Status: 400 Bad Request'
echo 'Content-Type: text/plain'
echo
echo 'Empty User-Agent request-header fields are not allowed.'
echo 'Robots need to use an identifying User-Agent.'
echo 'A package/program/framework default User-Agent is not allowed.'
