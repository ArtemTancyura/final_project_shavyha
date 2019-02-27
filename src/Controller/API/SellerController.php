<?php

namespace App\Controller\API;

use App\Entity\Seller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use App\Exception\JsonHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Ramsey\Uuid\Uuid;
use FOS\RestBundle\Controller\Annotations as Rest;

class SellerController extends AbstractController
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;


    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Rest\Post("/api/registration/seller")
     */
    public function registrationSellerAction(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, UserPasswordEncoderInterface $passwordEncoder)
    {
        /** @var Seller $seller */
        $seller = $serializer->deserialize($request->getContent(), Seller::class, JsonEncoder::FORMAT);
        $errors = $validator->validate($seller);
        if (count($errors)) {
            throw new JsonHttpException(400, $errors);
        }
        $password = $passwordEncoder->encodePassword($seller, $seller->getPassword());
        $seller->setPassword($password);
        $seller->setApiToken($uuid4 = Uuid::uuid4());
        $this->getDoctrine()->getManager()->persist($seller);
        $this->getDoctrine()->getManager()->flush();

        return $this->json($seller);
    }
}