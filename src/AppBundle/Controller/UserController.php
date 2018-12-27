<?php

namespace AppBundle\Controller;

use AppBundle\Entity\EntityMerger;
use AppBundle\Entity\User;
use AppBundle\Exception\ValidationException;
use FOS\RestBundle\Controller\Annotations as Rest;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * @Security("is_anonymous() or is_authenticated()")
 */
class UserController extends AbstractController
{
    private $passwordEncoder;
    private $JWTEncoder;
    private $entityMerger;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, JWTEncoderInterface $JWTEncoder, EntityMerger $entityMerger)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->JWTEncoder = $JWTEncoder;
        $this->entityMerger = $entityMerger;
    }

    /**
     * @Rest\View()
     * @Security("is_granted('show', theUser)", message="Access denied")
     */
    public function getUserAction(?User $theUser)
    {
        if (null === $theUser) {
            throw new NotFoundHttpException();
        }

        return $theUser;
    }

    /**
     * @Rest\View(statusCode=201)
     * @Rest\NoRoute()
     * @ParamConverter("user", converter="fos_rest.request_body", options={"deserializationContext"={"groups"={"Deserialize"}}})
     */
    public function postUserAction(User $user, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            throw new ValidationException($validationErrors);
        }

        $this->encodePassword($user);
        $user->setRoles([User::ROLE_USER]);
        $this->persistUser($user);

        return $user;
    }

    /**
     * @Rest\NoRoute()
     * @ParamConverter("modifiedUser", converter="fos_rest.request_body",
     *     options={
     *     "validator"={"groups"={"Patch"}},
     *     "deserializationContext"={"groups"={"Deserialize"}}
     *     }
     * )
     * @Security("is_granted('edit', theUser)", message="Access denied")
     */
    public function patchUserAction(?User $theUser, User $modifiedUser, ConstraintViolationListInterface $validationErrors)
    {
        if (null === $theUser) {
            throw new NotFoundHttpException();
        }

        if (count($validationErrors) > 0) {
            throw new ValidationException($validationErrors);
        }

        if (empty($modifiedUser->getPassword())) {
            $modifiedUser->setPassword(null);
        }

        //Merge entities
        $this->entityMerger->merge($theUser, $modifiedUser);

        $this->encodePassword($theUser);
        //Persist
        $this->persistUser($theUser);

        //Return
        return $theUser;

    }

    private function encodePassword(User $user): void
    {
        $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));
    }

    private function persistUser(User $user): void
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
    }
}
