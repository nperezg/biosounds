<?php

namespace BioSounds\Listener\Exception;

interface ExceptionListenerInterface
{
    /**
     * @param \Throwable $throwable
     */
    public function handleException(\Throwable $throwable);
}
