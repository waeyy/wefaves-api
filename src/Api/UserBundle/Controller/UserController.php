<?php

namespace Api\UserBundle\Controller;

use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\GetResponseNullableUserEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Api\UserBundle\Representation\Users;

class UserController extends Controller {

    /**
     *
     * @Rest\View(
     *     statusCode = 200,
     * )
     * @Rest\QueryParam(
     *     name="keyword",
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
     * @Rest\Get("/users")
     *
     */
    public function getUsersAction(ParamFetcherInterface $paramFetcher)
    {
        $pager = $this->getDoctrine()->getRepository('ApiUserBundle:User')->filter(
            $paramFetcher->get('keyword'),
            $paramFetcher->get('order'),
            $paramFetcher->get('limit'),
            $paramFetcher->get('offset')
        );

        //var_dump(new Users($pager));

        //return $pager->getCurrentPageResults();
        return new Users($pager);

        /*$userManager = $this->get('fos_user.user_manager');
        $users = $userManager->findUsers();

        if (empty($users))
            return new JsonResponse(null, Response::HTTP_NO_CONTENT);

        return $users;*/
    }

    /*/**
     *
     * @Rest\View(
     *     statusCode = 200,
     *     serializerGroups = {"GET_AUTHENTICATED_USER"}
     * )
     * @Rest\Get("/users/profile")
     *
     */
    /*public function getUserProfileAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new JsonResponse(array("message" => "Requires authentication", "documentation" => $this->getParameter('link_documentation')), Response::HTTP_UNAUTHORIZED);
        }

        $user = $this->getUser();

        return $user;
    }*/

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
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new JsonResponse(array("message" => "Requires authentication", "documentation" => $this->getParameter('link_documentation')), Response::HTTP_UNAUTHORIZED);
        }

        if (strcmp($request->get("id"), "profile") == 0) {
            $user = $this->getUser();
        } else {
            $userManager = $this->get('fos_user.user_manager');
            $user = $userManager->findUserBy(array("id" => $request->get("id")));
        }

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

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

            $userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('fos_user_registration_confirmed');
                $response = new RedirectResponse($url);
            }

            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

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
    public function patchUserSelfAction(Request $request) {

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new JsonResponse(array("message" => "Requires authentication", "documentation" => $this->getParameter('link_documentation')), Response::HTTP_UNAUTHORIZED);
        }

        $formFactory = $this->get('fos_user.profile.form.factory');
        $userManager = $this->get('fos_user.user_manager');
        $dispatcher = $this->get('event_dispatcher');

        $user = $this->getUser();

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $formFactory->createForm(array(
            "method" => "PATCH",
        ));

        $form->setData($user);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $event = new FormEvent($form, $request) ;
            $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_SUCCESS, $event);

            $userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('fos_user_profile_show');
                $response = new RedirectResponse($url);
            }

            $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

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
     * @Rest\Patch("/users/self/change-password")
     *
     */
    public function patchUserSelfPasswordAction(Request $request) {

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new JsonResponse(array("message" => "Requires authentication", "documentation" => $this->getParameter('link_documentation')), Response::HTTP_UNAUTHORIZED);
        }

        $user = $this->getUser();

        $formFactory = $this->get('fos_user.change_password.form.factory');
        $userManager = $this->get('fos_user.user_manager');
        $dispatcher = $this->get('event_dispatcher');

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $formFactory->createForm(array(
            "method" => "PATCH",
        ));

        $form->setData($user);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_SUCCESS, $event);

            $userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('fos_user_profile_show');
                $response = new RedirectResponse($url);
            }

            $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

            return $user;
        }


        return $form;
    }

    /**
     *
     * @Rest\View(
     *     statusCode = 200
     * )
     * @Rest\Post("/users/resetting/request")
     *
     */
    public function postUserResettingRequestAction(Request $request) {

        if ( count($this->get('jms_serializer')->deserialize($request->getContent(), 'array', 'json')) != 1) {
            return new JsonResponse(array("message" => "This form should not contain extra fields.", "documentation" => $this->getParameter('link_documentation')), Response::HTTP_BAD_REQUEST);
        }

        $email = $request->request->get('email');

        if (empty($email)) {
            return new JsonResponse(array("message" => "Email parameter not found", "documentation" => $this->getParameter('link_documentation')), Response::HTTP_BAD_REQUEST);
        }

        $user = $this->get('fos_user.user_manager')->findUserByUsernameOrEmail($email);

        if (empty($user)) {
            return new JsonResponse(array("message" => "User not found"), Response::HTTP_NOT_FOUND);
        }

        $dispatcher = $this->get('event_dispatcher');

        /* Dispatch init event */
        $event = new GetResponseNullableUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::RESETTING_SEND_EMAIL_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $ttl = $this->container->getParameter('fos_user.resetting.retry_ttl');

        if (null !== $user && !$user->isPasswordRequestNonExpired($ttl)) {
            $event = new GetResponseUserEvent($user, $request);
            $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_REQUEST, $event);

            if (null !== $event->getResponse()) {
                return $event->getResponse();
            }

            if (null === $user->getConfirmationToken()) {
                /** @var $tokenGenerator TokenGeneratorInterface */
                $tokenGenerator = $this->get('fos_user.util.token_generator');
                $user->setConfirmationToken($tokenGenerator->generateToken());
            }

            /* Dispatch confirm event */
            $event = new GetResponseUserEvent($user, $request);
            $dispatcher->dispatch(FOSUserEvents::RESETTING_SEND_EMAIL_CONFIRM, $event);

            if (null !== $event->getResponse()) {
                return $event->getResponse();
            }

            $this->get('fos_user.mailer')->sendResettingEmailMessage($user);
            $user->setPasswordRequestedAt(new \DateTime());
            $this->get('fos_user.user_manager')->updateUser($user);

            /* Dispatch completed event */
            $event = new GetResponseUserEvent($user, $request);
            $dispatcher->dispatch(FOSUserEvents::RESETTING_SEND_EMAIL_COMPLETED, $event);

            if (null !== $event->getResponse()) {
                return $event->getResponse();
            }

            return array("message" => "An email has been sent. It contains a link you must click to reset your password.");
        }

        $note = "You can only request a new password once within ". ceil($this->container->getParameter('fos_user.resetting.retry_ttl') / 3600) ." hours.";
        $message = "An email has been already sent. $note";

        return new JsonResponse(array("message" => $message), Response::HTTP_OK);
    }

    /**
     *
     * @Rest\View(
     *     statusCode = 200
     * )
     * @Rest\Patch("/users/resetting/reset")
     * @Rest\QueryParam(name="token")
     *
     */
    public function postUserResettingResetAction(Request $request, $token) {

        $formFactory = $this->get('fos_user.resetting.form.factory');
        $userManager = $this->get('fos_user.user_manager');
        $dispatcher = $this->get('event_dispatcher');

        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            $message = "The user with confirmation token does not exist for value '$token'.";
            return new JsonResponse(array("message" => $message), Response::HTTP_NOT_FOUND);
        }

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $formFactory->createForm(array(
            "method" => "PATCH",
        ));

        $form->setData($user);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_SUCCESS, $event);

            $userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('fos_user_profile_show');
                $response = new RedirectResponse($url);
            }

            $dispatcher->dispatch(
                FOSUserEvents::RESETTING_RESET_COMPLETED,
                new FilterUserResponseEvent($user, $request, $response)
            );

            return array("message" => "The password has been reset successfully.");
        }

        return $form;
    }
}
