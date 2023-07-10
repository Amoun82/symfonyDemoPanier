<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(RequestStack $rs, ProductRepository $repo): Response
    {

        $session = $rs->getSession() ;

        $cart = $session->get('cart', []) ;

        // je vais creer un nouveau tableau qui contiandra des objets products et les quandtités de chaque objet
        $cartWithData = [] ;

        $total = 0;

        // pour chaque $id qui se trouve dans mon tableau $cart, j'ajoute une case au tableau  $cartWithData, qui est multidimensionnel

        //* chaque case est elle-même un tableau associatif contenant 2 cases : une case 'product' (produit entier récupéré en BDD) et une case 'quantity' (avec la quantité de se produit présent dans le panier)
        foreach ($cart as $id => $quantity)
        {
            $produit= $repo->find($id) ;
            $cartWithData[] = [
                "product" => $produit,
                "quantity" => $quantity
            ];

            $total += $produit->getPrice() * $quantity ;
        }

        return $this->render('cart/index.html.twig', [
            'items' => $cartWithData,
            'total' => $total
        ]);
    }

    #[Route('/cart/add/{id}', name:'cart_add')]
    public function add($id, RequestStack $rs)
    {
        // nous allons recupere ou creer une session grave a la class RequestStack
        $session = $rs->getSession() ;

        $cart = $session->get('cart', []) ;
        // recupéré l'attribut de session 'cart' s'il existe ou un tableau vide 
        
        // si le produit existe deja dans ma cart j'incrémente sa quantité
        if(!empty($cart[$id]))
        {
            $cart[$id]++ ;
        }else {
            $cart[$id] = 1;
            // dans mon tableau $cart, à la casse $id, je donna la valeur 1
        }

        $session->set('cart', $cart) ;
        // sauvegard l'état de mon panier en session a l'attirbut de sessions 'cart'


        //dd($session->get('cart')) ;

        return $this->redirectToRoute('app_product') ;
    }

    #[Route('cart/remove/{id}', name:'cart_remove')]
    public function remove($id, RequestStack $rs)
    {
        $session = $rs->getSession();
        $cart = $session->get('cart', []);

        if(!empty($cart[$id]))
        {
            unset($cart[$id]) ;
        }

        $session->set('cart', $cart) ;

        return $this->redirectToRoute('app_cart') ;
    }
}
