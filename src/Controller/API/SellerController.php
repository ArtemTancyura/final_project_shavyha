<?php

namespace App\Controller\API;

use App\Entity\Seller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use App\Exception\JsonHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use App\Normalizer\SellerNormalizer;
use FOS\RestBundle\Controller\Annotations as Rest;

/**
 * @Route("api")
 */
class SellerController extends AbstractController
{

    /**
     * For viewing seller`s own profile
     *
     * @Rest\Get("/seller/info")
     */
    public function infoAction(Request $request)
    {
        $seller = $this->getUser();
        return $this->json($seller, 200, [], [AbstractNormalizer::GROUPS => [SellerNormalizer::PROFILE]]);
    }

    /**
     * For updating user`s own profile
     *
     * @Rest\Put("/seller/update")
     */
    public function updateAction(Request $request, ValidatorInterface $validator, SerializerInterface $serializer, UserPasswordEncoderInterface $passwordEncoder)
    {
        $seller = $this->getUser();
        $data = $serializer->deserialize($request->getContent(), Seller::class, JsonEncoder::FORMAT);
        $errors = $validator->validate($data);
        if (count($errors)) {
            throw new JsonHttpException(400, $errors);
        }
        if($data->getEmail() != NULL){
            $seller->setEmail($data->getEmail());
        }
        if($data->getName() != NULL){
            $seller->setName($data->getName());
        }
        if($data->getSurname() != NULL){
            $seller->setSurname($data->getSurname());
        }
        if($data->getTitle() != NULL){
            $seller->setTitle($data->getTitle());
        }
        if($data->getTelephone() != NULL){
            $seller->setTelephone($data->getTelephone());
        }
        if($data->getStation() != NULL){
            $seller->setStation($data->getStation());
        }
        if($data->getPassword() != NULL){
            $password = $passwordEncoder->encodePassword($seller, $data->getPassword());
            $seller->setPassword($password);
            $seller->setPassword($password);
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($seller);
        $em->flush();

        return $this->json($seller, 200, [], [AbstractNormalizer::GROUPS => [SellerNormalizer::PROFILE]]);
    }
}