<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SecurityController extends AbstractController
{
    /**
    * @Route("/", name="home")
    */
    public function home(Request $request) {
      //$username = new User();

      return $this->render('home.html.twig', [


      ]);
    }
    /**
    * @Route("/inscription", name="security_registration")
    */
    public function registration(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder) {
      $user = new User();

      $form = $this->createForm(RegistrationType::class, $user);

      $form->handleRequest($request);

      if($form->isSubmitted() && $form->isValid()) {
          $hash = $encoder->encodePassword($user, $user->getPassword());

          $user->setPassword($hash);

          $manager->persist($user);
          $manager->flush();

          return $this->redirectToRoute('security_login');
      }

      return $this->render('security/registration.html.twig', [
          'form' => $form->createView()
      ]);
    }

    /**
    * @Route("/connexion", name="security_login")
    */
    public function login() {
        return $this->render('security/login.html.twig');
    }

    /**
    * @Route("/deconnexion", name="security_logout")
    */
    public function logout() {}

    /**
    * @Route("/profil", name="security_profil")
    */
    public function profil() {
        return $this->render('security/profil.html.twig');
    }

    /**
    * @Route("/admin", name="security_admin")
    */
    public function admin() {
        return $this->render('security/admin.html.twig');
    }

    /**
     * @Route("/motdepasse-oublie", name="security_forgotten_password")
     */
     public function forgottenPassword(
         Request $request,
         UserPasswordEncoderInterface $encoder,
         \Swift_Mailer $mailer,
         TokenGeneratorInterface $tokenGenerator
     ): Response
     {

         if ($request->isMethod('POST')) {

             $email = $request->request->get('email');

             $entityManager = $this->getDoctrine()->getManager();
             $user = $entityManager->getRepository(User::class)->findOneByEmail($email);
             /* @var $user User */

             if ($user === null) {
                 $this->addFlash('danger', 'Email Inconnu');
                 return $this->redirectToRoute('homepage');
             }
             $token = $tokenGenerator->generateToken();

             try{
                 $user->setResetToken($token);
                 $entityManager->flush();
             } catch (\Exception $e) {
                 $this->addFlash('warning', $e->getMessage());
                 return $this->redirectToRoute('home');
             }

             $url = $this->generateUrl('security_reset_password', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);

             $message = (new \Swift_Message('Forgot Password'))
                 ->setFrom('ruben.lecomte@gmail.com')
                 ->setTo($user->getEmail())
                 ->setBody(
                     "blablabla voici le token pour reseter votre mot de passe : " . $url,
                     'text/html'
                 );

             $mailer->send($message);

             $this->addFlash('notice', 'Mail envoyé');

             return $this->redirectToRoute('home');
         }

         return $this->render('security/forgotten_password.html.twig');
     }

    /**
      * @Route("/motdepasse-oublie/{token}", name="security_reset_password")
      */
     public function resetPassword(Request $request, string $token, UserPasswordEncoderInterface $passwordEncoder)
     {

         if ($request->isMethod('POST')) {
             $entityManager = $this->getDoctrine()->getManager();

             $user = $entityManager->getRepository(User::class)->findOneByResetToken($token);
             /* @var $user User */

             if ($user === null) {
                 $this->addFlash('danger', 'Token Inconnu');
                 return $this->redirectToRoute('home');
             }

             $user->setResetToken(null);
             $user->setPassword($passwordEncoder->encodePassword($user, $request->request->get('password')));
             $entityManager->flush();

             $this->addFlash('notice', 'Mot de passe mis à jour');

             return $this->redirectToRoute('home');
         } else {

             return $this->render('security/reset_password.html.twig', ['token' => $token]);
         }

     }
}
