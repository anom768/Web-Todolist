<?php

namespace BangkitAnomSedhayu\Belajar\PHP\MVC\Model;

class UserPasswordUpdateRequest
{
    public ?string $id = null;

    public ?string $oldPassword = null;

    public ?string $newPassword = null;
    public ?string $newPassword2 = null;
}