<?php

namespace App\Exceptions;

use App\Http\Common\ResponseUtils;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    const DEFAULT_MESSAGE = '系统繁忙，请稍后再试！';

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthenticationException::class,
        AuthorizationException::class,
        HttpException::class,
        ValidationException::class,
        ModelNotFoundException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = ['password', 'password_confirmation',];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception $exception
     * @return void
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param Exception $exception
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Exception $exception)
    {
        if ($request->expectsJson() && $exception instanceof BizException) {
            return ResponseUtils::build(false, $exception->getCode(), $exception->getMessage());
        }

        if ($request->expectsJson() && $exception instanceof ModelNotFoundException) {
            return ResponseUtils::notFound('未找到该记录');
        }

        return parent::render($request, $exception);

    }

    /**
     * @param Request $request
     * @param AuthenticationException $exception
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $request->expectsJson()
            ? ResponseUtils::unauthorized($exception->getMessage())
            : redirect('/');
    }


    /**
     * @param ValidationException $e
     * @param Request $request
     * @return JsonResponse|\Illuminate\Http\Response|null|\Symfony\Component\HttpFoundation\Response
     */
    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        if ($e->response) {
            return $e->response;
        }

        return $request->expectsJson()
            ? ResponseUtils::build(false, $e->status, $this->getValidationMessage($e->errors()), null)
            : $this->invalid($request, $e);
    }


    /**
     * @param Request $request
     * @param Exception $e
     * @return JsonResponse
     */
    protected function prepareJsonResponse($request, Exception $e)
    {
        $status = 500;
        $headers = [];

        if ($e instanceof HttpException) {
            $status = $e->getStatusCode();
            $headers =  $e->getHeaders();
        }

        return ResponseUtils::build(false, $status, $this->getExceptionMessage($e), $this->getExceptionData($e),
            $headers, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    /**
     * @param array $errors
     * @return string
     */
    protected function getValidationMessage(array $errors)
    {
        $errorMessage = '';
        $i = 0;

        foreach ($errors as $errorArray) {
            foreach ($errorArray as $error) {
                $errorMessage .=  $i ++ === 0 ? $error : "<p style='margin-left:50px;text-align:left'>$error</p>";
            }
        }

        return $errorMessage;
    }

    /**
     * 获取异常信息
     *
     * @param Exception $e
     * @return string
     */
    protected function getExceptionMessage(Exception $e) {
        return $this->determineEnvironment() ? $e->getMessage() : static::DEFAULT_MESSAGE;
    }

    /**
     * 获取异常数据
     *
     * @param Exception $e
     * @return array
     */
    protected function getExceptionData(Exception $e) {
        return $this->determineEnvironment() ? $this->convertExceptionToArray($e) : [];
    }

    /**
     * 非生产环境或debug模式
     *
     * @return bool
     */
    protected function determineEnvironment() {
        return !App::environment('production') || config('app.debug');
    }
}
