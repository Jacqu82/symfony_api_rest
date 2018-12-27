<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\ControllerTrait;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

/**
 * @Security("is_anonymous() or is_authenticated()")
 */
class TokenController extends AbstractController
{
    use ControllerTrait;

    private $passwordEncoder;
    private $JWTEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, JWTEncoderInterface $JWTEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->JWTEncoder = $JWTEncoder;
    }

    /**
     * @Rest\View(statusCode=201)
     */
    public function postTokenAction(Request $request)
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
