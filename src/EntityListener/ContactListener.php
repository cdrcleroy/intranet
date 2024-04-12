<?php

namespace App\EntityListener;

use App\Entity\Contact;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ContactListener
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function prePersist(Contact $contact)
    {
        $this->encodePassword($contact);
    }

    public function preUpdate(Contact $contact)
    {
        $this->encodePassword($contact);
    }

    /**
     * Encode password based on plainPassword
     *
     * @param Contact $contact
     * @return void
     */
    public function encodePassword(Contact $contact)
    {
        if($contact->getPlainPassword() === null) {
            return;
        }

        $contact->setPassword(
            $this->hasher->hashPassword(
                $contact,
                $contact->getPlainPassword()
            )
        );

        $contact->setPlainPassword(null);
    }
}