<?php

namespace App\Controller\API;

use App\Entity\User;
use App\Entity\Seller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Exception\JsonHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use App\Normalizer\UserNormalizer;
use FOS\RestBundle\Controller\Annotations as Rest;

/**
 * @Route("api")
 */
class UserController extends AbstractController
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
     * For registration as user
     *
     * @Rest\Post("/registration/user")
     */
    public function registrationAction(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, UserPasswordEncoderInterface $passwordEncoder)
    {
        /** @var User $user */
        $user = $serializer->deserialize($request->getContent(), User::class, JsonEncoder::FORMAT);
        $errors = $validator->validate($user);
        if (count($errors)) {
            throw new JsonHttpException(400, $errors);
        }
        $password = $passwordEncoder->encodePassword($user, $user->getPassword());
        $user->setPassword($password);
        $user->setApiToken($uuid4 = Uuid::uuid4());
        $this->getDoctrine()->getManager()->persist($user);
        $this->getDoctrine()->getManager()->flush();

        return $this->json($user, 200, [], [AbstractNormalizer::GROUPS => [UserNormalizer::DETAIL]]);
    }

    /**
     * For viewing user own profile
     *
     * @Rest\Get("/user/info")
     */
    public function infoAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = $this->getUser();
        if (!$user) {
            throw new JsonHttpException(400, 'Please log in!');
        }else{
            return $this->json($user, 200, [], [AbstractNormalizer::GROUPS => [UserNormalizer::PROFILE]]);
        }
    }

    /**
     * For deleting user own account
     *
     * @Rest\Delete("/user/delete")
     */
    public function deleteAction(Request $request)
    {
        $user = $this->getUser();
        if (!$user) {
            throw new JsonHttpException(400, 'Please log in!');
        }else {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
            return $this->json('Good bye!', 200);
        }
    }

    /**
     * For updating user own profile
     *
     * @Rest\Put("/user/update")
     */
    public function updateAction(Request $request)
    {
        if (!$content = $request->getContent()) {
            throw new JsonHttpException(400, 'Bad Request');
        }
        $user = $this->getUser();
        $data = json_decode($content, true);
        if ($data['email']) {
            $user->setEmail($data['email']);
        }else if ($data['name']) {
            $user->setName($data['name']);
        }elseif ($data['surname']){
            $user->setSurname($data['surname']);
        }elseif ($data['telephone']){
            $user->setTelephone($data['telephone']);
        }else{
            return $this->json("You don`t can change that field");
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();


        return $this->json($user, 200, [], [AbstractNormalizer::GROUPS => [UserNormalizer::PROFILE]]);
    }


}
