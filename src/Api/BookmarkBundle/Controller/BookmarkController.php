<?php

namespace Api\BookmarkBundle\Controller;

use Api\BookmarkBundle\Entity\Bookmark;
use Api\BookmarkBundle\Entity\BookmarkFolder;
use Api\BookmarkBundle\Form\Type\BookmarkEditFormType;
use Api\BookmarkBundle\Form\Type\BookmarkFolderEditFormType;
use Api\BookmarkBundle\Form\Type\BookmarkFolderFormType;
use Api\BookmarkBundle\Form\Type\BookmarkFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class BookmarkController extends Controller
{
    /**
     *
     * @Rest\View(
     *     statusCode = 200,
     *     serializerGroups = {"GET_BOOKMARKS_FOLDERS"}
     * )
     * @Rest\Get("/users/self/bookmarks")
     */
    public function getBookmarksAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new JsonResponse(array("message" => "Requires authentication", "documentation" => $this->getParameter('link_documentation')), Response::HTTP_UNAUTHORIZED);
        }

        $user = $this->getUser();

        $repository = $this->getDoctrine()->getRepository('ApiBookmarkBundle:BookmarkFolder')->getListBookmarksFolders($user);

        if (empty($repository)) {
            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return $repository;
    }

    /**
     *
     * @Rest\View(
     *     statusCode = 200,
     *     serializerGroups = {"GET_BOOKMARKS_FOLDERS"}
     * )
     * @Rest\Get("/users/self/bookmarks/{id}")
     */
    public function getBookmarkAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new JsonResponse(array("message" => "Requires authentication", "documentation" => $this->getParameter('link_documentation')), Response::HTTP_UNAUTHORIZED);
        }

        $user = $this->getUser();

        $repository = $this->getDoctrine()->getRepository('ApiBookmarkBundle:Bookmark');
        $bookmark = $repository->find($request->get("id"));

        if (empty($bookmark)) {
            return new JsonResponse(array("message" => "The requested bookmark was not found."), Response::HTTP_NOT_FOUND);
        }

        if ($bookmark->getUser()->getId() != $user->getId()) {
            return new JsonResponse(array("message" => "You are not allowed to access to the requested resource."), Response::HTTP_UNAUTHORIZED);
        }

        return $bookmark;
    }

    /**
     *
     * @Rest\View(
     *     statusCode = 200,
     *     serializerGroups = {"GET_BOOKMARKS_FOLDERS"}
     * )
     * @Rest\Get("/users/self/bookmarks/folder/{id}")
     */
    public function getBookmarkFolderAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new JsonResponse(array("message" => "Requires authentication", "documentation" => $this->getParameter('link_documentation')), Response::HTTP_UNAUTHORIZED);
        }

        $user = $this->getUser();

        $repository = $this->getDoctrine()->getRepository('ApiBookmarkBundle:BookmarkFolder');
        $bookmarkFolder = $repository->find($request->get("id"));

        if (empty($bookmarkFolder)) {
            return new JsonResponse(array("message" => "The requested bookmark folder was not found."), Response::HTTP_NOT_FOUND);
        }

        if ($bookmarkFolder->getUser()->getId() != $user->getId()) {
            return new JsonResponse(array("message" => "You are not allowed to access to the requested resource."), Response::HTTP_UNAUTHORIZED);
        }

        return $bookmarkFolder;
    }

    /**
     *
     * @Rest\View(
     *     statusCode = 201,
     *     serializerGroups = {"GET_BOOKMARKS_FOLDERS"}
     * )
     * @Rest\Post("/users/self/bookmarks")
     *
     */
    public function postBookmarkAction(Request $request) {

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new JsonResponse(array("message" => "Requires authentication", "documentation" => $this->getParameter('link_documentation')), Response::HTTP_UNAUTHORIZED);
        }

        $user = $this->getUser();
        $bookmark = new Bookmark();

        $repository = $this->getDoctrine()->getRepository('ApiBookmarkBundle:BookmarkFolder');
        $bookmarkFolder = $repository->findOneBy(array("itemId" => $request->request->get('parentId')));

        if (empty($bookmarkFolder) && $request->request->get('parentId') > '0')
            return new JsonResponse(array("message" => "ParentId is not valid: Requested folder was not found."), Response::HTTP_NOT_FOUND);

        $form = $this->createForm(BookmarkFormType::class, $bookmark);
        $form->submit($request->request->all());

        if ($form->isValid()) {

            if (!empty($bookmarkFolder))
                $bookmarkFolder->addBookmark($bookmark);
            $user->addBookmark($bookmark);

            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($bookmark);
            $em->flush();

            return $bookmark;
        }

        return $form;
    }

    /**
     *
     * @Rest\View(
     *     statusCode = 201,
     *     serializerGroups = {"GET_BOOKMARKS_FOLDERS"}
     * )
     * @Rest\Post("/users/self/bookmarks/folder")
     *
     */
    public function postBookmarkFolderAction(Request $request) {

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new JsonResponse(array("message" => "Requires authentication", "documentation" => $this->getParameter('link_documentation')), Response::HTTP_UNAUTHORIZED);
        }

        $user = $this->getUser();
        $bookmarkFolder = new BookmarkFolder();

        $repository = $this->getDoctrine()->getRepository('ApiBookmarkBundle:BookmarkFolder');
        $bookmarkFolderParent = $repository->findOneBy(array("itemId" => $request->request->get('parentId')));

        if (empty($bookmarkFolderParent) && $request->request->get('parentId') > '0')
            return new JsonResponse(array("message" => "ParentId is not valid: Requested folder was not found."), Response::HTTP_NOT_FOUND);

        $form = $this->createForm(BookmarkFolderFormType::class, $bookmarkFolder);
        $form->submit($request->request->all());

        if ($form->isValid()) {

            $bookmarkFolder->setBookmarkFolderParent($bookmarkFolderParent);
            $user->addBookmarkFolder($bookmarkFolder);

            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($bookmarkFolder);
            $em->flush();

            return $bookmarkFolder;
        }

        return $form;
    }

    /**
     *
     * @Rest\View(
     *     statusCode = 200,
     *     serializerGroups = {"GET_BOOKMARKS_FOLDERS"}
     * )
     * @Rest\Patch("/users/self/bookmarks/{id}")
     *
     */
    public function patchBookmarkAction(Request $request) {

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new JsonResponse(array("message" => "Requires authentication", "documentation" => $this->getParameter('link_documentation')), Response::HTTP_UNAUTHORIZED);
        }

        $repository = $this->getDoctrine()->getRepository('ApiBookmarkBundle:Bookmark');
        $bookmark = $repository->find($request->get("id"));

        if (empty($bookmark)) {
            return new JsonResponse(array("message" => "Requested entity was not found."), Response::HTTP_NOT_FOUND);
        }

        $user = $this->getUser();

        if ($bookmark->getUser()->getId() != $user->getId()) {
            return new JsonResponse(array("message" => "You are not allowed to access to the requested resource."), Response::HTTP_UNAUTHORIZED);
        }

        $form = $this->createForm(BookmarkEditFormType::class, $bookmark);
        $form->submit($request->request->all(), false);

        if ($form->isValid()) {

            $em = $this->get('doctrine.orm.entity_manager');
            $em->merge($bookmark);
            $em->flush();

            return $bookmark;
        }

        return $form;
    }

    /**
     *
     * @Rest\View(
     *     statusCode = 200,
     *     serializerGroups = {"GET_BOOKMARKS_FOLDERS"}
     * )
     * @Rest\Patch("/users/self/bookmarks/folder/{id}")
     *
     */
    public function patchBookmarkFolderAction(Request $request) {

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new JsonResponse(array("message" => "Requires authentication", "documentation" => $this->getParameter('link_documentation')), Response::HTTP_UNAUTHORIZED);
        }

        $repository = $this->getDoctrine()->getRepository('ApiBookmarkBundle:BookmarkFolder');
        $bookmarkFolder = $repository->find($request->get("id"));

        if (empty($bookmarkFolder)) {
            return new JsonResponse(array("message" => "Requested entity was not found."), Response::HTTP_NOT_FOUND);
        }

        $user = $this->getUser();

        if ($bookmarkFolder->getUser()->getId() != $user->getId()) {
            return new JsonResponse(array("message" => "You are not allowed to access to the requested resource."), Response::HTTP_UNAUTHORIZED);
        }

        $form = $this->createForm(BookmarkFolderEditFormType::class, $bookmarkFolder);
        $form->submit($request->request->all(), false);

        if ($form->isValid()) {

            $em = $this->get('doctrine.orm.entity_manager');
            $em->merge($bookmarkFolder);
            $em->flush();

            return $bookmarkFolder;
        }

        return $form;
    }

    /**
     *
     * @Rest\View(
     *     statusCode = 200,
     *     serializerGroups = {"GET_BOOKMARKS_FOLDERS"}
     * )
     * @Rest\Delete("/users/self/bookmarks/{id}")
     *
     */
    public function deleteBookmarkAction(Request $request) {

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new JsonResponse(array("message" => "Requires authentication", "documentation" => $this->getParameter('link_documentation')), Response::HTTP_UNAUTHORIZED);
        }

        $user = $this->getUser();

        $repositoryBookmark = $this->getDoctrine()->getRepository('ApiBookmarkBundle:Bookmark');
        $repositoryBookmarkFolder = $this->getDoctrine()->getRepository('ApiBookmarkBundle:BookmarkFolder');

        $bookmark = $repositoryBookmark->find($request->get("id"));

        if (!empty($bookmark))
            $bookmarkFolder = $repositoryBookmarkFolder->find($bookmark->getParentId());
        else
            return new JsonResponse(array("message" => "Requested entity was not found."), Response::HTTP_NOT_FOUND);

        if ($bookmark->getUser()->getId() != $user->getId()) {
            return new JsonResponse(array("message" => "You are not allowed to access to the requested resource."), Response::HTTP_UNAUTHORIZED);
        }

        if (!empty($bookmarkFolder))
            $bookmarkFolder->removeBookmark($bookmark);

        $user->removeBookmark($bookmark);

        $em = $this->getDoctrine()->getManager();
        $em->remove($bookmark);
        $em->flush();

        return  new JsonResponse(array("message" => "Requested entity was deleted."), Response::HTTP_OK);
    }

    /**
     *
     * @Rest\View(
     *     statusCode = 200,
     *     serializerGroups = {"GET_BOOKMARKS_FOLDERS"}
     * )
     * @Rest\Delete("/users/self/bookmarks/folder/{id}")
     *
     */
    public function deleteBookmarkFolderAction(Request $request) {

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new JsonResponse(array("message" => "Requires authentication", "documentation" => $this->getParameter('link_documentation')), Response::HTTP_UNAUTHORIZED);
        }

        $user = $this->getUser();

        $repositoryBookmarkFolder = $this->getDoctrine()->getRepository('ApiBookmarkBundle:BookmarkFolder');

        $bookmarkFolder = $repositoryBookmarkFolder->find($request->get("id"));

        if (!empty($bookmarkFolder)) 
            $bookmarkFolderParent = $repositoryBookmarkFolder->find($bookmarkFolder->getParentId());
        else
            return new JsonResponse(array("message" => "Requested entity was not found."), Response::HTTP_NOT_FOUND);

        if ($bookmarkFolder->getUser()->getId() != $user->getId()) {
            return new JsonResponse(array("message" => "You are not allowed to access to the requested resource."), Response::HTTP_UNAUTHORIZED);
        }

        if (!empty($bookmarkFolderParent))
            $bookmarkFolderParent->removeBookmarkFolderChild($bookmarkFolder);

        $user->removeBookmarkFolder($bookmarkFolder);

        $em = $this->getDoctrine()->getManager();
        $em->remove($bookmarkFolder);
        $em->flush();

        return  new JsonResponse(array("message" => "Requested entity was deleted."), Response::HTTP_OK);
    }
}
