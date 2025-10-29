<?php
# src/Security/GoogleAuthenticator.php
namespace App\Infrastructure\Shared\Security;

use App\Application\User\DTO\UserDTO;
use App\Application\UserProfil\DTO\UserProfilDTO;
use App\Domain\User\Port\In\CreateUserInterface;
use App\Domain\User\Port\Out\UserRepositoryInterface;
use App\Domain\User\ValueObject\UserRole;
use App\Domain\User\ValueObject\UserStatus;
use App\Domain\UserProfil\Port\In\CreateUserProfilInterface;
use App\Infrastructure\User\Service\UserService;
use League\OAuth2\Client\Provider\GoogleUser;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

final class GoogleAuthenticator extends OAuth2Authenticator
{
    private ClientRegistry $clientRegistry;
    private RouterInterface $router;

    public function __construct(
        ClientRegistry $clientRegistry,
        EntityManagerInterface $entityManager,
        RouterInterface $router,
        private readonly UserRepositoryInterface $userRepository,
        private readonly CreateUserInterface $createUser,
        private readonly CreateUserProfilInterface $createUserProfil,
        private readonly UserService $userService,
    )
    {
        $this->clientRegistry = $clientRegistry;
        $this->router = $router;
    }

    public function supports(Request $request): ?bool
    {
        // continue ONLY if the current ROUTE matches the check ROUTE
        return $request->attributes->get('_route') === 'connect_google_check';
    }

    public function authenticate(Request $request): Passport
    {
        $client = $this->clientRegistry->getClient('google');
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function () use ($accessToken, $client) {
                /** @var GoogleUser $googleUser */
                $googleUser = $client->fetchUserFromToken($accessToken);

                $email = $googleUser->getEmail();

                // Cherche l'utilisateur existant
                $existingUser = $this->userRepository->getByGoogleId($googleUser->getId());

                // ✅ Si l'utilisateur existe déjà, on le retourne
                if ($existingUser) {
                    return $existingUser;
                }

                // ❌ Sinon, on le crée
                $plainPassword = $this->userService->generateRandomPassword();

                $uDto = new UserDTO();
                $uDto->email = $email;
                $uDto->roles = [UserRole::CLIENT->value];
                $uDto->status = UserStatus::ACTIVE;
                $uDto->lang = 'fr';
                $uDto->isVerified = true;
                $uDto->plainPassword = $plainPassword;
                $uDto->googleId = $googleUser->getId();
                $uDto->hostedDomain = $googleUser->getHostedDomain();

                $user = ($this->createUser)($uDto);

                $pDto = new UserProfilDTO();
                $pDto->firstname = $googleUser->getFirstName();
                $pDto->lastname = $googleUser->getLastName();
                $pDto->agreeToTerms = true;
                $pDto->userUuid = $user->id;
                $pDto->emailContact = $email;

                ($this->createUserProfil)($pDto);

                // ✅ On retourne le USER nouvellement créé !
                return $user;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Redirige vers la page d'accueil ou le tableau de bord
        return new RedirectResponse($this->router->generate('application_home'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new RedirectResponse($this->router->generate('app_login', [
            'error' => $message
        ]));
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return new RedirectResponse($this->router->generate('app_login'));
    }

}
