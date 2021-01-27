<?php

namespace App\Controller;

use App\Entity\Product;
use App\Service\Cart\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @Route("/cart", name="cart.index")
     */
    public function index(CartService $cartService): Response
    {

        return $this->render('cart/index.html.twig', [
            'items' => $cartService->getFullCart(),
            'total' => $cartService->getTotal()
        ]);
    }

    /**
     * @Route("/cart/add/{id}", name="cart.add")
     */
    public function add($id, CartService $cartService, Product $product, Request $request)
    {
        $quantity = $request->query->get('quantity');

        $this->addFlash('success', 'Le produit a bien ete ajoute au panier!');
        $cartService->add($id, $product, $quantity);
       //dd($session->get('panier'));
        return $this->redirect($_SERVER['HTTP_REFERER']);
    }


    /**
     * @Route("/cart/remove/{id}", name="cart.remove")
     */
    public function remove($id, CartService $cartService, Product $product)
    {
        $cartService->remove($id, $product);

        return $this->redirectToRoute('cart.index');
    }
}
