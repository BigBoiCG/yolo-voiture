<?php

namespace App\Controller;

use DateInterval;
use App\Entity\Membre;
use App\Entity\Commande;
use App\Entity\Vehicule;
use App\Form\CommandeType;
use Doctrine\ORM\Mapping\Id;
use App\Repository\MembreRepository;
use App\Repository\CommandeRepository;
use App\Repository\VehiculeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AppController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home(VehiculeRepository $repo): Response
    {
        $vehicules = $repo->findAll();
        return $this->render('base.html.twig', [
            'vehicules' => $vehicules
        ]);
    }
    
    #[Route('/view/{id}', name: 'view_vehicule')]
    public function index(Vehicule $vehicule): Response
    {
        $vehicule->getId();
        return $this->render('app/index.html.twig', [
            'vehicule' => $vehicule
        ]);
    }
    
    #[Route('/reservation/{id}/{user}', name: 'reserve_vehicule')]
    public function reserve(Vehicule $vehicule, Request $request, EntityManagerInterface $manager): Response
    {
        $idVehicule = $vehicule;
        $prixVehicule = $vehicule->getPrixJournalier();
        $idMembre = $this->getUser('id');
        $commande = new Commande;
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $dateDebut = $commande->getDateHeureDepart();
            $dateFin = $commande->getDateHeureFin();
            $diffDate = date_diff($dateDebut, $dateFin);
            $commande->setDateEnregistrement(new \Datetime);
            $commande->setPrixTotal($prixVehicule*$diffDate->d );
            $commande->setIdMembre($idMembre);
            $commande->setIdVehicule($idVehicule);
            $manager->persist($commande);
            $manager->flush();
            $this->addFlash('success', 'Réservation enregistrée !');
            return $this->redirectToRoute('home');
        }

        return $this->render('app/commande.html.twig', [
            'vehicule' => $vehicule,
            'formCommande' => $form
        ]);
    }

    #[Route('/profile/{id}/edit/{order}', name: 'user_commande_edit')]
    public function userEditCommandes($id, $order, CommandeRepository $commandeRepo, MembreRepository $membreRepo, Request $request, EntityManagerInterface $manager): Response
    {   
        $idMembre = $membreRepo->findOneBy(['id' => $id]);
        $commandes = $commandeRepo->findBy(['id_membre' => $idMembre], ['date_heure_depart' => 'DESC']);
        $commande = $commandeRepo->findOneBy(['id' => $order]);
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);
        $prixVehicule = $commande->getIdVehicule()->getPrixJournalier();
        
        if($form->isSubmitted() && $form->isValid())
        {
            
            $dateDebut = $commande->getDateHeureDepart();
            $dateFin = $commande->getDateHeureFin();
            $diffDate = date_diff($dateDebut, $dateFin);
             $commande->setPrixTotal($prixVehicule*$diffDate->d );
            $commande->setIdMembre($idMembre);
            $manager->persist($commande); 
            $manager->persist($commande);
            $manager->flush();
            $this->addFlash('success', 'Réservation modifiée');
            return $this->redirectToRoute('home');
        }


        return $this->render('app/profile.html.twig', [
            'commandes' => $commandes,
            'editForm' => $form,
            "editMode" => $commande->getId()!=null
        ]);
    }

    #[Route('/profile/{id}', name: 'profil_commandes')]
    public function profilCommandes(Membre $membre, CommandeRepository $repo, Request $request, EntityManagerInterface $manager): Response
    {   
        $idMembre = strval($membre->getId());
        $commandes = $repo->findBy(['id_membre' => $idMembre], ['date_heure_depart' => 'DESC']);

        return $this->render('app/profile.html.twig', [
            'commandes' => $commandes,
            "editMode" => null
        ]);
    }

    #[Route('/profile/delete/{id}', name: 'user_delete_commande')]
    public function userDeleteCommande(Commande $commande, EntityManagerInterface $manager): Response
    {
        $manager->remove($commande);
        $manager->flush();
        $this->addFlash(
            'success',
            'Réservation supprimée'); 
        return $this->redirectToRoute('home');
    }


}
