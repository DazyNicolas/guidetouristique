<?php

namespace App\Controller;

use App\Entity\Guide;
use App\Form\RegistrationFormType;
use App\Repository\GuideRepository;
use App\Security\EmailVerifier;
use App\Security\LoginFormAuthenticator;
use App\Service\JWTService;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, LoginFormAuthenticator $authenticator, EntityManagerInterface $entityManager, FlashyNotifier $flashy, SendMailService $mail, JWTService $jwt): Response
    {
        if ($this->getUser()) {
            $flashy->warning('Vous ete déjat connecté');
             return $this->redirectToRoute('app_home');
         }

        $user = new Guide();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
                
            );
            
            $entityManager->persist($user);
            $entityManager->flush();

           //On génère le JWT de l'utilisateur
           // On crée le header
           $header = [
            "alg"=>"HS256",
            "typ"=> "JWT"
           ];
           //On crée le Payload

           $payload = [
                'user_id' => $user->getId()
           ];

           //On géner le token
           $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret') );

            // On envoie un email sans bundel

            $mail->send(
                'no-replay@monsite.com',
                $user->getEmail(),
                'Actiovation de votre compte sur guide touristique',
                'register',
                compact('user', 'token')
            );

            
            // generate a signed url and email it to the user
          //  $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
          //      (new TemplatedEmail())
           //         ->from(new Address('noreply@guide.com', 'Guide'))
           //         ->to($user->getEmail())
           //         ->subject('Please Confirm your Email')
           //         ->htmlTemplate('registration/confirmation_email.html.twig')
          //  );
            // do anything else you need here, like send an email
            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request,
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/{token}', name: 'app_verify_guide')]
    public function verify_guide($token, JWTService $jwt, FlashyNotifier $flashy, GuideRepository $guide, EntityManagerInterface $entityManager){
      //On vérifie si le token est valide, n'a pas expiré et n'a pas été modifié

      if($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->check($token, $this->getParameter('app.jwtsecret')) )
      {
        //On récupère le payload

        $payload = $jwt->getPayload($token);

        // On récupere le user du token
        $user = $guide->find($payload['user_id']);

        // On vérifie que l'utilisateur existe et n'a pas encore activé son compte
        if($user && !$user->getIsVerified()){
            $user->setIsVerified(true);
            $entityManager->flush($user);
            $flashy->warning('Votre compte est activé');
            return $this->redirectToRoute('app_publication_create');

        }

      }

      // Ici un problème se dans le token
      $flashy->warning('Token invalide ou a expiré');

      return $this->redirectToRoute('app_login');
    }

    
    #[Route('/renvoiverif', name: 'resend_verif')]

    public function resendVerif(JWTService $jwt, SendMailService $email, GuideRepository $guide, FlashyNotifier $flashy): Response
    {
        $user= $this->getUser();

        if(!$user){
            $flashy->warning('Vous devez être connecté pour accéder à cette page');
            return $this->redirectToRoute('app_login');
        }

        if($user->getIsVerified()){
            $flashy->warning('Vous compte est déjat activé');
            return $this->redirectToRoute('app_publication_create');
        }

        

            //On génère le JWT de l'utilisateur
            // On crée le header
            $header = [
                "alg"=>"HS256",
                "typ"=> "JWT"
            ];
            //On crée le Payload

            $payload = [
                    'user_id' => $user->getId()
            ];

            //On géner le token
            $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret') );

            // On envoie un email sans bundel

                $email->send(
                    'no-replay@monsite.com',
                    $user->getEmail(),
                    'Actiovation de votre compte sur guide touristique',
                    'register',
                    compact('user', 'token')
                );

                $flashy->success('Email de vérification envoyé');
                return $this->redirectToRoute('app_publication_create');
    }

    
}
