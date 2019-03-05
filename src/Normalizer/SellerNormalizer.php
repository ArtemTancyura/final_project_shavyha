<?php

namespace App\Normalizer;

use App\Entity\Seller;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class SellerNormalizer implements NormalizerInterface
{
    const DETAIL = 'seller details';
    const PROFILE = 'seller profile info';
    /**
     * @param Seller $seller
     * @param null $format
     * @param array $context
     * @return array|bool|float|int|string
     */
    public function normalize($seller, $format = null, array $context = [])
    {
        $data = [
            'id' => $seller->getId(),
            'title' => $seller->getTitle(),
            'email' => $seller->getEmail(),
            'apiToken' => $seller->getApiToken()
        ];


        if (isset($context[AbstractNormalizer::GROUPS]) && in_array($this::DETAIL, $context[AbstractNormalizer::GROUPS])) {
            $data = [
                'id' => $seller->getId(),
                'email' => $seller->getEmail(),
                'title' => $seller->getTitle(),
                'telephone' => $seller->getTelephone(),
                'station' => $seller->getStation(),
                'products' => $seller->getProducts(),
                'corporation' => $seller->getCorporation(),
                'apiToken' => $seller->getApiToken(),
                'roles' => $seller->getRoles()
            ];
        }

        if (isset($context[AbstractNormalizer::GROUPS]) && in_array($this::PROFILE, $context[AbstractNormalizer::GROUPS])) {
            $data = [
                'id' => $seller->getId(),
                'email' => $seller->getEmail(),
                'title' => $seller->getTitle(),
                'name' => $seller->getName(),
                'surname' => $seller->getSurname(),
                'telephone' => $seller->getTelephone(),
                'station' => $seller->getStation(),
                'products' => $seller->getProducts(),
                'corporation' => $seller->getCorporation(),
                'orders' => $seller->getOrders(),
                'password' => $seller->getPassword(),
            ];
        }

        return $data;
    }
    public function supportsNormalization($seller, $format = null)
    {
        return $seller instanceof Seller;
    }

}
