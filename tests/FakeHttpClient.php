<?php

declare(strict_types=1);

namespace App\Tests;

use App\Http\HttpClientInterface;
use App\Http\HttpResponse;

final class FakeHttpClient implements HttpClientInterface
{
    /** @var list<HttpResponse> */
    private array $responses = [];

    /** @var list<array{method: string, url: string, headers: array<string, string>, body: string|null}> */
    public array $requests = [];

    public function queueResponse(HttpResponse $response): void
    {
        $this->responses[] = $response;
    }

    public function request(string $method, string $url, array $headers = [], ?string $body = null): HttpResponse
    {
        $this->requests[] = [
            'method' => $method,
            'url' => $url,
            'headers' => $headers,
            'body' => $body,
        ];

        return array_shift($this->responses) ?? new HttpResponse(500, '');
    }
}
