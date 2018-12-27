<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @ORM\Table(name="user")
 * @UniqueEntity("username", groups={"Default", "Patch"})
 */
class User implements UserInterface
{
    const ROLE_USER = 'ROLE_USER';
    const ROLE_ADMIN = 'ROLE_ADMIN';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"Default", "Deserialize"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotBlank(groups={"Default"})
     * @Serializer\Groups({"Default", "Deserialize"})
     */
    private $username;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(groups={"Default"})
     * @Assert\Regex(
     *     pattern="/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{7,}/",
     *     message="Password must be seven characters long and contain at least one digit, one upper case and one lower case letter",
     *     groups={"Default", "Patch"}
     * )
     * @Serializer\Groups({"Deserialize"})
     */
    private $password;

    /**
     * @Assert\NotBlank(groups={"Default"})
     * @Assert\Expression(
     *     "this.getPassword() === this.getRetypedPassword()",
     *     message="Passwords does not match",
     *     groups={"Default", "Patch"}
     * )
     * @Serializer\Type("string")
     * @Serializer\Groups({"Deserialize"})
     */
    private $retypedPassword;

    /**
     * @ORM\Column(type="simple_array", length=200)
     * @Serializer\Exclude()
     */
    private $roles;

    public function getId(): int
    {
        return $this->id;
    }

    public function getRetypedPassword(): ?string
    {
        return $this->retypedPassword;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function setRetypedPassword(?string $retypedPassword)
    {
        $this->retypedPassword = $retypedPassword;

        return $this;
    }

    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getSalt()
    {

    }

    public function getUsername()
    {
        return $this->username;
    }

    public function eraseCredentials()
    {

    }

    public function setUsername(?string $username)
    {
        $this->username = $username;

        return $this;
    }

    public function setPassword(?string $password)
    {
        $this->password = $password;

        return $this;
    }

}
