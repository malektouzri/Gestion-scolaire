<?php

namespace GS\BackOfficeBundle\Controller;

use GS\BackOfficeBundle\Entity\Absence;
use GS\BackOfficeBundle\Entity\Classe;
use GS\BackOfficeBundle\Entity\Convocation;
use GS\BackOfficeBundle\Entity\Document;
use GS\BackOfficeBundle\Entity\Ecole;
use GS\BackOfficeBundle\Entity\Eleve;
use GS\BackOfficeBundle\Entity\Enseignant;
use GS\BackOfficeBundle\Entity\EnsMatClasse;
use GS\BackOfficeBundle\Entity\Matiere;
use GS\BackOfficeBundle\Entity\Note;
use GS\BackOfficeBundle\Entity\ParentEleve;
use GS\BackOfficeBundle\Form\AbsenceType;
use GS\BackOfficeBundle\Form\ClasseType;
use GS\BackOfficeBundle\Form\DocumentType;
use GS\BackOfficeBundle\Form\EcoleType;
use GS\BackOfficeBundle\Form\EleveType;
use GS\BackOfficeBundle\Form\EnseignantType;
use GS\BackOfficeBundle\Form\MatiereType;
use GS\BackOfficeBundle\Form\ModifDocType;
use GS\BackOfficeBundle\Form\NoteType;
use GS\BackOfficeBundle\Form\ParentEleveType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class DefaultController extends Controller
{
    //mail auto
    // + déploiement

    public function homeAction(Request $request){

        return $this->render('BackOfficeBundle:Default:home.html.twig',array());
    }

    public function proposAction(){

        return $this->render('BackOfficeBundle:Default:propos.html.twig',array());
    }

    public function accueilAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $conv = $em->getRepository('BackOfficeBundle:Convocation')->getAll(false,$id);
        $nb = $em->getRepository('BackOfficeBundle:Convocation')->getRows(false,$id);

        return $this->render('BackOfficeBundle:Default:accueil.html.twig',array('conv'=>$conv,'nb'=>$nb,'id'=>$id));
    }

    public function espaceElvAction($id){

        $em = $this->getDoctrine()->getManager();
        $elv = $em->getRepository('BackOfficeBundle:Eleve')->find($id);

        return $this->render('BackOfficeBundle:Default:espaceElv.html.twig',array('elv'=>$elv));
    }

    public function espaceParAction($id){

        $em = $this->getDoctrine()->getManager();
        $par = $em->getRepository('BackOfficeBundle:ParentEleve')->find($id);
        $conv = $em->getRepository('BackOfficeBundle:Convocation')->getConv($par->getId(),'Reçu');

        return $this->render('BackOfficeBundle:Default:espacePar.html.twig',array('par'=>$par,'conv'=>$conv));
    }


    public function chmodElvAction(Request $request,$id){

        $em = $this->getDoctrine()->getManager();
        $elv = $em->getRepository('BackOfficeBundle:Eleve')->find($id);
        if (isset($_POST['valider'])){
            $converter = new Encryption();
            $decoded = $converter->decode($elv->getMotDePasse());
            if ($request->get('amdp') != $decoded) {
                $this->get('session')->getFlashBag()->add('Erreur','Veuillez vérifier votre ancien mot de passe!');
            }elseif (($request->get('nmdp') != $request->get('cmdp'))) {
                $this->get('session')->getFlashBag()->add('Erreur','Veuillez vérifier les nouveaux mots de passe!');
            }else{
                if (!empty($request->get('nmdp'))){
                    $converter = new Encryption();
                    $encoded = $converter->encode($request->get('nmdp'));
                    $elv->setMotDePasse($encoded);
                $em->persist($elv);
                $em->flush();
                $this->get('session')->getFlashBag()->add('Notice','Modification effectuée avec succés!');}

            }
        }

        return $this->render('BackOfficeBundle:Default:chmodElv.html.twig',array('e'=>$elv));
    }

    public function profilElvAction(Request $request,$id){

        $em = $this->getDoctrine()->getManager();
        $elv = $em->getRepository('BackOfficeBundle:Eleve')->find($id);
        $form = $this->createForm(NoteType::class);
        $form->handleRequest($request);

        if (isset($_POST['valider'])){

                if ($form['fichier']->getData() == null){
                    /**
                     * @var UploadedFile $file
                     */
                    $elv->setPhoto($elv->getPhoto());
                }else{
                    /**
                     * @var UploadedFile $file
                     */
                    $elv->setPhoto(null);
                    $em->persist($elv);
                    $em->flush();
                    $file = $form['fichier']->getData();
                    $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                    $file->move(
                        $this->getParameter('image_directory'), $fileName
                    );
                    $elv->setPhoto($fileName);
                }

                $em->persist($elv);
                $em->flush();
                $this->get('session')->getFlashBag()->add('Notice','Modification effectuée avec succés!');
        }

        return $this->render('BackOfficeBundle:Default:profilElv.html.twig',array('e'=>$elv,'form'=>$form->createView()));
    }

    public function chmodEnsAction(Request $request,$id){

        $em = $this->getDoctrine()->getManager();
        $ens = $em->getRepository('BackOfficeBundle:Enseignant')->find($id);
        if (isset($_POST['valider'])){
            $converter = new Encryption();
            $decoded = $converter->decode($ens->getMotDePasse());
            if ($request->get('amdp') != $decoded) {
                $this->get('session')->getFlashBag()->add('Erreur','Veuillez vérifier votre ancien mot de passe!');
            }elseif (($request->get('nmdp') != $request->get('cmdp'))) {
                $this->get('session')->getFlashBag()->add('Erreur','Veuillez vérifier les nouveaux mots de passe!');
            }else{
                if (!empty($request->get('nmdp'))){
                    $converter = new Encryption();
                    $encoded = $converter->encode($request->get('nmdp'));
                    $ens->setMotDePasse($encoded);
                $em->persist($ens);
                $em->flush();
                $this->get('session')->getFlashBag()->add('Notice','Modification effectuée avec succés!');}

            }
        }

        return $this->render('BackOfficeBundle:Default:chmodEns.html.twig',array('e'=>$ens));
    }

    public function profilEnsAction(Request $request,$id){

        $em = $this->getDoctrine()->getManager();
        $ens = $em->getRepository('BackOfficeBundle:Enseignant')->find($id);
        $form = $this->createForm(NoteType::class);
        $form->handleRequest($request);

        if (isset($_POST['valider'])){

            if ($form['fichier']->getData() == null){
                /**
                 * @var UploadedFile $file
                 */
                $ens->setPhoto($ens->getPhoto());
            }else{
                /**
                 * @var UploadedFile $file
                 */
                $ens->setPhoto(null);
                $em->persist($ens);
                $em->flush();
                $file = $form['fichier']->getData();
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                $file->move(
                    $this->getParameter('image_directory'), $fileName
                );
                $ens->setPhoto($fileName);
            }
            $ens->setTelephone($request->get('tel'));
            $ens->setAdresse($request->get('adr'));
            $ens->setEmail($request->get('mail'));

            $em->persist($ens);
            $em->flush();
                $this->get('session')->getFlashBag()->add('Notice','Modification effectuée avec succés!');



            }
        return $this->render('BackOfficeBundle:Default:profilEns.html.twig',array('e'=>$ens,'form'=>$form->createView()));
    }

    public function chmodParAction(Request $request,$id){

        $em = $this->getDoctrine()->getManager();
        $par = $em->getRepository('BackOfficeBundle:ParentEleve')->find($id);
        if (isset($_POST['valider'])){
            $converter = new Encryption();
            $decoded = $converter->decode($par->getMotDePasse());
            if ($request->get('amdp') != $decoded) {
                $this->get('session')->getFlashBag()->add('Erreur','Veuillez vérifier votre ancien mot de passe!');
            }elseif (($request->get('nmdp') != $request->get('cmdp'))) {
                $this->get('session')->getFlashBag()->add('Erreur','Veuillez vérifier les nouveaux mots de passe!');
            }else{
                if (!empty($request->get('nmdp'))){
                    $converter = new Encryption();
                    $encoded = $converter->encode($request->get('nmdp'));
                    $par->setMotDePasse($encoded);
                $em->persist($par);
                $em->flush();
                $this->get('session')->getFlashBag()->add('Notice','Modification effectuée avec succés!');}
            }
        }

        return $this->render('BackOfficeBundle:Default:chmodPar.html.twig',array('p'=>$par));
    }

    public function profilParAction(Request $request,$id){

        $em = $this->getDoctrine()->getManager();
        $par = $em->getRepository('BackOfficeBundle:ParentEleve')->find($id);
        $form = $this->createForm(NoteType::class);
        $form->handleRequest($request);

        if (isset($_POST['valider'])){

                if ($form['fichier']->getData() == null){
                    /**
                     * @var UploadedFile $file
                     */
                    $par->setPhoto($par->getPhoto());
                }else{
                    /**
                     * @var UploadedFile $file
                     */
                    $par->setPhoto(null);
                    $em->persist($par);
                    $em->flush();
                    $file = $form['fichier']->getData();
                    $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                    $file->move(
                        $this->getParameter('image_directory'), $fileName
                    );
                    $par->setPhoto($fileName);
                }
                $par->setTelephone($request->get('tel'));
                $par->setAdresse($request->get('adr'));
                $par->setEmail($request->get('mail'));


                $em->persist($par);
                $em->flush();
                $this->get('session')->getFlashBag()->add('Notice','Modification effectuée avec succés!');

            }

        return $this->render('BackOfficeBundle:Default:profilPar.html.twig',array('p'=>$par,'form'=>$form->createView()));
    }

    public function suppConvAction($id,$idE){

        $em = $this->getDoctrine()->getManager();
        $conv = $em->getRepository('BackOfficeBundle:Convocation')->find($id);
        $em->remove($conv);
        $em->flush();

        return $this->redirectToRoute('_afficheConv',['id'=>$idE]);
    }


    public function afficheConvAction(Request $request,$id){

        $em = $this->getDoctrine()->getManager();
        $conv = $em->getRepository('BackOfficeBundle:Convocation')->getOrdred($id);

        /**
         * @var $paginator \knp\Component\Pager\Paginator
         */
        $paginator = $this->get('knp_paginator');
        $result = $paginator->paginate($conv,
            $request->query->getInt('page',1),
            $request->query->getInt('limit',5));
       return $this->render('BackOfficeBundle:Default:afficheConv.html.twig',array('id'=>$id,'conv'=>$result));
    }

    public function detConvAction(Request $request,$id){

        $em = $this->getDoctrine()->getManager();
        $conv = $em->getRepository('BackOfficeBundle:Convocation')->find($id);
        $conv->setLu(true);
        $em->persist($conv);
        $em->flush();
        $parent = $em->getRepository('BackOfficeBundle:ParentEleve')->find($conv->getIdEleve()->getParent()->getId());

        return $this->render('BackOfficeBundle:Default:detConv.html.twig',array('c'=>$conv,'p'=>$parent,'id'=>$conv->getIdEcole()->getId()));
    }

    public function detRefusAction(Request $request,$id){

        $em = $this->getDoctrine()->getManager();
        $conv = $em->getRepository('BackOfficeBundle:Convocation')->find($id);
        $conv->setEtat('ok');
        $em->persist($conv);
        $em->flush();

        $parent = $em->getRepository('BackOfficeBundle:ParentEleve')->find($conv->getIdEleve()->getParent()->getId());

        return $this->render('BackOfficeBundle:Default:detRefus.html.twig',array('c'=>$conv,'p'=>$parent,'id'=>$parent->getIdEcole()->getId()));
    }


    public function envoiMailAction($id,$c){

        $em = $this->getDoctrine()->getManager();
        $parent = $em->getRepository('BackOfficeBundle:ParentEleve')->find($id);
        $conv = $em->getRepository('BackOfficeBundle:Convocation')->find($c);
        $conv->setEtat('Reçu');
        $em->persist($conv);
        $em->flush();
        $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com',465,'ssl')
            ->setUsername('gst.ecole.test@gmail.com')
            ->setPassword('malektouzri94');
        $mailer = $this->container->get('mailer');
        $mailer = \Swift_Mailer::newInstance($transport);
        $dat = $conv->getDate();
        $date = $dat->format('d-m-Y');
        $message = 'Votre êtes convoqué le '.$date.' par l\'enseignant(e) '.$conv->getIdEns()->getPrenom().' '.$conv->getIdEns()->getNom()
            .' sous le motif de: '.$conv->getMotif().'
                
               Ce mail est envoyé automatiquement, Veuillez ne pas répondre et contacter l\'école, Merci';
        $message = \Swift_Message::newInstance('test')
            ->setSubject('Convocation')
            ->setFrom('gst.ecole.test@gmail.com')
            ->setTo($parent->getEmail())
            ->setCharset('utf-8')
            ->setContentType('text/html')
            ->setBody($message);

       $this->get('mailer')->send($message);
       $this->get('session')->getFlashBag()->add('Notice','Un mail ainsi une q\'une notification sont envoyés au parent!');



        return $this->render('BackOfficeBundle:Default:detConv.html.twig',array('c'=>$conv,'p'=>$parent,'id'=>$conv->getIdEcole()->getId()));
    }

    public function notifEnsAction(Request $request,$id){
        $em = $this->getDoctrine()->getManager();
        $conv = $em->getRepository('BackOfficeBundle:Convocation')->get($id,'Confirmé');
        $ens = $em->getRepository('BackOfficeBundle:Enseignant')->find($id);
        foreach ($conv as $c){
           $c->setEtat('Ok');
           $em->persist($c);
           $em->flush();
        }
        return $this->render('BackOfficeBundle:Default:notifEns.html.twig',array('ens'=>$ens,'conv'=>$conv));
    }

    public function liaisonAdminAction(Request $request,$id){

        $em = $this->getDoctrine()->getManager();
        $conv = $em->getRepository('BackOfficeBundle:Convocation')->getAllPar($id);
        $par = $em->getRepository('BackOfficeBundle:ParentEleve')->find($id);
        return $this->render('BackOfficeBundle:Default:liaisonAdmin.html.twig',array('par'=>$par,'conv'=>$conv));
    }

    public function notifParAction(Request $request,$id){

        $em = $this->getDoctrine()->getManager();
        $conv = $em->getRepository('BackOfficeBundle:Convocation')->getConv($id,'Reçu');
        $par = $em->getRepository('BackOfficeBundle:ParentEleve')->find($id);
        $eleve = $em->getRepository('BackOfficeBundle:Eleve')->findOneBy(array('parent'=>$par));


        return $this->render('BackOfficeBundle:Default:notifPar.html.twig',array('par'=>$par,'conv'=>$conv,'id'=>$eleve->getIdEcole()->getId()));
    }

    public function accepterAction($id,$idP){

        $em = $this->getDoctrine()->getManager();
        $convs = $em->getRepository('BackOfficeBundle:Convocation')->getConv($idP,'En attente');
        $par = $em->getRepository('BackOfficeBundle:ParentEleve')->find($idP);
        $conv = $em->getRepository('BackOfficeBundle:Convocation')->find($id);
        $conv->setEtat('Confirmé');
        $em->persist($conv);
        $em->flush();
        $this->get('session')->getFlashBag()->add('Notice','Vous avez accepté le RDV!');


        return $this->render('BackOfficeBundle:Default:notifPar.html.twig',array('par'=>$par,'conv'=>$convs,'id'=>$conv->getIdEcole()->getId()));
    }

    public function refuserAction($id,$idP){

        $em = $this->getDoctrine()->getManager();
        $convs = $em->getRepository('BackOfficeBundle:Convocation')->getConv($idP,'En attente');
        $par = $em->getRepository('BackOfficeBundle:ParentEleve')->find($idP);
        $conv = $em->getRepository('BackOfficeBundle:Convocation')->find($id);
        $conv->setEtat('Refusé');
        $em->persist($conv);
        $em->flush();
        $this->get('session')->getFlashBag()->add('Notice','Vous avez refusé le RDV!');

        return $this->render('BackOfficeBundle:Default:notifPar.html.twig',array('par'=>$par,'conv'=>$convs,'id'=>$conv->getIdEcole()->getId()));
    }


    public function espaceEnsAction($id){

        $em = $this->getDoctrine()->getManager();
        $ens = $em->getRepository('BackOfficeBundle:Enseignant')->find($id);
        $conv = $em->getRepository('BackOfficeBundle:Convocation')->get($ens->getId(),'Confirmé');

        return $this->render('BackOfficeBundle:Default:espaceEns.html.twig',array('ens'=>$ens,'conv'=>$conv));
    }

    public function convParentAction(Request $request,$id){

        $em = $this->getDoctrine()->getManager();

        $ens = $em->getRepository('BackOfficeBundle:Enseignant')->find($id);
        $classe = $em->getRepository('BackOfficeBundle:EnsMatClasse')->findBy(array('ensId'=>$ens));
        if (isset($_POST['valider'])){
            $conv = new Convocation();

            $conv->setIdEns($ens);
            $eleve = $em->getRepository('BackOfficeBundle:Eleve')->find($request->get('el'));
            $conv->setIdEleve($eleve);
            $ecole = $em->getRepository('BackOfficeBundle:Ecole')->find($eleve->getIdEcole()->getId());
            $conv->setIdEcole($ecole);
            $conv->setMotif($request->get('motif'));
            $conv->setLu(false);
            $conv->setDate(new \DateTime('now'));
            $conv->setEtat('En attente');
            $em->persist($conv);
            $em->flush();
            $this->get('session')->getFlashBag()->add('Notice','Le parent de l\'élève '. $eleve->getPrenom().' '.$eleve->getNom().' sera informé de votre convocation');

        }

        return $this->render('BackOfficeBundle:Default:convParent.html.twig',array('cl'=>$classe,'cc'=>$request->get('classe'),'ens'=>$ens));
    }

    public function classeEnsAction(Request $request,$id){

        $em = $this->getDoctrine()->getManager();

        $classe = null;
        $ens = $em->getRepository('BackOfficeBundle:Enseignant')->find($id);
        $classes = $em->getRepository('BackOfficeBundle:EnsMatClasse')->findBy(array('ensId'=>$ens));

        if (isset($_POST['valider'])){
            $classe = $em->getRepository('BackOfficeBundle:Classe')->find($request->get('classe'));
            if($classe->getFichier() == null){
                $this->get('session')->getFlashBag()->add('Erreur','Pas encore disponible!');
            }else{
            $this->render('BackOfficeBundle:Default:classeEns.html.twig',array('cl'=>$classes,'cc'=>$request->get('classe'),'class'=>$classe,'ens'=>$ens));
            }
        }

        return $this->render('BackOfficeBundle:Default:classeEns.html.twig',array('cl'=>$classes,'cc'=>$request->get('classe'),'class'=>$classe,'ens'=>$ens));
    }

    public function planExamAction(Request $request,$id){

        $em = $this->getDoctrine()->getManager();

        $classe = null;
        $ens = $em->getRepository('BackOfficeBundle:Enseignant')->find($id);
        $classes = $em->getRepository('BackOfficeBundle:EnsMatClasse')->findBy(array('ensId'=>$ens));
        if (isset($_POST['valider'])){
            $classe = $em->getRepository('BackOfficeBundle:Classe')->find($request->get('classe'));
            if($classe->getPlanning() == null){
                $this->get('session')->getFlashBag()->add('Erreur','Pas encore disponible!');
            }else{
            $this->render('BackOfficeBundle:Default:planExam.html.twig',array('cl'=>$classes,'cc'=>$request->get('classe'),'class'=>$classe,'ens'=>$ens));
        }}

        return  $this->render('BackOfficeBundle:Default:planExam.html.twig',array('cl'=>$classes,'cc'=>$request->get('classe'),'class'=>$classe,'ens'=>$ens));
    }

    public function planExamElvAction($id){

        $em = $this->getDoctrine()->getManager();
        $elv = $em->getRepository('BackOfficeBundle:Eleve')->find($id);
        $classe = $em->getRepository('BackOfficeBundle:Classe')->find($elv->getClasse()->getId());
        if ($classe->getPlanning() != null){
            $this->render('BackOfficeBundle:Default:planExamElv.html.twig',array('class'=>$classe,'elv'=>$elv));
        }else{
            $this->get('session')->getFlashBag()->add('Erreur','Pas encore disponible!');
        }

        return $this->render('BackOfficeBundle:Default:planExamElv.html.twig',array('class'=>$classe,'elv'=>$elv));
    }

    public function planExamParAction(Request $request,$id){

        $em = $this->getDoctrine()->getManager();
        $par = $em->getRepository('BackOfficeBundle:ParentEleve')->find($id);
        $conv = $em->getRepository('BackOfficeBundle:Convocation')->getConv($id,'Reçu');
        $eleve = $em->getRepository('BackOfficeBundle:Eleve')->findBy(array('parent'=>$par));
        $classe = null;

        if (isset($_POST['valider'])){
            $elv = $em->getRepository('BackOfficeBundle:Eleve')->find($request->get('enfant'));
            $classe = $em->getRepository('BackOfficeBundle:Classe')->find($elv->getClasse()->getId());
            if ($classe->getPlanning() != null){
            $this->render('BackOfficeBundle:Default:planExamPar.html.twig',array('conv'=>$conv,'par'=>$par,'class'=>$classe,'enfant'=>$eleve,'ee'=>$request->get('enfant')));
        }else{$this->get('session')->getFlashBag()->add('Erreur','Pas encore disponible!');}}

        return $this->render('BackOfficeBundle:Default:planExamPar.html.twig',array('conv'=>$conv,'par'=>$par,'class'=>$classe,'enfant'=>$eleve,'ee'=>$request->get('enfant')));
    }

    public function noteElvAction(Request $request,$id){

        $em = $this->getDoctrine()->getManager();
        $elv = $em->getRepository('BackOfficeBundle:Eleve')->find($id);
        $note = $em->getRepository('BackOfficeBundle:Note')->findBy(array('idElv'=>$elv));

        return $this->render('BackOfficeBundle:Default:noteElv.html.twig',array('note'=>$note,'elv'=>$elv));
    }

    public function afficheAbsElvAction($id){

        $em = $this->getDoctrine()->getManager();
        $elv = $em->getRepository('BackOfficeBundle:Eleve')->find($id);
        $abs = $em->getRepository('BackOfficeBundle:Absence')->findBy(array('idEleve'=>$elv));
        if ($abs != null){
            $this->render('BackOfficeBundle:Default:afficheAbsElv.html.twig',array('abs'=>$abs,'elv'=>$elv));
        }else{$this->get('session')->getFlashBag()->add('Notice','Aucune absence!');
        }

        return $this->render('BackOfficeBundle:Default:afficheAbsElv.html.twig',array('abs'=>$abs,'elv'=>$elv));
    }

    public function afficheAbsParAction(Request $request,$id){

        $em = $this->getDoctrine()->getManager();
        $par = $em->getRepository('BackOfficeBundle:ParentEleve')->find($id);
        $conv = $em->getRepository('BackOfficeBundle:Convocation')->getConv($id,'Reçu');
        $eleve = $em->getRepository('BackOfficeBundle:Eleve')->findBy(array('parent'=>$par));
        $abs = null;

        if (isset($_POST['valider'])){
            $elv = $em->getRepository('BackOfficeBundle:Eleve')->find($request->get('enfant'));
            $abs = $em->getRepository('BackOfficeBundle:Absence')->findBy(array('idEleve'=>$elv));
            if ($abs != null ){
                $this->render('BackOfficeBundle:Default:afficheAbsPar.html.twig',array('conv'=>$conv,'par'=>$par,'abs'=>$abs,'enfant'=>$eleve,'ee'=>$request->get('enfant')));
            }else{$this->get('session')->getFlashBag()->add('Notice','Aucune absence!');
            }
        }

        return $this->render('BackOfficeBundle:Default:afficheAbsPar.html.twig',array('conv'=>$conv,'par'=>$par,'abs'=>$abs,'enfant'=>$eleve,'ee'=>$request->get('enfant')));
    }

    public function noteParAction(Request $request,$id){

        $em = $this->getDoctrine()->getManager();
        $par = $em->getRepository('BackOfficeBundle:ParentEleve')->find($id);
        $conv = $em->getRepository('BackOfficeBundle:Convocation')->getConv($id,'Reçu');
        $eleve = $em->getRepository('BackOfficeBundle:Eleve')->findBy(array('parent'=>$par));
        $note = null;

        if (isset($_POST['valider'])){
            $elv = $em->getRepository('BackOfficeBundle:Eleve')->find($request->get('enfant'));
            $note = $em->getRepository('BackOfficeBundle:Note')->findBy(array('idElv'=>$elv));
            $this->render('BackOfficeBundle:Default:notePar.html.twig',array('conv'=>$conv,'par'=>$par,'enfant'=>$eleve,'note'=>$note,'ee'=>$request->get('enfant')));
        }

        return $this->render('BackOfficeBundle:Default:notePar.html.twig',array('conv'=>$conv,'par'=>$par,'enfant'=>$eleve,'note'=>$note,'ee'=>$request->get('enfant')));
    }

    public function emploiParAction(Request $request,$id){

        $em = $this->getDoctrine()->getManager();
        $par = $em->getRepository('BackOfficeBundle:ParentEleve')->find($id);
        $conv = $em->getRepository('BackOfficeBundle:Convocation')->getConv($id,'Reçu');
        $eleve = $em->getRepository('BackOfficeBundle:Eleve')->findBy(array('parent'=>$par));
        $classe = null;

        if (isset($_POST['valider'])){
            $elv = $em->getRepository('BackOfficeBundle:Eleve')->find($request->get('enfant'));
            $classe = $em->getRepository('BackOfficeBundle:Classe')->findOneBy(array('id'=>$elv->getClasse()->getId()));
            $this->render('BackOfficeBundle:Default:emploiPar.html.twig',array('conv'=>$conv,'par'=>$par,'enfant'=>$eleve,'class'=>$classe,'ee'=>$request->get('enfant')));
        }

        return $this->render('BackOfficeBundle:Default:emploiPar.html.twig',array('conv'=>$conv,'par'=>$par,'enfant'=>$eleve,'class'=>$classe,'ee'=>$request->get('enfant')));
    }

    public  function modAbsAction(Request $request,$id,$idE){

        $em = $this->getDoctrine()->getManager();
        $abs = $em->getRepository('BackOfficeBundle:Absence')->find($id);
        $class = $em->getRepository('BackOfficeBundle:Classe')->allOrdred();
        if (isset($_POST['valider'])){
            $elv = $em->getRepository('BackOfficeBundle:Eleve')->find($request->get('eleve'));
            $abs->setIdEleve($elv);
            $clas = $em->getRepository('BackOfficeBundle:Classe')->find($request->get('classe'));
            $abs->setClasse($clas);
            $mat = $em->getRepository('BackOfficeBundle:Matiere')->find($request->get('matiere'));
            $abs->setIdMat($mat);
            $abs->setDate(new \DateTime($request->get('date')));
            $abs->setHeure(new \DateTime($request->get('heure')));
            $em->persist($abs);
            $em->flush();
            $this->get('session')->getFlashBag()->add('Notice','Modification effectuée avec succés!');
        }

        return $this->render('BackOfficeBundle:Default:modAbs.html.twig',array('id'=>$idE,'abs'=>$abs,'cl'=>$class));
    }

    public function modifNoteAction(Request $request,$id,$idE){

        $em = $this->getDoctrine()->getManager();
        $note = $em->getRepository('BackOfficeBundle:Note')->find($id);
        $ens = $em->getRepository('BackOfficeBundle:Enseignant')->find($idE);
        $class = $em->getRepository('BackOfficeBundle:Classe')->allOrdred();
        $fileUploader = new FileUploader('uploads/pdf');
        $form = $this->createForm(NoteType::class);
        $form->handleRequest($request);

        if (isset($_POST['valider'])){

            if (empty($form['fichier']->getData())){
                /**
                 * @var UploadedFile $file1
                 */
                $note->setFichier($note->getFichier());

            }else{
                /**
                 * @var UploadedFile $file1
                 */
                $note->setFichier(null);
                $em->persist($note);
                $em->flush();
                $file1 = $note->getFichier();
                $file1 = $form['fichier']->getData();
                $fileName1 = md5(uniqid()) . '.' . $file1->guessExtension();
                $file1->move(
                   $this->getParameter('pdf_directory'), $fileName1
                );
                //$fileName1 = $fileUploader->upload($file1);
                $note->setFichier($fileName1);
                $em->persist($note);
                $em->flush();

            }
         $note->setNote($request->get('not'));
         $note->setRemarque($request->get('rq'));
         $em->persist($note);
        $em->flush();
            $this->get('session')->getFlashBag()->add('Notice','Modification effectuée avec succés!');

        }
        return $this->render('BackOfficeBundle:Default:modifNote.html.twig',array('n'=>$note->getNote(),'form'=>$form->createView(),'ens'=>$ens,'nota'=>$note,'cl'=>$class));
    }

    public function suppAbsAction($id){

        $em = $this->getDoctrine()->getManager();
        $abs = $em->getRepository('BackOfficeBundle:Absence')->find($id);
        $eleve = $em->getRepository('BackOfficeBundle:Eleve')->find($abs->getIdEleve()->getId());
        $em->remove($abs);
        $em->flush();

        return $this->redirectToRoute('_afficheAbs',['id'=>$eleve->getIdEcole()->getId()]);
    }

    public function afficheAbsAction(Request $request,$id){

        $em = $this->getDoctrine()->getManager();
        $abs = null;
        $mat = null;

        $classe = $em->getRepository('BackOfficeBundle:Classe')->allOrdred();

        if (isset($_POST['valider'])){
            $class = $em->getRepository('BackOfficeBundle:Classe')->find($request->get('classe'));
            $mat = $em->getRepository('BackOfficeBundle:Matiere')->find($request->get('matiere'));
            if(!empty($request->get('date'))){
            $abs = $em->getRepository('BackOfficeBundle:Absence')->getAbs($class->getId(),$mat->getId(),$request->get('date'));
            if ($abs != null){
                $this->render('BackOfficeBundle:Default:afficheAbs.html.twig',array('id'=>$id,'abs'=>$abs,'cl'=>$classe,'cc'=>$request->get('classe')));
            }}else{
                $abs = $em->getRepository('BackOfficeBundle:Absence')->getAbse($class->getId(),$mat->getId());
                if ($abs != null){
                    $this->render('BackOfficeBundle:Default:afficheAbs.html.twig',array('id'=>$id,'abs'=>$abs,'cl'=>$classe,'cc'=>$request->get('classe')));
                }
            }
        }

        return $this->render('BackOfficeBundle:Default:afficheAbs.html.twig',array('id'=>$id,'abs'=>$abs,'cl'=>$classe,'cc'=>$request->get('classe')));
    }

    public function afficheNoteAction(Request $request,$id){

        $em = $this->getDoctrine()->getManager();
        $note = null;
        $mat = null;

        $ens = $em->getRepository('BackOfficeBundle:Enseignant')->find($id);
        $classe = $em->getRepository('BackOfficeBundle:EnsMatClasse')->findBy(array('ensId'=>$ens));
        if(isset($_POST['valider'])){
            $class = $em->getRepository('BackOfficeBundle:Classe')->find($request->get('classe'));
            $mat = $em->getRepository('BackOfficeBundle:Matiere')->find($request->get('matiere'));
            $note = $em->getRepository('BackOfficeBundle:Note')->findBy(array('idClasse'=>$class,'idMat'=>$mat));
            if($note != null){
            $this->render('BackOfficeBundle:Default:afficheNote.html.twig',array('note'=>$note,'ens'=>$ens,'cl'=>$classe,'cc'=>$request->get('classe'),'mat'=>$mat));
        }else{$this->get('session')->getFlashBag()->add('Erreur','Aucun résultat trouvé!');}
        }

        return $this->render('BackOfficeBundle:Default:afficheNote.html.twig',array('note'=>$note,'ens'=>$ens,'cl'=>$classe,'cc'=>$request->get('classe'),'mat'=>$mat));
    }

    public function ajoutAbsAction(Request $request,$id){

        $em = $this->getDoctrine()->getManager();
        $classe = $em->getRepository('BackOfficeBundle:Classe')->allOrdred();
        $emc = null;
        $elv = null;
        $m = null;
        $c = null;


        if (isset($_POST['valider']) ){
            $emc = $em->getRepository('BackOfficeBundle:EnsMatClasse')->findBy(array('classeId'=>$request->get('classe')));
            $elv = $em->getRepository('BackOfficeBundle:Eleve')->findBy(array('classe'=>$request->get('classe')));
            $m = $em->getRepository('BackOfficeBundle:Matiere')->find($request->get('matiere'));
            if ($emc != null && $elv != null){
                $this->render('BackOfficeBundle:Default:ajoutAbs.html.twig',array('id'=>$id,'cl'=>$classe,'emc'=>$emc,'elv'=>$elv,'ccc'=>$request->get('classe'),'m'=>$m));
            }
        }
        elseif (isset($_POST['enreg'])){
            $elvv = $em->getRepository('BackOfficeBundle:Eleve')->findBy(array('classe'=>$request->get('cl')));
            $m = $em->getRepository('BackOfficeBundle:Matiere')->find($request->get('mt'));
            foreach ($elvv as $e){
               // echo'ffff'.$request->get('abs'.$e->getId());
                if ($request->get('abs'.$e->getId()) == 'oui'){
                    $a = new Absence();
                    $a->setIdEleve($e);
                    $a->setIdMat($m);
                    $a->setRemarque($request->get('rq'.$e->getId()));
                    $a->setDate(new \DateTime($request->get('date')));
                    $a->setHeure(new \DateTime($request->get('heure')));
                    $a->setClasse($e->getClasse());
                    $em->persist($a);
                    $em->flush();
                    $this->get('session')->getFlashBag()->add('Notice','Enregistrement effectué avec succés!');
                }
            }

        }

        return $this->render('BackOfficeBundle:Default:ajoutAbs.html.twig',array('id'=>$id,'cl'=>$classe,'emc'=>$emc,'elv'=>$elv,'ccc'=>$request->get('classe'),'m'=>$m));

    }

    public function ajoutNoteAction(Request $request,$id){

        $em = $this->getDoctrine()->getManager();
        $emc = null;
        $elv = null;
        $m = null;
        $ens = $em->getRepository('BackOfficeBundle:Enseignant')->find($id);
        $form = $this->createForm(NoteType::class);
        $form->handleRequest($request);

        $fileUploader = new FileUploader('uploads/pdf');

        $classe = $em->getRepository('BackOfficeBundle:EnsMatClasse')->findBy(array('ensId'=>$ens));

        if (isset($_POST['valider']) ){

            $emc = $em->getRepository('BackOfficeBundle:EnsMatClasse')->findBy(array('classeId'=>$request->get('classe')));
            $elv = $em->getRepository('BackOfficeBundle:Eleve')->findBy(array('classe'=>$request->get('classe')));
            $m = $em->getRepository('BackOfficeBundle:Matiere')->find($request->get('matiere'));
            if ($emc != null && $elv != null){
                $this->render('BackOfficeBundle:Default:ajoutNote.html.twig',array('cl'=>$classe,'emc'=>$emc,'elv'=>$elv,'cc'=>$request->get('classe'),'form'=>$form->createView(),'ty'=>$request->get('type'),'at'=>$request->get('autre'),'m'=>$m,'ens'=>$ens));
            }
        }
        elseif (isset($_POST['enreg'])&& $form->isSubmitted()){
            $elvv = $em->getRepository('BackOfficeBundle:Eleve')->findBy(array('classe'=>$request->get('cl')));
            $c = $em->getRepository('BackOfficeBundle:Classe')->find($request->get('cl'));
            $m = $em->getRepository('BackOfficeBundle:Matiere')->find($request->get('mt'));

            /**
             * @var UploadedFile $file
             */
            $file = $m->getFichier();
            $file = $form['fichier']->getData();
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();
            $fileName = $fileUploader->upload($file);
            $m->setFichier($fileName);
            $em->persist($m);
            $em->flush();
            foreach ($elvv as $e){

                $note = new Note();
                $note->setIdElv($e);
                $note->setIdClasse($c);
                $note->setIdMat($m);
                $note->setNote($request->get($e->getId()));
                $note->setRemarque($request->get('rq'.$e->getId()));
                $note->setDate(new \DateTime('now'));
                if (!empty($request->files->get('fl'.$e->getId()))){

                    /**
                     * @var UploadedFile $file1
                     */
                    $file1= $note->getFichier();
                    $file1 = $request->files->get('fl'.$e->getId());
                    $fileName1 = md5(uniqid()) . '.' . $file1->guessExtension();
                    $fileName1 = $fileUploader->upload($file1);
                    $note->setFichier($fileName1);
                }

                if ($request->get('1') == 'Autre'){
                    $note->setType($request->get('2'));

                }else{
                    $note->setType($request->get('1'));
                }

                $em->persist($note);
                $em->flush();
            }
            $this->get('session')->getFlashBag()->add('Notice','Enregistrement effectué avec succés!');


        }

        return $this->render('BackOfficeBundle:Default:ajoutNote.html.twig',array('cl'=>$classe,'ens'=>$ens,'emc'=>$emc,'elv'=>$elv,'cc'=>$request->get('classe'),'form'=>$form->createView(),'ty'=>$request->get('type'),'at'=>$request->get('autre'),'m'=>$m));
    }


    public function docElvAction(Request $request,$id){

        $em = $this->getDoctrine()->getManager();
        $elv = $em->getRepository('BackOfficeBundle:Eleve')->find($id);
        $doc = $em->getRepository('BackOfficeBundle:Document')->tri($elv->getClasse()->getId());
        /**
         * @var $paginator \knp\Component\Pager\Paginator
         */
        $paginator = $this->get('knp_paginator');
        $result = $paginator->paginate($doc,
            $request->query->getInt('page',1),
            $request->query->getInt('limit',10));
        return $this->render('BackOfficeBundle:Default:docElv.html.twig',array('doc'=>$result,'elv'=>$elv));
    }

    public function afficheDocAction(Request $request,$id){
        $em = $this->getDoctrine()->getManager();
        $ens = $em->getRepository('BackOfficeBundle:Enseignant')->find($id);
        $doc = $em->getRepository('BackOfficeBundle:Document')->triDate($ens->getId());

        /**
         * @var $paginator \knp\Component\Pager\Paginator
         */
        $paginator = $this->get('knp_paginator');
        $result = $paginator->paginate($doc,
            $request->query->getInt('page',1),
            $request->query->getInt('limit',10));
        return $this->render('BackOfficeBundle:Default:afficheDoc.html.twig',array('doc'=>$result,'ens'=>$ens));
    }

    public function detDocAction($id,$idE){
        $em = $this->getDoctrine()->getManager();
        $elv = $em->getRepository('BackOfficeBundle:Eleve')->find($idE);
        $doc = $em->getRepository('BackOfficeBundle:Document')->find($id);
        $classe = $em->getRepository('BackOfficeBundle:EnsMatClasse')->findOneBy(array('matId'=>$doc->getIdMat()));
        return $this->render('BackOfficeBundle:Default:detDoc.html.twig',array('elv'=>$elv,'d'=>$doc,'c'=>$classe));
    }

    public function detDocEnsAction($id,$idE){
        $em = $this->getDoctrine()->getManager();
        $ens = $em->getRepository('BackOfficeBundle:Enseignant')->find($idE);
        $doc = $em->getRepository('BackOfficeBundle:Document')->find($id);
        $classe = $em->getRepository('BackOfficeBundle:EnsMatClasse')->findOneBy(array('matId'=>$doc->getIdMat()));
        return $this->render('BackOfficeBundle:Default:detDocEns.html.twig',array('ens'=>$ens,'d'=>$doc,'c'=>$classe));
    }

    public function suppDocAction($id,$idE){
        $em = $this->getDoctrine()->getManager();
        $doc = $em->getRepository('BackOfficeBundle:Document')->find($id);
        $em->remove($doc);
        $em->flush();
        return $this->redirectToRoute('_afficheDoc',['id'=>$idE]);
    }


    public function ajoutDocAction(Request $request,$id){

        $em = $this->getDoctrine()->getManager();
        $doc = new Document();

        $ens = $em->getRepository('BackOfficeBundle:Enseignant')->find($id);
        $classe = $em->getRepository('BackOfficeBundle:EnsMatClasse')->findBy(array('ensId'=>$ens));
        $form = $this->createForm(DocumentType::class);
        $form->handleRequest($request);
        $fileUploader = new FileUploader('uploads/pdf');

        if (isset($_POST['valider']) && $form->isSubmitted() ){
            $ens = $em->getRepository('BackOfficeBundle:Enseignant')->find(1);
            $mat = $em->getRepository('BackOfficeBundle:Matiere')->find($request->get('matiere'));
            $class = $em->getRepository('BackOfficeBundle:Classe')->find($request->get('classe'));
            $d = $em->getRepository('BackOfficeBundle:Document')->findBy(array('nom'=>$request->get('nom'),'type'=>$form['type']->getData(),'idMat'=>$mat,'idClasse'=>$class,'idEns'=>$ens));
            if ($d != null){
                $this->get('session')->getFlashBag()->add('Erreur','Document existant, Veuillez vérifier les données saisies!');
            }else{
            /**
             * @var UploadedFile $file
             */
            $file = $doc->getFichier();
            $file = $form['fichier']->getData();
            $fileName = $fileUploader->upload($file);
           /* $fileName = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move(
                $this->getParameter('pdf_directory'), $fileName
            );*/
            $doc->setFichier($fileName);
            $doc->setNom($request->get('nom'));
            $doc->setType($form['type']->getData());
            $doc->setDate(new \DateTime('now'));
            $mat = $em->getRepository('BackOfficeBundle:Matiere')->find($request->get('matiere'));
            $class = $em->getRepository('BackOfficeBundle:Classe')->find($request->get('classe'));
            $ens = $em->getRepository('BackOfficeBundle:Enseignant')->find(1);
            $doc->setIdMat($mat);
            $doc->setIdClasse($class);
            $doc->setIdEns($ens);
            $doc->setDescription($request->get('desc'));
            $em->persist($doc);
            $em->flush();
            $this->get('session')->getFlashBag()->add('Notice','Ajout effectué avec succés!');

        }}

        return $this->render('BackOfficeBundle:Default:ajoutDoc.html.twig',array('ens'=>$ens,'cl'=>$classe,'form'=>$form->createView()));
    }

    public function ajoutEnsAction(Request $request,$id){

        $em = $this->getDoctrine()->getManager();
        $ens = new Enseignant();
        $form = $this->createForm(EnseignantType::class,$ens);
        $form->handleRequest($request);

        if ($form->isSubmitted() ){
            $emat = $em->getRepository('BackOfficeBundle:Enseignant')->findOneBy(array('matricule'=>$form['matricule']->getData()));
            $e = $em->getRepository('BackOfficeBundle:Enseignant')->findOneBy(array('cin'=>$form['cin']->getData()));

            if(!empty($form['photo']->getData())){
                /**
                 * @var UploadedFile $file
                 */
                $file = $form['photo']->getData();
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                $file->move(
                    $this->getParameter('image_directory'), $fileName
                );
                $ens->setPhoto($fileName);
            }
            if(!empty($form['fichier']->getData())){
                /**
                 * @var UploadedFile $file
                 */
                $file = $form['fichier']->getData();
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                $file->move(
                    $this->getParameter('pdf_directory'), $fileName
                );
                $ens->setFichier($fileName);
            }
            if ( strlen($form['telephone']->getData())!=8  || !preg_match("/[20|21|22|23|24|25|26|27|28|29|30|31|32|33|40|41|42|43|50|51|52|53|54|55|56|57|58|59|70|71|72|73|77|78|79|90|91|92|93|94|95|96|97|98|99][0-9]{7}$/",$form['telephone']->getData()))
            {  $this->get('session')->getFlashBag()->add('Erreur','N° téléphone invalide!');
            }elseif (strlen($form['cin']->getData())!=8 )
            { $this->get('session')->getFlashBag()->add('Erreur','N° cin invalide!');}
            elseif ($e != null)
            {$this->get('session')->getFlashBag()->add('Erreur','N° cin invalide ou enseignant existant!');}
            elseif ($emat != null)
            {$this->get('session')->getFlashBag()->add('Erreur','Matricule invalide!');}
            else{
               $ens->setNom($form['nom']->getData());
               $ens->setPrenom($form['prenom']->getData());
               $ens->setDateNaissance(new \DateTime($request->get('dateNaissance')));
               $ens->setDateEmbauche(new \DateTime($request->get('dateEmbauche')));
               $ens->setMatricule($form['matricule']->getData());
               $ens->setSpecialite($form['specialite']->getData());
               $ens->setSexe($form['sexe']->getData());
               $ens->setAdresse($form['adresse']->getData());
               $ens->setEmail($form['email']->getData());
               $ens->setTelephone($form['telephone']->getData());
               $ens->setLogin($form['cin']->getData());
                $converter = new Encryption();
                $encoded = $converter->encode($form['cin']->getData());
               $ens->setMotDePasse($encoded);
               $ens->setCin($form['cin']->getData());
               $ens->setAnnuler(false);
               $ecole = $em->getRepository('BackOfficeBundle:Ecole')->find($id);
               $ens->setIdEcole($ecole);

            $em->persist($ens);
            $em->flush();
           $this->get('session')->getFlashBag()->add('Notice','Ajout effectué avec succés!');

           return $this->redirectToRoute('_ajoutEns',['id'=>$id]);}

        }

      return $this->render('BackOfficeBundle:Default:ajoutEns.html.twig',array('form'=>$form->createView(),'id'=>$id));
    }

    public function ajoutClasseAction(Request $request,$id){
           $em = $this->getDoctrine()->getManager();
           $classe = new Classe();
           $fileUploader = new FileUploader('uploads/pdf');
           $form = $this->createForm(NoteType::class);
           $form->handleRequest($request);

       if ((isset($_POST['valider']) && $form->isSubmitted()) || $request->isXmlHttpRequest()  ) {
           $pos = strpos($request->get('scolaire'), "/");
           $cl = $em->getRepository('BackOfficeBundle:Classe')->findOneBy(array('nom'=>$request->get('nom'),'niveau'=>$request->get('classe'),'anneeScolaire'=>$request->get('scolaire')));
           if ($cl != null) {
               $this->get('session')->getFlashBag()->add('Erreur', 'Classe existante, Veuillez vérifier les données saisies !');

           }else{
           if ($pos != 4) {
               $this->get('session')->getFlashBag()->add('Erreur', 'Veuillez vérifier l\'année scolaire saisie!');
           } else {

               /**
                * @var UploadedFile $file
                */
               $file = $classe->getFichier();
               $file = $form['fichier']->getData();
               $fileName = $fileUploader->upload($file);
               $classe->setFichier($fileName);

               /**
                * @var UploadedFile $file1
                */
               $file1 = $classe->getPlanning();
               $file1 = $request->files->get('planning');
               $fileName1 = $fileUploader->upload($file1);
               $classe->setPlanning($fileName1);

               $classe->setNom($request->get('nom'));
               $classe->setAnneeScolaire($request->get('scolaire'));
               $classe->setNiveau($request->get('classe'));
               $classe->setAnnuler(false);
               $ec = $em->getRepository('BackOfficeBundle:Ecole')->find($id);
               $classe->setIdEcole($ec);

               $em->persist($classe);
               $em->flush();

               $emc = new EnsMatClasse();
               $emc->setClasseId($classe);
               $ens = $em->getRepository('BackOfficeBundle:Enseignant')->find($request->request->get('enseignant'));
               $emc->setEnsId($ens);
               $mat = $em->getRepository('BackOfficeBundle:Matiere')->find($request->request->get('matiere'));
               $emc->setMatId($mat);

              $em->persist($emc);
               $em->flush();
                   $ajout = true;
                   $bool = true;
                   $i = 2;
                   while ($bool == true && $request->request->get('matiere' . $i) != null ) {
                       if ($request->request->get('matiere' . $i) != null) {
                           $emc = $em->getRepository('BackOfficeBundle:EnsMatClasse')->findBy(array('classeId'=>$classe->getId(),'ensId'=>$request->request->get('enseignant'.$i),'matId'=>$request->request->get('matiere'.$i)));
                           if ($emc != null )
                           { $this->get('session')->getFlashBag()->add('Erreur', 'Veuillez vérifier les champs du tableau!');
                           $ajout = false;}
                           else
                           {
                               $emc1 = new EnsMatClasse();
                               $emc1->setClasseId($classe);
                               $mat1 = $em->getRepository('BackOfficeBundle:Matiere')->find($request->request->get('matiere'.$i));
                               $emc1->setMatId($mat1);
                               $ens1 = $em->getRepository('BackOfficeBundle:Enseignant')->find($request->request->get('enseignant'.$i));
                               $emc1->setEnsId($ens1);
                               $em->persist($emc1);
                               $em->flush();
                           }
                       } else {
                           $bool = false;}
                       $i++;

                   }
                   if ($ajout == true)
                   {
                       $this->get('session')->getFlashBag()->add('Notice', 'Ajout effectué avec succés!');
                   }

       }}}
        return $this->render('BackOfficeBundle:Default:ajoutClasse.html.twig',array('id'=>$id,'form'=>$form->createView()));
     }



     public function ajaxClasseAction(Request $request){
         $em = $this->getDoctrine()->getManager();

         if ($request->isXmlHttpRequest()){
          $input = $request->request->get('input');
             $class = $em->getRepository('BackOfficeBundle:Classe')->findOneBy(array('niveau'=>$input));
             if ($class != null){
                 $mat = $em->getRepository('BackOfficeBundle:Matiere')->findByNiveau($input);
                 $ser = new Serializer(array(new ObjectNormalizer()));
                 $s = $ser->normalize($mat);
                 return new JsonResponse($s);
             }
         }

         return $this->render('BackOfficeBundle:Default:ajoutClasse.html.twig',array());
     }

     public function ajaxConvAction(Request $request){

         $em = $this->getDoctrine()->getManager();

         if ($request->isXmlHttpRequest()){
             $input= $request->get('input');
             $class = $em->getRepository('BackOfficeBundle:Classe')->find($input);
             if ($class != null){
                 $eleve = $em->getRepository('BackOfficeBundle:Eleve')->findBy(array('classe'=>$class));
                 $ser = new Serializer(array(new ObjectNormalizer()));
                 $s = $ser->normalize($eleve);
                 return new JsonResponse($s);

             }
         }
         return $this->render('BackOfficeBundle:Default:convParent.html.twig');
     }

     public function ajaxDocAction(Request $request){
         $em = $this->getDoctrine()->getManager();
         if ($request->isXmlHttpRequest()){
             $input = $request->request->get('input');

             $emc = $em->getRepository('BackOfficeBundle:EnsMatClasse')->findBy(array('classeId'=>$input));

                 $ser = new Serializer(array(new ObjectNormalizer()));
                 $s = $ser->normalize($emc);
                 return new JsonResponse($s);

         }
         return $this->render('BackOfficeBundle:Default:ajoutDoc.html.twig',array());
     }

    public function affAnClasseAction(Request $request){
      $em = $this->getDoctrine()->getManager();

      if($request->isXmlHttpRequest()){
          $input = $request->request->get('input');
          $class = $em->getRepository('BackOfficeBundle:Classe')->anneeOrd($input);
          if ($class != null){
              $ser = new Serializer(array(new ObjectNormalizer()));
              $s =$ser->normalize($class);
              return new JsonResponse($s);
          }
      }
      return $this->render('BackOfficeBundle:Default:afficheClass.html.twig',array());
    }

     public function ajaxEnsClasseAction(Request $request){
         $em = $this->getDoctrine()->getManager();

         if ($request->isXmlHttpRequest()){
             $ens  = $em->getRepository('BackOfficeBundle:Enseignant')->findAll();
                 $ser = new Serializer(array(new ObjectNormalizer()));
                 $s = $ser->normalize($ens);
                 return new JsonResponse($s);

         }

         return $this->render('BackOfficeBundle:Default:ajoutClasse.html.twig',array());
     }

    public function modifClasse2Action(Request $request, $id,$idE){

        $em = $this->getDoctrine()->getManager();
        $classe = $em->getRepository('BackOfficeBundle:Classe')->find($id);
        $emc = $em->getRepository('BackOfficeBundle:EnsMatClasse')->findBy(array('classeId'=>$classe->getId()));
        $fileUploader = new FileUploader('uploads/pdf');
        $form = $this->createForm(NoteType::class);
        $form->handleRequest($request);

        if(isset($_POST['valider']) && $form->isSubmitted()){

            $pos = strpos($request->get('scolaire'), "/");
            if ($pos != 4) {
                $this->get('session')->getFlashBag()->add('Erreur', 'Veuillez vérifier l\'année scolaire saisie!');
            } else {
                if ($form['fichier']->getData() == null){
                    $classe->setFichier($classe->getFichier());
                }else{
                    /**
                     * @var UploadedFile $file
                     */
                    $file = $classe->getFichier();
                    $file = $form['fichier']->getData();
                    $fileName = $fileUploader->upload($file);
                    $classe->setFichier($fileName);
                }

                if ($request->files->get('planning') == null){
                    /**
                     * @var UploadedFile $file
                     */
                    $classe->setPlanning($classe->getPlanning());
                }else{
                    /**
                     * @var UploadedFile $file
                     */
                    $classe->setPlanning(null);
                    $em->persist($classe);
                    $em->flush();
                    $file = $request->files->get('planning');
                    $fileName = $fileUploader->upload($file);
                    $classe->setPlanning($fileName);
                }
                $classe->setNom($request->get('nom'));
                $classe->setAnneeScolaire($request->get('scolaire'));
                $classe->setNiveau($request->get('classe'));

                $em->persist($classe);
                $em->flush();

                $this->get('session')->getFlashBag()->add('Notice', 'Modification effectuée avec succés!');


            }}
        return $this->render('BackOfficeBundle:Default:modifClasse2.html.twig',array('id'=>$idE,'cl'=>$classe,'emc'=>$emc,'e'=>$classe,'form'=>$form->createView()));
    }

    public function modLigneAction(Request $request,$id,$idE){

        $em = $this->getDoctrine()->getManager();
        $emc = $em->getRepository('BackOfficeBundle:EnsMatClasse')->find($id);
        $mat = $em->getRepository('BackOfficeBundle:Matiere')->findBy(array('niveau'=>$emc->getClasseId()->getNiveau()));
        $ens = $em->getRepository('BackOfficeBundle:Enseignant')->findAll();
        if (isset($_POST['valider'])){
          $m = $em->getRepository('BackOfficeBundle:Matiere')->find($request->get('mat'));
          $emc->setMatId($m);
          $e = $em->getRepository('BackOfficeBundle:Enseignant')->find($request->get('ens'));
          $emc->setEnsId($e);
          $em->persist($emc);
          $em->flush();
            return $this->redirectToRoute('_modifClasse',['id'=>$emc->getClasseId()->getId(),'idE'=>$idE]);
        }

        return $this->render('BackOfficeBundle:Default:modLigne.html.twig',array('id'=>$idE,'emc'=>$emc,'mat'=>$mat,'ens'=>$ens));
    }
    public function modLigne2Action(Request $request,$id,$idE){

        $em = $this->getDoctrine()->getManager();
        $emc = $em->getRepository('BackOfficeBundle:EnsMatClasse')->find($id);
        $mat = $em->getRepository('BackOfficeBundle:Matiere')->findBy(array('niveau'=>$emc->getClasseId()->getNiveau()));
        $ens = $em->getRepository('BackOfficeBundle:Enseignant')->findAll();
        if (isset($_POST['valider'])){
            $m = $em->getRepository('BackOfficeBundle:Matiere')->find($request->get('mat'));
            $emc->setMatId($m);
            $e = $em->getRepository('BackOfficeBundle:Enseignant')->find($request->get('ens'));
            $emc->setEnsId($e);
            $em->persist($emc);
            $em->flush();
            return $this->redirectToRoute('_modifClasse2',['id'=>$emc->getClasseId()->getId(),'idE'=>$idE]);
        }

        return $this->render('BackOfficeBundle:Default:modLigne2.html.twig',array('id'=>$idE,'emc'=>$emc,'mat'=>$mat,'ens'=>$ens));
    }

    public function suppLigneAction($id){

        $em = $this->getDoctrine()->getManager();
        $emc = $em->getRepository('BackOfficeBundle:EnsMatClasse')->find($id);
        $idC = $emc->getClasseId()->getId();
        $em->remove($emc);
        $em->flush();

        return $this->redirectToRoute('_modifClasse',['id'=>$idC]);
    }
    public function suppLigne2Action($id){

        $em = $this->getDoctrine()->getManager();
        $emc = $em->getRepository('BackOfficeBundle:EnsMatClasse')->find($id);
        $idC = $emc->getClasseId()->getId();
        $em->remove($emc);
        $em->flush();

        return $this->redirectToRoute('_modifClasse2',['id'=>$idC]);
    }

    public function ajoutLigneAction(Request $request,$id,$idE){

        $em = $this->getDoctrine()->getManager();
        $classe = $em->getRepository('BackOfficeBundle:Classe')->find($id);
        $mat = $em->getRepository('BackOfficeBundle:Matiere')->findBy(array('niveau'=>$classe->getNiveau()));
        $ens = $em->getRepository('BackOfficeBundle:Enseignant')->findAll();
        if (isset($_POST['valider'])){
            $emc = new EnsMatClasse();
            $m = $em->getRepository('BackOfficeBundle:Matiere')->find($request->get('mat'));
            $emc->setClasseId($classe);
            $emc->setMatId($m);
            $e = $em->getRepository('BackOfficeBundle:Enseignant')->find($request->get('ens'));
            $emc->setEnsId($e);
            $em->persist($emc);
            $em->flush();
            return $this->redirectToRoute('_modifClasse',['id'=>$classe->getId(),'idE'=>$idE]);

        }

        return $this->render('BackOfficeBundle:Default:ajoutLigne.html.twig',array('id'=>$idE,'mat'=>$mat,'ens'=>$ens,'cl'=>$classe));
    }
    public function ajoutLigne2Action(Request $request,$id,$idE){

        $em = $this->getDoctrine()->getManager();
        $classe = $em->getRepository('BackOfficeBundle:Classe')->find($id);
        $mat = $em->getRepository('BackOfficeBundle:Matiere')->findBy(array('niveau'=>$classe->getNiveau()));
        $ens = $em->getRepository('BackOfficeBundle:Enseignant')->findAll();
        if (isset($_POST['valider'])){
            $emc = new EnsMatClasse();
            $m = $em->getRepository('BackOfficeBundle:Matiere')->find($request->get('mat'));
            $emc->setClasseId($classe);
            $emc->setMatId($m);
            $e = $em->getRepository('BackOfficeBundle:Enseignant')->find($request->get('ens'));
            $emc->setEnsId($e);
            $em->persist($emc);
            $em->flush();
            return $this->redirectToRoute('_modifClasse2',['id'=>$classe->getId(),'idE'=>$idE]);

        }

        return $this->render('BackOfficeBundle:Default:ajoutLigne2.html.twig',array('id'=>$idE,'mat'=>$mat,'ens'=>$ens,'cl'=>$classe));
    }
     public function modifClasseAction(Request $request, $id,$idE){

         $em = $this->getDoctrine()->getManager();
         $classe = $em->getRepository('BackOfficeBundle:Classe')->find($id);
         $emc = $em->getRepository('BackOfficeBundle:EnsMatClasse')->findBy(array('classeId'=>$classe->getId()));
         $fileUploader = new FileUploader('uploads/pdf');
         $form = $this->createForm(NoteType::class);
         $form->handleRequest($request);

         if(isset($_POST['valider']) && $form->isSubmitted()){

             $pos = strpos($request->get('scolaire'), "/");
             if ($pos != 4) {
                 $this->get('session')->getFlashBag()->add('Erreur', 'Veuillez vérifier l\'année scolaire saisie!');
             } else {
                 if ($form['fichier']->getData() == null){
                     $classe->setFichier($classe->getFichier());
                 }else{
                     /**
                      * @var UploadedFile $file
                      */
                     $file = $classe->getFichier();
                     $file = $form['fichier']->getData();
                     $fileName = $fileUploader->upload($file);
                     $classe->setFichier($fileName);
                 }

                 if ($request->files->get('planning') == null){
                     /**
                      * @var UploadedFile $file
                      */
                     $classe->setPlanning($classe->getPlanning());
                 }else{
                     /**
                      * @var UploadedFile $file
                      */
                     $classe->setPlanning(null);
                     $em->persist($classe);
                     $em->flush();
                     $file = $request->files->get('planning');
                     $fileName = $fileUploader->upload($file);
                     $classe->setPlanning($fileName);
                 }
                 $classe->setNom($request->get('nom'));
                 $classe->setAnneeScolaire($request->get('scolaire'));
                 $classe->setNiveau($request->get('classe'));

                 $em->persist($classe);
                 $em->flush();

                     $this->get('session')->getFlashBag()->add('Notice', 'Modification effectuée avec succés!');


             }}
         return $this->render('BackOfficeBundle:Default:modifClasse.html.twig',array('id'=>$id,'cl'=>$classe,'emc'=>$emc,'e'=>$classe,'form'=>$form->createView()));
     }

     public function ajoutMatiereAction(Request $request,$id){

         $matiere = new Matiere();
         $em = $this->getDoctrine()->getManager();
         $form = $this->createForm(MatiereType::class,$matiere);
         $form->handleRequest($request);

         if ($form->isSubmitted()){
             $m = $em->getRepository('BackOfficeBundle:Matiere')->findOneBy(array('nom'=>$form['nom']->getData(),'niveau'=>$request->get('classe')));
           if ($m != null){
               $this->get('session')->getFlashBag()->add('Erreur', 'Matière existante, Veuillez vérifier les données saisies!!');
           }else{
             $matiere->setNom($form['nom']->getData());
             $matiere->setCoefficient($form['coefficient']->getData());
             $matiere->setAnnuler(false);
             $matiere->setNiveau($request->get('classe'));
             $ec = $em->getRepository('BackOfficeBundle:Ecole')->find($id);
             $matiere->setIdEcole($ec);
             $em->persist($matiere);
             $em->flush();

             $this->get('session')->getFlashBag()->add('Notice','Ajout effectué avec succés!');

             return $this->redirectToRoute('_ajoutMatiere',['id'=>$id]);}}


         return $this->render('BackOfficeBundle:Default:ajoutMatiere.html.twig',array('id'=>$id,'form'=>$form->createView()));
     }

     public function ajoutEleveAction (Request $request,$id){

         $em = $this->getDoctrine()->getManager();
         $eleve = new Eleve();
         $form = $this->createForm(EleveType::class,$eleve);
         $form->handleRequest($request);
         $classes = $em->getRepository('BackOfficeBundle:Classe')->allOrdred();
         $ec = $em->getRepository('BackOfficeBundle:Ecole')->find($id);

         if ($form->isSubmitted()) {
            $e = $em->getRepository('BackOfficeBundle:Eleve')->findOneBy(array('numInscription'=>$form['numInscription']->getData()));
             /**
              * @var UploadedFile $file
              */
            $file = $form['photo']->getData();
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move(
                $this->getParameter('image_directory'), $fileName
            );
            $parent = $em->getRepository('BackOfficeBundle:ParentEleve')->findOneBy(array('cin' => $request->get('cin')));
            if ($parent == null) {
                $this->get('session')->getFlashBag()->add('Erreur', 'Cin invalide!');
            }elseif ($e != null)
            { $this->get('session')->getFlashBag()->add('Erreur', 'N°inscription invalide ou élève existant!');}
            else {
               $eleve->setNom($form['nom']->getData());
                $eleve->setPrenom($form['prenom']->getData());
                $eleve->setDateNaissance(new \DateTime($request->get('dateNaissance')));
                $eleve->setAdresse($form['adresse']->getData());
                $eleve->setNumInscription($form['numInscription']->getData());
                $eleve->setPhoto($fileName);
                $eleve->setSexe($form['sexe']->getData());
                $converter = new Encryption();
                $encoded = $converter->encode($form['numInscription']->getData());
                $eleve->setMotDePasse($encoded);
                $eleve->setLogin($form['numInscription']->getData());
                $clas = $em->getRepository('BackOfficeBundle:Classe')->find($request->get('classe'));
                $eleve->setClasse($clas);
                $eleve->setParent($parent);
                $eleve->setAnnuler(false);
                $eleve->setIdEcole($ec);

                $em->persist($eleve);
                $em->flush();
                $this->get('session')->getFlashBag()->add('Notice', 'Ajout effectué avec succés!');

                return $this->redirectToRoute('_ajoutEleve',['id'=>$id]);
            }


        }

        return $this->render('BackOfficeBundle:Default:ajoutEleve.html.twig',array('cc'=>$request->get('classe'),'cl'=>$classes,'id'=>$id,'form'=>$form->createView()));
    }


    public function ajoutParentAction(Request $request,$id){
        $parentElv = new ParentEleve();
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(ParentEleveType::class,$parentElv);
        $form->handleRequest($request);

        if ($form->isSubmitted()){
            $par = $em->getRepository('BackOfficeBundle:ParentEleve')->findOneBy(array('cin'=>$form['cin']->getData()));
            if(!empty($form['photo']->getData())){
            /**
             * @var UploadedFile $file
             */
            $file = $form['photo']->getData();
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move(
                $this->getParameter('image_directory'), $fileName
            );
                $parentElv->setPhoto($fileName);
            }
            if ( strlen($form['telephone']->getData())!=8  || !preg_match("/[20|21|22|23|24|25|26|27|28|29|30|31|32|33|40|41|42|43|50|51|52|53|54|55|56|57|58|59|70|71|72|73|77|78|79|90|91|92|93|94|95|96|97|98|99][0-9]{7}$/",$form['telephone']->getData()))
            {$this->get('session')->getFlashBag()->add('Erreur', 'N° téléphone invalide!');}
            elseif (strlen($form['cin']->getData())!=8 )
            {$this->get('session')->getFlashBag()->add('Erreur', 'N° cin invalide!');}
            elseif($par != null)
            {$this->get('session')->getFlashBag()->add('Erreur', 'N° cin invalide ou parent existant!');}
            else{
            $parentElv->setNom($form['nom']->getData());
            $parentElv->setPrenom($form['prenom']->getData());

            $parentElv->setDateNaissance(new \DateTime($request->get('dateNaissance')));
            $parentElv->setAdresse($form['adresse']->getData());

            $parentElv->setTelephone($form['telephone']->getData());
            $parentElv->setEmail($form['email']->getData());
            $parentElv->setCin($form['cin']->getData());
            $parentElv->setLogin($form['cin']->getData());
                $converter = new Encryption();
                $encoded = $converter->encode($form['cin']->getData());
            $parentElv->setMotDePasse($encoded);
            $parentElv->setAnnuler(false);
            $ec = $em->getRepository('BackOfficeBundle:Ecole')->find($id);
            $parentElv->setIdEcole($ec);

            $em->persist($parentElv);
            $em->flush();
            $this->get('session')->getFlashBag()->add('Notice', 'Ajout effectué avec succés!');

            return $this->redirectToRoute('_ajoutParent',['id'=>$id]);
        }}

        return $this->render('BackOfficeBundle:Default:ajoutParent.html.twig',array('id'=>$id,'form'=>$form->createView()));
    }




    public function afficheEnsAction(Request $request,$id){
        $em =$this->getDoctrine()->getManager();
        $enseignants = $em->getRepository('BackOfficeBundle:Enseignant')->alphaNum(false,$id);
        /**
         * @var $paginator \knp\Component\Pager\Paginator
         */
        $paginator = $this->get('knp_paginator');
        $result = $paginator->paginate($enseignants,
           $request->query->getInt('page',1),
            $request->query->getInt('limit',10));
        return $this->render('BackOfficeBundle:Default:afficheEns.html.twig',array('ens'=>$result,'id'=>$id));
    }

    public function afficheElvAction(Request $request,$id){
        $em =$this->getDoctrine()->getManager();
        $eleves = $em->getRepository('BackOfficeBundle:Eleve')->alphaNum(false,$id);
        /**
         * @var $paginator \knp\Component\Pager\Paginator
         */
        $paginator = $this->get('knp_paginator');
        $result = $paginator->paginate($eleves,
            $request->query->getInt('page',1),
            $request->query->getInt('limit',10));
        return $this->render('BackOfficeBundle:Default:afficheEleve.html.twig',array('id'=>$id,'elv'=>$result));
    }

    public function afficheParAction(Request $request,$id){
        $em =$this->getDoctrine()->getManager();
        $parents = $em->getRepository('BackOfficeBundle:ParentEleve')->alphaNum(false,$id);
        /**
         * @var $paginator \knp\Component\Pager\Paginator
         */
        $paginator = $this->get('knp_paginator');
        $result = $paginator->paginate($parents,
            $request->query->getInt('page',1),
            $request->query->getInt('limit',10));
        return $this->render('BackOfficeBundle:Default:affichePar.html.twig',array('id'=>$id,'par'=>$result));
    }

    public function afficheClassAction(Request $request,$id){
        $em =$this->getDoctrine()->getManager();
        $classes = $em->getRepository('BackOfficeBundle:Classe')->alphaNum(false,$id);
        $ann = $em->getRepository('BackOfficeBundle:Classe')->anneeOrder();

        /**
         * @var $paginator \knp\Component\Pager\Paginator
         */
        $paginator = $this->get('knp_paginator');
        $result = $paginator->paginate($classes,
            $request->query->getInt('page',1),
            $request->query->getInt('limit',10));
        return $this->render('BackOfficeBundle:Default:afficheClass.html.twig',array('id'=>$id,'cl'=>$result,'an'=>$ann));
    }



    public function afficheMatAction(Request $request,$id){
        $em =$this->getDoctrine()->getManager();
        $matieres = $em->getRepository('BackOfficeBundle:Matiere')->alphaNum(false,$id);
        /**
         * @var $paginator \knp\Component\Pager\Paginator
         */
        $paginator = $this->get('knp_paginator');
        $result = $paginator->paginate($matieres,
            $request->query->getInt('page',1),
            $request->query->getInt('limit',10));
        return $this->render('BackOfficeBundle:Default:afficheMat.html.twig',array('id'=>$id,'mat'=>$result));

    }

    public function affDetClasseAction($id,$idE){
        $em = $this->getDoctrine()->getManager();
        $classe = $em->getRepository('BackOfficeBundle:Classe')->find($id);
        $emc = $em->getRepository('BackOfficeBundle:EnsMatClasse')->findBy(array('classeId'=>$id));

        return $this->render('BackOfficeBundle:Default:affDetClasse.html.twig',array('id'=>$idE,'cl'=>$classe,'emc'=>$emc));
    }

    public function affDetEleveAction($id,$idE){
        $em =$this->getDoctrine()->getManager();
        $eleve = $em->getRepository('BackOfficeBundle:Eleve')->find($id);

        return $this->render('BackOfficeBundle:Default:affDetEleve.html.twig',array('id'=>$idE,'elv'=>$eleve));
    }

    public function modifDoc2Action(Request $request,$id,$idE){

        $em = $this->getDoctrine()->getManager();
        $doc = $em->getRepository('BackOfficeBundle:Document')->find($id);
        $classe = $em->getRepository('BackOfficeBundle:Classe')->allOrdred();
        $fileUploader = new FileUploader('uploads/pdf');
        $form = $this->createForm(ModifDocType::class);
        $form->handleRequest($request);
        $ens = $em->getRepository('BackOfficeBundle:Enseignant')->find($idE);

        if (isset($_POST['valider']) && $form->isSubmitted()){
            if ($form['fichier']->getData() == null){
                /**
                 * @var UploadedFile $file
                 */
                $doc->setFichier($doc->getFichier());
            }else{
                /**
                 * @var UploadedFile $file
                 */
                // $file = $doc->getFichier();
                $doc->setFichier(null);
                $em->persist($doc);
                $em->flush();
                $file = $form['fichier']->getData();
                $fileName = $fileUploader->upload($file);
                $doc->setFichier($fileName);
            }
            $doc->setType($request->get('type'));
            $mat = $em->getRepository('BackOfficeBundle:Matiere')->find($request->get('matiere'));
            $doc->setIdMat($mat);
            $clas = $em->getRepository('BackOfficeBundle:Classe')->find($request->get('classe'));
            $doc->setIdClasse($clas);
            $ens = $em->getRepository('BackOfficeBundle:Enseignant')->find(1);
            $doc->setIdEns($ens);
            $doc->setDescription($request->get('desc'));
            $doc->setDate(new \DateTime('now'));

            $em->persist($doc);
            $em->flush();
            $this->get('session')->getFlashBag()->add('Notice', 'Modification effectuée avec succés!');

        }
        return $this->render('BackOfficeBundle:Default:modifDoc2.html.twig',array('id'=>$idE,'ens'=>$ens,'doc'=>$doc,'cl'=>$classe,'form'=>$form->createView()));
    }

    public function modifDocAction(Request $request,$id,$idE){

        $em = $this->getDoctrine()->getManager();
        $ens = $em->getRepository('BackOfficeBundle:Enseignant')->find($idE);
        $doc = $em->getRepository('BackOfficeBundle:Document')->find($id);
        $classe = $em->getRepository('BackOfficeBundle:Classe')->allOrdred();
        $fileUploader = new FileUploader('uploads/pdf');
        $form = $this->createForm(ModifDocType::class);
        $form->handleRequest($request);

        if (isset($_POST['valider']) && $form->isSubmitted()){
        if ($form['fichier']->getData() == null){
            /**
             * @var UploadedFile $file
             */
           $doc->setFichier($doc->getFichier());
        }else{
            /**
             * @var UploadedFile $file
             */
           // $file = $doc->getFichier();
            $doc->setFichier(null);
            $em->persist($doc);
            $em->flush();
            $file = $form['fichier']->getData();
            $fileName = $fileUploader->upload($file);
            $doc->setFichier($fileName);
        }
        $doc->setType($request->get('type'));
        $mat = $em->getRepository('BackOfficeBundle:Matiere')->find($request->get('matiere'));
        $doc->setIdMat($mat);
        $clas = $em->getRepository('BackOfficeBundle:Classe')->find($request->get('classe'));
        $doc->setIdClasse($clas);
        $ens = $em->getRepository('BackOfficeBundle:Enseignant')->find(1);
        $doc->setIdEns($ens);
        $doc->setDescription($request->get('desc'));
        $doc->setDate(new \DateTime('now'));

        $em->persist($doc);
        $em->flush();
            $this->get('session')->getFlashBag()->add('Notice', 'Modification effectuée avec succés!');

        }
        return $this->render('BackOfficeBundle:Default:modifDoc.html.twig',array('id'=>$idE,'ens'=>$ens,'doc'=>$doc,'cl'=>$classe,'form'=>$form->createView()));
    }

    public function modifEleve2Action(Request $request,$id,$idE){

        $em = $this->getDoctrine()->getManager();
        $classes = $em->getRepository('BackOfficeBundle:Classe')->findAll();
        $eleve = $em->getRepository('BackOfficeBundle:Eleve')->find($id);

        $parent = $em->getRepository('BackOfficeBundle:ParentEleve')->findOneBy(array('cin' => $request->get('cin')));

        if (isset($_POST['valider']))
        { $res = $em->getRepository('BackOfficeBundle:Eleve')->rechercheNumInscri($id,$request->get('numInscription'));
            if ($parent == null)
            {$this->get('session')->getFlashBag()->add('Erreur', 'Cin invalide!');}
            elseif ( ($res !=null) &&($eleve->getNumInscription()!= $request->get('numInscription')) )
            {$this->get('session')->getFlashBag()->add('Erreur', 'N°inscription invalide!');}
            else{
                $eleve->setParent($parent);
                $eleve->setNom($request->get('nom'));
                $eleve->setPrenom($request->get('prenom'));
                $eleve->setAdresse($request->get('adresse'));
                $eleve->setNumInscription($request->get('numInscription'));
                $converter = new Encryption();
                $encoded = $converter->encode($request->get('numInscription'));
                $eleve->setMotDePasse($encoded);
                $class = $em->getRepository('BackOfficeBundle:Classe')->find($request->get('classe'));
                $eleve->setClasse($class);
                $eleve->setSexe($request->get('sexe'));
                $eleve->setDateNaissance(new \DateTime($request->get('dateNaissance')));

                $em->persist($eleve);
                $em->flush();
                $this->get('session')->getFlashBag()->add('Notice', 'Modification effectuée avec succés!');
            }
        }


        return $this->render('BackOfficeBundle:Default:modifEleve2.html.twig',array('id'=>$idE,'elv'=>$eleve,'cl'=>$classes));
    }

    public function modifEleveAction(Request $request,$id,$idE){

        $em = $this->getDoctrine()->getManager();
        $classes = $em->getRepository('BackOfficeBundle:Classe')->findAll();
        $eleve = $em->getRepository('BackOfficeBundle:Eleve')->find($id);

                $parent = $em->getRepository('BackOfficeBundle:ParentEleve')->findOneBy(array('cin' => $request->get('cin')));

                if (isset($_POST['valider']))
                { $res = $em->getRepository('BackOfficeBundle:Eleve')->rechercheNumInscri($id,$request->get('numInscription'));
                if ($parent == null)
                {$this->get('session')->getFlashBag()->add('Erreur', 'Cin invalide!');}
                elseif ( ($res !=null) &&($eleve->getNumInscription()!= $request->get('numInscription')) )
                {$this->get('session')->getFlashBag()->add('Erreur', 'N°inscription invalide!');}
                else{
            $eleve->setParent($parent);
            $eleve->setNom($request->get('nom'));
            $eleve->setPrenom($request->get('prenom'));
            $eleve->setAdresse($request->get('adresse'));
            $eleve->setNumInscription($request->get('numInscription'));
                    $converter = new Encryption();
                    $encoded = $converter->encode($request->get('numInscription'));
            $eleve->setMotDePasse($encoded);
            $class = $em->getRepository('BackOfficeBundle:Classe')->find($request->get('classe'));
            $eleve->setClasse($class);
            $eleve->setSexe($request->get('sexe'));
            $eleve->setDateNaissance(new \DateTime($request->get('dateNaissance')));

            $em->persist($eleve);
            $em->flush();
                $this->get('session')->getFlashBag()->add('Notice', 'Modification effectuée avec succés!');
                }
                }


        return $this->render('BackOfficeBundle:Default:modifEleve.html.twig',array('id'=>$idE,'elv'=>$eleve,'cl'=>$classes));
    }

    public function suppEleveAction(Request $request,$id,$idE){

        $em = $this->getDoctrine()->getManager();
        $eleve = $em->getRepository('BackOfficeBundle:Eleve')->find($id);
        $eleve->setAnnuler(true);
        $eleve->setDateAnnuler(new \DateTime('now'));
        $em->persist($eleve);
        $em->flush();
        $this->afficheElvAction($request,$idE);
        return $this->redirectToRoute('_afficheElv',['id'=>$idE]);
    }

    public function affDetEnsAction($id,$idE){
        $em = $this->getDoctrine()->getManager();
        $ens = $em->getRepository('BackOfficeBundle:Enseignant')->find($id);

        return $this->render('BackOfficeBundle:Default:affDetEns.html.twig',array('id'=>$idE,'e'=>$ens));
    }

    public function modifEns2Action(Request $request, $id,$idE){
        $em = $this->getDoctrine()->getManager();
        $ens = $em->getRepository('BackOfficeBundle:Enseignant')->find($id);
        $fileUploader = new FileUploader('uploads/pdf');

        if(isset($_POST['valider']))
        { $e = $em->getRepository('BackOfficeBundle:Enseignant')->rechercheCin($id,$request->get('cin'));
            $e1 = $em->getRepository('BackOfficeBundle:Enseignant')->rechercheMatricule($id,$request->get('matricule'));
            if ($e != null && $ens->getCin()!= $request->get('cin'))
            {$this->get('session')->getFlashBag()->add('Erreur', 'Cin invalide!');}
            elseif ($e1 != null && ($ens->getMatricule() != $request->get('matricule')))
            {$this->get('session')->getFlashBag()->add('Erreur', 'Matricule invalide!');}
            elseif ( strlen($request->get('telephone'))!=8  || !preg_match("/[20|21|22|23|24|25|26|27|28|29|30|31|32|33|40|41|42|43|50|51|52|53|54|55|56|57|58|59|70|71|72|73|77|78|79|90|91|92|93|94|95|96|97|98|99][0-9]{7}$/",$request->get('telephone')))
            {$this->get('session')->getFlashBag()->add('Erreur', 'N° téléphone invalide!');}
            elseif (strlen($request->get('cin'))!=8 )
            {$this->get('session')->getFlashBag()->add('Erreur', 'N° cin invalide!');}
            else{
                if ($request->files->get('fichier') == null){
                    /**
                     * @var UploadedFile $file
                     */
                    $ens->setFichier($ens->getFichier());
                }else{
                    /**
                     * @var UploadedFile $file
                     */
                    // $file = $doc->getFichier();
                    $ens->setFichier(null);
                    $em->persist($ens);
                    $em->flush();
                    $file = $request->files->get('fichier');
                    $fileName = $fileUploader->upload($file);
                    $ens->setFichier($fileName);
                }
                $ens->setNom($request->get('nom'));
                $ens->setPrenom($request->get('prenom'));
                $ens->setMatricule($request->get('matricule'));
                $ens->setDateNaissance(new \DateTime($request->get('dateNaissance')));
                $ens->setDateEmbauche(new \DateTime($request->get('dateEmbauche')));
                $ens->setSpecialite($request->get('specialite'));
                $ens->setSexe($request->get('sexe'));
                $ens->setAdresse($request->get('adresse'));
                $ens->setEmail($request->get('email'));
                $ens->setTelephone($request->get('telephone'));
                $ens->setCin($request->get('cin'));
                $ens->setLogin($request->get('cin'));
                $converter = new Encryption();
                $encoded = $converter->encode($request->get('cin'));
                $ens->setMotDePasse($encoded);
                $em->persist($ens);
                $em->flush();
                $this->get('session')->getFlashBag()->add('Notice', 'Modification effectuée avec succés!');}
        }
        return $this->render('BackOfficeBundle:Default:modifEns2.html.twig',array('id'=>$idE,'ens'=>$ens));
    }

    public function modifEnsAction(Request $request, $id,$idE){
        $em = $this->getDoctrine()->getManager();
        $ens = $em->getRepository('BackOfficeBundle:Enseignant')->find($id);
        $fileUploader = new FileUploader('uploads/pdf');

        if(isset($_POST['valider']))
        { $e = $em->getRepository('BackOfficeBundle:Enseignant')->rechercheCin($id,$request->get('cin'));
          $e1 = $em->getRepository('BackOfficeBundle:Enseignant')->rechercheMatricule($id,$request->get('matricule'));
        if ($e != null && $ens->getCin()!= $request->get('cin'))
            {$this->get('session')->getFlashBag()->add('Erreur', 'Cin invalide!');}
            elseif ($e1 != null && ($ens->getMatricule() != $request->get('matricule')))
            {$this->get('session')->getFlashBag()->add('Erreur', 'Matricule invalide!');}
            elseif ( strlen($request->get('telephone'))!=8  || !preg_match("/[20|21|22|23|24|25|26|27|28|29|30|31|32|33|40|41|42|43|50|51|52|53|54|55|56|57|58|59|70|71|72|73|77|78|79|90|91|92|93|94|95|96|97|98|99][0-9]{7}$/",$request->get('telephone')))
            {$this->get('session')->getFlashBag()->add('Erreur', 'N° téléphone invalide!');}
            elseif (strlen($request->get('cin'))!=8 )
            {$this->get('session')->getFlashBag()->add('Erreur', 'N° cin invalide!');}
            else{
                if ($request->files->get('fichier') == null){
                    /**
                     * @var UploadedFile $file
                     */
                    $ens->setFichier($ens->getFichier());
                }else{
                    /**
                     * @var UploadedFile $file
                     */
                    // $file = $doc->getFichier();
                    $ens->setFichier(null);
                    $em->persist($ens);
                    $em->flush();
                    $file = $request->files->get('fichier');
                    $fileName = $fileUploader->upload($file);
                    $ens->setFichier($fileName);
                }
            $ens->setNom($request->get('nom'));
            $ens->setPrenom($request->get('prenom'));
            $ens->setMatricule($request->get('matricule'));
            $ens->setDateNaissance(new \DateTime($request->get('dateNaissance')));
            $ens->setDateEmbauche(new \DateTime($request->get('dateEmbauche')));
            $ens->setSpecialite($request->get('specialite'));
            $ens->setSexe($request->get('sexe'));
            $ens->setAdresse($request->get('adresse'));
            $ens->setEmail($request->get('email'));
            $ens->setTelephone($request->get('telephone'));
            $ens->setCin($request->get('cin'));
            $ens->setLogin($request->get('cin'));
                $converter = new Encryption();
                $encoded = $converter->encode($request->get('cin'));
            $ens->setMotDePasse($encoded);
            $em->persist($ens);
            $em->flush();
            $this->get('session')->getFlashBag()->add('Notice', 'Modification effectuée avec succés!');}
        }
      return $this->render('BackOfficeBundle:Default:modifEns.html.twig',array('id'=>$idE,'ens'=>$ens));
    }

    public function suppEnsAction(Request $request,$id,$idE){

        $em = $this->getDoctrine()->getManager();
        $ens = $em->getRepository('BackOfficeBundle:Enseignant')->find($id);
        $ens->setAnnuler(true);
        $ens->setDateAnnuler(new \DateTime('now'));
        $em->persist($ens);
        $em->flush();
        $this->afficheEnsAction($request,$idE);
        return $this->redirectToRoute('_afficheEns',['id'=>$idE]);
    }

    public function affDetParentAction($id,$idE){
        $em = $this->getDoctrine()->getManager();
        $par = $em->getRepository('BackOfficeBundle:ParentEleve')->find($id);
         return $this->render('BackOfficeBundle:Default:affDetParent.html.twig',array('id'=>$idE,'e'=>$par));
    }

    public function modifParent2Action(Request $request, $id,$idE){
        $em = $this->getDoctrine()->getManager();
        $par= $em->getRepository('BackOfficeBundle:ParentEleve')->find($id);

        if(isset($_POST['valider']))
        {
            $p = $em->getRepository('BackOfficeBundle:ParentEleve')->rechercheCin($id,$request->get('cin'));
            if ($p != null && ($par->getCin()!= $request->get('cin')))
            {$this->get('session')->getFlashBag()->add('Erreur', 'Cin invalide!');}
            elseif ( strlen($request->get('telephone'))!=8  || !preg_match("/[20|21|22|23|24|25|26|27|28|29|30|31|32|33|40|41|42|43|50|51|52|53|54|55|56|57|58|59|70|71|72|73|77|78|79|90|91|92|93|94|95|96|97|98|99][0-9]{7}$/",$request->get('telephone')))
            {$this->get('session')->getFlashBag()->add('Erreur', 'N° téléphone invalide!');}
            elseif (strlen($request->get('cin'))!=8 )
            {$this->get('session')->getFlashBag()->add('Erreur', 'N° cin invalide!');}
            else{
                $par->setNom($request->get('nom'));
                $par->setPrenom($request->get('prenom'));
                $par->setCin($request->get('cin'));
                $par->setLogin($request->get('cin'));
                $converter = new Encryption();
                $encoded = $converter->encode($request->get('cin'));
                $par->setMotDePasse($encoded);
                $par->setAdresse($request->get('adresse'));
                $par->setEmail($request->get('email'));
                $par->setTelephone($request->get('telephone'));
                $par->setDateNaissance(new \DateTime($request->get('dateNaissance')));
                $em->persist($par);
                $em->flush();
                $this->get('session')->getFlashBag()->add('Notice', 'Modification effectuée avec succés!');}
        }

        return $this->render('BackOfficeBundle:Default:modifParent2.html.twig',array('id'=>$idE,'par'=>$par));

    }

    public function modifParentAction(Request $request, $id,$idE){
        $em = $this->getDoctrine()->getManager();
        $par= $em->getRepository('BackOfficeBundle:ParentEleve')->find($id);

        if(isset($_POST['valider']))
        {
            $p = $em->getRepository('BackOfficeBundle:ParentEleve')->rechercheCin($id,$request->get('cin'));
            if ($p != null && ($par->getCin()!= $request->get('cin')))
            {$this->get('session')->getFlashBag()->add('Erreur', 'Cin invalide!');}
            elseif ( strlen($request->get('telephone'))!=8  || !preg_match("/[20|21|22|23|24|25|26|27|28|29|30|31|32|33|40|41|42|43|50|51|52|53|54|55|56|57|58|59|70|71|72|73|77|78|79|90|91|92|93|94|95|96|97|98|99][0-9]{7}$/",$request->get('telephone')))
            {$this->get('session')->getFlashBag()->add('Erreur', 'N° téléphone invalide!');}
            elseif (strlen($request->get('cin'))!=8 )
            {$this->get('session')->getFlashBag()->add('Erreur', 'N° cin invalide!');}
            else{
            $par->setNom($request->get('nom'));
            $par->setPrenom($request->get('prenom'));
            $par->setCin($request->get('cin'));
            $par->setLogin($request->get('cin'));
                $converter = new Encryption();
                $encoded = $converter->encode($request->get('cin'));
            $par->setMotDePasse($encoded);
            $par->setAdresse($request->get('adresse'));
            $par->setEmail($request->get('email'));
            $par->setTelephone($request->get('telephone'));
            $par->setDateNaissance(new \DateTime($request->get('dateNaissance')));
            $em->persist($par);
            $em->flush();
            $this->get('session')->getFlashBag()->add('Notice', 'Modification effectuée avec succés!');}
        }

        return $this->render('BackOfficeBundle:Default:modifParent.html.twig',array('id'=>$idE,'par'=>$par));

    }

    public function suppParentAction(Request $request,$id,$idE){
        $em = $this->getDoctrine()->getManager();
        $par = $em->getRepository('BackOfficeBundle:ParentEleve')->find($id);
        $par->setAnnuler(true);
        $par->setDateAnnuler(new \DateTime('now'));
        $em->persist($par);
        $em->flush();
        $this->afficheParAction($request,$idE);
        return $this->redirectToRoute('_affichePar',['id'=>$idE]);
    }

    public function modifMatiere2Action(Request $request, $id,$idE)
    {

        $em = $this->getDoctrine()->getManager();
        $mat = $em->getRepository('BackOfficeBundle:Matiere')->find($id);


        if (isset($_POST['valider'])) {

            $mat->setNom($request->get('nom'));
            $mat->setCoefficient($request->get('coefficient'));
            $mat->setNiveau($request->get('niveau'));
            $em->persist($mat);
            $em->flush();
            $this->get('session')->getFlashBag()->add('Notice', 'Modification effectuée avec succés!');

        }

        return $this->render('BackOfficeBundle:Default:modifMatiere2.html.twig',array('id'=>$idE,'mat'=>$mat));
    }

    public function modifMatiereAction(Request $request, $id,$idE)
    {

        $em = $this->getDoctrine()->getManager();
        $mat = $em->getRepository('BackOfficeBundle:Matiere')->find($id);


        if (isset($_POST['valider'])) {

            $mat->setNom($request->get('nom'));
            $mat->setCoefficient($request->get('coefficient'));
            $mat->setNiveau($request->get('niveau'));
            $em->persist($mat);
            $em->flush();
            $this->get('session')->getFlashBag()->add('Notice', 'Modification effectuée avec succés!');

        }

        return $this->render('BackOfficeBundle:Default:modifMatiere.html.twig',array('id'=>$idE,'mat'=>$mat));
    }

    public function suppMatiereAction (Request $request,$id,$idE){
        $em = $this->getDoctrine()->getManager();
        $mat = $em->getRepository('BackOfficeBundle:Matiere')->find($id);
        $mat->setAnnuler(true);
        $mat->setDateAnnuler(new \DateTime('now'));
        $em->persist($mat);
        $em->flush();
        $this->afficheMatAction($request,$idE);
        return $this->redirectToRoute('_afficheMat',['id'=>$idE]);
    }

    public function suppClasseAction (Request $request,$id,$idE){
        $em = $this->getDoctrine()->getManager();
        $class = $em->getRepository('BackOfficeBundle:Classe')->find($id);
        $class->setAnnuler(true);
        $class->setDateAnnuler(new \DateTime('now'));
        $em->persist($class);
        $em->flush();
        $this->afficheClassAction($request,$idE);
        return $this->redirectToRoute('_afficheClass',['id'=>$idE]);
    }

    public function histEleveAction(Request $request,$id){
        $em = $this->getDoctrine()->getManager();
        $elev = $em->getRepository('BackOfficeBundle:Eleve')->alphaNum(true,$id);
        /**
         * @var $paginator \knp\Component\Pager\Paginator
         */
        $paginator = $this->get('knp_paginator');
        $result = $paginator->paginate($elev,
            $request->query->getInt('page',1),
            $request->query->getInt('limit',10));
        return $this->render('BackOfficeBundle:Default:histEleve.html.twig',array('id'=>$id,'elv'=>$result));
    }

    public function histClasseAction(Request $request,$id){
        $em = $this->getDoctrine()->getManager();
        $class = $em->getRepository('BackOfficeBundle:Classe')->alphaNum(true,$id);
        /**
         * @var $paginator \knp\Component\Pager\Paginator
         */
        $paginator = $this->get('knp_paginator');
        $result = $paginator->paginate($class,
            $request->query->getInt('page',1),
            $request->query->getInt('limit',10));
        return $this->render('BackOfficeBundle:Default:histClasse.html.twig',array('id'=>$id,'elv'=>$result));
    }

    public function histEnseignantAction(Request $request,$id){
        $em = $this->getDoctrine()->getManager();
        $ens = $em->getRepository('BackOfficeBundle:Enseignant')->alphaNum(true,$id);
        /**
         * @var $paginator \knp\Component\Pager\Paginator
         */
        $paginator = $this->get('knp_paginator');
        $result = $paginator->paginate($ens,
            $request->query->getInt('page',1),
            $request->query->getInt('limit',10));
        return $this->render('BackOfficeBundle:Default:histEnseignant.html.twig',array('elv'=>$result,'id'=>$id));
    }

    public function histMatiereAction(Request $request,$id){
        $em = $this->getDoctrine()->getManager();
        $mat = $em->getRepository('BackOfficeBundle:Matiere')->alphaNum(true,$id);
        /**
         * @var $paginator \knp\Component\Pager\Paginator
         */
        $paginator = $this->get('knp_paginator');
        $result = $paginator->paginate($mat,
            $request->query->getInt('page',1),
            $request->query->getInt('limit',10));
        return $this->render('BackOfficeBundle:Default:histMatiere.html.twig',array('elv'=>$result,'id'=>$id));
    }

    public function histParentAction(Request $request,$id){
        $em = $this->getDoctrine()->getManager();
        $par = $em->getRepository('BackOfficeBundle:ParentEleve')->alphaNum(true,$id);
        /**
         * @var $paginator \knp\Component\Pager\Paginator
         */
        $paginator = $this->get('knp_paginator');
        $result = $paginator->paginate($par,
            $request->query->getInt('page',1),
            $request->query->getInt('limit',10));
        return $this->render('BackOfficeBundle:Default:histParent.html.twig',array('elv'=>$result,'id'=>$id));
    }

    public function restaurerEleveAction(Request $request,$id,$idE){
        $em = $this->getDoctrine()->getManager();
        $elv = $em->getRepository('BackOfficeBundle:Eleve')->find($id);
        $elv->setAnnuler(false);
        $elv->setDateAnnuler(null);
        $em->persist($elv);
        $em->flush();
        $this->histEleveAction($request,$idE);
        return $this->redirectToRoute('_histEleve',['id'=>$idE]);
    }

    public function restaurerClasseAction(Request $request,$id,$idE){
        $em = $this->getDoctrine()->getManager();
        $class = $em->getRepository('BackOfficeBundle:Classe')->find($id);
        $class->setAnnuler(false);
        $class->setDateAnnuler(null);
        $em->persist($class);
        $em->flush();
        $this->histClasseAction($request,$idE);
        return $this->redirectToRoute('_histClasse',['id'=>$idE]);
    }

    public function restaurerEnseignantAction(Request $request,$id,$idE){
        $em = $this->getDoctrine()->getManager();
        $ens = $em->getRepository('BackOfficeBundle:Enseignant')->find($id);
        $ens->setAnnuler(false);
        $ens->setDateAnnuler(null);
        $em->persist($ens);
        $em->flush();
        $this->histEnseignantAction($request,$idE);
        return $this->redirectToRoute('_histEnseignant',['id'=>$idE]);
    }

    public function restaurerMatiereAction(Request $request,$id,$idE){
        $em = $this->getDoctrine()->getManager();
        $mat = $em->getRepository('BackOfficeBundle:Matiere')->find($id);
        $mat->setAnnuler(false);
        $mat->setDateAnnuler(null);
        $em->persist($mat);
        $em->flush();
        $this->histMatiereAction($request,$idE);
        return $this->redirectToRoute('_histMatiere',['id'=>$idE]);
    }

    public function restaurerParentAction(Request $request,$id,$idE){
        $em = $this->getDoctrine()->getManager();
        $par = $em->getRepository('BackOfficeBundle:ParentEleve')->find($id);
        $par->setAnnuler(false);
        $par->setDateAnnuler(null);
        $em->persist($par);
        $em->flush();
        $this->histParentAction($request,$idE);
        return $this->redirectToRoute('_histParent',['id'=>$idE]);
    }

    public function suppDefEleveAction($id,$idE){
        $em =$this->getDoctrine()->getManager();
        $elv = $em->getRepository('BackOfficeBundle:Eleve')->find($id);
        $em->remove($elv);
        $em->flush();
        return $this->redirectToRoute('_histEleve',['id'=>$idE]);
    }

    public function suppDefClasseAction($id,$idE){
        $em =$this->getDoctrine()->getManager();
        $class = $em->getRepository('BackOfficeBundle:Classe')->find($id);
       $emc = $em->getRepository('BackOfficeBundle:EnsMatClasse')->findBy(array('classeId'=>$class->getId()));
        foreach ($emc as $item) {
          $em->remove($item);
          $em->flush();
       }
        $em->remove($class);
        $em->flush();
        return $this->redirectToRoute('_histClasse',['id'=>$idE]);
    }

    public function suppDefEnseignantAction($id,$idE){
        $em =$this->getDoctrine()->getManager();
        $ens = $em->getRepository('BackOfficeBundle:Enseignant')->find($id);
        $em->remove($ens);
        $em->flush();
        return $this->redirectToRoute('_histEnseignant',['id'=>$idE]);
    }

    public function suppDefMatiereAction($id,$idE){
        $em =$this->getDoctrine()->getManager();
        $mat = $em->getRepository('BackOfficeBundle:Matiere')->find($id);
        $em->remove($mat);
        $em->flush();
        return $this->redirectToRoute('_histMatiere',['id'=>$idE]);
    }

    public function suppDefParentAction($id,$idE){
        $em =$this->getDoctrine()->getManager();
        $par = $em->getRepository('BackOfficeBundle:ParentEleve')->find($id);
        $em->remove($par);
        $em->flush();
        return $this->redirectToRoute('_histParent',['id'=>$idE]);
    }

   public function nomEnsAction(Request $request){
      if ($request->isXmlHttpRequest()){
          $search = $request->request->get('search');

          $em = $this->getDoctrine()->getManager();
          $ens = $em->getRepository('BackOfficeBundle:Enseignant')->nomP($search);
          $response =   array();
          if ($ens != null){
              foreach ($ens as $e){
                  $response[] = array("label"=>$e->getNom());
                  $response[] = array("label"=>$e->getPrenom());
              }
             return new JsonResponse($response);
          }
      }
      return $this->render('BackOfficeBundle:Default:rechercheEns.html.twig',array());
   }

   public function usernameEcoleAction(Request $request){

       if ($request->isXmlHttpRequest()){
           $search = $request->request->get('search');

           $em = $this->getDoctrine()->getManager();
           $ecole = $em->getRepository('BackOfficeBundle:Ecole')->usernameP($search);
           $response =   array();
           if ($ecole != null){
               foreach ($ecole as $e){
                   $response[] = array("label"=>$e->getAdresse());
               }
               return new JsonResponse($response);
           }
       }

       return $this->render('BackOfficebundle:Default:rechercheEcole.html.twig',array());
   }

   public function adresseEcoleAction(Request $request){

       if ($request->isXmlHttpRequest()){
           $search = $request->request->get('kaka');

           $em = $this->getDoctrine()->getManager();
           $ecole = $em->getRepository('BackOfficeBundle:Ecole')->khraa($search);
           $response =   array();
           if ($ecole != null){
               foreach ($ecole as $e){
                   $response[] = array("label"=>$e->getAdresse());
               }
               return new JsonResponse($response);
           }
       }

       return $this->render('BackOfficeBundle:Default:rechercheEcole.html.twig',array());
   }

   public function prenomEnsAction(Request $request){
       if ($request->isXmlHttpRequest()){
           $search = $request->request->get('search');

           $em = $this->getDoctrine()->getManager();
           $ens = $em->getRepository('BackOfficeBundle:Enseignant')->nomP($search);
           $response =   array();
           if ($ens != null){
               foreach ($ens as $e){
                   $response[] = array("label"=>$e->getPrenom());
                   $response[] = array("label"=>$e->getNom());
               }
               return new JsonResponse($response);
           }
       }
       return $this->render('BackOfficeBundle:Default:rechercheEns.html.twig',array());
   }


    public function rechercheConvAction(Request $request,$id){

        $em = $this->getDoctrine()->getManager();
        $conv = null;

        if(isset($_POST['valider'])){
            if(!empty($request->get('ens')) && !empty($request->get('elv'))){
                $conv = $em->getRepository('BackOfficeBundle:Convocation')->getEnsElv($id,$request->get('ens'),$request->get('elv'));
                $this->render('BackOfficeBundle:Default:rechercheConv.html.twig',array('id'=>$id,'conv'=>$conv,'ens'=>$request->get('ens'),'elv'=>$request->get('elv')));
            }elseif (empty($request->get('ens')) &&(!empty($request->get('elv')))){
                $conv = $em->getRepository('BackOfficeBundle:Convocation')->getEleve($id,$request->get('elv'));
               if ($conv != null) {
                   $this->render('BackOfficeBundle:Default:rechercheConv.html.twig', array('id' => $id, 'conv' => $conv, 'ens' => $request->get('ens'), 'elv' => $request->get('elv')));
               }else{$this->get('session')->getFlashBag()->add('Erreur', 'Aucun résultat!');}
               }elseif (!empty($request->get('ens')) &&(empty($request->get('elv')))){
                $conv = $em->getRepository('BackOfficeBundle:Convocation')->getEns($id,$request->get('ens'));
                if ($conv != null) {
                $this->render('BackOfficeBundle:Default:rechercheConv.html.twig',array('id'=>$id,'conv'=>$conv,'ens'=>$request->get('ens'),'elv'=>$request->get('elv')));
                }else{$this->get('session')->getFlashBag()->add('Erreur', 'Aucun résultat!');}
            }
        }

        return $this->render('BackOfficeBundle:Default:rechercheConv.html.twig',array('id'=>$id,'conv'=>$conv,'ens'=>$request->get('ens'),'elv'=>$request->get('elv')));
    }

   public function rechercheEnsAction(Request $request,$id){
        $em = $this->getDoctrine()->getManager();
        $ens =null;

       if (isset($_POST['valider'])){
           if(!empty($request->get('nom')) && !empty($request->get('prenom'))){
               $request->request->set('nom',$request->get('nom'));
               $ens = $em->getRepository('BackOfficeBundle:Enseignant')->nomPren2($request->get('nom'),$request->get('prenom'),$id);
               if ($ens != null){
                   $this->render('BackOfficeBundle:Default:rechercheEns.html.twig',array('id'=>$id,'ens'=>$ens,'n'=>$request->get('nom'),'p'=>$request->get('prenom')));
               }else{$this->get('session')->getFlashBag()->add('Erreur', 'Aucun résultat!');}
           }elseif (empty($request->get('nom')) && !empty($request->get('prenom'))){
               $ens = $em->getRepository('BackOfficeBundle:Enseignant')->nomPren($request->get('prenom'),$id);
               if ($ens != null){
                   $this->render('BackOfficeBundle:Default:rechercheEns.html.twig',array('id'=>$id,'ens'=>$ens,'p'=>$request->get('prenom'),'n'=>$request->get('nom')));
               }else{$this->get('session')->getFlashBag()->add('Erreur', 'Aucun résultat!');}
           }elseif (!empty($request->get('nom')) && empty($request->get('prenom'))){
               $ens = $em->getRepository('BackOfficeBundle:Enseignant')->nomPren($request->get('nom'),$id);
               if ($ens != null){
                   $this->render('BackOfficeBundle:Default:rechercheEns.html.twig',array('id'=>$id,'ens'=>$ens,'n'=>$request->get('nom'),'p'=>$request->get('prenom')));
               }else{$this->get('session')->getFlashBag()->add('Erreur', 'Aucun résultat!');}
           }
       }
       return $this->render('BackOfficeBundle:Default:rechercheEns.html.twig',array('id'=>$id,'ens'=>$ens,'n'=>$request->get('nom'),'p'=>$request->get('prenom')));

   }

   public function nomEleveAction(Request $request){
       if ($request->isXmlHttpRequest()){
           $search = $request->request->get('search');

           $em = $this->getDoctrine()->getManager();
           $ens = $em->getRepository('BackOfficeBundle:Eleve')->nomP($search);
           $response =   array();
           if ($ens != null){
               foreach ($ens as $e){
                   $response[] = array("label"=>$e->getNom());
                   $response[] = array("label"=>$e->getPrenom());
               }
               return new JsonResponse($response);
           }
       }
       return $this->render('BackOfficeBundle:Default:rechercheEleve.html.twig',array());
   }

   public function prenomEleveAction(Request $request){
       if ($request->isXmlHttpRequest()){
           $search = $request->request->get('search');

           $em = $this->getDoctrine()->getManager();
           $ens = $em->getRepository('BackOfficeBundle:Eleve')->nomP($search);
           $response =   array();
           if ($ens != null){
               foreach ($ens as $e){
                   $response[] = array("label"=>$e->getPrenom());
                   $response[] = array("label"=>$e->getNom());
               }
               return new JsonResponse($response);
           }
       }
       return $this->render('BackOfficeBundle:Default:rechercheEleve.html.twig',array());
   }

   public function rechercheEleveAction(Request $request,$id){
       $em = $this->getDoctrine()->getManager();
       $ens =null;
       if (isset($_POST['valider'])){
           if(!empty($request->get('nom')) && !empty($request->get('prenom'))){
               $ens = $em->getRepository('BackOfficeBundle:Eleve')->nomPren2($request->get('nom'),$request->get('prenom'),$id);
               if ($ens != null){
                   $this->render('BackOfficeBundle:Default:rechercheEleve.html.twig',array('id'=>$id,'elv'=>$ens,'n'=>$request->get('nom'),'p'=>$request->get('prenom')));
               }else{$this->get('session')->getFlashBag()->add('Erreur', 'Aucun résultat!');}
           }elseif (empty($request->get('nom')) && !empty($request->get('prenom'))){
               $ens = $em->getRepository('BackOfficeBundle:Eleve')->nomPren($request->get('prenom'),$id);
               if ($ens != null){
                   $this->render('BackOfficeBundle:Default:rechercheEleve.html.twig',array('id'=>$id,'elv'=>$ens,'n'=>$request->get('nom'),'p'=>$request->get('prenom')));
               }else{$this->get('session')->getFlashBag()->add('Erreur', 'Aucun résultat!');}
           }elseif (!empty($request->get('nom')) && empty($request->get('prenom'))){
               $ens = $em->getRepository('BackOfficeBundle:Eleve')->nomPren($request->get('nom'),$id);
               if ($ens != null){
                   $this->render('BackOfficeBundle:Default:rechercheEleve.html.twig',array('id'=>$id,'elv'=>$ens,'n'=>$request->get('nom'),'p'=>$request->get('prenom')));
               }else{$this->get('session')->getFlashBag()->add('Erreur', 'Aucun résultat!');}
           }
       }
       return $this->render('BackOfficeBundle:Default:rechercheEleve.html.twig',array('id'=>$id,'elv'=>$ens,'n'=>$request->get('nom'),'p'=>$request->get('prenom')));

   }

   public function nomParentAction(Request $request){
       if ($request->isXmlHttpRequest()){
           $search = $request->request->get('search');

           $em = $this->getDoctrine()->getManager();
           $ens = $em->getRepository('BackOfficeBundle:ParentEleve')->nomP($search);
           $response =   array();
           if ($ens != null){
               foreach ($ens as $e){
                   $response[] = array("label"=>$e->getNom());
                   $response[] = array("label"=>$e->getPrenom());
               }
               return new JsonResponse($response);
           }
       }
       return $this->render('BackOfficeBundle:Default:rechercheParent.html.twig',array());
   }

   public function prenomParentAction(Request $request){
       if ($request->isXmlHttpRequest()){
           $search = $request->request->get('search');

           $em = $this->getDoctrine()->getManager();
           $ens = $em->getRepository('BackOfficeBundle:ParentEleve')->nomP($search);
           $response =   array();
           if ($ens != null){
               foreach ($ens as $e){
                   $response[] = array("label"=>$e->getPrenom());
                   $response[] = array("label"=>$e->getNom());
               }
               return new JsonResponse($response);
           }
       }
       return $this->render('BackOfficeBundle:Default:rechercheParent.html.twig',array());
   }

   public function rechercheParentAction(Request $request,$id){
       $em = $this->getDoctrine()->getManager();
       $ens =null;
       if (isset($_POST['valider'])){
           if(!empty($request->get('nom')) && !empty($request->get('prenom'))){
               $ens = $em->getRepository('BackOfficeBundle:ParentEleve')->nomPren2($request->get('nom'),$request->get('prenom'),$id);
               if ($ens != null){
                   $this->render('BackOfficeBundle:Default:rechercheParent.html.twig',array('id'=>$id,'ens'=>$ens,'n'=>$request->get('nom'),'p'=>$request->get('prenom')));
               }else{$this->get('session')->getFlashBag()->add('Erreur', 'Aucun résultat!');}
           }elseif (empty($request->get('nom')) && !empty($request->get('prenom'))){
               $ens = $em->getRepository('BackOfficeBundle:ParentEleve')->nomPren($request->get('prenom'),$id);
               if ($ens != null){
                   $this->render('BackOfficeBundle:Default:rechercheParent.html.twig',array('id'=>$id,'ens'=>$ens,'n'=>$request->get('nom'),'p'=>$request->get('prenom')));
               }else{$this->get('session')->getFlashBag()->add('Erreur', 'Aucun résultat!');}
           }elseif (!empty($request->get('nom')) && empty($request->get('prenom'))){
               $ens = $em->getRepository('BackOfficeBundle:ParentEleve')->nomPren($request->get('nom'),$id);
               if ($ens != null){
                   $this->render('BackOfficeBundle:Default:rechercheParent.html.twig',array('id'=>$id,'ens'=>$ens,'n'=>$request->get('nom'),'p'=>$request->get('prenom')));
               }else{$this->get('session')->getFlashBag()->add('Erreur', 'Aucun résultat!');}
           }
       }
       return $this->render('BackOfficeBundle:Default:rechercheParent.html.twig',array('id'=>$id,'ens'=>$ens,'n'=>$request->get('nom'),'p'=>$request->get('prenom')));
   }

    public function nomDocAction(Request $request){
        if ($request->isXmlHttpRequest()){
            $search = $request->request->get('search');

            $em = $this->getDoctrine()->getManager();
            $ens = $em->getRepository('BackOfficeBundle:Document')->nom($search);
            $response =   array();
            if ($ens != null){
                foreach ($ens as $e){
                    $response[] = array("label"=>$e->getNom());
                }
                return new JsonResponse($response);
            }
        }
        return $this->render('BackOfficeBundle:Default:rechDoc.html.twig',array());
    }

    public function rechDocElvAction(Request $request,$id){

        $em = $this->getDoctrine()->getManager();
        $elv = $em->getRepository('BackOfficeBundle:Eleve')->find($id);
        $ems = $em->getRepository('BackOfficeBundle:EnsMatClasse')->findBy(array('classeId'=>$elv->getClasse()));
        $doc = null;
        if (isset($_POST['valider'])){
            $this->get('session')->getFlashBag()->add('Erreur', 'Aucun résultat!');
            if (!empty($request->get('nom')) && !empty($request->get('type')) && !empty($request->get('matiere'))){
                $mat = $em->getRepository('BackOfficeBundle:Matiere')->find($request->get('matiere'));
                if ($request->get('type') == 'TOUT'){
                    $doc = $em->getRepository('BackOfficeBundle:Document')->findBy(array('nom'=>$request->get('nom'),'idClasse'=>$elv->getClasse(),'idMat'=>$mat));
                }else{
                $doc = $em->getRepository('BackOfficeBundle:Document')->findBy(array('nom'=>$request->get('nom'),'type'=>$request->get('type'),'idClasse'=>$elv->getClasse(),'idMat'=>$mat));
                }
                if ($doc != null){

                    $this->render('BackOfficeBundle:Default:rechDocElv.html.twig',array('elv'=>$elv,'mat'=>$ems,'doc'=>$doc,'n'=>$request->get('nom'),'matId'=>$request->get('matiere'),'t'=>$request->get('type')));
                }else{$this->get('session')->getFlashBag()->add('Erreur', 'Aucun résultat!');}
            }
            if (empty($request->get('nom')) && !empty($request->get('type')) && !empty($request->get('matiere'))){
                $mat = $em->getRepository('BackOfficeBundle:Matiere')->find($request->get('matiere'));
                if ($request->get('type') == 'TOUT'){
                    $doc = $em->getRepository('BackOfficeBundle:Document')->findBy(array('idClasse'=>$elv->getClasse(),'idMat'=>$mat));
                }else {
                    $doc = $em->getRepository('BackOfficeBundle:Document')->findBy(array('type' => $request->get('type'), 'idClasse' => $elv->getClasse(), 'idMat' => $mat));
                }
                    if ($doc != null){

                    $this->render('BackOfficeBundle:Default:rechDocElv.html.twig',array('elv'=>$elv,'mat'=>$ems,'doc'=>$doc,'n'=>$request->get('nom'),'matId'=>$request->get('matiere'),'t'=>$request->get('type')));
                }else{$this->get('session')->getFlashBag()->add('Erreur', 'Aucun résultat!');}
            }
            if (!empty($request->get('nom')) && empty($request->get('type')) && !empty($request->get('matiere'))){
                $mat = $em->getRepository('BackOfficeBundle:Matiere')->find($request->get('matiere'));
                $doc = $em->getRepository('BackOfficeBundle:Document')->findBy(array('nom'=>$request->get('nom'),'idClasse'=>$elv->getClasse(),'idMat'=>$mat));
                if ($doc != null){

                    $this->render('BackOfficeBundle:Default:rechDocElv.html.twig',array('elv'=>$elv,'mat'=>$ems,'doc'=>$doc,'n'=>$request->get('nom'),'matId'=>$request->get('matiere'),'t'=>$request->get('type')));
                }else{$this->get('session')->getFlashBag()->add('Erreur', 'Aucun résultat!');}
            }
            if (!empty($request->get('nom')) && !empty($request->get('type')) && empty($request->get('matiere'))){
                if ($request->get('type') == 'TOUT'){
                    $doc = $em->getRepository('BackOfficeBundle:Document')->findBy(array('nom'=>$request->get('nom'),'idClasse'=>$elv->getClasse()));
                }else {
                    $doc = $em->getRepository('BackOfficeBundle:Document')->findBy(array('nom' => $request->get('nom'), 'type' => $request->get('type'), 'idClasse' => $elv->getClasse()));
                }
                if ($doc != null){

                    $this->render('BackOfficeBundle:Default:rechDocElv.html.twig',array('elv'=>$elv,'mat'=>$ems,'doc'=>$doc,'n'=>$request->get('nom'),'matId'=>$request->get('matiere'),'t'=>$request->get('type')));
                }else{$this->get('session')->getFlashBag()->add('Erreur', 'Aucun résultat!');}
            }
            if (!empty($request->get('nom')) && empty($request->get('type')) && empty($request->get('matiere'))){
                $doc = $em->getRepository('BackOfficeBundle:Document')->findBy(array('nom'=>$request->get('nom'),'idClasse'=>$elv->getClasse()));
                if ($doc != null){

                    $this->render('BackOfficeBundle:Default:rechDocElv.html.twig',array('elv'=>$elv,'mat'=>$ems,'doc'=>$doc,'n'=>$request->get('nom'),'matId'=>$request->get('matiere'),'t'=>$request->get('type')));
                }else{$this->get('session')->getFlashBag()->add('Erreur', 'Aucun résultat!');}
            }
            if (empty($request->get('nom')) && !empty($request->get('type')) && empty($request->get('matiere'))){
                if ($request->get('type') == 'TOUT'){
                    $doc = $em->getRepository('BackOfficeBundle:Document')->findBy(array('idClasse'=>$elv->getClasse()));
                }else {
                    $doc = $em->getRepository('BackOfficeBundle:Document')->findBy(array('type' => $request->get('type'), 'idClasse' => $elv->getClasse()));
                }
                if ($doc != null){

                    $this->render('BackOfficeBundle:Default:rechDocElv.html.twig',array('elv'=>$elv,'mat'=>$ems,'doc'=>$doc,'n'=>$request->get('nom'),'matId'=>$request->get('matiere'),'t'=>$request->get('type')));
                }else{$this->get('session')->getFlashBag()->add('Erreur', 'Aucun résultat!');}
            }
            if (empty($request->get('nom')) && empty($request->get('type')) && !empty($request->get('matiere'))){
                $mat = $em->getRepository('BackOfficeBundle:Matiere')->find($request->get('matiere'));
                $doc = $em->getRepository('BackOfficeBundle:Document')->findBy(array('idClasse'=>$elv->getClasse(),'idMat'=>$mat));
                if ($doc != null){

                    $this->render('BackOfficeBundle:Default:rechDocElv.html.twig',array('elv'=>$elv,'mat'=>$ems,'doc'=>$doc,'n'=>$request->get('nom'),'matId'=>$request->get('matiere'),'t'=>$request->get('type')));
                }else{$this->get('session')->getFlashBag()->add('Erreur', 'Aucun résultat!');}
            }
        }

        return $this->render('BackOfficeBundle:Default:rechDocElv.html.twig',array('elv'=>$elv,'mat'=>$ems,'doc'=>$doc,'n'=>$request->get('nom'),'matId'=>$request->get('matiere'),'t'=>$request->get('type')));
    }

   public function rechDocAction(Request $request,$id){
       $em = $this->getDoctrine()->getManager();
       $ens = $em->getRepository('BackOfficeBundle:Enseignant')->find($id);
       $cl = $em->getRepository('BackOfficeBundle:EnsMatClasse')->findBy(array('ensId'=>$ens));

       $doc = null;
       if (isset($_POST['valider'])){
           $this->get('session')->getFlashBag()->add('Erreur', 'Aucun résultat!');
           if (!empty($request->get('nom')) && !empty($request->get('type')) && !empty($request->get('classe')) && !empty($request->get('matiere'))){
               $mat = $em->getRepository('BackOfficeBundle:Matiere')->find($request->get('matiere'));
               $classe = $em->getRepository('BackOfficeBundle:Classe')->find($request->get('classe'));
               if ($request->get('type') == 'TOUT'){
                   $doc = $em->getRepository('BackOfficeBundle:Document')->findBy(array('nom'=>$request->get('nom'),'idClasse'=>$classe,'idMat'=>$mat));

               }else{
               $doc = $em->getRepository('BackOfficeBundle:Document')->findBy(array('nom'=>$request->get('nom'),'type'=>$request->get('type'),'idClasse'=>$classe,'idMat'=>$mat));
               }
               if ($doc != null){

                   $this->render('BackOfficeBundle:Default:rechDoc.html.twig',array('ens'=>$ens,'cl'=>$cl,'doc'=>$doc,'n'=>$request->get('nom'),'t'=>$request->get('type'),'cc'=>$request->get('classe')));
               }else{$this->get('session')->getFlashBag()->add('Erreur', 'Aucun résultat!');}
           }
           if (empty($request->get('nom')) && !empty($request->get('type')) && !empty($request->get('classe'))){
               $classe = $em->getRepository('BackOfficeBundle:Classe')->find($request->get('classe'));
               $mat = $em->getRepository('BackOfficeBundle:Matiere')->find($request->get('matiere'));
               if ($request->get('type') == 'TOUT'){
                   $doc = $em->getRepository('BackOfficeBundle:Document')->findBy(array('idClasse'=>$classe,'idMat'=>$mat));

               }else {
                   $doc = $em->getRepository('BackOfficeBundle:Document')->findBy(array('type' => $request->get('type'), 'idClasse' => $classe, 'idMat' => $mat));
               }
               if ($doc != null){
                   $this->render('BackOfficeBundle:Default:rechDoc.html.twig',array('ens'=>$ens,'cl'=>$cl,'doc'=>$doc,'n'=>$request->get('nom'),'t'=>$request->get('type'),'cc'=>$request->get('classe')));
               }else{$this->get('session')->getFlashBag()->add('Erreur', 'Aucun résultat!');}
           }
           if (empty($request->get('nom')) && empty($request->get('type')) && !empty($request->get('classe'))){
               $classe = $em->getRepository('BackOfficeBundle:Classe')->find($request->get('classe'));
               $mat = $em->getRepository('BackOfficeBundle:Matiere')->find($request->get('matiere'));
               $doc = $em->getRepository('BackOfficeBundle:Document')->findBy(array('idClasse'=>$classe,'idMat'=>$mat));
               if ($doc != null){
                   $this->render('BackOfficeBundle:Default:rechDoc.html.twig',array('ens'=>$ens,'cl'=>$cl,'doc'=>$doc,'n'=>$request->get('nom'),'t'=>$request->get('type'),'cc'=>$request->get('classe')));
               }else{$this->get('session')->getFlashBag()->add('Erreur', 'Aucun résultat!');}
           }
           if (!empty($request->get('nom')) && !empty($request->get('type')) && empty($request->get('classe'))){
               if ($request->get('type') == 'TOUT'){
                   $doc = $em->getRepository('BackOfficeBundle:Document')->findBy(array('nom'=>$request->get('nom')));

               }else {
                   $doc = $em->getRepository('BackOfficeBundle:Document')->findBy(array('nom' => $request->get('nom'), 'type' => $request->get('type')));
               }
               if ($doc != null){
                   $this->render('BackOfficeBundle:Default:rechDoc.html.twig',array('ens'=>$ens,'cl'=>$cl,'doc'=>$doc,'n'=>$request->get('nom'),'t'=>$request->get('type'),'cc'=>$request->get('classe')));
               }else{$this->get('session')->getFlashBag()->add('Erreur', 'Aucun résultat!');}
           }
           if (!empty($request->get('nom')) && empty($request->get('type')) && !empty($request->get('classe'))){
               $classe = $em->getRepository('BackOfficeBundle:Classe')->find($request->get('classe'));
               $mat = $em->getRepository('BackOfficeBundle:Matiere')->find($request->get('matiere'));
               $doc = $em->getRepository('BackOfficeBundle:Document')->findBy(array('nom'=>$request->get('nom'),'idClasse'=>$classe,'idMat'=>$mat));
               if ($doc != null){
                   $this->render('BackOfficeBundle:Default:rechDoc.html.twig',array('ens'=>$ens,'cl'=>$cl,'doc'=>$doc,'n'=>$request->get('nom'),'t'=>$request->get('type'),'cc'=>$request->get('classe')));
               }else{$this->get('session')->getFlashBag()->add('Erreur', 'Aucun résultat!');}
           }
           if (!empty($request->get('nom')) && empty($request->get('type')) && empty($request->get('classe'))){
               $doc = $em->getRepository('BackOfficeBundle:Document')->findBy(array('nom'=>$request->get('nom')));
               if ($doc != null){
                   $this->render('BackOfficeBundle:Default:rechDoc.html.twig',array('ens'=>$ens,'cl'=>$cl,'doc'=>$doc,'n'=>$request->get('nom'),'t'=>$request->get('type'),'cc'=>$request->get('classe')));
               }else{$this->get('session')->getFlashBag()->add('Erreur', 'Aucun résultat!');}
           }
           if (empty($request->get('nom')) && !empty($request->get('type')) && empty($request->get('classe'))){
               if ($request->get('type') == 'TOUT'){
                   $doc = $em->getRepository('BackOfficeBundle:Document')->findAll();

               }else {
                   $doc = $em->getRepository('BackOfficeBundle:Document')->findBy(array('type' => $request->get('type')));
               }
               if ($doc != null){
                   $this->render('BackOfficeBundle:Default:rechDoc.html.twig',array('ens'=>$ens,'cl'=>$cl,'doc'=>$doc,'n'=>$request->get('nom'),'t'=>$request->get('type'),'cc'=>$request->get('classe')));
               }else{$this->get('session')->getFlashBag()->add('Erreur', 'Aucun résultat!');}
           }
       }
       return $this->render('BackOfficeBundle:Default:rechDoc.html.twig',array('ens'=>$ens,'doc'=>$doc,'cl'=>$cl,'n'=>$request->get('nom'),'t'=>$request->get('type'),'cc'=>$request->get('classe')));
   }

   public function nomMatiereAction(Request $request){
       if ($request->isXmlHttpRequest()){
           $search = $request->request->get('search');

           $em = $this->getDoctrine()->getManager();
           $ens = $em->getRepository('BackOfficeBundle:Matiere')->rechNom($search);
           $response =   array();
           $a[] = "";
           $bool = true;
           if ($ens != null){
               foreach ($ens as $e){
                   foreach ($a as $item){
                       if ($item == $e->getNom())
                       {$bool = false;}
                   }
                   if ($bool == true)
                   {$a[] =  $e->getNom();}

               }
               foreach ($a as $v){
                   $response[] = array("label"=>$v);
               }
               return new JsonResponse($response);
           }
       }
       return $this->render('BackOfficeBundle:Default:rechercheMatiere.html.twig',array());
   }

   public function rechercheMatiereAction(Request $request,$id){
       $em = $this->getDoctrine()->getManager();
       $class = $em->getRepository('BackOfficeBundle:Classe')->allOrdred();
       $ens =null;
       if (isset($_POST['valider'])){
           if(!empty($request->get('nom')) && !empty($request->get('classe'))){
               $ens = $em->getRepository('BackOfficeBundle:Matiere')->nomClasse($request->get('nom'),$request->get('classe'),$id);
               if ($ens != null){
                   $this->render('BackOfficeBundle:Default:rechercheMatiere.html.twig',array('id'=>$id,'ens'=>$ens,'n'=>$request->get('nom'),'c'=>$request->get('classe')));
               }else{$this->get('session')->getFlashBag()->add('Erreur', 'Aucun résultat!');}
           }elseif (empty($request->get('nom')) && !empty($request->get('classe'))){
               $ens = $em->getRepository('BackOfficeBundle:Matiere')->findByNiveau($request->get('classe'));
               if ($ens != null){
                   $this->render('BackOfficeBundle:Default:rechercheMatiere.html.twig',array('id'=>$id,'ens'=>$ens,'n'=>$request->get('nom'),'c'=>$request->get('classe')));
               }else{$this->get('session')->getFlashBag()->add('Erreur', 'Aucun résultat!');}
           }elseif (!empty($request->get('nom')) && empty($request->get('classe'))){
               $ens = $em->getRepository('BackOfficeBundle:Matiere')->findBy(array('nom'=>$request->get('nom')));
               if ($ens != null){
                   $this->render('BackOfficeBundle:Default:rechercheMatiere.html.twig',array('id'=>$id,'ens'=>$ens,'n'=>$request->get('nom'),'c'=>$request->get('classe')));
               }else{$this->get('session')->getFlashBag()->add('Erreur', 'Aucun résultat!');}
           }
       }
       return $this->render('BackOfficeBundle:Default:rechercheMatiere.html.twig',array('id'=>$id,'ens'=>$ens,'n'=>$request->get('nom'),'c'=>$request->get('classe')));
   }
   public  function nomClasseAction(Request $request){
       if ($request->isXmlHttpRequest()){
           $search = $request->request->get('search');

           $em = $this->getDoctrine()->getManager();
           $ens = $em->getRepository('BackOfficeBundle:Classe')->rechNom($search);
           $response =   array();
           if ($ens != null){
               foreach ($ens as $e){
                   $response[] = array("label"=>$e->getNom());
               }
               return new JsonResponse($response);
           }
       }
       return $this->render('BackOfficeBundle:Default:rechercheClasse.html.twig',array());
   }

   public function anneeClasseAction(Request $request){
       if ($request->isXmlHttpRequest()){
           $search = $request->request->get('search');
           $em = $this->getDoctrine()->getManager();
           $ens = $em->getRepository('BackOfficeBundle:Classe')->rechAnnee($search);
           $response =   array();
           $a[] = "";
           $bool = true;
           if ($ens != null){
               foreach ($ens as $e){
                   foreach ($a as $item){
                       if ($item == $e->getAnneeScolaire())
                       {$bool = false;}
                   }
                   if ($bool == true)
                   {$a[] =  $e->getAnneeScolaire();}

               }
               foreach ($a as $v){
                   $response[] = array("label"=>$v);
               }
               return new JsonResponse($response);
           }
       }
       return $this->render('BackOfficeBundle:Default:rechercheClasse.html.twig',array());
   }

   public function rechercheClasseAction(Request $request,$id){
       $em = $this->getDoctrine()->getManager();
       $ens =null;
       if (isset($_POST['valider'])){
           if(!empty($request->get('nom')) && !empty($request->get('niveau')) && !empty($request->get('scolaire'))){
               $ens = $em->getRepository('BackOfficeBundle:Classe')->findBy(array('nom'=>$request->get('nom'),'niveau'=>$request->get('niveau'),'anneeScolaire'=>$request->get('scolaire')));
               if ($ens != null){
                   $this->render('BackOfficeBundle:Default:rechercheClasse.html.twig',array('id'=>$id,'ens'=>$ens,'n'=>$request->get('nom'),'c'=>$request->get('niveau'),'a'=>$request->get('scolaire')));
               }else{$this->get('session')->getFlashBag()->add('Erreur', 'Aucun résultat!');}
           }elseif (empty($request->get('nom')) && !empty($request->get('niveau')) && !empty($request->get('scolaire'))){
               $ens = $em->getRepository('BackOfficeBundle:Classe')->findBy(array('anneeScolaire'=>$request->get('scolaire'),'niveau'=>$request->get('niveau')));
               if ($ens != null){
                   $this->render('BackOfficeBundle:Default:rechercheClasse.html.twig',array('id'=>$id,'ens'=>$ens,'n'=>$request->get('nom'),'c'=>$request->get('niveau'),'a'=>$request->get('scolaire')));
               }else{$this->get('session')->getFlashBag()->add('Erreur', 'Aucun résultat!');}
           }elseif (!empty($request->get('nom')) && empty($request->get('niveau'))&& !empty($request->get('scolaire'))){
               $ens = $em->getRepository('BackOfficeBundle:Classe')->findBy(array('nom'=>$request->get('nom'),'anneeScolaire'=>$request->get('scolaire')));
               if ($ens != null){
                   $this->render('BackOfficeBundle:Default:rechercheClasse.html.twig',array('id'=>$id,'ens'=>$ens,'n'=>$request->get('nom'),'c'=>$request->get('niveau'),'a'=>$request->get('scolaire')));
               }else{$this->get('session')->getFlashBag()->add('Erreur', 'Aucun résultat!');}
           }elseif (!empty($request->get('nom')) && empty($request->get('niveau')) && empty($request->get('scolaire')) ){
               $ens = $em->getRepository('BackOfficeBundle:Classe')->findBy(array('nom'=>$request->get('nom')));
               if ($ens != null){
                   $this->render('BackOfficeBundle:Default:rechercheClasse.html.twig',array('id'=>$id,'ens'=>$ens,'n'=>$request->get('nom'),'c'=>$request->get('niveau'),'a'=>$request->get('scolaire')));
               }else{$this->get('session')->getFlashBag()->add('Erreur', 'Aucun résultat!');}
           }elseif (empty($request->get('nom')) && !empty($request->get('niveau')) && empty($request->get('scolaire')) ){
               $ens = $em->getRepository('BackOfficeBundle:Classe')->findBy(array('niveau'=>$request->get('niveau')));
               if ($ens != null){
                   $this->render('BackOfficeBundle:Default:rechercheClasse.html.twig',array('id'=>$id,'ens'=>$ens,'n'=>$request->get('nom'),'c'=>$request->get('niveau'),'a'=>$request->get('scolaire')));
               }else{$this->get('session')->getFlashBag()->add('Erreur', 'Aucun résultat!');}
           }elseif (empty($request->get('nom')) && empty($request->get('niveau')) && !empty($request->get('scolaire')) ){
               $ens = $em->getRepository('BackOfficeBundle:Classe')->findBy(array('anneeScolaire'=>$request->get('scolaire')));
               if ($ens != null){
                   $this->render('BackOfficeBundle:Default:rechercheClasse.html.twig',array('id'=>$id,'ens'=>$ens,'n'=>$request->get('nom'),'c'=>$request->get('niveau'),'a'=>$request->get('scolaire')));
               }else{$this->get('session')->getFlashBag()->add('Erreur', 'Aucun résultat!');}
           }elseif (!empty($request->get('nom')) && !empty($request->get('niveau')) && empty($request->get('scolaire')) ){
               $ens = $em->getRepository('BackOfficeBundle:Classe')->findBy(array('nom'=>$request->get('nom'),'niveau'=>$request->get('niveau')));
               if ($ens != null){
                   $this->render('BackOfficeBundle:Default:rechercheClasse.html.twig',array('id'=>$id,'ens'=>$ens,'n'=>$request->get('nom'),'c'=>$request->get('niveau'),'a'=>$request->get('scolaire')));
               }else{$this->get('session')->getFlashBag()->add('Erreur', 'Aucun résultat!');}
           }
       }
       return $this->render('BackOfficeBundle:Default:rechercheClasse.html.twig',array('id'=>$id,'ens'=>$ens,'n'=>$request->get('nom'),'c'=>$request->get('niveau'),'a'=>$request->get('scolaire')));
   }

   public function ecoleAccueilAction(Request $request){

       return $this->render('BackOfficeBundle:Default:ecoleAccueil.html.twig',array());
   }

   public function ajoutEcoleAction(Request $request){

       $em = $this->getDoctrine()->getManager();
       $form = $this->createForm(EcoleType::class);
       $form->handleRequest($request);


       if ($form->isSubmitted() && $form->isValid()){

           $ec = $em->getRepository('BackOfficeBundle:Ecole')->findOneBy(array('username'=>$form['username']->getData()));
           if ($ec != null ){
               $this->get('session')->getFlashBag()->add('Erreur', 'Nom d\'utilisateur existant, Veuillez en choisir un autre!');

           }elseif  ( strlen($form['telephone']->getData())!=8  || !preg_match("/[20|21|22|23|24|25|26|27|28|29|30|31|32|33|40|41|42|43|50|51|52|53|54|55|56|57|58|59|70|71|72|73|77|78|79|90|91|92|93|94|95|96|97|98|99][0-9]{7}$/",$form['telephone']->getData())){
               $this->get('session')->getFlashBag()->add('Erreur', 'Veuillez vérifier le numéro de téléphone saisi!');
           }else{
           $ecole = new Ecole();
           $ecole->setUsername($form['usernme']->getData());
               $converter = new Encryption();
               $encoded = $converter->encode($form['password']->getData());
           $ecole->setPassword($encoded);
           $ecole->setEmail($form['email']->getData());
           $ecole->setAdresse($form['adresse']->getData());
           $ecole->setTelephone($form['telephone']->getData());
           $em->persist($ecole);
           $em->flush();
               $this->get('session')->getFlashBag()->add('Notice', 'Création effectuée avec succés!');
           }
       }

       return $this->render('BackOfficeBundle:Default:ajoutEcole.html.twig',array('form'=>$form->createView()));
   }

   public function afficheEcoleAction(Request $request){

       $em = $this->getDoctrine()->getManager();
       $ecoles = $em->getRepository('BackOfficeBundle:Ecole')->alphaNum(false);
       /**
        * @var $paginator \knp\Component\Pager\Paginator
        */
       $paginator = $this->get('knp_paginator');
       $result = $paginator->paginate($ecoles,
           $request->query->getInt('page',1),
           $request->query->getInt('limit',10));
       return $this->render('BackOfficeBundle:Default:afficheEcole.html.twig',array('ec'=>$result));
   }

   public function modifEcoleAction(Request $request,$id){

       $em = $this->getDoctrine()->getManager();
       $ecole = $em->getRepository('BackOfficeBundle:Ecole')->find($id);

       if (isset($_POST['valider'])){
       if ( strlen($request->get('telephone'))!=8  || !preg_match("/[20|21|22|23|24|25|26|27|28|29|30|31|32|33|40|41|42|43|50|51|52|53|54|55|56|57|58|59|70|71|72|73|77|78|79|90|91|92|93|94|95|96|97|98|99][0-9]{7}$/",$request->get('telephone'))){
           $this->get('session')->getFlashBag()->add('Erreur', 'Veuillez vérifier le numéro de téléphone saisi!');
       }elseif (($request->get('password') != $ecole->getPassword()) && (!empty($request->get('password')))){
           $this->get('session')->getFlashBag()->add('Erreur', 'Veuillez vérifier l\'ancien mot de passe!');
       }elseif ($request->get('npassword') != $request->get('cpassword')){
           $this->get('session')->getFlashBag()->add('Erreur', 'Les nouveaux mots de passe saisis ne sont pas identiques!');
       }else{
           $ecole->setUsername($request->get('username'));
           if (!empty($request->get('cpassword'))) {
               $ecole->setPassword($request->get('cpassword'));
           }
           $ecole->setAdresse($request->get('adresse'));
           $ecole->setEmail($request->get('email'));
           $ecole->setTelephone($request->get('telephone'));
           $em->persist($ecole);
           $em->flush();
           $this->get('session')->getFlashBag()->add('Notice', 'Modification effectuée avec succés!');
       }
       }

       return $this->render('BackOfficeBundle:Default:modifEcole.html.twig',array('ec'=>$ecole));
   }

   public function modifEcole2Action(Request $request,$id){

       $em = $this->getDoctrine()->getManager();
       $ecole = $em->getRepository('BackOfficeBundle:Ecole')->find($id);

       if (isset($_POST['valider'])){
           if ( strlen($request->get('telephone'))!=8  || !preg_match("/[20|21|22|23|24|25|26|27|28|29|30|31|32|33|40|41|42|43|50|51|52|53|54|55|56|57|58|59|70|71|72|73|77|78|79|90|91|92|93|94|95|96|97|98|99][0-9]{7}$/",$request->get('telephone'))){
               $this->get('session')->getFlashBag()->add('Erreur', 'Veuillez vérifier le numéro de téléphone saisi!');
           }elseif (($request->get('password') != $ecole->getPassword()) && (!empty($request->get('password')))){
               $this->get('session')->getFlashBag()->add('Erreur', 'Veuillez vérifier l\'ancien mot de passe!');
           }elseif ($request->get('npassword') != $request->get('cpassword')){
               $this->get('session')->getFlashBag()->add('Erreur', 'Les nouveaux mots de passe saisis ne sont pas identiques!');
           }else{
               $ecole->setUsername($request->get('username'));
               if (!empty($request->get('cpassword'))) {
                   $ecole->setPassword($request->get('cpassword'));
               }
               $ecole->setAdresse($request->get('adresse'));
               $ecole->setEmail($request->get('email'));
               $ecole->setTelephone($request->get('telephone'));
               $em->persist($ecole);
               $em->flush();
               $this->get('session')->getFlashBag()->add('Notice', 'Modification effectuée avec succés!');
           }
       }


       return $this->render('BackOfficeBundle:Default:modifEcole2.html.twig',array('ec'=>$ecole));
   }

   public function suppEcoleAction(Request $request,$id){

       $em = $this->getDoctrine()->getManager();
       $ecole = $em->getRepository('BackOfficeBundle:Ecole')->find($id);
       $ecole->setAnnuler(true);
       $ecole->setDateAnnuler(new \DateTime('now'));
       $em->persist($ecole);
       $em->flush();
       $this->afficheEcoleAction($request,$id);

       return $this->redirectToRoute('_afficheEcole');
   }

   public function histEcoleAction(Request $request){

       $em = $this->getDoctrine()->getManager();
       $ecoles = $em->getRepository('BackOfficeBundle:Ecole')->alphaNum(true);

       /**
        * @var $paginator \knp\Component\Pager\Paginator
        */
       $paginator = $this->get('knp_paginator');
       $result = $paginator->paginate($ecoles,
           $request->query->getInt('page',1),
           $request->query->getInt('limit',10));

       return $this->render('BackOfficeBundle:Default:histEcole.html.twig',array('ec'=>$result));
   }

   public function suppDefEcoleAction(Request $request,$id){

       $em = $this->getDoctrine()->getManager();
       $ecole = $em->getRepository('BackOfficeBundle:Ecole')->find($id);
       $em->remove($ecole);
       $em->flush();

       return $this->redirectToRoute('_histEcole');
   }

   public function restaurerEcoleAction(Request $request,$id){

       $em = $this->getDoctrine()->getManager();
       $ecole = $em->getRepository('BackOfficeBundle:Ecole')->find($id);
       $ecole->setAnnuler(false);
       $ecole->setDateAnnuler(null);
       $em->persist($ecole);
       $em->flush();
       $this->histEcoleAction($request,$id);

       return $this->redirectToRoute('_histEcole');
   }

   public function rechercheEcoleAction(Request $request){

       $em = $this->getDoctrine()->getManager();
       $ecole = null;
       if (isset($_POST['valider'])){
           if (!empty($request->get('username'))  && !empty($request->get('adresse'))){
               $ecole = $em->getRepository('BackOfficeBundle:Ecole')->findBy(array('username'=>$request->get('username'),'adresse'=>$request->get('adresse')));
              if ($ecole != null ){
                $this->render('BackOfficeBundle:Default:rechercheEcole.html.twig',array('ec'=>$ecole,'n'=>$request->get('username'),'a'=>$request->get('adresse')));
              } else{
                  $this->get('session')->getFlashBag()->add('Erreur', 'Aucun résultat!');}
           }elseif (empty($request->get('username'))  && !empty($request->get('adresse'))){
               $ecole = $em->getRepository('BackOfficeBundle:Ecole')->findBy(array('adresse'=>$request->get('adresse')));
               if ($ecole != null ){
                   $this->render('BackOfficeBundle:Default:rechercheEcole.html.twig',array('ec'=>$ecole,'n'=>$request->get('username'),'a'=>$request->get('adresse')));
               } else{
                   $this->get('session')->getFlashBag()->add('Erreur', 'Aucun résultat!');}
           }elseif (!empty($request->get('username'))  && empty($request->get('adresse'))){
               $ecole = $em->getRepository('BackOfficeBundle:Ecole')->findBy(array('username'=>$request->get('username')));
               if ($ecole != null ){
                   $this->render('BackOfficeBundle:Default:rechercheEcole.html.twig',array('ec'=>$ecole,'n'=>$request->get('username'),'a'=>$request->get('adresse')));
               } else{
                   $this->get('session')->getFlashBag()->add('Erreur', 'Aucun résultat!');}
           }elseif(empty($request->get('username'))  && empty($request->get('adresse'))){
               $ecole = $em->getRepository('BackOfficeBundle:Ecole')->findAll();
               if ($ecole != null ){
                   $this->render('BackOfficeBundle:Default:rechercheEcole.html.twig',array('ec'=>$ecole,'n'=>$request->get('username'),'a'=>$request->get('adresse')));
               }
           }
       }

       return $this->render('BackOfficeBundle:Default:rechercheEcole.html.twig',array('ec'=>$ecole,'n'=>$request->get('username'),'a'=>$request->get('adresse')));
   }

    public function loginAAction(Request $request){

        $em = $this->getDoctrine()->getManager();
        if (isset($_POST['cnx'])){
            $username = $request->get('username');
            $password = $request->get('password');
            $ecole = $em->getRepository('BackOfficeBundle:Ecole')->findAll();
            foreach ($ecole as $e) {
                //$converter = new Encryption();
               // $encoded = $converter->decode($e->getPassword());

                if ($e->getPassword() == $request->get('password') && $e->getUsername() == $request->get('username')) {
                    return $this->redirectToRoute('gs_back_office_homepage',['id'=>$e->getId()]);
                }
            else{
                $this->get('session')->getFlashBag()->add('Erreur', 'Veuillez vérifier votre login ou votre mot de passe!');
            }}



            $sup = $em->getRepository('BackOfficeBundle:SupAdmin')->find(1);
            if ($sup->getUsername() == $username && $sup->getPassword() == $password){
                return $this->redirectToRoute('_ecoleAccueil');
            }elseif (($sup->getUsername() != $username && $sup->getPassword() == $password) || ($sup->getUsername() == $username && $sup->getPassword() != $password)){
                $this->get('session')->getFlashBag()->add('Erreur', 'Veuillez vérifier votre login ou votre mot de passe!');
            }
            else{
                $this->get('session')->getFlashBag()->add('Erreur', 'Veuillez vérifier vos données!');
            }
        }
        return $this->render('BackOfficeBundle:Default:loginA.html.twig',array());
    }

   public function loginEAction(Request $request){

       $em = $this->getDoctrine()->getManager();
       if (isset($_POST['cnx'])) {
           $elv = $em->getRepository('BackOfficeBundle:Eleve')->findAll();
           foreach ($elv as $e) {
               $converter = new Encryption();
               $encoded = $converter->decode($e->getMotDePasse());
               if ($encoded == $request->get('password') && $e->getNumInscription() == $request->get('username')) {
                   return $this->redirectToRoute('_espaceElv', ['id' => $e->getId()]);
               } else {
                   $this->get('session')->getFlashBag()->add('Erreur', 'Veuillez vérifier votre login ou votre mot de passe!');
               }
           }
       }

       return $this->render('BackOfficeBundle:Default:loginE.html.twig',array());
   }



    public function loginEnAction(Request $request){

        $em = $this->getDoctrine()->getManager();
        if (isset($_POST['cnx'])){
            $ens = $em->getRepository('BackOfficeBundle:Enseignant')->findAll();
            foreach ($ens as $e) {
                $converter = new Encryption();
                $encoded = $converter->decode($e->getMotDePasse());
                if ($encoded == $request->get('password') && $e->getCin() == $request->get('username')){
                return $this->redirectToRoute('_espaceEns',['id'=>$e->getId()]);
            }else{
                $this->get('session')->getFlashBag()->add('Erreur', 'Veuillez vérifier votre login ou votre mot de passe!');
            }
        }}

        return $this->render('BackOfficeBundle:Default:loginEn.html.twig',array());
    }

    public function loginPAction(Request $request){

        $em = $this->getDoctrine()->getManager();
        if (isset($_POST['cnx'])){
            $par = $em->getRepository('BackOfficeBundle:ParentEleve')->findAll();
            foreach ($par as $e) {
                $converter = new Encryption();
                $encoded = $converter->decode($e->getMotDePasse());
                if ($encoded == $request->get('password') && $e->getCin() == $request->get('username')){
                return $this->redirectToRoute('_espacePar',['id'=>$e->getId()]);
            }else{
                $this->get('session')->getFlashBag()->add('Erreur', 'Veuillez vérifier votre login ou votre mot de passe!');
            }
        }}

        return $this->render('BackOfficeBundle:Default:loginP.html.twig',array());
    }

}
