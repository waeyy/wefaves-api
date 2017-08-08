<?php

namespace Api\HistoryBundle\Controller;

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
     *     serializerGroups = {"GET_HISTORIES"}
     * )
     * @Rest\Get("/users/self/history")
     *
     */
    public function getHistoriesAction(Request $request) {

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new JsonResponse(array("message" => "Requires authentication", "documentation" => $this->getParameter('link_documentation')), Response::HTTP_UNAUTHORIZED);
        }

        $user = $this->getUser();
        $history = $user->getHistories();

        if ($history->isEmpty()) {
            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return $history;
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
}
