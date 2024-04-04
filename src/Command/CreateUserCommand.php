<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Ramsey\Uuid\Uuid;

class CreateUserCommand extends Command
{

    protected static $defaultName = 'app:user:create';
    private array $validRoles;

    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $userPasswordHasher
    ) {
        parent::__construct();
//        TODO: add into validator or service
        $this->validRoles = explode(',', $_ENV["VALID_ROLES"]);
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                'email',
                InputArgument::REQUIRED,
                'user\'s email'
            )
            ->addArgument(
                'password',
                InputArgument::REQUIRED,
                'user\'s password'
            )
            ->addArgument(
                'role',
                InputArgument::REQUIRED,
                'user\'s role'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('email');
        $plainPassword = $input->getArgument('password');
        $role = $input->getArgument('role');

        if (!\is_string($email)) {
            $output->writeln('<error>Por favor, especifica un email válido</error>');
            return Command::FAILURE;
        }

        if (!\is_string($plainPassword)) {
            $output->writeln('<error>Por favor, especifica una contraseña válida</error>');
            return Command::FAILURE;
        }

        if(!in_array($role, $this->validRoles)) {
            $output->writeln('<error>Por favor, especifica un role valido</error>');
            return Command::FAILURE;
        }

        $user = new User(
            Uuid::uuid4(),
            $email
        );
        $password = $this->userPasswordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($password);
        $user->setRoles([$role]);
        $this->userRepository->save($user);

        $output->writeln(sprintf('Created user with email: <comment>%s</comment>', $email));
        return Command::SUCCESS;
    }
}
