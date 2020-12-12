<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Products;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductsController extends AbstractController
{
    public function showProducts(Request $request, $state, $amount)
    {
        $products = $this->getDoctrine()->getRepository(Products::class)->findAll();
        $resp = [];

        if ($state == "in") {
            foreach ($products as $product) {
                switch ($amount) {
                    case "any":
                        if ($product->getAmount())
                            $resp[$product->getId()] = $product;
                        break;
                    case "low":
                        if ($product->getAmount() > 0 && $product->getAmount() < 5)
                            $resp[$product->getId()] = $product;
                        break;
                    case "high":
                        if ($product->getAmount() >= 5)
                            $resp[$product->getId()] = $product;
                        break;
                }
            }
        } else if ($state == "out") {
            foreach ($products as $product) {
                if (!$product->getAmount())
                    $resp[$product->getId()] = $product;
            }
        } else if ($state == "all") {
            $resp = $products;
        }

        return $this->render('stock/index.html.twig', array(
            'products' => $resp
        ));
    }


    public function newProduct(Request $request): Response
    {
        $product = new Products();

        $form = $this->createFormBuilder($product)
            ->add("name", TextType::class, array("attr" => array("class" => "form-control mb-3")))
            ->add("amount", TextType::class, array("attr" => array("class" => "form-control mb-3")))
            ->add("confirm", SubmitType::class, array("label" => "Add", "attr" => array("class" => "btn btn-primary mt-3")))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('products');
        }

        return $this->render("products/new.html.twig", array(
            "form" => $form->createView()
        ));
    }

    public function deleteProduct(Request $request, $id)
    {
        $product = $this->getDoctrine()->getRepository(Products::class)->find($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($product);
        $em->flush();

        $response = new Response();
        $response->send();
    }

    public function editProduct(Request $request, $id)
    {
        $product = $this->getDoctrine()->getRepository(Products::class)->find($id);

        $form = $this->createFormBuilder($product)
            ->add("name", TextType::class, array("attr" => array("class" => "form-control mb-3")))
            ->add("amount", TextType::class, array("attr" => array("class" => "form-control mb-3")))
            ->add("confirm", SubmitType::class, array("label" => "Confirm", "attr" => array("class" => "btn btn-primary mt-3")))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('products');
        }

        return $this->render("products/edit.html.twig", array(
            "form" => $form->createView()
        ));
    }
}
