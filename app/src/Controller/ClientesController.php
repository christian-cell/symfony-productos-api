<?php

namespace App\Controller;

use App\Entity\Clientes;
use App\Form\ClientesType;
use App\Repository\ClientesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/clientes")
 */
class ClientesController extends AbstractController
{

    /**
     * @var integer HTTP status code - 200 (OK) by default
     */
    protected $statusCode = 200;

  
    /********************************** retornamos todos los clientes ************************************/

    /**
     * @Route("/", name="app_clientes_index", methods={"GET"})
     */
    public function index(ClientesRepository $clientesRepository): Response
    {
        $clientes = $clientesRepository->findAll();
        $array_clientes = array();

        foreach($clientes as $cliente) {
            $array_clientes[] = array(
            'id_cliente' => $cliente->getIdCliente(),
            'nombre' => $cliente->getNombre(),
            'apellido_1' => $cliente->getApellido1(),
            'apellido_2' => $cliente->getApellido2(),
            'edad' => $cliente->getEdad(),
            );
        }
        
        return new JsonResponse($array_clientes);
    }

    /**
     * @Route("/new", name="app_clientes_new", methods={"POST"})
     */
    public function new(Request $request, ClientesRepository $clientesRepository , EntityManagerInterface $em): Response 
    {
        $request = $this->transformJsonBody($request);
      

        $cliente = new Clientes;
        $cliente->setIdCliente($request->get('id_cliente')); 
        $cliente->setNombre($request->get('nombre')); 
        $cliente->setApellido1($request->get('apellido_1')); 
        $cliente->setApellido2($request->get('apellido_2')); 
        $cliente->setEdad($request->get('edad')); 
        $em->persist($cliente);
        $em->flush();

        return new Response(
            'Cliente Creado con exito', 
             Response::HTTP_OK
        );
    }


    /**
     * @Route("/{id}", name="app_clientes_show", methods={"GET"})
    */
    public function show($id , ClientesRepository $clientesRepository)/* : JsonResponse */
    {
        $cliente = $clientesRepository->findOneBy(['id' => $id]);

        $data = [
            'id' => $cliente->getIdCliente(),
            'nombre' => $cliente->getNombre(),
            'apellido_1' => $cliente->getApellido1(),
            'apellido_2' => $cliente->getApellido2(),
            'edad' => $cliente->getEdad()
        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**  
     * @Route("/{id}" , name="app_clientes_update", methods={"PUT"})
    */

    public function updateCliente($id ,Request $request, ClientesRepository $clientesRepository , EntityManagerInterface $em):Response 
    {
    //    dump($id);

       $cliente = $clientesRepository->findOneBy(['id' => $id]);


       $data = json_decode($request->getContent(),true);

    //    dump($data);

       empty($data['id']) ? true : $cliente->setIdCliente($data['id']);
       empty($data['nombre']) ? true : $cliente->setNombre($data['nombre']);
       empty($data['apellido_1']) ? true : $cliente->setApellido1($data['apellido_1']);
       empty($data['apellido_2']) ? true : $cliente->setApellido2($data['apellido_2']);
       empty($data['edad']) ? true : $cliente->setEdad($data['id']);

      $em->persist($cliente);
      $em->flush();
   
        return new Response(
            'cliente actualizado', 
            Response::HTTP_OK
        );
    }

    /** 
     * @Route("/{id}", name="app_clientes_delete", methods={"DELETE"})
    */
    public function deleteCliente($id , ClientesRepository $clientesRepository , EntityManagerInterface $em):Response
    {
        $cliente = $clientesRepository->findOneBy(['id' => $id]);

        $em->remove($cliente);
        $em->flush();

        return new JsonResponse(['status'=> 'cliente eliminado'], Response::HTTP_NO_CONTENT);
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

    /**
 * Returns a 422 Unprocessable Entity
 *
 * @param string $message
 *
 * @return Symfony\Component\HttpFoundation\JsonResponse
 */
    public function respondValidationError($message = 'Validation errors')
    {
        return $this->setStatusCode(422)->respondWithErrors($message);
    }

    /**
 * Returns a 201 Created
 *
 * @param array $data
 *
 * @return Symfony\Component\HttpFoundation\JsonResponse
 */
    public function respondCreated($data = [])
    {
        return $this->setStatusCode(201)->respondCreated($data);
    }

 /**
     * Sets the value of statusCode.
     *
     * @param integer $statusCode the status code
     *
     * @return self
     */
    protected function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

     /**
     * Sets an error message and returns a JSON response
     *
     * @param string $errors
     *
     * @return Symfony\Component\HttpFoundation\JsonResponse
     */
    public function respondWithErrors($errors, $headers = [])
    {
        $data = [
            'errors' => $errors,
        ];

        return new JsonResponse($data, $this->getStatusCode(), $headers);
    }

    /**
     * Gets the value of statusCode.
     *
     * @return integer
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function respond($data, $headers = [])
    {
        return new JsonResponse($data, $this->getStatusCode(), $headers);
    }

}
