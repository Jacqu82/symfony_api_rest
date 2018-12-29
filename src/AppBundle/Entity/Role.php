<?php

namespace AppBundle\Entity;

use AppBundle\Annotation as App;
use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RoleRepository")
 * @ORM\Table(name="role")
 * @Serializer\ExclusionPolicy("ALL")
 * @Hateoas\Relation(
 *     "person",
 *     href=@Hateoas\Route("get_human", parameters={"person"="expr(object.getPerson().getId())"})
 * )
 */
class Role
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"Default", "Deserialize"})
     * @Serializer\Expose()
     */
    private $id;

    /**
     * @var Person
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Person")
     * @App\DeserializeEntity(type="AppBundle\Entity\Person", idField="id", idGetter="getId", setter="setPerson")
     * @Assert\NotBlank()
     * @Serializer\Groups({"Deserialize"})
     * @Serializer\Expose()
     */
    private $person;

    /**
     * @var Movie
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Movie", inversedBy="roles")
     */
    private $movie;

    /**
     * @ORM\Column(type="string", name="played_name", length=100)
     * @Assert\NotBlank()
     * @Assert\Length(min=1, max=100)
     * @Serializer\Groups({"Default", "Deserialize"})
     * @Serializer\Expose()
     */
    private $playedName;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Person
     */
    public function getPerson(): Person
    {
        return $this->person;
    }

    /**
     * @param Person $person
     * @return Role
     */
    public function setPerson(Person $person): Role
    {
        $this->person = $person;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPlayedName()
    {
        return $this->playedName;
    }

    /**
     * @param mixed $playedName
     * @return Role
     */
    public function setPlayedName($playedName)
    {
        $this->playedName = $playedName;

        return $this;
    }

    /**
     * @return Movie
     */
    public function getMovie(): Movie
    {
        return $this->movie;
    }

    /**
     * @param Movie $movie
     * @return Role
     */
    public function setMovie(Movie $movie): Role
    {
        $this->movie = $movie;

        return $this;
    }
}
