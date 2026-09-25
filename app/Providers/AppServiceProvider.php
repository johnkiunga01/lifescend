<?php

namespace App\Providers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Transport\Smtp\Stream\SocketStream;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerLenientSmtpTransport();
    }

    /**
     * Register an SMTP mail transport that does not verify the server's TLS
     * certificate. The shared hosting mail server presents a wildcard cert
     * (*.web-hosting.com) that does not match mail.lifescend.events, which
     * otherwise breaks STARTTLS. Laravel's default transport offers no way to
     * relax peer verification, so we build the transport ourselves.
     */
    protected function registerLenientSmtpTransport(): void
    {
        Mail::extend('smtp', function (array $config) {
            $tls = ($config['scheme'] ?? null) === 'smtps'
                || (int) ($config['port'] ?? 0) === 465;

            $transport = new EsmtpTransport(
                $config['host'] ?? '127.0.0.1',
                (int) ($config['port'] ?? 587),
                $tls,
            );

            $stream = $transport->getStream();

            if ($stream instanceof SocketStream) {
                $stream->setStreamOptions([
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true,
                    ],
                ]);

                if (! empty($config['timeout'])) {
                    $stream->setTimeout($config['timeout']);
                }
            }

            if (! empty($config['username'])) {
                $transport->setUsername($config['username']);
            }

            if (! empty($config['password'])) {
                $transport->setPassword($config['password']);
            }

            if (! empty($config['local_domain'])) {
                $transport->setLocalDomain($config['local_domain']);
            }

            return $transport;
        });
    }
}
