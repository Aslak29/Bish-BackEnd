<?php

namespace App\Controller;

use App\Entity\Contact;
use OpenApi\Annotations as OA;
use App\Repository\ContactRepository;
use App\Repository\UserRepository;
use PHPUnit\Util\Json;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/contact')]
class ContactController extends AbstractController
{
    /**
     * @param ContactRepository $contactRepository
     * @return JsonResponse
     * @OA\Tag (name="Contact")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/', name: 'app_contact', methods: ['GET'])]
    public function index(ContactRepository $contactRepository): JsonResponse
    {
        $contacts =  $contactRepository->findAll();
        $arrayContacts = [];

        foreach ($contacts as $contact){
            $arrayContacts[] = [
                'id' => $contact->getId(),
                'name' => $contact->getName(),
                'surname' => $contact->getSurname(),
                'email' => $contact->getEmail(),
                'phone' => $contact->getPhone(),
                'date' => $contact->getDate(),
                'message' => $contact->getMessage()
            ];
        }
        return new JsonResponse($arrayContacts,200);
    }


    /**
     * @param ContactRepository $contactRepository
     * @param UserRepository $userRepository
     * @param Request $request
     * @return JsonResponse
     * @OA\Tag (name="Contact")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/add/{name}/{surname}/{email}/{message}/{phone}',name : 'app_contact_add' , methods: ['POST'])]
    public function addContact(ContactRepository $contactRepository ,UserRepository $userRepository, Request $request, ValidatorInterface $validator):JsonResponse{

        $contact = new Contact();
        $contact->setName($request->attributes->get('name'));
        $contact->setSurname($request->attributes->get('surname'));
        $contact->setEmail($request->attributes->get('email'));
        $contact->setMessage($request->attributes->get('message'));
        $contact->setPhone($request->attributes->get("phone"));


        $errors = $validator->validate($contact);
        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            return new JsonResponse($errorsString,400);
        }

        $userByMail = $userRepository->findUserByMail($contact->getEmail());

        if ($userByMail != null){
            $contact->setUser($userByMail[0]);
        }

        $contactRepository->save($contact,true);
        return new JsonResponse([
            'successCode' => '003',
            'successMessage' => 'This contact has been create'
        ],200);
    }


    /**
     * @param ContactRepository $contactRepository
     * @param Request $request
     * @return JsonResponse
     * @OA\Tag (name="Contact")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/remove/{id}',name: 'app_contact_delete' , methods: ['DELETE'])]
    public function removeContact(ContactRepository $contactRepository,Request $request):JsonResponse{

        $removeContact = $contactRepository->find($request->attributes->get('id'));
        if($removeContact != null){
            $contactRepository->remove($removeContact,true);
        }else{
            return new JsonResponse([
                'errorCode' => "013",
                'errorMessage' => "This contact don't exist"
            ],409);
        }

        return new JsonResponse([
            'successCode' => "004",
            'successMessage' => "This contact has been remove"
        ],200);

    }
}
