<?php

namespace Api\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
    public function getUserSelfAction(Request $request)
    {

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new JsonResponse(array("message" => "Requires authentication", "documentation" => $this->getParameter('link_documentation')), Response::HTTP_UNAUTHORIZED);
        }

        $user = $this->get('security.token_storage')->getToken()->getUser();

        return $user;
    }

    /**
     *
     * @Rest\View(
     *     statusCode = 200,
     *     serializerGroups = {"GET_USER"}
     * )
     * @Rest\Get("/users/{id}")
     *
     */
    public function getUserAction(Request $request)
    {

        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserBy(array("id" => $request->get("id")));

        if (empty($user))
            return new JsonResponse(array("message" => "Not Found", "documentation" => $this->getParameter('link_documentation')), Response::HTTP_NOT_FOUND);

        return $user;
    }

    /**
     *
     * @Rest\View(
     *     statusCode = 201,
     *     serializerGroups = {"POST_USER"}
     * )
     * @Rest\Post("/users")
     *
     */
    public function postUserAction(Request $request)
    {
        $formFactory = $this->get('fos_user.registration.form.factory');
        $userManager = $this->get('fos_user.user_manager');
        $dispatcher = $this->get('event_dispatcher');

        $user = $userManager->createUser();
        $user->setEnabled(true);

        $event = new \FOS\UserBundle\Event\GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(\FOS\UserBundle\FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $event = new \FOS\UserBundle\Event\FormEvent($form, $request);
            $dispatcher->dispatch(\FOS\UserBundle\FOSUserEvents::REGISTRATION_SUCCESS, $event);

            $userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('fos_user_registration_confirmed');
                $response = new \Symfony\Component\HttpFoundation\RedirectResponse($url);
            }

            $dispatcher->dispatch(\FOS\UserBundle\FOSUserEvents::REGISTRATION_COMPLETED, new \FOS\UserBundle\Event\FilterUserResponseEvent($user, $request, $response));

            return $user;
        }

        return $form;
    }

    /**
     *
     * @Rest\View(
     *     statusCode = 200,
     *     serializerGroups = {"GET_USER"}
     * )
     * @Rest\Patch("/users/self")
     *
     */
    public function patchUsersAction(Request $request) {

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new JsonResponse(array("message" => "Requires authentication", "documentation" => $this->getParameter('link_documentation')), Response::HTTP_UNAUTHORIZED);
        }

        $formFactory = $this->get('fos_user.profile.form.factory');
        $userManager = $this->get('fos_user.user_manager');
        $dispatcher = $this->get('event_dispatcher');

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $event = new \FOS\UserBundle\Event\GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(\FOS\UserBundle\FOSUserEvents::PROFILE_EDIT_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $formFactory->createForm(array(
            "method" => "PATCH",
        ));

        $form->setData($user);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $event = new \FOS\UserBundle\Event\FormEvent($form, $request) ;
            $dispatcher->dispatch(\FOS\UserBundle\FOSUserEvents::PROFILE_EDIT_SUCCESS, $event);

            $userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('fos_user_profile_show');
                $response = new RedirectResponse($url);
                $response = new \Symfony\Component\HttpFoundation\RedirectResponse($url);
            }

            $dispatcher->dispatch(\FOS\UserBundle\FOSUserEvents::PROFILE_EDIT_COMPLETED, new \FOS\UserBundle\Event\FilterUserResponseEvent($user, $request, $response));

            return $user;
        }

        return $form;
    }

}
