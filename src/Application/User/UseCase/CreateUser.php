<?php

declare(strict_types=1);

namespace App\Application\User\UseCase;

use App\Application\User\DTO\Request\CreateUserRequest;
use App\Application\User\DTO\Response\UserCreatedResponse;
use App\Application\User\DTO\Response\UserErrorResponse;
use App\Domain\User\Entity\User;
use App\Domain\User\Port\In\CreateUserInterface;
use App\Domain\User\Port\Out\UserRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class CreateUser implements CreateUserInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private UserPasswordHasherInterface $passwordHasher,
        private LoggerInterface $logger,
        private ObjectMapperInterface $mapper,
    ) {
    }

    public function __invoke(CreateUserRequest $request): UserCreatedResponse|UserErrorResponse
    {
        try {
            // 1️⃣ Vérification de l'unicité de l'email
            $normalizedEmail = $request->getNormalizedEmail();

            if (null !== $this->userRepository->findOneBy(['email' => $normalizedEmail])) {
                $this->logger->warning('Tentative de création avec email existant', [
                    'email' => $normalizedEmail,
                ]);

                return UserErrorResponse::emailAlreadyExists($normalizedEmail);
            }

            // 2️⃣ Création de l'utilisateur
            $user = new User();

            // 3️⃣ Hash du mot de passe
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $request->plainPassword
            );
            $user->password = $hashedPassword;

            // 5️⃣ Persistance
            $this->mapper->map($request, $user);
            $this->userRepository->save($user);

            $this->logger->info('Utilisateur créé avec succès', [
                'userId' => $user->id->toRfc4122(),
                'email' => $user->email,
            ]);

            return UserCreatedResponse::fromEntity($user);
        } catch (\Throwable $e) {
            $this->logger->error('Erreur lors de la création de l\'utilisateur', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return UserErrorResponse::validationFailed([
                'system' => 'Une erreur est survenue lors de la création de l\'utilisateur',
            ]);
        }
    }
}
