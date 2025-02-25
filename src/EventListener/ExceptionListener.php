<?php
namespace App\EventListener;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $code = $exception->getCode()==0?500:$exception->getCode();
        if($exception instanceof UniqueConstraintViolationException){
            $message = [
                'type' => 'Error',
                'code' => 500,
                'message'=>$exception->getMessage(),
            ];
        }
        else {
            $message = [
                'type' => 'Error',
                'code' => $code,
                'message'=>$exception->getMessage(),
            ];
        }


        $response = new JsonResponse( $message,$message['code']);

        $event->setResponse($response);
    }
}