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

/**
 * Update operation.
 *
 * @param object $entity The treated object
 * @param array  $params Additional arguments
 *
 * @return bool False on failure or true if everything worked well
 *
 * @throws RuntimeException Thrown if executing the workflow action fails
 */
function ZikulaRoutesModule_operation_update(&$entity, $params)
{

    // get attributes read from the workflow
    if (isset($params['nextstate']) && !empty($params['nextstate'])) {
        // assign value to the data object
        $entity['workflowState'] = $params['nextstate'];
        if ($params['nextstate'] == 'archived') {
            // bypass validator (for example an end date could have lost it's "value in future")
            $entity['_bypassValidation'] = true;
        }
    }
    
    // get entity manager
    $serviceManager = \ServiceUtil::getManager();
    $entityManager = $serviceManager->get('doctrine.orm.default_entity_manager');
    $logger = $serviceManager->get('logger');
    $logArgs = ['app' => 'ZikulaRoutesModule', 'user' => $serviceManager->get('zikula_users_module.current_user')->get('uname')];
    
    // save entity data
    try {
        //$this->entityManager->transactional(function($entityManager) {
        $entityManager->persist($entity);
        $entityManager->flush();
        //});
        $result = true;
        $logger->notice('{app}: User {user} updated an entity.', $logArgs);
    } catch (\Exception $e) {
        $logger->error('{app}: User {user} tried to update an entity, but failed.', $logArgs);
        throw new \RuntimeException($e->getMessage());
    }

    // return result of this operation
    return $result;
}
