<?php

namespace App\Controller\Client;

use App\Services\UploadService;
use App\Repository\UsersRepository;
use App\Form\Users\Client\ProfilType;
use App\Form\Users\Client\ChangePasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
* @Route("/client/profil")
*/
class ProfilController extends AbstractController
{
    /**
     * @Route("/edit", name="app_profil_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, UsersRepository $usersRepository, UploadService $upload): Response
    {
        $user = $this->getUser();

        

        $form = $this->createForm(ProfilType::class, $user);
        $form->handleRequest($request);

        

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setFullName($user->getNom(). " " .$user->getPrenom());

            // Ont récupère la photo de profil
            $fichier = $form->get('avatar')->getData();
            //Ont initialise les extensions autorisé
            $valideExt = ['image/png', 'image/jpg', 'image/jpeg'];
            //Ont initialise le repertoire
            $directory = 'avatar_directory';

            // Si nous avons des fichiers
            if ($fichier) {
                // Ont appel le service d'upload
                if ($upload->sendAvatar($fichier, $valideExt, $directory, $user)['statut'] === 'success') {

                    /**
                     * Si nous avons un statut success
                     * Ont initialise un message flash success
                     */
                    $this->addFlash(
                        'primary',
                        "Votre profil a bien été modifié"
                    );

                }
                /**
                 * Si l'extension n'est pas autorisé
                 * Ont initialise un message flash danger
                 */
                else {
                    $this->addFlash(
                        'danger',
                        "Seul les images sont autorisés"
                    );
                }
            }
            /**
             * Si nous n'avons pas d'image
             * Ont fait un update de l'user
             */
            else {
                $usersRepository->save($user, true);

                $this->addFlash(
                    'primary',
                    "Votre profil a bien été modifié"
                );
            }



            return $this->redirectToRoute('app_profil_edit', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('profil/client/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/update-password", name="app_profil_update_password", methods={"GET", "POST"})
     */
    public function updatefPassword(Request $request, UserPasswordHasherInterface $encoder, UsersRepository $usersRepository) {
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $verifiedPassword = $encoder->isPasswordValid($user, $form->get('ancienPassword')->getData());

            if ($verifiedPassword === false) {
                $this->addFlash(
                    'danger',
                    "Votre mot de passe est invalide !"
                );

                return $this->redirectToRoute('app_profil_update_password');
            }
            else {
                if ($form->get('plainPassword')->getData() === $form->get('ancienPassword')->getData()) {
                    $this->addFlash(
                        'warning',
                        "Votre mot de passe correspond à celui enregistré !"
                    );
                    return $this->redirectToRoute('app_profil_update_password');
                }
                else {
                    $passwordEncoded = $encoder->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                        );

                    

                    $user->setPassword($passwordEncoded);
                    $usersRepository->add($user, true);
                        $this->addFlash(
                        'success',
                        "Votre mot de passe a bien été modifié<br>Vous devez vous reconnecter !"
                    );
                    return $this->redirectToRoute('app_logout');
                }
            }

        }

        return $this->render('profil/client/update_pass.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
