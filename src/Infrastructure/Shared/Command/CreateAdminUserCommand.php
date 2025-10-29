<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Command;

use App\Application\User\DTO\Request\CreateUserRequest;
use App\Domain\User\Port\In\CreateUserInterface;
use App\Domain\User\Port\Out\UserRepositoryInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Crée un utilisateur administrateur',
)]
class CreateAdminUserCommand extends Command
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly CreateUserInterface $createUser,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::OPTIONAL, 'Email de l\'administrateur')
            ->addArgument('password', InputArgument::OPTIONAL, 'Mot de passe')
            ->addOption('first-name', 'f', InputOption::VALUE_REQUIRED, 'Prénom')
            ->addOption('last-name', 'l', InputOption::VALUE_REQUIRED, 'Nom')
            ->addOption('super-admin', 's', InputOption::VALUE_NONE, 'Créer un super administrateur (ROLE_SUPER_ADMIN)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('🔐 Création d\'un Administrateur');

        // 1️⃣ Récupération de l'email
        $email = $input->getArgument('email');
        if (! $email) {
            $question = new Question('Email de l\'administrateur');
            $question->setValidator(function (?string $value): string {
                if (empty($value)) {
                    throw new \RuntimeException('L\'email ne peut pas être vide');
                }
                if (! filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    throw new \RuntimeException('L\'email n\'est pas valide');
                }

                return $value;
            });
            $email = $io->askQuestion($question);
        }

        $normalizedEmail = strtolower(trim($email));

        // 2️⃣ Vérification de l'existence de l'email
        if (null !== $this->userRepository->findOneBy(['email' => $normalizedEmail])) {
            $io->error(sprintf('Un utilisateur existe déjà avec l\'email "%s"', $normalizedEmail));

            return Command::FAILURE;
        }

        // 3️⃣ Récupération du mot de passe
        $password = $input->getArgument('password');
        if (! $password) {
            $question = new Question('Mot de passe (min. 12 caractères)');
            $question->setHidden(true);
            $question->setHiddenFallback(false);

            $password = $io->askQuestion($question);

            // Confirmation du mot de passe
            $confirmQuestion = new Question('Confirmer le mot de passe');
            $confirmQuestion->setHidden(true);
            $confirmQuestion->setHiddenFallback(false);
            $confirmPassword = $io->askQuestion($confirmQuestion);

            if ($password !== $confirmPassword) {
                $io->error('Les mots de passe ne correspondent pas');

                return Command::FAILURE;
            }
        }

        // 4️⃣ Récupération du prénom
        $firstName = $input->getOption('first-name');
        if (! $firstName) {
            $question = new Question('Prénom', 'Admin');
            $firstName = $io->askQuestion($question);
        }

        // 5️⃣ Récupération du nom
        $lastName = $input->getOption('last-name');
        if (! $lastName) {
            $question = new Question('Nom', 'LeadForge');
            $lastName = $io->askQuestion($question);
        }

        // 7️⃣ Détermination du rôle
        $isSuperAdmin = $input->getOption('super-admin');
        $role = $isSuperAdmin ? 'ROLE_SUPER_ADMIN' : 'ROLE_ADMIN';

        // 8️⃣ Création de l'utilisateur
        try {
            $userDTO = new CreateUserRequest();
            $userDTO->email = $email;
            $userDTO->firstName = $firstName;
            $userDTO->lastName = $lastName;
            $userDTO->plainPassword = $password;
            $userDTO->roles = ['ROLE_USER', $role];

            $userDTO->isVerified = true; // Admin vérifié par défaut

            // Validation
//            $violations = $this->validator->validate($userDTO);
//            if (count($violations) > 0) {
//                $io->error('Erreurs de validation :');
//                foreach ($violations as $violation) {
//                    $io->text(sprintf('  • %s: %s', $violation->getPropertyPath(), $violation->getMessage()));
//                }
//
//                return Command::FAILURE;
//            }

            // Persistance
            ($this->createUser)($userDTO);
            // 9️⃣ Affichage du résumé
            $io->success('Administrateur créé avec succès !');

            $io->note([
                'Vous pouvez maintenant vous connecter avec ces identifiants.',
                sprintf('Email : %s', $email),
            ]);

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $io->error([
                'Une erreur est survenue lors de la création de l\'administrateur',
                $e->getMessage(),
            ]);

            return Command::FAILURE;
        }
    }
}
