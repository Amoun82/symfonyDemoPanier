<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/', name: 'app_product')]
    public function index(ProductRepository $repo): Response
    {
        $products = $repo->findAll() ;
        return $this->render('product/index.html.twig', [
            'produits' => $products,
        ]);
    }
}
