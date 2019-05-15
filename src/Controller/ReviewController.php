<?php

namespace App\Controller;

use App\Entity\Review;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ReviewController extends AbstractController
{
    /**
     * @Route("/review", name="review_get_all", methods={"GET"})
     */
    public function getReviews(SerializerInterface $serializer, EntityManagerInterface $entityManager)
    {

        $reviews = $entityManager->getRepository(Review::class)->findAll();

        $json = $serializer->serialize($reviews, 'json', ['groups' => 'api']);

        return new Response($json, 200, [
            'Content-Type' => 'application/json',
        ]);
    }


    /**
     * @Route("/review/{id}", name="review_get", methods={"GET"})
     */
    public function getBook(Review $review, SerializerInterface $serializer)
    {

        $json = $serializer->serialize($review, 'json', ['groups' => 'api']);

        return new Response($json, 200, [
            'Content-Type' => 'application/json',
        ]);
    }

    /**
     * @Route("/review", name="review_create", methods={"POST"}, defaults={"_format"="json"})
     */
    public function createReview(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $data = $request->getContent();

        $newObject  =  $serializer->deserialize($data, Review::class, 'json');

        /** @var ConstraintViolationList $errors */
        $errors = $validator->validate($newObject, null ,['groups' => 'review', ]);

        if (count($errors) > 0) {

            return new Response($this->generateJsonErrors($errors), 400);
        }


        $entityManager->persist($newObject);
        $entityManager->flush();

        $json = $serializer->serialize($newObject, 'json', ['groups' => 'review']);

        return new Response($json, 200, [
            'Content-Type' => 'application/json',
        ]);
    }

    /**
     * @Route("/review/{id}", name="review_update", methods={"PUT"})
     */
    public function updateReview(Review $review, Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        $data = $request->getContent();

        $serializer->deserialize($data, Review::class, 'json',  ['object_to_populate' => $review, 'groups' => 'api']);

        /** @var ConstraintViolationList $errors */
        $errors = $validator->validate($review, null ,['groups' => 'api']);

        if (count($errors) > 0) {

            return new Response($this->generateJsonErrors($errors), 400);
        }

        $entityManager->flush();

        $json = $serializer->serialize($review, 'json', ['groups' => 'api']);

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
