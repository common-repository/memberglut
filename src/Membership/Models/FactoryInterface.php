<?php

namespace MemberGlut\Core\Membership\Models;

interface FactoryInterface
{
    public static function make($data);

    public static function fromId($id);
}