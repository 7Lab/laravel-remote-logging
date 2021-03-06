<?php

namespace SevenLab\RemoteLogging;

use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Throwable;

class RemoteLogging
{
    protected $enabled;
    protected $client;

    public function __construct()
    {
        $this->enabled = config('remote-logging.enabled');
        $this->client =  new Client([
            'base_uri' => config('remote-logging.url'),
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . config('remote-logging.token'),
            ]
        ]);
    }

    /**
     * Log an exception to the server defined in remote-logging config
     *
     * @param \Throwable $exception The Throwable/Exception object.
     * @param array $data Additional attributes to pass with this event.
     * @return void
     */
    public function captureException(Throwable $exception)
    {
        if ($this->enabled === false || $this->shouldntReport($exception)) {
            return;
        }

        $message = $exception->getMessage();
        if (empty($message)) {
            $message = 'Not applicable';
        }

        if (method_exists($exception, 'getStatusCode')) {
            $code = $exception->getStatusCode();
        } else {
            $code = $exception->getCode();
        }

        $data = [
            'type' => 'Laravel',
            'status_code' => $code,
            'error' => $message,
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'stacktrace' => $exception->getTraceAsString(),
            'env' => app()->environment(),
            'url' => request()->fullUrl(),
        ];

        $this->sendTo('projects/error/logs', $data);
    }

    public function sendFailedJob($data)
    {
        if ($this->enabled === false) {
            return;
        }

        $this->sendTo('projects/error/failed-job', $data);
    }

    protected function sendTo($path, $data)
    {
        try {
            $this->client->post($path, [
                'form_params' => $data
            ]);
        } catch (Throwable $e) { }
    }

    /**
     * Determine if the exception should be reported.
     *
     * @param  \Throwable  $e
     * @return bool
     */
    public function shouldReport(Throwable $exception)
    {
        return ! $this->shouldntReport($exception);
    }

    /**
     * Determine if the exception is in the "do not report" list.
     *
     * @param  \Throwable  $e
     * @return bool
     */
    protected function shouldntReport(Throwable $exception)
    {
        $dontReport = config('remote-logging.dontReport');

        return ! is_null(Arr::first($dontReport, function ($type) use ($exception) {
            return $exception instanceof $type;
        }));
    }
}
