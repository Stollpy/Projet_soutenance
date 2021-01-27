<?php

namespace App\Controller;

use App\Entity\Shop;
use App\Entity\User;
use App\Form\EditUserType;
use App\Form\UserType;
use App\Repository\OrderRepository;
use App\Security\LoginFormAuthenticator;
use App\Service\Uploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\HttpFoundation\Session\Session;

class UserController extends AbstractController
{
    private Uploader $uploader;

    public function __construct(Uploader $uploader)
    {
        $this->uploader = $uploader;
    }

    /**
     * @Route("/signup", name="user.index")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @param EntityManagerInterface $manager
     * @param GuardAuthenticatorHandler $GuardAuthenticatorHandler
     * @param LoginFormAuthenticator $authentifiacator
     * @return Response
     */
    public function index(Request $request,
                          UserPasswordEncoderInterface $encoder,
                          EntityManagerInterface $manager,
                          GuardAuthenticatorHandler $GuardAuthenticatorHandler,
                          LoginFormAuthenticator $authentifiacator): Response
    {

        $form = $this->createForm(UserType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $user = $form->getData();

            $PasswordNotEncoder = $form->get('PasswordNotEncoder')->getData();

            $hashedPassword = $encoder->encodePassword($user, $PasswordNotEncoder);

            $user->setPassword($hashedPassword);

            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success', 'Votre compte à bien été crée');

            return $GuardAuthenticatorHandler->authenticateUserAndHandleSuccess($user, $request, $authentifiacator, 'main');
        }


        return $this->render('user/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/user/add-bookmarks/{id}", name="user.addBookmarks")
     */
    public function addBookmarks(Shop $shop, EntityManagerInterface $manager, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
          $user = $this->getUser();
        // dd($user);

        $shop->addUser($user);
        $manager->flush();

        if ($request->isXmlHttpRequest()){
            return $this->json('ok');
        }
        $this->addFlash('success','Shop ajouté au favoris');
        return $this->redirectToRoute('home.index');
    }

    /**
     * @Route("/user/remove-bookmarks/{id}", name="user.removeBookmarks")
     */
    public function removeBookmarks(Shop $shop, EntityManagerInterface $manager, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();
        $shop->removeUser($user);
        $manager->flush();

        if ($request->isXmlHttpRequest()){
            return $this->json('ok');
        }

        $this->addFlash('success','Le shop à bien été supprimé de vos favoris');
        return $this->redirectToRoute('home.index'); 
    }

    /**
     * @Route("/signup-shop", name="user.shop")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function shopSignup(Request $request, EntityManagerInterface $manager): Response
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $shop = new Shop();

        if ($request->isMethod('post')) {
            $shop_name = $request->request->get('shopName');
            if(!empty($shop_name)) {
                $shop->setName($shop_name);
            }

            $shop_address = $request->request->get('addressLine1');
            if(!empty($shop_address)) {
                $shop->setShopAddress($shop_address);
            }

            $shop_city = $request->request->get('city');
            if(!empty($shop_city)) {
                $shop->setShopCity($shop_city);
            }

            $shop_zipcode = $request->request->get('zipcode');
            if(!empty($shop_zipcode) && strlen($shop_zipcode) == 5) {
                $shop->setShopZipcode($shop_zipcode);
            }

            $siret = $request->request->get('SIRET2');
            if(!empty($siret) && strlen($siret) == 14) {
                $shop->setSIRET($siret);
            }

            // Gestion du telechargement d'image avec le service Uploader
            $uploadedFile = $request->files->get('imageFile');
            $this->uploader->uploadShopImage($shop, $uploadedFile, 'uploads/shop/image');

            // On affecte le role de ADMIN
            $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);

            // On affecte a la shop l'utilisateur en session
            $shop->setUser($user);

            // peristence en BDD
            $manager->persist($shop);
            $manager->flush();

            // redirection vers le tableau de bord et affichage de message de succes
            $this->addFlash('success', 'Votre boutique a bien ete creee.');
            return $this->redirectToRoute('dashboard.index');
        }

        return $this->render('user/shop.signup.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route("/user/dashboard/edit/{id<\d+>}", name="user.edit")
     * @param User $user
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return RedirectResponse|Response
     */

    public function edit(User $user, Request $request, EntityManagerInterface $manager)
    {

        $this->denyAccessUnlessGranted('ROLE_USER');

        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $formData = $form->getData();

            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success', 'Votre compte à bien été modifie');

            return $this->render('user/dashboard/edit.html.twig', [
                'form' => $form->createView(),

            ]);
        }

        return $this->render('user/dashboard/edit.html.twig', [
            'form' => $form->createView(),

        ]);
    }

    /**
     * @Route("/user/dashboard/remove/{id<\d+>}", name="user.remove")
     * @param User $user
     * @param EntityManagerInterface $manager
     * @return RedirectResponse
     */

    public function remove(User $user, EntityManagerInterface $manager)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $manager->remove($user);
        $manager->flush();
        $session = new Session();
        $session->invalidate();
        return $this->redirectToRoute('app_logout');
    }

    /**
     * @Route("/user/order-listing/{id<\d+>}", name="user.order_listing")
     * @param OrderRepository $order
     * @return Response
     */
    public function orderListing(OrderRepository $order)
    {
        $user = $this->getUser();
        $orders = $order->findBy(["user_id" => $user->getId()]);
        //dd($ordersLines);

        //dd($orders);

        return $this->render('user/dashboard/order_listing.html.twig', [
            'orders' => $orders
        ]);
    }
}
