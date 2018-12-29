<?php

namespace AppBundle\Controller;

use AppBundle\Entity\EntityMerger;
use AppBundle\Entity\Movie;
use AppBundle\Entity\Role;
use AppBundle\Exception\ValidationException;
use AppBundle\Resource\Filtering\Movie\MovieFilterDefinitionFactory;
use AppBundle\Resource\Filtering\Role\RoleFilterDefinitionFactory;
use AppBundle\Resource\Pagination\Movie\MoviePagination;
use AppBundle\Resource\Pagination\PageRequestFactory;
use AppBundle\Resource\Pagination\Role\RolePagination;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\ControllerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * @Security("is_anonymous() or is_authenticated()")
 */
class MovieController extends AbstractController
{
    use ControllerTrait;

    private $entityMerger;
    private $moviePagination;
    private $rolePagination;

    public function __construct(EntityMerger $entityMerger, MoviePagination $moviePagination, RolePagination $rolePagination)
    {
        $this->entityMerger = $entityMerger;
        $this->moviePagination = $moviePagination;
        $this->rolePagination = $rolePagination;
    }

    /**
     * @Rest\View()
     */
    public function getMoviesAction(Request $request)
    {
        $pageRequestFactory = new PageRequestFactory();
        $page = $pageRequestFactory->fromRequest($request);

        $movieFilterDefinitionFactory = new MovieFilterDefinitionFactory();
        $movieFilterDefinition = $movieFilterDefinitionFactory->factory($request);

        return $this->moviePagination->paginate($page, $movieFilterDefinition);
    }

    /**
     * @Rest\View(statusCode=201)
     * @ParamConverter("movie", converter="fos_rest.request_body")
     * @Rest\NoRoute()
     */
    public function postMoviesAction(Movie $movie, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            throw new ValidationException($validationErrors);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($movie);
        $em->flush();

        return $movie;
    }

    /**
     * @Rest\View()
     */
    public function deleteMovieAction(?Movie $movie)
    {
        if (null === $movie) {
            return $this->view(null, 404);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($movie);
        $em->flush();
    }

    /**
     * @Rest\View()
     */
    public function getMovieAction(?Movie $movie)
    {
        if (null === $movie) {
            return $this->view(null, 404);
        }

        return $movie;
    }

    /**
     * @Rest\View()
     */
    public function getMovieRolesAction(Request $request, Movie $movie)
    {
        $pageRequestFactory = new PageRequestFactory();
        $page = $pageRequestFactory->fromRequest($request);

        $roleFilterDefinitionFactory = new RoleFilterDefinitionFactory();
        $roleFilterDefinition = $roleFilterDefinitionFactory->factory($request, $movie->getId());

        return $this->rolePagination->paginate($page, $roleFilterDefinition);
    }

    /**
     * @Rest\View(statusCode=201)
     * @ParamConverter("role", converter="fos_rest.request_body", options={"deserializationContext"={"groups"={"Deserialize"}}})
     * @Rest\NoRoute()
     */
    public function postMovieRolesAction(Movie $movie, Role $role, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0) {
            throw new ValidationException($validationErrors);
        }

        $role->setMovie($movie);
        $em = $this->getDoctrine()->getManager();
        $em->persist($role);
        $movie->addRole($role);
        $em->persist($movie);
        $em->flush();

        return $role;
    }

    /**
     * @Rest\NoRoute()
     * @ParamConverter("modifiedMovie", converter="fos_rest.request_body", options={"validator"={"groups"={"Patch"}}})
     * @Security("is_authenticated()")
     */
    public function patchMovieAction(?Movie $movie, Movie $modifiedMovie, ConstraintViolationListInterface $validationErrors)
    {
        if (null === $movie) {
            return $this->view(null, 404);
        }

        if (count($validationErrors) > 0) {
            throw new ValidationException($validationErrors);
        }

        //Merge entities
        $this->entityMerger->merge($movie, $modifiedMovie);

        //Persist
        $em = $this->getDoctrine()->getManager();
        $em->persist($movie);
        $em->flush();

        //Return
        return $movie;
    }
}
