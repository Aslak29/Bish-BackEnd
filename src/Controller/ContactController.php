<?php

namespace App\Controller;

use App\Entity\Contact;
use App\GlobalFunction\FunctionErrors;
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
        $contacts =  $contactRepository->findAllOrderBy();
        $arrayContacts = [];

        foreach ($contacts as $contact){
            $arrayContacts[] = [
                'id' => $contact->getId(),
                'name' => $contact->getName(),
                'surname' => $contact->getSurname(),
                'email' => $contact->getEmail(),
                'phone' => $contact->getPhone(),
                'date' => $contact->getDate(),
                'message' => $contact->getMessage(),
                'isFinish' => $contact->isFinish()
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
    #[Route('/add',name : 'app_contact_add' , methods: ['POST'])]
    public function addContact(ContactRepository $contactRepository ,UserRepository $userRepository, Request $request, ValidatorInterface $validator):JsonResponse{

        $data = json_decode($request->getContent(), true);

        $contact = new Contact();
        $contact->setName($data['name']);
        $contact->setSurname($data['surname']);
        $contact->setEmail($data['email']);
        $contact->setMessage($data['message']);
        $contact->setPhone($data['phone']);

        if($data['userId']) {
            $user = $userRepository->find($data['userId']);
            if ($user) {
                $contact->setUser($user);
            };
        }

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

    /**
     * @param ContactRepository $contactRepository
     * @param Request $request
     * @param FunctionErrors $errorCode
     * @return JsonResponse
     * @OA\Tag (name="Contact")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/update/isFinish/{contactId}/{isFinish}',name: 'app_contact_update_isFinish' , methods:['POST'])]
    public function updateIsFinish(ContactRepository $contactRepository,Request $request, FunctionErrors $errorCode):JsonResponse
    {
        $contact = $contactRepository->find($request->attributes->get('contactId'));

        if (!$contact) {
            return $errorCode->generateCodeError007();
        }

        if ($request->attributes->get('isFinish') === "true") {
            $contact->setIsFinish(true);
        }elseif ($request->attributes->get('isFinish') === "false") {
            $contact->setIsFinish(false);
        }else {
            return $errorCode->generateCodeError006();
        }

        $contactRepository->save($contact, true);

        $categorieArray = [
            "id" => $contact->getId(),
        ];

        return new JsonResponse($categorieArray, 200);
    }

    /**
     * @param ContactRepository $contactRepository
     * @param Request $request
     * @param FunctionErrors $errorCode
     * @return JsonResponse
     * @OA\Tag (name="Contact")
     * @OA\Response(
     *     response="200",
     *     description = "OK"
     * )
     */
    #[Route('/multipleUpdate/isFinish/{isFinish}',name: 'app_contact_multiple_update_isFinish' , methods:['POST'])]
    public function multipleUpdateIsFinish(ContactRepository $contactRepository,Request $request, FunctionErrors $errorCode):JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        foreach($data as $id) {
            $contact = $contactRepository->find($id);

            if (!$contact) {
                return $errorCode->generateCodeError007();
            }

            if ($request->attributes->get('isFinish') === "true") {
                $contact->setIsFinish(true);
            }elseif ($request->attributes->get('isFinish') === "false") {
                $contact->setIsFinish(false);
            }else {
                return $errorCode->generateCodeError006();
            }

            $contactRepository->save($contact, true);
        }
        
        return new JsonResponse(null, 200);
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
    #[Route('/multipleRemove',name: 'app_contact_multiple_delete' , methods: ['POST'])]
    public function multipleRemoveContact(ContactRepository $contactRepository,Request $request):JsonResponse{
        $data = json_decode($request->getContent(), true);

        foreach($data as $id) {
            $removeContact = $contactRepository->find($id);
            if($removeContact != null){
                $contactRepository->remove($removeContact,true);
            }else{
                return new JsonResponse([
                    'errorCode' => "013",
                    'errorMessage' => "This contact don't exist"
                ],409);
            }
        }
        
        return new JsonResponse(null, 200);
    }
}
