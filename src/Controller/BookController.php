<?php

namespace App\Controller;

use App\Entity\Book;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class BookController extends AbstractController
{
    /**
     * @Route("/book", name="book_get_all", methods={"GET"})
     */
    public function getBooks()
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/BookController.php',
        ]);
    }


    /**
     * @Route("/book/{id}", name="book_get", methods={"GET"})
     */
    public function getBook(Book $book, SerializerInterface $serializer)
    {
       // dd($book);
        /*return $this->json([
            'book' => $serializer->serialize($book, '', ['groups' => 'api']),
            'path' => 'src/Controller/BookController.php',
        ]);*/

        $json = $serializer->serialize($book, 'json', ['groups' => 'api']);

        return new Response($json, 200, [
            'Content-Type' => 'application/json',
        ]);
    }

    /**
     * @Route("/book", name="book_create", methods={"POST"}, defaults={"_format"="json"})
     */
    public function createBook(Request $request)
    {

        //dd(json_decode($request->getContent(), true));

        $data = $request->request->get('data');

        dd($data);

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/BookController.php',
        ]);
    }

    /**
     * @Route("/book/{id}", name="book_update", methods={"PUT"})
     */
    public function updateBook()
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/BookController.php',
        ]);
    }

}
