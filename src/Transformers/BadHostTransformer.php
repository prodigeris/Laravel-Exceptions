<?php

declare(strict_types=1);

/*
 * This file is part of Laravel Exceptions.
 *
 * (c) Graham Campbell <graham@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Exceptions\Transformers;

use Exception;
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * This is the bad host transformer class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class BadHostTransformer implements TransformerInterface
{
    /**
     * Transform the provided exception.
     *
     * @param \Exception $exception
     *
     * @return \Exception
     */
    public function transform(Exception $exception)
    {
        if ($exception instanceof SuspiciousOperationException) {
            $exception = new NotFoundHttpException('Bad hostname provided.', $exception, $exception->getCode());
        }

        return $exception;
    }
}
