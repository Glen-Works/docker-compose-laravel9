<?php

namespace App\Dto;

class OutputAuthUserInfoDto
{
    public ?int $id;
    public ?string $name;
    public ?string $email;
    public ?string $userType;

    public function __construct(int $id, string $name, string $email, string $userType)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->userType = $userType;
    }
}
