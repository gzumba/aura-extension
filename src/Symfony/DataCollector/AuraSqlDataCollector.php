<?php

namespace Zumba\Symfony\DataCollector;

use Aura\Sql\Profiler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class AuraSqlDataCollector extends DataCollector
{
    private Profiler $profiler;

    public function __construct(Profiler $profiler)
    {
        $this->profiler = $profiler;
    }

    public function collect(Request $request, Response $response, \Throwable $throwable = null)
    {
        $this->data['profiles'] = $this->profiler->getProfiles();
        $cnt = 0;
        foreach ($this->profiler->getProfiles() as $profile) {
            if ($profile['function'] === 'perform') {
                $cnt++;
            }
        }
        $this->data['query_cnt'] = $cnt;
        $this->data['duration'] = array_sum(array_map(function ($profile) {
            return $profile['duration'];
        }, $this->profiler->getProfiles()));
    }

    public function getProfiles()
    {
        return $this->data['profiles'];
    }

    public function getDuration()
    {
        return $this->data['duration'];
    }

    public function getCount()
    {
        return $this->data['query_cnt'];
    }

    /**
     * Returns the name of the collector.
     *
     * @return string The collector name
     *
     * @api
     */
    public function getName()
    {
        return "aura";
    }


    public function reset()
    {
        $this->data = [];
        $this->profiler->resetProfiles();
    }
}

