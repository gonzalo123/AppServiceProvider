<?php

namespace G;

interface SecurityIface
{
    public function validateCredentials($user, $pass);

    public function getUserFromToken($token);
}