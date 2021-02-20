<?php


namespace App\Enums\users;


use BenSampo\Enum\Enum;

final class UsersTokenState extends Enum
{
    const 正常 = 1001;
    const 过期 = 1002;
}
