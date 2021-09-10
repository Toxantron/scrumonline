<?php

use JiraRestApi\Configuration\ArrayConfiguration;
use JiraRestApi\Issue\IssueService;
use JiraRestApi\JiraException;

/*
 * Jira controller class to handle all Jira operations
 */

class JiraController extends ControllerBase
{

    public function getIssues()
    {
        $parameters = array_replace_recursive($this->jiraConfiguration, $_POST);
        $jql = $parameters['jql'];

        $CurrentIssueService = new IssueService(new ArrayConfiguration(
            array(
                'jiraHost' => $parameters['base_url'],
                'jiraUser' => $parameters['username'],
                'jiraPassword' => $parameters['password']
            )
        ));

        if (!$parameters['disable_jira_fields']) {
            $jql = 'project = ' . $parameters['project'] . ' ' . $parameters['jql'];
        }

        try {
            $ret = $CurrentIssueService->search($jql, 0, $parameters['issue-limit']);
            $ret->base_url = $parameters['base_url'];
        } catch (JiraException $e) {
            $ret = "" . $e;
        }

        return $ret;
    }
}

return new JiraController($entityManager);
