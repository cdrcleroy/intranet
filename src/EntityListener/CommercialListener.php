<?php

namespace App\EntityListener;

use App\Entity\Commercial;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CommercialListener
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function prePersist(Commercial $commercial)
    {
        $this->encodePassword($commercial);
    }

    public function preUpdate(Commercial $commercial)
    {
        $this->encodePassword($commercial);
    }

    /**
     * Encode password based on plainPassword
     *
     * @param Commercial $commercial
     * @return void
     */
    public function encodePassword(Commercial $commercial)
    {
        if($commercial->getPlainPassword() === null) {
            return;
        }

        $commercial->setPassword(
            $this->hasher->hashPassword(
                $commercial,
                $commercial->getPlainPassword()
            )
        );

        $commercial->setPlainPassword(null);
    }
}