<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Shop;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShopController extends AbstractController
{
    /**
     * @Route("/shop/{id}", name="shop.index")
     * @param Shop $shop
     * @param CategoryRepository $categoryRepository
     * @param ProductRepository $productRepository
     * @param Request $request
     * @return Response
     */
    public function index(Shop $shop, CategoryRepository $categoryRepository, ProductRepository $productRepository, Request $request): Response
    {
        $user = $this->getUser();

        $categories = $categoryRepository->findAll();

        $products = $shop->getProducts();

        $search = $request->get('search');

        if (!empty($search)) {
            $products = $productRepository->findByNameAndShop($search, $shop->getId());
        }

        if($user) {

            $ratings = $user->getRatings();

            if (is_array($ratings) and !empty($ratings)) {
                $productsRated = [];
                foreach ($ratings as $rating) {
                    array_push($productsRated, $rating['product_id']);
                }

                return $this->render('home/shop_products.html.twig', [
                    'user' => $user,
                    'shop' => $shop,
                    'categories' => $categories,
                    'products' => $products,
                    'context' => 'Recherche de produits...',
                    'rated' => $productsRated
                ]);
            }

            return $this->render('home/shop_products.html.twig', [
                'user' => $user,
                'shop' => $shop,
                'categories' => $categories,
                'products' => $products,
                'context' => 'Recherche de produits...',
            ]);
        }

        return $this->render('home/shop_products.html.twig', [
            'user' => $user,
            'shop' => $shop,
            'categories' => $categories,
            'products' => $products,
            'context' => 'Recherche de produits...',
        ]);
    }

    /**
     * @Route("/shop/product/rating/{id}", name="shop.product.rating")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function rating(Request $request, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();
        //dd($request);
        $rating = $request->getContent();
        $user->addRating(json_decode($rating));
        $manager->flush();

        return $this->json([
            'code' => 200,
            'message' => 'Rating ajoute',
            'donnees' => json_decode($rating)
        ]);
    }

    
}
