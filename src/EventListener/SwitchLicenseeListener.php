<?php

namespace App\EventListener;

use App\Entity\User;
use App\Helper\LicenseeHelper;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Security;

#[AsEventListener(event: 'kernel.request')]
class SwitchLicenseeListener
{
    public function __construct(
        protected Security $security,
        protected LicenseeHelper $licenseeHelper,
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        $licenseeCode = $request->get('_switch_licensee');

        if (null === $licenseeCode || '' === $licenseeCode) {
            return;
        }

        /** @var User $user */
        $user = $this->security->getUser();
        $licensee = $user->getLicenseeWithCode($licenseeCode);
        if (null !== $licensee) {
            $this->licenseeHelper->setSelectedLicensee($licensee);
        }

        $request->query->remove('_switch_licensee');
        $request->server->set(
            'QUERY_STRING',
            http_build_query($request->query->all(), '', '&'),
        );
        $response = new RedirectResponse($request->getUri(), 302);

        $event->setResponse($response);
    }
}
