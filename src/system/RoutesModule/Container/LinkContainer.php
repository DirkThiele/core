<?php
/**
 * Routes.
 *
 * @copyright Zikula contributors (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Zikula contributors <support@zikula.org>.
 * @link http://www.zikula.org
 * @link http://zikula.org
 * @version Generated by ModuleStudio 0.7.0 (http://modulestudio.de).
 */

namespace Zikula\RoutesModule\Container;

use SecurityUtil;
use Zikula\Core\LinkContainer\LinkContainerInterface;
use Zikula\RoutesModule\Container\Base\LinkContainer as BaseLinkContainer;

/**
 * This is the link container service implementation class.
 */
class LinkContainer extends BaseLinkContainer
{
    public function getLinks($type = LinkContainerInterface::TYPE_ADMIN)
    {
        $links = [];

        if (SecurityUtil::checkPermission($this->getBundleName() . ':Route:', '::', ACCESS_ADMIN)) {
            $links[] = array('url' => $this->router->generate('zikularoutesmodule_route_view', array('lct' => 'admin')),
                'text' => $this->translator->__('Routes'),
                'title' => $this->translator->__('Route list'));
            $links[] = array('url' => $this->router->generate('zikularoutesmodule_route_reload', array('lct' => 'admin')),
                'text' => $this->translator->__('Reload routes'),
                'title' => $this->translator->__('Reload routes'));
            $links[] = array('url' => $this->router->generate('zikularoutesmodule_route_renew', array('lct' => 'admin')),
                'text' => $this->translator->__('Reload multilingual routing settings'),
                'title' => $this->translator->__('Reload multilingual routing settings'));
            $links[] = array('url' => $this->router->generate('zikularoutesmodule_route_dumpjsroutes', array('lct' => 'admin')),
                'text' => $this->translator->__('Dump exposed js routes to file'),
                'title' => $this->translator->__('Dump exposed js routes to file'));
        }

        return $links;
    }
    // feel free to add own extensions here
}