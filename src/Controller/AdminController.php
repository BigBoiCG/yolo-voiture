<?php

namespace App\Controller;

use App\Entity\Membre;
use App\Entity\Commande;
use App\Entity\Vehicule;
use App\Form\AdminCommandType;
use App\Form\MembreType;
use App\Form\VehiculeType;
use App\Repository\MembreRepository;
use App\Repository\CommandeRepository;
use App\Repository\VehiculeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'base_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    #[Route('/admin/vehicule/{id}', name: 'vehicule_edit')]
    #[Route('/admin/vehicule', name: 'gestion_vehicule')]
    public function gestionVehicule(Request $request, EntityManagerInterface $manager, VehiculeRepository $repo, Vehicule $vehicule = null): Response
    {
        $colonnes = $manager->getClassMetadata(Vehicule::class)->getFieldNames();
        $tousVehicules = $repo->findAll();

        if($vehicule == null)
        {
        $vehicule = new Vehicule;
        }
        $form = $this->createForm(VehiculeType::class, $vehicule);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $vehicule->setDateEnregistrement(new \Datetime);
            $manager->persist($vehicule);
            $manager->flush();
            $this->addFlash('success', 'Modifications enregistrées');
            return $this->redirectToRoute('base_admin');
        }
        return $this->render('admin/gestionVehicule.html.twig', [
            "formVehicule" => $form,
            "colonnes" => $colonnes,
            "vehicules" => $tousVehicules,
            "editMode" => $vehicule->getId()!=null
        ]);
    }

    #[Route('/admin/membre/{id}', name: 'membre_edit')]
    #[Route('/admin/membre', name: 'gestion_membre')]
    public function gestionMembre(Request $request, EntityManagerInterface $manager, MembreRepository $repo, Membre $membre = null, UserPasswordHasherInterface $membrePasswordHasher): Response
    {
        $colonnes = $manager->getClassMetadata(Membre::class)->getFieldNames();
        $tousMembres = $repo->findAll();

        if($membre == null)
        {
        $membre = new Membre;
        }
        $form = $this->createForm(MembreType::class, $membre);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid())
        {
            if($membre == null)
            {
            $membre->setDateEnregistrement(new \Datetime);
            }
            $manager->persist($membre);
            $manager->flush();
            $this->addFlash('success', 'Modifications enregistrées');
            return $this->redirectToRoute('base_admin');
        }

        

        return $this->render('admin/gestionMembre.html.twig', [
            "formMembre" => $form,
            "colonnes" => $colonnes,
            "membres" => $tousMembres,
            "editMode" => $membre->getId()!=null
        ]);
    }

    #[Route('/admin/delete_membre/{id}', name: 'delete_membre')]
    public function deleteMembre(Membre $membre, EntityManagerInterface $manager): Response
    {
        $manager->remove($membre);
        $manager->flush();
        $this->addFlash('success', "Suppression effectuée");
        return $this->redirectToRoute('base_admin');
    }

    #[Route('/admin/delete_vehicule/{id}', name: 'delete_vehicule')]
    public function deleteVehicule(Vehicule $vehicule, EntityManagerInterface $manager): Response
    {
        $manager->remove($vehicule);
        $manager->flush();
        $this->addFlash('success', "Suppression effectuée");
        return $this->redirectToRoute('base_admin');
    }

    #[Route('/admin/commande', name: 'gestion_commande')]
    // #[Route('/admin/commande/{id}', name: 'admin_edit_commande')]
    public function gestionCommande(EntityManagerInterface $manager, CommandeRepository $repo, Request $request): Response
    {
        $toutesCommandes = $repo->findAll();

        // if($commande == null)
        // {
        // $commande = new Commande;
        // }
        // $form = $this->createForm(AdminCommandType::class, $commande);
        // $form->handleRequest($request);
        // if($form->isSubmitted() && $form->isValid())
        // {
        //     if($commande == null)
        //     {
        //     $commande->setDateEnregistrement(new \Datetime);
        //     }
        //     $dateDebut = $commande->getDateHeureDepart();
        //     $dateFin = $commande->getDateHeureFin();
        //     $diffDate = date_diff($dateDebut, $dateFin);
        //     $prixVehicule = $commande->getIdVehicule()->getPrixJournalier();
        //     $commande->setPrixTotal($prixVehicule*$diffDate->d );
        //     $manager->persist($commande);
        //     $manager->flush();
        //     $this->addFlash('success', 'Modifications enregistrées');
        //     return $this->redirectToRoute('base_admin');
        // }

        return $this->render('admin/gestionCommandes.html.twig', [
            // "formAdminCommand" => $form,
            "commandes" => $toutesCommandes,
        ]);
    }

    #[Route('/admin/delete_commande/{id}', name: 'admin_delete_commande')]
    public function adminDeleteCommande(Commande $commande, EntityManagerInterface $manager): Response
    {
        $manager->remove($commande);
        $manager->flush();
        $this->addFlash(
            'success',
            'Réservation supprimée'); 
        return $this->redirectToRoute('base_admin');
    }
}
