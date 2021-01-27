<?php

namespace App\Controller;


use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\Recommendation;
use App\Entity\Order;
use App\Form\SearchProductType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\ShopRepository;
use App\Service\Order\OrderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    private Recommendation $recommendation;

    public function __construct(Recommendation $recommendation)
    {
        $this->recommendation = $recommendation;
    }

    /**
     * @Route("/", name="home.index")
     * @param Request $request
     * @param CategoryRepository $categoryRepository
     * @param ShopRepository $shopRepository
     * @param ProductRepository $prod
     * @return Response
     */
    public function index(Request $request, CategoryRepository $categoryRepository,
                          ShopRepository $shopRepository, ProductRepository $prod,
                          OrderService $orderService): Response
    {
        $user = $this->getUser();

        if($user){

            $Bookmarks = $user->getBookmarks();

            $categories = $categoryRepository->findAll();

            $shops = $shopRepository->findAll();
          
            if(is_array($user->getRatings()) and !empty($user->getRatings())) {
                $recommendations = $this->getRecommendations($user);
                $recList = [];
                foreach ($recommendations as $pid => $rate) {
                    if($rate > 4) {
                        array_push($recList, $prod->find($pid));
                    }
                }

                $session_id = $request->query->get('session_id');


                if($session_id){

                    $firstName = $user->getFirstname();
                    \Stripe\Stripe::setApiKey('sk_test_51I8rTvH4q7zkMh9zE1kvYOs4ukoUiYF8XjbdtXSNpX4XioJQ0ndGm8ZMYskLJjxUXrhBRvQA7N5HUxGGhFvsB7lq00irX1uDdS');
                    $session = \Stripe\Checkout\Session::retrieve($session_id);
                    $customer = \Stripe\Customer::retrieve($session->customer);

                    $orderService->addOrder($user);

                    return $this->render('home/index.html.twig', [
                        'user' => $user,
                        'categories' => $categories,
                        'shops' => $shops,
                        'Bookmarks' => $Bookmarks,
                        'context' => 'Recherche de boutique...',
                        'firstName' => $firstName,
                        'session_id' => $session_id,
                        'recommendations' => $recList
                    ]);
                }

                return $this->render('home/index.html.twig', [
                    'user' => $user,
                    'categories' => $categories,
                    'shops' => $shops,
                    'Bookmarks' => $Bookmarks,
                    'context' => 'Recherche de boutique...',
                    'recommendations' => $recList
                ]);
            }


            $session_id = $request->query->get('session_id');


            if($session_id){

                $firstName = $user->getFirstname();
                \Stripe\Stripe::setApiKey('sk_test_51I8rTvH4q7zkMh9zE1kvYOs4ukoUiYF8XjbdtXSNpX4XioJQ0ndGm8ZMYskLJjxUXrhBRvQA7N5HUxGGhFvsB7lq00irX1uDdS');
                $session = \Stripe\Checkout\Session::retrieve($session_id);
                $customer = \Stripe\Customer::retrieve($session->customer);

                 $orderService->addOrder($user);

                return $this->render('home/index.html.twig', [
                    'user' => $user,
                    'categories' => $categories,
                    'shops' => $shops,
                    'Bookmarks' => $Bookmarks,
                    'context' => 'Recherche de boutique...',
                    'firstName' => $firstName,
                    'session_id' => $session_id
                ]);
            }

            // recuperation de la recherche
            $search = $request->get('search');
            if (!empty($search)) {
                $shops = $shopRepository->findByName($search);
            }

           
            return $this->render('home/index.html.twig', [
                'user' => $user,
                'categories' => $categories,
                'shops' => $shops,
                'Bookmarks' => $Bookmarks,
                'context' => 'Recherche de boutique...',
                'recommendations' => isset($recList) ? $recList : []
            ]);
        }

        $categories = $categoryRepository->findAll();

        $shops = $shopRepository->findAll();

        // recuperation de la recherche
        $search = $request->get('search');

        if (!empty($search)) {
            $shops = $shopRepository->findByName($search);
        }

        return $this->render('home/index.html.twig', [
            'user' => $user,
            'categories' => $categories,
            'shops' => $shops,
            'context' => 'Recherche de boutique...'
        ]);
    }

    /**
     * @param User $user
     * @return array|float[]|int[]
     */
    public function getRecommendations(User $user)
    {
        // @Route("/recommended", name="home.recommended")
        // dd($this->pearsonSim($user1, $user2));
        return ($this->recommendation->getRecommendations($user));
    }


    /**
     * @Route("/load", name="home.loadMore")
     */

    // public function loadMore(Request $request, ShopRepository $shopRepository)
    //{
    //$shops = $shopRepository->findBy(array(), array(), 4, null);


    //if($request->isXmlHttpRequest()){
    // $jsonData = array();

    //$idx = 0;
    //   foreach($shops as $shop){
    //  $temp = array(
    // 'name' => $shop->getName(),
    //'picture' => $shop->getPicture(),
    //  'id' => $shop->getId(),
    //     'siret' => $shop->getSiret()
    //   );
    //   $jsonData[$idx++] = $temp;
    //  }
    // return new JsonResponse($jsonData);
    // }else{
    //    return new JsonResponse(['test' => 'sa a foirer']);
    // }

    //}

}
