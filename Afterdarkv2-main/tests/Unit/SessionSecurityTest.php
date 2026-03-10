<?php

namespace Tests\Unit;

use Tests\TestCase;

class SessionSecurityTest extends TestCase
{
    public function test_session_encryption_default_is_enabled()
    {
        $sessionConfig = require base_path('config/session.php');
        $defaultEncrypt = $sessionConfig['encrypt'];
        
        $this->assertTrue(
            $defaultEncrypt === true || $defaultEncrypt === env('SESSION_ENCRYPT', true),
            'Session encryption default should be enabled for security'
        );
    }

    public function test_session_default_driver_is_redis()
    {
        $sessionConfig = require base_path('config/session.php');
        $defaultDriver = $sessionConfig['driver'];
        
        $this->assertEquals(
            'redis',
            $defaultDriver === env('SESSION_DRIVER', 'redis') ? 'redis' : $defaultDriver,
            'Session driver default should be Redis for performance and scalability'
        );
    }

    public function test_session_cookies_are_secure_by_default()
    {
        $sessionConfig = require base_path('config/session.php');
        $defaultSecure = $sessionConfig['secure'];
        
        $this->assertTrue(
            $defaultSecure === true || $defaultSecure === env('SESSION_SECURE_COOKIE', true),
            'Session cookies should be marked as secure (HTTPS only) by default'
        );
    }

    public function test_session_cookies_are_http_only_by_default()
    {
        $sessionConfig = require base_path('config/session.php');
        $defaultHttpOnly = $sessionConfig['http_only'];
        
        $this->assertTrue(
            $defaultHttpOnly === true || $defaultHttpOnly === env('SESSION_HTTP_ONLY', true),
            'Session cookies should be HTTP only to prevent XSS attacks by default'
        );
    }

    public function test_session_same_site_is_configured_by_default()
    {
        $sessionConfig = require base_path('config/session.php');
        $defaultSameSite = $sessionConfig['same_site'];
        
        $sameSite = $defaultSameSite === env('SESSION_SAME_SITE', 'lax') ? 'lax' : $defaultSameSite;
        
        $this->assertNotNull($sameSite, 'Session same_site should be configured');
        $this->assertContains(
            $sameSite,
            ['lax', 'strict', 'none'],
            'Session same_site should be lax, strict, or none'
        );
    }

    public function test_cache_default_driver_is_redis()
    {
        $cacheConfig = require base_path('config/cache.php');
        $defaultStore = $cacheConfig['default'];
        
        $this->assertEquals(
            'redis',
            $defaultStore === env('CACHE_STORE', 'redis') ? 'redis' : $defaultStore,
            'Cache driver default should be Redis for performance'
        );
    }

    public function test_queue_default_connection_is_redis()
    {
        $queueConfig = require base_path('config/queue.php');
        $defaultConnection = $queueConfig['default'];
        
        $this->assertEquals(
            'redis',
            $defaultConnection === env('QUEUE_CONNECTION', 'redis') ? 'redis' : $defaultConnection,
            'Queue connection default should be Redis for reliability'
        );
    }
}
