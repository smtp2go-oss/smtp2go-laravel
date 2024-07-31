<?php

namespace Tests;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class SymfonyTestKernel extends BaseKernel
{
    use \Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;

    public function registerBundles(): iterable
    {
        return [
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new class extends \Symfony\Component\HttpKernel\Bundle\Bundle
            {
                public function shutdown(): void
                {
                    restore_exception_handler();
                }
            }
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(function ($container) {
            $container->loadFromExtension('framework', [
                'test' => true,
                'mailer' => [
                    'dsn' => 'smtp://null',
                ],
            ]);
        });
    }
}
