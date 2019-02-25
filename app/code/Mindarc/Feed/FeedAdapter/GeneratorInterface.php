<?php
/**
 * Copyright © pmk, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mindarc\Feed\FeedAdapter;

/**
 * Interface GeneratorInterface
 * @package Mindarc\Feed\FeedAdapter
 */
interface GeneratorInterface
{
    /**
     * @param string $filePrefix
     * @return mixed
     */
    public function generate(string $filePrefix);
}
