<?php

use Symfony\Bundle\FrameworkBundle\HttpCache\HttpCache;
use Symfony\Component\HttpFoundation\Request;

class AppCache extends HttpCache implements \FOS\HttpCache\SymfonyCache\CacheInvalidation
{
    use \FOS\HttpCache\SymfonyCache\EventDispatchingHttpCache;

    public function fetch(Request $request, $catch = false)
    {
        return parent::fetch($request, $catch);
    }

    public function __construct(\Symfony\Component\HttpKernel\HttpKernelInterface $kernel, $cacheDir = null)
    {
        parent::__construct($kernel, $cacheDir);

        $this->addSubscriber(new \FOS\HttpCache\SymfonyCache\CustomTtlListener());
        $this->addSubscriber(new \FOS\HttpCache\SymfonyCache\PurgeListener());
        $this->addSubscriber(new \FOS\HttpCache\SymfonyCache\RefreshListener());
        $this->addSubscriber(new \FOS\HttpCache\SymfonyCache\UserContextListener());

//        if (isset($options['debug']) && $options['debug']) {
//            $this->addSubscriber(new \FOS\HttpCache\SymfonyCache\DebugListener());
//        }
    }

    protected function invalidate(Request $request, $catch = false)
    {
        if ('PURGE' !== $request->getMethod()) {
            return parent::invalidate($request, $catch);
        }

        $response = new \Symfony\Component\HttpFoundation\Response();

        if ($this->getStore()->purge($request->getUri())) {
            $response->setStatusCode(200, 'Purged');
        } else {
            $response->setStatusCode(404, 'Not found');
        }

        return $response;
    }
}
