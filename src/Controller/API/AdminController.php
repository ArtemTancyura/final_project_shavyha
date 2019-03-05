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
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use App\Normalizer\UserNormalizer;
use App\Normalizer\SellerNormalizer;
use FOS\RestBundle\Controller\Annotations as Rest;

/**
 * @Route("api/admin")
 */
class AdminController extends AbstractController
{
    /**
     * For viewing all of users/sellers/all
     *
     * @Rest\Get("/info/{type}")
     */
    public function allUsersAction(Request $request, $type)
    {
        if($type == 'all') {
            $user = $this->getDoctrine()->getRepository(User::class)->findAll();
            return $this->json($user);
        }elseif($type == 'user') {
            $user = $this->getDoctrine()->getRepository(User::class)->findBy(['roles' => '["ROLE_USER"]']);
            return $this->json($user);
        }elseif($type == 'seller'){
            $seller = $this->getDoctrine()->getRepository(Seller::class)->findAll();
            return $this->json($seller);
        }
    }

    /**
     * For viewing one user
     *
     * @Rest\Get("/info/user/{id}")
     */
    public function oneUserAction(Request $request, User $user)
    {
        if($user->getRoles() == ["ROLE_USER"]){
            return $this->json($user, 200, [], [AbstractNormalizer::GROUPS => [UserNormalizer::DETAIL]]);
        }else{
            throw new JsonHttpException(400, 'This is not user');
        }

    }

    /**
     * For viewing one seller
     *
     * @Rest\Get("/info/seller/{id}")
     */
    public function oneSellerAction(Request $request, Seller $seller)
    {
        return $this->json($seller, 200, [], [AbstractNormalizer::GROUPS => [SellerNormalizer::DETAIL]]);
    }

    /**
     * @Rest\Delete("/delete/{user}")
     */
    public function deleteAction(Request $request, User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();
        return $this->json('Good bye!', 200);

    }
}