<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // Exclude cleaner AJAX routes that properly send CSRF tokens in headers
        '~^cleaner/tasks/\d+/update-location$',
        '~^cleaner/tasks/\d+/progress$',
        '~^cleaner/tasks/\d+/status$',
        '~^cleaner/tasks/\d+/accept$',
        'cleaner/location',
        'cleaner/status',
        // Exclude admin AJAX routes
        '~^admin/users/\d+/toggle-status$',
        '~^admin/cleaners/\d+/toggle-status$',
        '~^admin/services/\d+/toggle-status$',
    ];
}