<?php

namespace App\Normalizer;

use App\Entity\User;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UserNormalizer implements NormalizerInterface
{
    const DETAIL = 'user details';
    const PROFILE = 'user profile info';
    /**
     * @param User $user
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($user, $format = null, array $context = [])
    {
        $data = [
            'id' => $user->getId(),
            'name' => $user->getUsername(),
            'email' => $user->getEmail(),
            'apiToken' => $user->getApiToken()
        ];


        if (isset($context[AbstractNormalizer::GROUPS]) && in_array($this::DETAIL, $context[AbstractNormalizer::GROUPS])) {
            $data = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'name' => $user->getName(),
                'surname' => $user->getSurname(),
                'telephone' => $user->getTelephone(),
                'apiToken' => $user->getApiToken(),
                'roles' => $user->getRoles()
            ];
        }

        if (isset($context[AbstractNormalizer::GROUPS]) && in_array($this::PROFILE, $context[AbstractNormalizer::GROUPS])) {
            $data = [
                'email' => $user->getEmail(),
                'name' => $user->getName(),
                'surname' => $user->getSurname(),
                'telephone' => $user->getTelephone(),
                'password' => $user->getPassword()
            ];
        }

        return $data;
    }
    public function supportsNormalization($user, $format = null)
    {
        return $user instanceof User;
    }
}
