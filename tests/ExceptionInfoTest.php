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

namespace GrahamCampbell\Tests\Exceptions;

use Exception;
use GrahamCampbell\Exceptions\ExceptionInfo;
use GrahamCampbell\Exceptions\ExceptionInfoInterface;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * This is the exception info test class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class ExceptionInfoTest extends AbstractTestCase
{
    public function testExistingError()
    {
        $info = $this->app->make(ExceptionInfoInterface::class)->generate(new BadRequestHttpException('Made a mess.'), 'foo', 400);

        $expected = ['id' => 'foo', 'code' => 400, 'name' => 'Bad Request', 'detail' => 'Made a mess.', 'summary' => 'Made a mess.'];

        $this->assertSame($expected, $info);
    }

    public function testShortError()
    {
        $info = $this->app->make(ExceptionInfoInterface::class)->generate(new PreconditionFailedHttpException(':('), 'bar', 412);

        $expected = ['id' => 'bar', 'code' => 412, 'name' => 'Precondition Failed', 'detail' => 'The server does not meet one of the preconditions that the requester put on the request.', 'summary' => 'Houston, We Have A Problem.'];

        $this->assertSame($expected, $info);
    }

    public function testLongError()
    {
        $info = $this->app->make(ExceptionInfoInterface::class)->generate(new UnprocessableEntityHttpException('Made a mess a really really big mess this time. Everything has broken, and unicorns are crying.'), 'baz', 422);

        $expected = ['id' => 'baz', 'code' => 422, 'name' => 'Unprocessable Entity', 'detail' => 'Made a mess a really really big mess this time. Everything has broken, and unicorns are crying.', 'summary' => 'Houston, We Have A Problem.'];

        $this->assertSame($expected, $info);
    }

    public function testBadError()
    {
        $info = $this->app->make(ExceptionInfoInterface::class)->generate(new Exception('Ooops.'), 'test', 666);

        $expected = ['id' => 'test', 'code' => 500, 'name' => 'Internal Server Error', 'detail' => 'An error has occurred and this resource cannot be displayed.', 'summary' => 'Houston, We Have A Problem.'];

        $this->assertSame($expected, $info);
    }

    public function testHiddenError()
    {
        $info = $this->app->make(ExceptionInfoInterface::class)->generate(new InvalidArgumentException('Made another mess.'), 'hi', 503);

        $expected = ['id' => 'hi', 'code' => 503, 'name' => 'Service Unavailable', 'detail' => 'The server is currently unavailable. It may be overloaded or down for maintenance.', 'summary' => 'Houston, We Have A Problem.'];

        $this->assertSame($expected, $info);
    }

    public function testFallback()
    {
        $info = (new ExceptionInfo())->generate(new BadRequestHttpException(), 'foo', 400);

        $expected = ['id' => 'foo', 'code' => 500, 'name' => 'Internal Server Error', 'detail' => 'An error has occurred and this resource cannot be displayed.', 'summary' => 'Houston, We Have A Problem.'];

        $this->assertSame($expected, $info);
    }

    public function testErrorsJson()
    {
        $path = __DIR__.'/../resources/errors.json';

        $this->assertFileExists($path);

        $decoded = json_decode(file_get_contents($path), true);

        $this->assertCount(40, $decoded);
        $this->assertSame('I\'m a teapot', $decoded[418]['name']);
        $this->assertSame('The resource that is being accessed is locked.', $decoded[423]['message']);
    }
}
