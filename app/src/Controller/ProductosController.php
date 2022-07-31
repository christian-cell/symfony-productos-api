<?php

namespace App\Controller;

use App\Entity\Productos;
use App\Repository\ProductosRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/productos")
 */

class ProductosController extends AbstractController
{

    /**
    * @Route("/listar", name="app_productos_listar", methods={"GET"})
    */
    public function index(ProductosRepository $productosRepository): Response
    {
        $productos = $productosRepository->findAll();
        $array_clientes = array();

        foreach($productos as $producto) {
            $array_producto[] = array(
            'nombre' => $producto->getNombre(),
            'precio' => $producto->getPrecio(),
            'codigo_barras' => $producto->getCodigoBarras(),
            'descuento' => $producto->getDescuento(),
            );
        }
        
        return new JsonResponse($array_producto);
    }

     /**
     * @Route("/new", name="app_productos_new", methods={"POST"})
     */
    public function new(Request $request, ProductosRepository $productosRepository , EntityManagerInterface $em): Response 
    {
        $request = $this->transformJsonBody($request);
      

        $producto = new Productos;
        $producto->setNombre($request->get('nombre')); 
        $producto->setCodigoBarras($request->get('codigo_barras')); 
        $producto->setPrecio($request->get('precio')); 
        $producto->setDescuento($request->get('descuento')); 
        
        $em->persist($producto);
        $em->flush();

        return new Response(
            'Producto Creado con exito', 
             Response::HTTP_OK
        );
    }

    /**
     * @Route("/{id}", name="app_productos_show", methods={"GET"})
    */
    public function show($id , ProductosRepository $productosRepository)/* : JsonResponse */
    {
        $producto = $productosRepository->findOneBy(['id' => $id]);

        $data = [
            'nombre' => $producto->getNombre(),
            'precio' => $producto->getPrecio(),
            'codigo_barras' => $producto->getCodigoBarras(),
            'descuento' => $producto->getDescuento()

        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }

     /**  
     * @Route("/{id}" , name="app_clientes_update", methods={"PUT"})
    */

    public function updateProducto($id ,Request $request, ProductosRepository $productosRepository , EntityManagerInterface $em):Response 
    {

       $producto = $productosRepository->findOneBy(['id' => $id]);


       $data = json_decode($request->getContent(),true);

    //    dump($data);

       empty($data['nombre']) ? true : $producto->setNombre($data['nombre']);
       empty($data['precio']) ? true : $producto->setPrecio($data['precio']);
       empty($data['codigo_barras']) ? true : $producto->setCodigoBarras($data['codigo_barras']);
       empty($data['descuento']) ? true : $producto->setDescuento($data['descuento']);

      $em->persist($producto);
      $em->flush();
   
        return new Response(
            'producto actualizado', 
            Response::HTTP_OK
        );
    }

    /** 
     * @Route("/{id}", name="app_productos_delete", methods={"DELETE"})
    */
    public function deleteCliente($id , ProductosRepository $productosRepository , EntityManagerInterface $em):Response
    {
        $producto = $productosRepository->findOneBy(['id' => $id]);

        $em->remove($producto);
        $em->flush();

        return new JsonResponse(['status'=> 'producto eliminado'], Response::HTTP_NO_CONTENT);
    }



    protected function transformJsonBody(\Symfony\Component\HttpFoundation\Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }

        if ($data === null) {
            return $request;
        }

        $request->request->replace($data);

        return $request;
    }
    


















    
    // @Route("/productos", name="app_productos")
     
    /* public function index(): Response
    {
        return $this->render('productos/index.html.twig', [
            'controller_name' => 'ProductosController',
        ]);
    } */
}
