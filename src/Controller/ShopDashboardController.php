<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Shop;
use App\Form\ProductType;
use App\Repository\OrderLineRepository;
use App\Repository\OrderRepository;
use App\Repository\ShopRepository;
use App\Service\Uploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShopDashboardController extends AbstractController
{
    private EntityManagerInterface $manager;
    private Uploader $uploader;

    public function __construct(
        EntityManagerInterface $manager,
        Uploader $uploader)
    {
        $this->uploader = $uploader;
        $this->manager = $manager;
    }

    /**
     * @Route("/dashboard/", name="dashboard.index")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $user = $this->getUser();
        $shop = $user->getShop();

        return $this->render('shop_dashboard/index.html.twig', [
            'shop' => $shop,
        ]);
    }

    /**
     * @Route("/dashboard/create/", name="dashboard.create")
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        $form = $this->createForm(ProductType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $product = $form->getData();

            $product->setShop($this->getUser()->getShop());

            // Gestion du telechargement d'image avec le service Uploader
            $uploadedFile = $form->get('imageFile')->getData();
            $this->uploader->uploadPostImage($product, $uploadedFile, 'uploads/product/image');

            // Persistence en BDD
            $this->manager->persist($product);
            $this->manager->flush();

            // AJout de message de success
            $this->addFlash('success', 'Produit ajoute');

            // Redirection vers l'index
            return $this->redirectToRoute('dashboard.index');
        }

        return $this->render('shop_dashboard/create.html.twig', [
           'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/dashboard/update/{id}", name="dashboard.update")
     * @param Request $request
     * @param Product $product
     * @return Response
     */
    public function update(Request $request, Product $product): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $product = $form->getData();
            $product->setShop($this->getUser()->getShop());

            // Gestion du telechargement d'image avec le service Uploader
            $uploadedFile = $form->get('imageFile')->getData();
            $this->uploader->uploadPostImage($product, $uploadedFile, 'uploads/product/image/');

            // Persistence en BDD
            $this->manager->flush();

            // AJout de message de success
            $this->addFlash('success', 'Produit modifie');

            // Redirection vers l'index
            return $this->redirectToRoute('dashboard.index');
        }

        return $this->render('shop_dashboard/update.html.twig', [
            'product' => $product,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/dashboard/delete/{id}", name="dashboard.delete")
     * @param Product $product
     * @return Response
     */
    public function delete(Product $product): Response
    {
        $this->manager->remove($product);
        $this->addFlash('success', 'Produit supprime');
        $this->manager->flush();
        return $this->redirectToRoute('dashboard.index');
    }



    /**
     * @Route("/dashboard/orders-listing/{id}", name="dashboard.listing-order")
     */
    public function orderListing(OrderLineRepository $order)
    {
        $user = $this->getUser();

        $orders = $order->findBy(["shop" => $user->getShop()]);
        //$test = $order->findBy(["orders" => 11]);
        //dd($orders);

        return $this->render('shop_dashboard/orders.html.twig', [
            'ordersLines' => $orders
        ]);
    }
}
