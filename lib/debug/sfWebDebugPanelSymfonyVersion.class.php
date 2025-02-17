<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWebDebugPanelSymfonyVersion adds a panel to the web debug toolbar with the symfony version.
 *
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * @version    SVN: $Id$
 */
class sfWebDebugPanelSymfonyVersion extends sfWebDebugPanel
{
    public function getTitle()
    {
        return '<span id="sfWebDebugSymfonyVersion">'.SYMFONY_VERSION.'</span>';
    }

    public function getPanelTitle()
    {
    }

    public function getPanelContent()
    {
    }
}
