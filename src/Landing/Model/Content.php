<?php

namespace Landing\Model;

use Silex\Application;

class Content
{

    private $app;

    public function __construct(Application $app, $previewMode = false)
    {
        $this->app = $app;
        $this->previewMode = $previewMode;
    }

    /**
     * Get contents from database
     * @param  integer $parentId Parent ID
     * @return [type]            Results List
     */
    public function getContents($parentId = 0)
    {
        $cacheKey = 'getContents_'.$parentId;

        $contents = [];

        // if not in preview mode, check cache for contents
        if (false == $this->previewMode) {
            $contents = $this->app['memcache']->get($cacheKey);
        }

        // If no contents from cache, go fetch some
        if (empty($contents)) {

            $sql = "SELECT *
                FROM contents C
                WHERE
                    C.parent_id = :parentId
                ";

            $contents = $this->app['db']->fetchAll(
                $sqlCampaigns,
                [
                    ':parentId' => $parentId
                ]
            );


            // if not in preview mode, put contents in cache
            if (false == $this->previewMode) {
                $this->app['memcache']->set($cacheKey, $contents, 600);
            }
        }

        return $contents;
    }
}
