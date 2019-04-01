<?php

namespace App\Controller;

use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
    public function createBook(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $data = $request->getContent();

        $newObject  =  $serializer->deserialize($data, Book::class, 'json', ['groups' => 'api']);

        $errors = $validator->validate($newObject, null ,['groups' => 'api']);

        if (count($errors) > 0) {
            /*
             * Uses a __toString method on the $errors variable which is a
             * ConstraintViolationList object. This gives us a nice string
             * for debugging.
             */
          //  $errorsString = (string) $errors;
            $jsonError = [];
            foreach ($errors as  $error){
                /** @var ConstraintViolation $error */
                array_push($jsonError, [
                    'message' => $error->getMessage(),
                    'path' => $error->getPropertyPath(),
                ]);
            }


            return new Response(json_encode($jsonError), 400);
        }


        $entityManager->persist($newObject);
        $entityManager->flush();

        $json = $serializer->serialize($newObject, 'json', ['groups' => 'api']);

        return new Response($json, 200, [
            'Content-Type' => 'application/json',
        ]);
    }

    /**
     * @Route("/book/{id}", name="book_update", methods={"PUT"})
     */
    public function updateBook(Book $book, Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager)
    {
        $data = $request->getContent();

        $serializer->deserialize($data, Book::class, 'json',  ['object_to_populate' => $book, 'groups' => 'api']);

        $entityManager->flush();

        $json = $serializer->serialize($book, 'json', ['groups' => 'api']);

        return new Response($json, 200, [
            'Content-Type' => 'application/json',
        ]);
    }

}
