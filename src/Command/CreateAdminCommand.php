<?php
namespace App\Command;

use App\Entity\User;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Ramsey\Uuid\Uuid;

class CreateAdminCommand extends ContainerAwareCommand
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('app:admin-create')
            ->setDescription('Creates admin of project.')
            ->setHelp('This command allows you to create a admin')
            ->setDefinition(
                new InputDefinition(array(
                    new InputOption('name', 'a', InputOption::VALUE_REQUIRED, "Name of user"),
                    new InputOption('surname', 'b', InputOption::VALUE_OPTIONAL, "Surname of user"),
                    new InputOption('email', 'c', InputOption::VALUE_REQUIRED, "Email of user"),
                    new InputOption('password', 'd', InputOption::VALUE_REQUIRED, "Password of user"),
                    new InputOption('telephone', 'f', InputOption::VALUE_REQUIRED, "Telephone of user"),
                ))
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Admin Creator',
            '================================================',
            '',
        ]);

        $name = $input->getOption('name');
        $surname = $input->getOption('surname');
        $email = $input->getOption('email');
        $password = $input->getOption('password');
        $telephone = $input->getOption('telephone');

        $user = new User();

        $user->setPassword($this->passwordEncoder->encodePassword($user, $password));
        $user->setName($name);
        $user->setSurname($surname);
        $user->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $user->setEmail($email);
        $user->setApiToken($uuid4 = Uuid::uuid4());
        $user->setTelephone($telephone);
        
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getEntityManager();
        $em->persist($user);
        $em->flush();

        $output->writeln("Admin created!");
    }
}

//   bin/console app:admin-create -a "Artem" -b "Tantsiura" -c "qwe@qwe.qwe" -d "qwerty" -f "0963164593"
