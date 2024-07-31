<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ContactDTO
{
    #[Assert\NotBlank()]
    #[Assert\Length(min: 4, max: 255)]
    public string $name = '';

    #[Assert\NotBlank()]
    #[Assert\Email()]
    public string $email = '';

    #[Assert\NotBlank()]
    #[Assert\Length(min: 10, max: 2000)]
    public string $message = '';

    #[Assert\NotBlank()]
    public string $service = '';

}
