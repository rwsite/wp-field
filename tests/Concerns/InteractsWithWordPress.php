<?php

namespace Tests\Concerns;

trait InteractsWithWordPress
{
    protected function setUpWordPressMocks(): void
    {
        // Ensure WordPress functions are available
        if (!function_exists('get_the_ID')) {
            function get_the_ID() {
                return 1;
            }
        }
        
        // Reset any global state
        if (function_exists('Brain\\Monkey\\setUp')) {
            Brain\Monkey\setUp();
        }
    }
    
    protected function tearDownWordPressMocks(): void
    {
        // Clean up mocks
        if (function_exists('Brain\\Monkey\\tearDown')) {
            Brain\Monkey\tearDown();
        }
    }
}
