<?php

namespace Kanboard\Plugin\SendTaskToWebhook\Action;

use Kanboard\Model\TaskModel;
use Kanboard\Core\Http\Client;
use Kanboard\Action\Base;

/**
 * Send a task to a webhook URL
 */
class SendTaskToWebhook extends Base
{
    /**
     * Get automatic action description
     *
     * @access public
     * @return string
     */
    public function getDescription()
    {
        return t('Send task to a webhook URL');
    }

    /**
     * Get the list of compatible events
     *
     * @access public
     * @return array
     */
    public function getCompatibleEvents()
    {
        return [
            TaskModel::EVENT_CREATE,
            TaskModel::EVENT_UPDATE,
            TaskModel::EVENT_CLOSE,
            TaskModel::EVENT_OPEN,
            TaskModel::EVENT_MOVE_COLUMN,
        ];
    }

    /**
     * Get the action parameters
     *
     * @access public
     * @return array
     */
    public function getActionParameters()
    {
        $projectId = $this->getProjectId();
        $columns = [];
        if ($projectId) {
            $columns = $this->columnModel->getList($projectId);
        }

        return [
            'webhook_url' => [
                'label' => t('Webhook URL'),
                'type' => 'text',
                'required' => true,
                'default' => '',
            ],
            'column_id' => [
                'label' => t('Target column (only for move column)'),
                'type' => 'select',
                'options' => $columns,
                'default' => 0,
                'required' => false,
            ],
        ];
    }

    private function getEventOptions()
    {
        return [
            TaskModel::EVENT_CREATE => t('Task Created'),
            TaskModel::EVENT_UPDATE => t('Task Updated'),
            TaskModel::EVENT_CLOSE => t('Task Closed'),
            TaskModel::EVENT_OPEN => t('Task Reopened'),
            TaskModel::EVENT_MOVE_COLUMN => t('Task Moved to Another Column'),
        ];
    }

    /**
     * Get the required parameter for the action (defined by the user)
     *
     * @access public
     * @return array
     */
    public function getActionRequiredParameters()
    {
        return array(
            'webhook_url' => t('Webhook URL'),
            'column_id' => t('Target column (only for move column)'),
        );
    }

    /**
     * Get the required parameter for the event
     *
     * @access public
     * @return string[]
     */
    public function getEventRequiredParameters()
    {
        return ['task_id'];
    }

    /**
     * Check if the event data meet the action condition
     *
     * @access public
     * @param  array   $data   Event data dictionary
     * @return bool
     */
    public function hasRequiredCondition(array $data)
    {
        $eventName = $data['event_name'] ?? '(sin nombre)';

        $targetColumnId = (int) $this->getParam('column_id');
        $currentColumnId = (int) ($data['task']['column_id'] ?? 0);


        // Si no hay coincidencia, no se ejecuta
        if ($targetColumnId > 0 && $currentColumnId !== $targetColumnId) {
            return false;
        }

        return true;

    }

    /**
     * Execute the action (send task data to webhook)
     * 
     * @access public
     * @param  array   $data   Event data dictionary
     * @return bool
     */
    public function doAction(array $data): bool
    {
        $taskId = $data['task_id'] ?? null;

        if ($data['task']['project_id'] != $this->getProjectId()) {
            return false;
        }

        $task = $this->taskFinderModel->getDetails($taskId);
        if (!$task) {
            return false;
        }

        $payload = [
            'event' => $data['event_name'],
            'task_id' => $taskId,
            'task' => $task,
            'project' => $task['project_name'] ?? 'unknown',
            'triggered_at' => date('c'),
        ];

        $url = $this->getParam('webhook_url');

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return true;
        }

        return false;
    }
}
