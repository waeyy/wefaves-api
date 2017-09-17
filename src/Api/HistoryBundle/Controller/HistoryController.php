<?php

namespace Api\HistoryBundle\Controller;

use Api\HistoryBundle\Entity\History;
use Api\HistoryBundle\Form\Type\HistoryFormType;
use Api\HistoryBundle\Representation\Histories;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class HistoryController extends Controller
{

    /**
     *
     * @Rest\View(
     *     statusCode = 200,
     * )
     * @Rest\QueryParam(
     *     name="title",
     *     requirements="[a-zA-Z0-9]+",
     *     nullable=true,
     *     description="The keyword to search for."
     * )
     * @Rest\QueryParam(
     *     name="order",
     *     requirements="asc|desc",
     *     default="asc",
     *     description="Sort order (asc or desc)"
     * )
     * @Rest\QueryParam(
     *     name="limit",
     *     requirements="\d+",
     *     default="15",
     *     description="Max number of users per page."
     * )
     * @Rest\QueryParam(
     *     name="offset",
     *     requirements="\d+",
     *     default="1",
     *     description="The pagination offset"
     * )
     * @Rest\Get("/users/histories")
     *
     */
    public function getHistoriesAction(ParamFetcherInterface $paramFetcher, Request $request) {

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new JsonResponse(array("message" => "Requires authentication", "documentation" => $this->getParameter('link_documentation')), Response::HTTP_UNAUTHORIZED);
        }
        $user = $this->getUser();

        /*if (strcmp($request->get("id"), "profile") == 0) {
            $user = $this->getUser();
        } else {
            $userManager = $this->get('fos_user.user_manager');
            $user = $userManager->findUserBy(array("id" => $request->get("id")));
        }*/

        $pager = $this->getDoctrine()->getRepository('ApiHistoryBundle:History')->filter(
            $paramFetcher->get('title'),
            $paramFetcher->get('order'),
            $paramFetcher->get('limit'),
            $paramFetcher->get('offset'),
            $user->getId()
        );

        return new Histories($pager);

        /*$user = $this->getUser();
        $history = $user->getHistories();

        if ($history->isEmpty()) {
            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return $history;*/
    }

    /**
     *
     * @Rest\View(
     *     statusCode = 200,
     *     serializerGroups = {"GET_HISTORIES"}
     * )
     * @Rest\Get("/users/self/history/{id}")
     *
     */
    public function getHistoryAction(Request $request) {

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new JsonResponse(array("message" => "Requires authentication", "documentation" => $this->getParameter('link_documentation')), Response::HTTP_UNAUTHORIZED);
        }

        $user = $this->getUser();

        $repository = $this->getDoctrine()->getRepository('ApiHistoryBundle:History');
        $history = $repository->find($request->get("id"));

        if (empty($history)) {
            return new JsonResponse(array("message" => "The requested history was not found."), Response::HTTP_NOT_FOUND);
        }

        if ($history->getUser()->getId() != $user->getId()) {
            return new JsonResponse(array("message" => "You are not allowed to access to the requested resource."), Response::HTTP_UNAUTHORIZED);
        }

        return $history;
    }

    /**
     *
     * @Rest\View(
     *     statusCode = 201,
     *     serializerGroups = {"GET_HISTORIES"}
     * )
     * @Rest\Post("/users/self/history")
     *
     */
    public function postHistoryAction(Request $request) {

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new JsonResponse(array("message" => "Requires authentication", "documentation" => $this->getParameter('link_documentation')), Response::HTTP_UNAUTHORIZED);
        }

        $user = $this->getUser();
        $history = new History();

        $form = $this->createForm(HistoryFormType::class, $history);
        $form->submit($request->request->all());

        if ($form->isValid()) {

            $user->addHistory($history);

            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($history);
            $em->flush();

            return $history;
        }

        return $form;
    }

    /**
     *
     * @Rest\View(
     *     statusCode = 200,
     *     serializerGroups = {"GET_HISTORIES"}
     * )
     * @Rest\Delete("/users/self/history/{id}")
     *
     */
    public function deleteHistoryAction(Request $request) {

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new JsonResponse(array("message" => "Requires authentication", "documentation" => $this->getParameter('link_documentation')), Response::HTTP_UNAUTHORIZED);
        }

        $user = $this->getUser();

        $repository = $this->getDoctrine()->getRepository('ApiHistoryBundle:History');
        $history = $repository->find($request->get("id"));

        if (empty($history)) {
            return new JsonResponse(array("message" => "The requested history was not found."), Response::HTTP_NOT_FOUND);
        }

        if ($history->getUser()->getId() != $user->getId()) {
            return new JsonResponse(array("message" => "You must be the owner of this history to delete."), Response::HTTP_UNAUTHORIZED);
        }

        $user->removeHistory($history);

        $em = $this->get('doctrine.orm.entity_manager');
        $em->remove($history);
        $em->flush();

        return  new JsonResponse(array("message" => "History deleted."), Response::HTTP_OK);
    }

    /**
     * @Rest\View(
     *     statusCode = 200
     * )
     * @Rest\Delete("/users/self/history")
     */
    public function deleteHistoriesAction(Request $request) {

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new JsonResponse(array("message" => "Requires authentication", "documentation" => $this->getParameter('link_documentation')), Response::HTTP_UNAUTHORIZED);
        }

        $user = $this->getUser();

        $repository = $this->getDoctrine()->getRepository('ApiHistoryBundle:History');

    }
}
