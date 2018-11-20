<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

class CreateAdminCommand extends Command
{
    private $passwordEncoder;

    private $entityManager;

    private $validator;

    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    ) {
        
        $this->passwordEncoder = $passwordEncoder;
        $this->entityManager = $entityManager;
        $this->validator = $validator;

        parent::__construct();
    }

    public function configure()
    {
        $this->setName('app:create-admin-user')
             ->setDescription('Creates an admin user')
             ->addArgument('email', InputArgument::REQUIRED, 'The user email')
             ->addArgument('password', InputArgument::REQUIRED, 'Plain password to be encrypted');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $user = new User();
        
        $password = $this->passwordEncoder->encodePassword($user, $input->getArgument('password'));
        
        $user->setEmail($input->getArgument('email'))
             ->setPassword($password)
             ->setRoles('ROLE_ADMIN');

        $errors = $this->validator->validate($user);

        if (count($errors) > 0) {
            $output->writeln((string) $errors);
        } else {
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $output->writeln(['Admin user successfully created and added to the database']);
        }
    }
}
