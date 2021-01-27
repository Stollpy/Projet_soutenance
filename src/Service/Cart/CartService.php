<?php

namespace App\Service\Cart;


use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService {

    protected $session;
    protected $productRepository;

    public function __construct(SessionInterface $session, ProductRepository $productRepository){
        $this->session = $session;
        $this->productRepository = $productRepository;
    }


    public function add(int $id, $product, $quantity)
    {
        $panier = $this->session->get('panier', []);

        if(!empty($panier[$product->getShop()->getId()][$id]))
        {
            $panier[$product->getShop()->getId()][$id] += $quantity;
        }else
        {
            $panier[$product->getShop()->getId()][$id] = $quantity;
        }

        $this->session->set('panier', $panier);

    }

    public function remove (int $id, $product)
    {

        $panier = $this->session->get('panier', []);

        if(!empty($panier[$product->getShop()->getId()][$id])){
            unset($panier[$product->getShop()->getId()][$id]);
            if(empty($panier[$product->getShop()->getId()])){
                unset($panier[$product->getShop()->getId()]);
            }
        }

        $this->session->set('panier', $panier);

    }

    public function getFullCart() : array
    {
        $panier = $this->session->get('panier', []);

        $panierData = [];

        foreach ($panier as $Shop_Id){
            foreach ($Shop_Id as $id => $quantity) {
                $panierData[] = [
                    'product' => $this->productRepository->find($id),
                    'quantity' => $quantity
                ];
            }
        }

        return $panierData;
    }

    public function getTotal(): float
    {

        $total = 0;

        foreach ($this->getFullCart() as $item){
            $total += $item['product']->getPrice() * $item['quantity'];
        }

        $shopTotal = count($this->session->get('panier', []));
        $total +=  $shopTotal * 6;

        return $total;
    }


}