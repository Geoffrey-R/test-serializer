<?php

namespace App\Controller;

use App\Entity\Author;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AuthorController extends AbstractController
{
    /**
     * @Route("/author", name="author_get_all", methods={"GET"})
     */
    public function getAuthors(SerializerInterface $serializer, EntityManagerInterface $entityManager)
    {
        $books = $entityManager->getRepository(Author::class)->findAll();

        $json = $serializer->serialize($books, 'json', ['groups' => 'api']);

        return new Response($json, 200, [
            'Content-Type' => 'application/json',
        ]);
    }


    /**
     * @Route("/author/{id}", name="author_get", methods={"GET"})
     */
    public function getAuthor(Author $author, SerializerInterface $serializer)
    {

        $json = $serializer->serialize($author, 'json', ['groups' => 'api']);

        return new Response($json, 200, [
            'Content-Type' => 'application/json',
        ]);
    }

    /**
     * @Route("/author", name="author_create", methods={"POST"}, defaults={"_format"="json"})
     */
    public function createBook(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $data = $request->getContent();

        $newObject  =  $serializer->deserialize($data, Author::class, 'json', ['groups' => 'api']);

        /** @var ConstraintViolationList $errors */
        $errors = $validator->validate($newObject, null ,['groups' => 'api']);

        if (count($errors) > 0) {

            return new Response($this->generateJsonErrors($errors), 400);
        }


        $entityManager->persist($newObject);
        $entityManager->flush();

        $json = $serializer->serialize($newObject, 'json', ['groups' => 'api']);

        return new Response($json, 200, [
            'Content-Type' => 'application/json',
        ]);
    }

    /**
     * @Route("/author/{id}", name="book_update", methods={"PUT"})
     */
    public function updateBook(Author $author, Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        $data = $request->getContent();

        $serializer->deserialize($data, Author::class, 'json',  ['object_to_populate' => $author, 'groups' => 'api']);

        /** @var ConstraintViolationList $errors */
        $errors = $validator->validate($author, null ,['groups' => 'api']);

        if (count($errors) > 0) {

            return new Response($this->generateJsonErrors($errors), 400);
        }

        $entityManager->flush();

        $json = $serializer->serialize($author, 'json', ['groups' => 'api']);

        return new Response($json, 200, [
            'Content-Type' => 'application/json',
        ]);
    }


    private function generateJsonErrors(ConstraintViolationList $errors)
    {
        $jsonError = [];
        foreach ($errors as  $error){
            /** @var ConstraintViolation $error */
            array_push($jsonError, [
                'message' => $error->getMessage(),
                'path' => $error->getPropertyPath(),
            ]);
        }

        return json_encode($jsonError);

    }
}
