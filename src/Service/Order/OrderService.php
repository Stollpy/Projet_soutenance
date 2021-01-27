<?php

namespace App\Service\Order;


use App\Entity\Order;
use App\Entity\OrderLine;
use App\Repository\ProductRepository;
use App\Repository\ShopRepository;
use App\Service\Cart\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class OrderService {

    protected $productRepository;
    protected $session;
    protected $manager;
    protected $shop;
    protected $cartService;


    public function __construct(SessionInterface $session, ProductRepository $productRepository, EntityManagerInterface $manager, ShopRepository $shop, CartService $cartService) {
        $this->session = $session;
        $this->productRepository = $productRepository;
        $this->manager = $manager;
        $this->shop = $shop;
        $this->cartService = $cartService;
    }

    public function addOrder($user)
    {
        $total = $this->cartService->getTotal();

        $order = new Order();
        $order->setUserId($user);
        $order->setTotal($total);
        $this->manager->persist($order);


        $panier = $this->session->get('panier', []);

        foreach ($panier as $Shop_Id => $Shop){

            $shop = $this->shop->find($Shop_Id);

            foreach ($Shop as $id => $quantity) {

                $orderLine = new OrderLine();
                $orderLine->setOrders($order);
                $product = $this->productRepository->find($id);
                $orderLine->setProduct($product);
                $orderLine->setQuantity($quantity);
                $orderLine->setShop($shop);
                $this->manager->persist($orderLine);

            }
        }

        $this->manager->flush();

        $this->session->remove('panier');

    }

}
