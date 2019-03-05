<?php

namespace App\Controller\API;

use App\Entity\User;
use App\Entity\Seller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Exception\JsonHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use App\Normalizer\UserNormalizer;
use App\Normalizer\SellerNormalizer;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("api")
 */
class DefaultController extends AbstractController
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
     * @Rest\Post("/registration/{type}")
     */
    public function registrationAction(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, UserPasswordEncoderInterface $passwordEncoder, $type)
    {
        if($type == 'user'){

            /** @var User $user */
            $user = $serializer->deserialize($request->getContent(), User::class, JsonEncoder::FORMAT);
            $errors = $validator->validate($user);
            if (count($errors)) {
                throw new JsonHttpException(400, $errors);
            }
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $user->setRoles(["ROLE_USER"]);
            $user->setApiToken($uuid4 = Uuid::uuid4());
            $this->getDoctrine()->getManager()->persist($user);
            $this->getDoctrine()->getManager()->flush();

            return $this->json($user, 200, [], [AbstractNormalizer::GROUPS => [UserNormalizer::DETAIL]]);

        }elseif ($type == 'seller'){

            /** @var Seller $seller */
            $seller = $serializer->deserialize($request->getContent(), Seller::class, JsonEncoder::FORMAT);
            $errors = $validator->validate($seller);
            if (count($errors)) {
                throw new JsonHttpException(400, $errors);
            }
            $password = $passwordEncoder->encodePassword($seller, $seller->getPassword());
            $seller->setPassword($password);
            $seller->setRoles(["ROLE_SELLER"]);
            $seller->setApiToken($uuid4 = Uuid::uuid4());
            $this->getDoctrine()->getManager()->persist($seller);
            $this->getDoctrine()->getManager()->flush();

            return $this->json($seller, 200, [], [AbstractNormalizer::GROUPS => [SellerNormalizer::DETAIL]]);

        }else{
            throw new JsonHttpException(Response::HTTP_BAD_REQUEST, 'Invalid URL');
        }

    }

    /**
     * @Rest\Post("/login")
     */
    public function loginAction(Request $request, SerializerInterface $serializer)
    {
        if (!$content = $request->getContent()) {
            throw new JsonHttpException(Response::HTTP_BAD_REQUEST, 'Bad Request');
        }
        $data = $serializer->deserialize($request->getContent(), User::class, JsonEncoder::FORMAT);
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email'=> $data->getEmail()]);
        if ($user instanceof User) {
            if ($this->passwordEncoder->isPasswordValid($user, $data->getPassword())) {
                $user->setApiToken($uuid4 = Uuid::uuid4());
                $this->getDoctrine()->getManager()->persist($user);
                $this->getDoctrine()->getManager()->flush();
                return $this->json($user);
            }
        }
        throw new JsonHttpException(Response::HTTP_BAD_REQUEST, 'Bad Request');
    }

    /**
     * @Rest\Delete("/delete")
     */
    public function deleteAction(Request $request)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();
        return $this->json('Good bye!', 200);

    }
}