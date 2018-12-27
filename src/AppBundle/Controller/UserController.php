<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

/**
 * @Security("is_anonymous() or is_authenticated()")
 */
class UserController extends AbstractController
{
    private $passwordEncoder;
    private $JWTEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, JWTEncoderInterface $JWTEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->JWTEncoder = $JWTEncoder;
    }

    /**
     * @Route("/user/token")
     * @Method("POST")
     */
    public function tokenAction(Request $request)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['username' => $request->getUser()]);
        if (!$user) {
            throw new BadCredentialsException();
        }

        $isPasswordValid = $this->passwordEncoder->isPasswordValid($user, $request->getPassword());

        if (!$isPasswordValid) {
            throw new BadCredentialsException();
        }

        $token = $this->JWTEncoder->encode([
            'username' => $user->getUsername(),
            'exp' => time() + 3600
        ]);

        return new JsonResponse(['token' => $token]);
    }
}
