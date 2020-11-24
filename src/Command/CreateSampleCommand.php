<?php

namespace App\Command;

use App\Entity\Document;
use App\Entity\Folder;
use App\Entity\User;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateSampleCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:create-sample';
    protected $passwordEncoder;
    protected $container;


    protected function configure()
    {
    }

    public function __construct(ContainerInterface $container, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->container = $container;
        $this->passwordEncoder = $passwordEncoder;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $owner = (new User())
            ->setCompany('Aa')
            ->setEmail("gestionnaire@gmail.com")
            ->setRoles(['ROLE_ADMIN']);
        $owner->setPassword($this->passwordEncoder->encodePassword(
            $owner,
            'denis'
        ));

        $em = $this->container ->get('doctrine.orm.default_entity_manager');
        $em->getConnection()->beginTransaction();
        $em->persist($owner);
        $em->getConnection()->commit();
        $em->flush();
        $em->clear();

        $user = (new User())
            ->setCompany('Aa')
            ->setEmail("destinataire@gmail.com")
            ->setRoles(['ROLE_ADMIN']);
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            'denis'
        ));

        $rentalFolder = (new Folder())
            ->setName('Dossier de location')
            ->setCategory('rental')
            ->setIsStandard(true)
        ;
        $rentalDocuments = [
            (new Document())->setName("Papier d'identité (permis, passport, carte nationale)")->setFolder($rentalFolder),
            (new Document())->setName("Revenus (3 derniers bulletins de salaires, bourse)")->setFolder($rentalFolder),
            (new Document())->setName("Contrat de travail")->setFolder($rentalFolder),
            (new Document())->setName("Justificatif de domicile")->setFolder($rentalFolder),
            (new Document())->setName("Caution")->setFolder($rentalFolder),
        ];

        $em = $this->container ->get('doctrine.orm.default_entity_manager');
        $em->getConnection()->beginTransaction();
        foreach($rentalDocuments as $rentalDocument) {
            $em->persist($rentalDocument);
        }
        $em->persist($rentalFolder);
        $em->persist($user);
        $em->getConnection()->commit();
        $em->flush();
        $em->clear();

        return Command::SUCCESS;
    }
}