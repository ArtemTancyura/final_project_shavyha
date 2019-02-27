<?php

namespace App\Controller\API;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Exception\JsonHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Ramsey\Uuid\Uuid;
use FOS\RestBundle\Controller\Annotations as Rest;

class LoginController extends AbstractController
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
     * @Rest\Post("/api/login")
     */
    public function loginAction(Request $request)
    {
        if (!$content = $request->getContent()) {
            throw new JsonHttpException(Response::HTTP_BAD_REQUEST, 'Bad Request');
        }
        $data = json_decode($content, true);
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email'=>$data['email']]);
        if ($user instanceof User) {
            if ($this->passwordEncoder->isPasswordValid($user, $data['password'])) {
                $user->setApiToken($uuid4 = Uuid::uuid4());
                $this->getDoctrine()->getManager()->persist($user);
                $this->getDoctrine()->getManager()->flush();
                return $this->json($user);
            }
        }
        throw new JsonHttpException(Response::HTTP_BAD_REQUEST, 'Bad Request');
    }
}