<?php

/*
 * This file is part of the ChrisyueAutoJsonResponseBundle package.
 *
 * (c) Chrisyue <http://chrisyue.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chrisyue\Bundle\AutoJsonResponseBundle\Tests\EventListener;

use Chrisyue\Bundle\AutoJsonResponseBundle\EventListener\AutoJsonResponseListener;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\Serializer\Serializer;

class AutoJsonResponseListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testOnKernelViewWithNonMasterRequest()
    {
        $event = $this->prophesizeEvent(false);

        $this->doTest($event);
    }

    public function testOnKernelViewWithNonJsonRequest()
    {
        $event = $this->prophesizeEvent(true);

        $this->doTest($event);
    }

    public function testOnKernelViewWithNullControllerResult()
    {
        $event = $this->prophesizeEvent(true, true, null);
        $event->setResponse(new JsonResponse(null, 204))->shouldBeCalledTimes(1);

        $this->doTest($event);
    }

    public function testOnKernelViewWithResponseControllerResult()
    {
        $event = $this->prophesizeEvent(true, true, new JsonResponse());

        $this->doTest($event);
    }

    public function testOnKernelViewWithNonObjectControllerResult()
    {
        $nonObject = ['foo' => 'bar'];
        $event = $this->prophesizeEvent(true, true, $nonObject);
        $event->setResponse(new JsonResponse($nonObject))->shouldBeCalledTimes(1);

        $this->doTest($event);
    }

    public function testOnKernelViewWithPostRequestAndNonObjectControllerResult()
    {
        $nonObject = 'hello world';
        $event = $this->prophesizeEvent(true, true, $nonObject, true);
        $event->setResponse(new JsonResponse($nonObject, 201))->shouldBeCalledTimes(1);

        $this->doTest($event);
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testOnKernelViewWithObjectControllerResultButNoSerializer()
    {
        $object = new \stdClass();
        $event = $this->prophesizeEvent(true, true, $object);

        $this->doTest($event);
    }

    public function testOnKernelViewWithObjectControllerResult()
    {
        $object = new \stdClass();
        $event = $this->prophesizeEvent(true, true, $object);

        $normalized = ['foo' => 'bar'];
        $serializer = $this->prophesizeSerializer($object, $normalized);
        $event->setResponse(new JsonResponse($normalized))->shouldBeCalledTimes(1);

        $this->doTest($event, $serializer);
    }

    public function testOnKernelViewWithPostRequestAndObjectControllerResult()
    {
        $object = new \stdClass();
        $event = $this->prophesizeEvent(true, true, $object, true);

        $normalized = ['bar' => 'foo'];
        $serializer = $this->prophesizeSerializer($object, $normalized);
        $event->setResponse(new JsonResponse($normalized, 201))->shouldBeCalledTimes(1);

        $this->doTest($event, $serializer);
    }

    private function prophesizeEvent($isMasterRequest, $isRequestJson = false, $controllerResult = null, $isMethodPost = false)
    {
        $event = $this->prophesize(GetResponseForControllerResultEvent::class);
        $event->isMasterRequest()->shouldBeCalledTimes(1)->willReturn($isMasterRequest);

        if (!$isMasterRequest) {
            return $event;
        }

        $request = $this->prophesize(Request::class);
        $request->getRequestFormat()->shouldBeCalledTimes(1)->willReturn($isRequestJson ? 'json' : '!json');
        $event->getRequest()->shouldBeCalledTimes(1)->willReturn($request->reveal());

        if (!$isRequestJson) {
            return $event;
        }

        $event->getControllerResult()->shouldBeCalledTimes(1)->willReturn($controllerResult);
        if ($controllerResult instanceof Response || null === $controllerResult) {
            return $event;
        }

        $request->isMethod('POST')->shouldBeCalledTimes(1)->willReturn($isMethodPost);

        return $event;
    }

    private function doTest(ObjectProphecy $event, ObjectProphecy $serializer = null)
    {
        $listener = new AutoJsonResponseListener(null === $serializer ? null : $serializer->reveal());
        $listener->onKernelView($event->reveal());
    }

    private function prophesizeSerializer(\stdClass $object, array $normalized)
    {
        $serializer = $this->prophesize(Serializer::class);
        $serializer->normalize($object)->shouldBeCalledTimes(1)->willReturn($normalized);

        return $serializer;
    }
}
