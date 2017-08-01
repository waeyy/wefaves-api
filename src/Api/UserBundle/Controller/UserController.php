<?php

namespace Api\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;

class UserController extends Controller {

    /**
     *
     * @Rest\View(
     *     statusCode = 200,
     *     serializerGroups = {"GET_USERS"}
     * )
     * @Rest\Get("/users")
     *
     */
    public function getUsersAction()
    {
        $userManager = $this->get('fos_user.user_manager');
        $users = $userManager->findUsers();

        if (empty($users))
            return new JsonResponse(null, Response::HTTP_NO_CONTENT);

        return $users;
    }

    /**
     *
     * @Rest\View(
     *     statusCode = 200,
     *     serializerGroups = {"GET_AUTHENTICATED_USER"}
     * )
     * @Rest\Get("/users/self")
     *
     */
    public function getUserAction(Request $request)
    {

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new JsonResponse(array("message" => "Requires authentication", "documentation" => "https://developers.wefaves.com/docs"), Response::HTTP_UNAUTHORIZED);
        }

        $user = $this->get('security.token_storage')->getToken()->getUser();

        return $user;
    }
}
