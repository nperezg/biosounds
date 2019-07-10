<?php


namespace BioSounds\Listener\Exception;

use BioSounds\Exception\ForbiddenException;
use BioSounds\Exception\NotAuthenticatedException;
use Twig\Environment;

class ExceptionListener implements ExceptionListenerInterface
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var string
     */
    private $title;

    /**
     * AppExceptionListener constructor.
     * @param Environment $twig
     * @param string $title
     */
    public function __construct(Environment $twig, string $title)
    {
        $this->twig = $twig;
        $this->title = $title;
    }

    /**
     * @param \Throwable $throwable
     * @return string|void
     * @throws \Exception
     */
    public function handleException(\Throwable $throwable)
    {
        error_log($throwable);
        if ($throwable instanceof NotAuthenticatedException) {
            header('Location: '.APP_URL);
        }

        echo $this->twig->render('index.html.twig', [
            'title' => $this->title,
            'error' => $throwable->getMessage(),
        ]);
    }
}
