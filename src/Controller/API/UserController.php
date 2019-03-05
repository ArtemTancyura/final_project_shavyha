<?php

namespace App\Controller\API;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Exception\JsonHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use App\Normalizer\UserNormalizer;
use FOS\RestBundle\Controller\Annotations as Rest;

/**
 * @Route("api/user")
 */
class UserController extends AbstractController
{

    /**
     * For viewing user`s own profile
     *
     * @Rest\Get("/info")
     */
    public function infoAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = $this->getUser();
        return $this->json($user, 200, [], [AbstractNormalizer::GROUPS => [UserNormalizer::PROFILE]]);
    }


    /**
     * For updating user`s own profile
     *
     * @Rest\Put("/update")
     */
    public function updateAction(Request $request, ValidatorInterface $validator, SerializerInterface $serializer, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = $this->getUser();
        $data = $serializer->deserialize($request->getContent(), User::class, JsonEncoder::FORMAT);
        $errors = $validator->validate($data);
        if (count($errors)) {
            throw new JsonHttpException(400, $errors);
        }
        if($data->getEmail() != NULL){
            $user->setEmail($data->getEmail());
        }
        if($data->getName() != NULL){
            $user->setName($data->getName());
        }
        if($data->getSurname() != NULL){
            $user->setSurname($data->getSurname());
        }
        if($data->getTelephone() != NULL){
            $user->setTelephone($data->getTelephone());
        }
        if($data->getPassword() != NULL){
            $password = $passwordEncoder->encodePassword($user, $data->getPassword());
            $user->setPassword($password);
            $user->setPassword($password);
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return $this->json($user, 200, [], [AbstractNormalizer::GROUPS => [UserNormalizer::PROFILE]]);
    }


}
