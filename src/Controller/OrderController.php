<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\OrderType;
use App\Service\Cart\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Stripe\Stripe;

class OrderController extends AbstractController
{
    /**
     * @Route("/order/{id<\d+>}", name="order.index")
     */
    public function index(User $user, Request $request, EntityManagerInterface $manager)
    {

        $form = $this->createForm(OrderType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $user = $form->getData();

            $manager->persist($user);
            $manager->flush();
            return $this->redirectToRoute('order.OrderDetails');
        }

        return $this->render('order/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/order/Details", name="order.OrderDetails")
     */

    public function OrderDetails()
    {

        return $this->render('order/payment/OrderDetails.html.twig', [

        ]);
    }

    /**
     * @Route("/order/payment/Stripe", name="order.payment")
     */

    public function Stripe(CartService $cartService)
    {
        \Stripe\Stripe::setApiKey('sk_test_51I8rTvH4q7zkMh9zE1kvYOs4ukoUiYF8XjbdtXSNpX4XioJQ0ndGm8ZMYskLJjxUXrhBRvQA7N5HUxGGhFvsB7lq00irX1uDdS');


        $total = $cartService->getTotal() * 100;

        $user = $this->getUser();
        $email = $user->getEmail();
        $name = $user->getFirstname();

        header('Content-Type: application/json');

        $session = \Stripe\Checkout\Session::create([
            'customer_email' => $email,
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => 'Produit de cbd',
                        'images' => ["https://i.imgur.com/Y3Y6rmt.jpg"],
                    ],
                    'unit_amount' => $total,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => 'https://localhost:8000/?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $this->generateUrl('order.error', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);



        return new JsonResponse([ 'id' => $session->id ]);
    }

    /**
     * @Route("/order/error", name="order.error")
     */

    public function payementError()
    {

            return $this->render('order/payment/OrderDetails.html.twig', [

            ]);

    }

}
